<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UsageLog;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageHelper;
use Barryvdh\DomPDF\Facade\Pdf;


class BillingController extends Controller
{
    /**
     * Display billing dashboard
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription?->plan;

        // Get usage stats
        $todayUsage = $user->getTodayUsage();
        $devicesCount = $user->devices()->count();
        $contactsCount = $user->contacts()->count();

        // Get limits based on plan
        $maxDevices = $plan ? ($plan->isUnlimited('devices') ? '∞' : $plan->max_devices) : 1;
        $maxMessages = $plan ? ($plan->isUnlimited('messages') ? '∞' : $plan->max_messages_per_day) : 100;
        $maxContacts = $plan ? ($plan->isUnlimited('contacts') ? '∞' : $plan->max_contacts) : 100;

        // Calculate percentages
        $devicesPercent = $maxDevices === '∞' ? 100 : min(100, ($devicesCount / $maxDevices) * 100);
        $messagesPercent = $maxMessages === '∞' ? 100 : min(100, ($todayUsage->messages_sent / $maxMessages) * 100);
        $contactsPercent = $maxContacts === '∞' ? 100 : min(100, ($contactsCount / $maxContacts) * 100);

        // Get recent invoices
        $invoices = $user->invoices()->latest()->take(10)->get();

        return view('billing.index', compact(
            'subscription',
            'plan',
            'todayUsage',
            'devicesCount',
            'contactsCount',
            'maxDevices',
            'maxMessages',
            'maxContacts',
            'devicesPercent',
            'messagesPercent',
            'contactsPercent',
            'invoices'
        ));
    }

    /**
     * Display available plans
     */
    public function plans()
    {
        $plans = Plan::active()->ordered()->get();
        /** @var User $user */
        $user = Auth::user();
        $currentPlan = $user->currentPlan();

        return view('billing.plans', compact('plans', 'currentPlan'));
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, Plan $plan)
    {
        // Handle potentially accidental GET requests
        if ($request->isMethod('get')) {
            return redirect()->route('billing.plans')
                ->with('info', 'Silakan pilih paket yang diinginkan.');
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if already has pending subscription
        $pendingSubscription = $user->subscriptions()->pending()->first();
        if ($pendingSubscription) {
            $pendingInvoice = $pendingSubscription->invoices()->pending()->first();

            if ($pendingInvoice) {
                return redirect()->route('billing.checkout', ['invoice' => $pendingInvoice->id])
                    ->with('info', 'Anda memiliki pembayaran yang belum selesai.');
            }

            // Zombie subscription found (no invoice), delete it to allow new subscription
            $pendingSubscription->delete();
        }

        // Check if already on this plan
        $currentPlan = $user->currentPlan();
        if ($currentPlan && $currentPlan->id === $plan->id) {
            return redirect()->route('billing.index')
                ->with('info', 'Anda sudah berlangganan paket ini.');
        }

        // Create pending subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'auto_renew' => false,
            'payment_method' => 'bank_transfer',
        ]);

        // Create invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => Invoice::generateNumber(),
            'amount' => $plan->price,
            'tax' => $plan->price * 0.11, // PPN 11%
            'total' => $plan->price * 1.11,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'due_date' => now()->addDays(3),
        ]);

        return redirect()->route('billing.checkout', ['invoice' => $invoice->id]);
    }

    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display checkout page
     */
    public function checkout(Invoice $invoice)
    {
        // Ensure user owns this invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription = $invoice->subscription;
        $plan = $subscription->plan;

        // Generate Snap Token
        $snapToken = $this->midtransService->createSnapToken($invoice);
        $paymentError = $snapToken ? null : 'Gagal membuat sesi pembayaran. Silakan hubungi admin atau cek konfigurasi Midtrans.';

        return view('billing.checkout', compact('invoice', 'subscription', 'plan', 'snapToken', 'paymentError'));
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request, Invoice $invoice)
    {
        // Ensure user owns this invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Convert to Base64 (Store directly in DB)
        $base64 = ImageHelper::fileToBase64($request->file('payment_proof'));

        $invoice->update([
            'payment_proof' => $base64,
        ]);

        // Send WhatsApp Notification to Admin
        try {
            $adminNumber = config('services.whatsapp.admin_number');
            $senderDeviceId = config('services.whatsapp.sender_device_id');

            if ($adminNumber && $senderDeviceId) {
                /** @var \App\Services\WhatsAppService $whatsappService */
                $whatsappService = app(\App\Services\WhatsAppService::class);

                // Use Base64 directly for WhatsApp Media
                $imageUrl = $base64;

                $message = "*BUKTI PEMBAYARAN BARU*\n";
                $message .= "Invoice: #{$invoice->invoice_number}\n";
                $message .= "User: {$invoice->user->name} ({$invoice->user->email})\n";
                $message .= "Total: {$invoice->formatted_total}\n\n";
                $message .= "Silakan cek dashboard atau balas pesan ini untuk aksi cepat:\n";
                $message .= "- Ketik *ACC {$invoice->invoice_number}* untuk Validasi Lunas.\n";
                $message .= "- Ketik *REJ {$invoice->invoice_number}* untuk Tolak.";

                $response = $whatsappService->sendMessage(
                    (int) $senderDeviceId,
                    $adminNumber,
                    $message,
                    'image',
                    $imageUrl
                );

                Log::info('Admin notification attempt', [
                    'invoice' => $invoice->invoice_number,
                    'device_id' => $senderDeviceId,
                    'response' => $response
                ]);
            } else {
                Log::warning('Admin notification skipped: missing config', [
                    'admin_number' => $adminNumber,
                    'sender_device_id' => $senderDeviceId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification for invoice payment', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('billing.checkout', ['invoice' => $invoice->id])
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin.');
    }

    /**
     * Display invoices list
     */
    public function invoices()
    {
        /** @var User $user */
        $user = Auth::user();
        $invoices = $user->invoices()->latest()->paginate(10);

        return view('billing.invoices', compact('invoices'));
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(Invoice $invoice)
    {
        // Ensure user owns this invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('billing.invoice_pdf', compact('invoice'));
        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Toggle auto-renewal
     */
    public function toggleAutoRenew()
    {
        $subscription = Auth::user()->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'Tidak ada langganan aktif.');
        }

        $subscription->update([
            'auto_renew' => !$subscription->auto_renew,
        ]);

        $message = $subscription->auto_renew
            ? 'Auto-renewal diaktifkan.'
            : 'Auto-renewal dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * Update tax information
     */
    public function updateTaxInfo(Request $request)
    {
        $request->validate([
            'npwp' => 'nullable|string|max:30',
            'company_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
        ]);

        // Update on pending invoices
        /** @var User $user */
        $user = Auth::user();
        $user->invoices()->pending()->update([
            'npwp' => $request->npwp,
            'company_name' => $request->company_name,
            'billing_address' => $request->billing_address,
        ]);

        return back()->with('success', 'Informasi pajak berhasil diperbarui.');
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $subscription = Auth::user()->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'Tidak ada langganan aktif.');
        }

        $subscription->cancel();

        return back()->with('success', 'Langganan berhasil dibatalkan. Akses akan tetap aktif hingga periode berakhir.');
    }


}

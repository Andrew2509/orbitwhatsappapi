<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Plan;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display the welcome page with dynamic data.
     */
    public function index()
    {
        // 1. Fetch active pricing plans
        $plans = Plan::active()->ordered()->get();

        // 2. Calculate global stats
        // Total messages sent (outbound + success)
        $totalMessages = Message::where('direction', 'outbound')
            ->whereIn('status', ['sent', 'delivered', 'read'])
            ->count();

        // Success rate calculation
        $totalOutbound = Message::where('direction', 'outbound')->count();
        $successRate = $totalOutbound > 0
            ? round(($totalMessages / $totalOutbound) * 100, 1)
            : 99.9;

        // Formatted total messages (e.g., 12.4K)
        $formattedMessages = $totalMessages >= 1000
            ? round($totalMessages / 1000, 1) . 'K+'
            : $totalMessages;

        // 3. Static FAQs (can be moved to DB later if needed)
        $faqs = [
            [
                'question' => 'Apakah layanan ini resmi?',
                'answer' => 'Orbit API menggunakan infrastruktur yang aman dan mengikuti protokol standar untuk memastikan pengiriman pesan yang stabil. Kami menyediakan interface API yang memudahkan integrasi dengan platform Anda.'
            ],
            [
                'question' => 'Bagaimana cara pembayarannya?',
                'answer' => 'Kami mendukung berbagai metode pembayaran melalui payment gateway Midtrans, termasuk Transfer Bank (VA), E-Wallet (Gopay, ShopeePay), dan Kartu Kredit.'
            ],
            [
                'question' => 'Dapatkah saya mencoba sebelum membeli?',
                'answer' => 'Tentu! Anda dapat mendaftar untuk akun gratis dan mendapatkan akses Free Trial untuk mencoba fitur-fitur utama kami sebelum memutuskan untuk berlangganan.'
            ],
            [
                'question' => 'Berapa banyak perangkat yang bisa saya hubungkan?',
                'answer' => 'Jumlah perangkat tergantung pada paket yang Anda pilih. Paket Basic mendukung 1 perangkat, Pro hingga 5 perangkat, dan Enterprise dapat mengelola jumlah perangkat yang tidak terbatas.'
            ]
        ];

        return view('welcome', compact('plans', 'formattedMessages', 'successRate', 'faqs'));
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Send WhatsApp Notification to Admin
            $adminNumber = config('services.whatsapp.admin_number');
            $senderDeviceId = config('services.whatsapp.sender_device_id');

            if ($adminNumber && $senderDeviceId) {
                $whatsappService = app(WhatsAppService::class);

                $content = "*CONTACT FORM INQUIRY*\n\n";
                $content .= "Nama: {$request->name}\n";
                $content .= "Email: {$request->email}\n";
                $content .= "Subjek: {$request->subject}\n";
                $content .= "Pesan: {$request->message}\n\n";
                $content .= "-- Orbit API Bot";

                $whatsappService->sendMessage((int)$senderDeviceId, $adminNumber, $content);
            }

            return back()->with('success', 'Pesan Anda telah terkirim! Tim kami akan segera menghubungi Anda.');
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengirim pesan. Silakan coba lagi nanti atau hubungi kami melalui WhatsApp.');
        }
    }
}

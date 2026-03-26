<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MidtransWebhookController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function handle(Request $request)
    {
        $notification = $this->midtransService->parseNotification();

        if (!$notification) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        $invoice = Invoice::where('invoice_number', $orderId)->first();

        if (!$invoice) {
            Log::error('Midtrans Webhook: Invoice not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        Log::info('Midtrans Webhook Received', [
            'order_id' => $orderId,
            'status' => $transaction,
            'payment_type' => $type
        ]);

        DB::beginTransaction();
        try {
            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $invoice->update(['status' => 'pending']);
                    } else {
                        $this->markAsPaid($invoice);
                    }
                }
            } else if ($transaction == 'settlement') {
                $this->markAsPaid($invoice);
            } else if ($transaction == 'pending') {
                $invoice->update(['status' => 'pending']);
            } else if ($transaction == 'deny') {
                $invoice->update(['status' => 'failed']);
            } else if ($transaction == 'expire') {
                $invoice->update(['status' => 'expired']);
            } else if ($transaction == 'cancel') {
                $invoice->update(['status' => 'failed']);
            }

            DB::commit();
            return response()->json(['message' => 'Webhook processed']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Webhook Processing Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    protected function markAsPaid(Invoice $invoice)
    {
        if ($invoice->isPaid()) {
            return;
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $subscription = $invoice->subscription;
        if ($subscription && $subscription->status !== 'active') {
            $subscription->update([
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);
        }
    }
}

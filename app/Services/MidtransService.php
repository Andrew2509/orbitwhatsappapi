<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        Log::debug('Midtrans Configuration Loaded', [
            'server_key_prefix' => substr(Config::$serverKey, 0, 15) . '...',
            'is_production' => Config::$isProduction,
            'merchant_id' => config('services.midtrans.merchant_id'),
        ]);
    }

    /**
     * Create Snap Token for an Invoice
     */
    public function createSnapToken(Invoice $invoice): ?string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $invoice->invoice_number,
                'gross_amount' => (int) $invoice->total,
            ],
            'customer_details' => [
                'first_name' => $invoice->user->name,
                'email' => $invoice->user->email,
                'phone' => $invoice->user->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'plan-' . ($invoice->subscription->plan->id ?? 'unknown'),
                    'price' => (int) $invoice->amount,
                    'quantity' => 1,
                    'name' => $invoice->subscription->plan->name ?? 'Subscription Plan',
                ],
                [
                    'id' => 'tax',
                    'price' => (int) $invoice->tax,
                    'quantity' => 1,
                    'name' => 'PPN 11%',
                ]
            ],
            'enabled_payments' => [
                'credit_card', 'mandiri_clickpay', 'cimb_clicks',
                'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel',
                'permata_va', 'bca_va', 'bni_va', 'bri_va', 'other_va',
                'gopay', 'indomaret', 'danamon_online', 'akulaku',
                'shopeepay', 'kredivo', 'alfamart'
            ],
        ];

        try {
            $token = Snap::getSnapToken($params);
            Log::debug('Midtrans Snap Token Generated', [
                'invoice' => $invoice->invoice_number,
                'token_preview' => substr($token, 0, 10) . '...'
            ]);
            return $token;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error', [
                'invoice' => $invoice->invoice_number,
                'error_type' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'params_sanitized' => array_merge($params, [
                    'customer_details' => array_merge($params['customer_details'], ['phone' => '***'])
                ])
            ]);
            return null;
        }
    }

    /**
     * Parse Midtrans Notification
     */
    public function parseNotification()
    {
        try {
            return new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Parse Error', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

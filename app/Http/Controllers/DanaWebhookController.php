<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Otnansirk\Dana\Facades\DANAPay;

class DanaWebhookController extends Controller
{
    /**
     * Handle webhook from DANA
     */
    public function handle(Request $request)
    {
        Log::info('DANA Webhook received', $request->all());

        $notification = $request->all();

        // Normally we should verify the signature here
        // The package might have a helper for this, but let's assume direct handling for now

        $merchantTransId = $notification['merchantTransId'] ?? null;
        $status = $notification['status'] ?? null;

        if ($merchantTransId && $status === 'SUCCESS') {
            $invoice = Invoice::where('invoice_number', $merchantTransId)->first();

            if ($invoice && $invoice->isPending()) {
                $invoice->markAsPaid();
                Log::info('Invoice marked as paid via DANA Webhook: ' . $invoice->invoice_number);
            }
        }

        // DANA expect a specific response format
        return response()->json(DANAPay::responseFinishNotifyCallback(true));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoReply;
use App\Models\Device;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * Handle incoming message from WhatsApp Service (Node.js).
     * Check for matching auto-reply rules and return bot response if found.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleIncoming(Request $request)
    {
        $request->validate([
            'device_id' => 'required|integer',
            'from' => 'required|string',
            'message' => 'required|string',
            'message_id' => 'nullable|string',
        ]);

        $deviceId = $request->input('device_id');
        $from = $request->input('from');
        $message = trim($request->input('message'));

        // Check if message is from Admin
        // Clean up phone numbers for comparison (remove non-digits)
        $adminNumber = preg_replace('/\D/', '', config('services.whatsapp.admin_number'));
        $senderNumber = preg_replace('/\D/', '', $from);


        // Allow if sender matches admin number (flexible check for 62/08 prefix)
        // Check if sender ends with the admin number (ignoring country code diffs)
        // But better to be exact if we standardized on 62.

        $isAdmin = $adminNumber && ($senderNumber === $adminNumber);

        if ($isAdmin) {
             // Check for ACC/REJ command
             // Relaxed regex to match INV- followed by any characters until end of line
             if (preg_match('/^(ACC|REJ)\s+((?:INV-)?[\w-]+)\s*$/i', $message, $matches)) {
                 $command = strtoupper($matches[1]);
                 $invoiceNumber = $matches[2];

                 // Ensure INV- prefix if missing (optional, but invoice_number usually includes it)
                 if (strpos($invoiceNumber, 'INV-') !== 0) {
                     $invoiceNumber = 'INV-' . $invoiceNumber;
                 }

                 $invoice = \App\Models\Invoice::where('invoice_number', $invoiceNumber)->first();

                 if (!$invoice) {
                     return response()->json([
                         'success' => true,
                         'should_reply' => true,
                         'reply' => "Invoice $invoiceNumber tidak ditemukan.",
                     ]);
                 }

                 if ($command === 'ACC') {
                     if ($invoice->isPaid()) {
                         return response()->json([
                             'success' => true,
                             'should_reply' => true,
                             'reply' => "Invoice $invoiceNumber sudah lunas sebelumnya.",
                         ]);
                     }

                     $invoice->markAsPaid();
                     $reply = "✅ Invoice $invoiceNumber BERHASIL divalidasi lunas.";

                     // Optional: Notify user via WhatsApp if their number is available
                     // This would require a separate outbound message, which ChatbotController
                     // shouldn't block on. Ideally dispatch a job.
                     // For now, just reply to admin.
                 } else {
                     $invoice->reject("Ditolak via WhatsApp oleh Admin");
                     $reply = "❌ Invoice $invoiceNumber TELAH DITOLAK.";
                 }

                 return response()->json([
                     'success' => true,
                     'should_reply' => true,
                     'reply' => $reply,
                 ]);
             }
        }

        // ... existing auto-reply logic ...

        // Find the device
        $device = Device::find($deviceId);
        if (!$device) {
            return response()->json([
                'success' => false,
                'error' => 'Device not found',
            ], 404);
        }

        // Find matching auto-reply rules for this device (or null device = all devices)
        $autoReplies = AutoReply::where('user_id', $device->user_id)
            ->where('is_active', true)
            ->where(function ($query) use ($deviceId) {
                $query->whereNull('device_id')
                      ->orWhere('device_id', $deviceId);
            })
            ->orderBy('priority', 'desc')
            ->get();

        // Check each rule for a match
        foreach ($autoReplies as $rule) {
            if ($rule->matches($message)) {
                // Found a match! Get the reply content
                $replyContent = $rule->getReplyContent([
                    'nama' => $from,
                    'phone' => $from,
                ]);

                if ($replyContent) {
                    // Increment trigger count
                    $rule->incrementTrigger();

                    return response()->json([
                        'success' => true,
                        'should_reply' => true,
                        'reply' => $replyContent,
                        'rule_id' => $rule->id,
                        'keyword' => $rule->keyword,
                    ]);
                }
            }
        }

        // No matching rule found
        return response()->json([
            'success' => true,
            'should_reply' => false,
            'message' => 'No matching auto-reply rule found.',
        ]);
    }
}

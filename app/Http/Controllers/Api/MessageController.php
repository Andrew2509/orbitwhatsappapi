<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Message;
use App\Models\Contact;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsAppService,
        protected \App\Services\AnalyticsService $analytics
    ) {}

    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'nullable|string',
            'file' => 'nullable|url',
            'template_id' => 'nullable|string',
            'variables' => 'nullable|array',
            'sandbox' => 'nullable|string',
            'device_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $application = $request->get('_application'); // Set by middleware if app_key used

        // Resolve phone number (to)
        $phone = preg_replace('/[^0-9]/', '', $request->to);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Get device
        $deviceQuery = Device::where('user_id', $user->id);

        // If using an application, only allow devices associated with that app
        if ($application) {
            $deviceQuery->whereHas('applications', function($q) use ($application) {
                $q->where('applications.id', $application->id);
            });
        }

        if ($request->device_id) {
            $device = $deviceQuery->find($request->device_id);
            if (!$device) {
                return response()->json([
                    'message_status' => 'Error',
                    'error' => 'Device not found or not associated with this application',
                ], 404);
            }
        } else {
            $device = $deviceQuery->where('status', 'connected')->first();
        }

        if (!$device) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'No connected device available for this application',
            ], 400);
        }

        // Find or create contact
        $contact = Contact::firstOrCreate(
            ['user_id' => $user->id, 'phone_number' => $phone],
            ['name' => null]
        );

        // Determine message type
        $type = 'text';
        if ($request->file) {
            $type = 'image'; // Default to image if file is provided, could be refined
        } elseif ($request->template_id) {
            $type = 'template';
        }

        // Create message record
        $message = Message::create([
            'device_id' => $device->id,
            'contact_id' => $contact->id,
            'direction' => 'outbound',
            'type' => $type,
            'content' => $request->message ?? '',
            'media_url' => $request->file,
            'status' => 'pending',
            'metadata' => [
                'template_id' => $request->template_id,
                'variables' => $request->variables,
                'sandbox' => $request->sandbox === 'true',
            ],
        ]);

        // Send via WhatsApp service
        try {
            $result = $this->whatsAppService->sendMessage(
                $device->id,
                $phone,
                $request->message ?? '',
                $type,
                $request->file
            );

            if (!isset($result['success']) || $result['success'] === false) {
                // If the service explicitly returns success=false or missing success key, mark message as failed
                $message->update([
                    'status' => 'failed',
                    'metadata' => array_merge($message->metadata ?? [], [
                        'error' => $result['error'] ?? 'Service returned failure',
                        'raw_response' => $result
                    ]),
                ]);

                return response()->json([
                    'message_status' => 'Error',
                    'error' => $result['error'] ?? 'Failed to dispatch message via WhatsApp service',
                ], 500);
            }

            // Successfully dispatched to the gateway
            $message->update([
                'status' => 'sent',
                'external_id' => 'MSG-' . $message->id . '-' . now()->format('Ymd'),
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            $message->update([
                'status' => 'failed',
                'metadata' => array_merge($message->metadata ?? [], ['error' => $e->getMessage()]),
            ]);

            return response()->json([
                'message_status' => 'Error',
                'error' => 'Exception during message dispatch: ' . $e->getMessage(),
            ], 500);
        }

        $device->increment('messages_sent');

        // Track event in GA4
        $this->analytics->trackMessageSent($type, 'sent', (string) $user->id);

        return response()->json([
            'message_status' => 'Success',
            'data' => [
                'from' => $device->phone_number,
                'to' => $phone,
                'message_id' => $message->external_id,
                'status' => $message->status,
                'timestamp' => $message->sent_at->toIso8601String(),
            ]
        ]);
    }

    public function status($messageId)
    {
        $message = Message::where('external_id', $messageId)
            ->whereHas('device', fn($q) => $q->where('user_id', request()->user()->id))
            ->firstOrFail();

        return response()->json([
            'message_id' => $message->external_id,
            'status' => $message->status,
            'sent_at' => $message->sent_at?->toIso8601String(),
            'delivered_at' => $message->delivered_at?->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
        ]);
    }

    public function index(Request $request)
    {
        $messages = Message::whereHas('device', fn($q) => $q->where('user_id', $request->user()->id))
            ->with(['contact:id,phone_number,name'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($messages);
    }
}

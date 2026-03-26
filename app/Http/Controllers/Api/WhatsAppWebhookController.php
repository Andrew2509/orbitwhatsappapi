<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Message;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected \App\Services\AnalyticsService $analytics
    ) {}
    /**
     * Handle incoming webhooks from WhatsApp service
     */
    public function handle(Request $request)
    {
        // Verify secret
        $secret = $request->header('X-WhatsApp-Secret');
        if ($secret !== config('services.whatsapp.secret', 'secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        Log::info('WhatsApp webhook received', ['event' => $event, 'data' => $data]);

        return match ($event) {
            'device.connected' => $this->handleDeviceConnected($data),
            'device.disconnected' => $this->handleDeviceDisconnected($data),
            'devices.sync' => $this->handleDevicesSync($data),
            'message.received' => $this->handleMessageReceived($data),
            'message.status' => $this->handleMessageStatus($data),
            default => response()->json(['status' => 'ignored']),
        };
    }

    /**
     * Sync device statuses - mark devices not in activeIds as disconnected
     */
    protected function handleDevicesSync(array $data)
    {
        $activeDeviceIds = $data['activeDeviceIds'] ?? [];

        Log::info('Syncing device statuses', ['active_ids' => $activeDeviceIds]);

        // Mark devices NOT in activeDeviceIds as disconnected
        Device::whereNotIn('id', $activeDeviceIds)
            ->where('status', 'connected')
            ->update(['status' => 'disconnected']);

        return response()->json(['status' => 'ok', 'synced' => true]);
    }

    protected function handleDeviceConnected(array $data)
    {
        $device = Device::find($data['deviceId']);
        if ($device) {
            $device->update([
                'status' => 'connected',
                'phone_number' => $data['phone'] ?? null,
                'last_connected_at' => now(),
                'qr_code' => null,
            ]);

            // Dispatch webhook
            $this->dispatchUserWebhooks($device->user_id, 'device.connected', [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'phone_number' => $data['phone'] ?? null,
            ]);

            // Track GA4 event
            $this->analytics->trackDeviceConnected((string) $device->id, (string) $device->user_id);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleDeviceDisconnected(array $data)
    {
        $device = Device::find($data['deviceId']);
        if ($device) {
            $device->update([
                'status' => 'disconnected',
                'session_data' => null,
            ]);

            // Dispatch webhook
            $this->dispatchUserWebhooks($device->user_id, 'device.disconnected', [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'reason' => $data['reason'] ?? 'Connection lost',
                'last_seen' => now()->toIso8601String(),
            ]);

            // Track GA4 event
            $this->analytics->trackDeviceDisconnected((string) $device->id, $data['reason'] ?? 'unknown', (string) $device->user_id);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleMessageReceived(array $data)
    {
        $device = Device::find($data['deviceId']);
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Find or create contact
        $contact = Contact::firstOrCreate(
            ['user_id' => $device->user_id, 'phone_number' => $data['sender']],
            ['name' => null]
        );

        // Create message record
        Message::create([
            'device_id' => $device->id,
            'contact_id' => $contact->id,
            'direction' => 'inbound',
            'type' => 'text',
            'content' => $data['content'],
            'external_id' => $data['messageId'],
            'status' => 'delivered',
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);

        // Increment device counter
        $device->increment('messages_received');

        // Update contact last message
        $contact->update(['last_message_at' => now()]);

        // Check auto-replies
        $this->checkAutoReplies($device, $contact, $data['content']);

        // Dispatch webhooks
        $this->dispatchUserWebhooks($device->user_id, 'message.received', [
            'phone' => $data['sender'],
            'content' => $data['content'],
            'device_id' => $device->id,
        ]);

        return response()->json(['status' => 'ok']);
    }

    protected function handleMessageStatus(array $data)
    {
        $message = Message::where('external_id', $data['messageId'])->first();
        if ($message) {
            $updates = ['status' => $data['status']];

            if ($data['status'] === 'delivered') {
                $updates['delivered_at'] = now();
            } elseif ($data['status'] === 'read') {
                $updates['read_at'] = now();
            }

            $message->update($updates);

            // Dispatch webhook for status update
            $device = $message->device;
            if ($device) {
                $eventName = 'message.' . $data['status'];
                $this->dispatchUserWebhooks($device->user_id, $eventName, [
                    'device_id' => $device->id,
                    'message_id' => $data['messageId'],
                    'to' => $message->contact?->phone_number,
                    'status' => $data['status'],
                    'timestamp' => now()->timestamp,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    protected function checkAutoReplies(Device $device, Contact $contact, string $content)
    {
        // Skip if contact is blocked
        if ($contact->is_blocked) return;

        $autoReplies = $device->autoReplies()
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($autoReplies as $autoReply) {
            if ($autoReply->matches($content)) {
                // Send auto-reply
                app(\App\Services\WhatsAppService::class)->sendMessage(
                    $device->id,
                    $contact->phone_number,
                    $autoReply->reply_message
                );

                $autoReply->incrementTrigger();
                break; // Only trigger first matching rule
            }
        }
    }

    protected function dispatchUserWebhooks(int $userId, string $event, array $data)
    {
        \App\Services\WebhookDispatcher::dispatch($userId, $event, $data);
    }
}

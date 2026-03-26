<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\Contact;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SingleSendController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        $devices = Device::where('user_id', Auth::id())
            ->where('status', 'connected')
            ->get();

        return view('single-send.index', compact('devices'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'phone' => 'required|string|min:10|max:20',
            'message' => 'required|string|max:4096',
            'type' => 'required|in:text,image,document',
            'media_file' => 'nullable|file|max:16384', // Max 16MB
            'media_url' => 'nullable|url',
        ]);

        // Check device ownership and status
        $device = Device::where('id', $request->device_id)
            ->where('user_id', Auth::id())
            ->where('status', 'connected')
            ->first();

        if (!$device) {
            return back()->with('error', 'Device not found or not connected.');
        }

        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Handle file upload
        $mediaUrl = $request->media_url;
        $uploadedFilePath = null;

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            // Sanitize filename - remove special characters and limit length
            $extension = $file->getClientOriginalExtension();
            $baseName = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = time() . '_' . substr($baseName, 0, 50) . '.' . $extension;
            
            $path = $file->storeAs('public/uploads/messages', $filename);
            $uploadedFilePath = $path;
            
            // Use Storage::path() for correct absolute path
            $mediaUrl = Storage::path($path);
        }

        try {
            // Send via WhatsApp service
            $result = $this->whatsAppService->sendMessage(
                $device->id,
                $phone,
                $request->message,
                $request->type,
                $mediaUrl
            );

            if (!isset($result['success']) || $result['success'] === false) {
                // Clean up uploaded file if failed
                if ($uploadedFilePath) {
                    Storage::delete($uploadedFilePath);
                }
                return back()->with('error', $result['error'] ?? 'Failed to send message.');
            }

            // Find or create contact
            $contact = Contact::firstOrCreate(
                ['user_id' => Auth::id(), 'phone_number' => $phone],
                ['name' => null]
            );

            // Save message to database
            Message::create([
                'device_id' => $device->id,
                'contact_id' => $contact->id,
                'direction' => 'outbound',
                'type' => $request->type,
                'content' => $request->message,
                'status' => 'sent',
                'external_id' => 'MSG-' . time() . '-' . Auth::id(), // Temporary ID for dashboard sends
                'sent_at' => now(),
                'metadata' => [
                    'source' => 'dashboard',
                    'media_url' => $mediaUrl,
                ],
            ]);

            // Increment device message counter
            $device->increment('messages_sent');

            return back()->with('success', 'Message sent successfully to ' . $phone);

        } catch (\Exception $e) {
            // Clean up uploaded file if failed
            if ($uploadedFilePath) {
                Storage::delete($uploadedFilePath);
            }
            return back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }
}

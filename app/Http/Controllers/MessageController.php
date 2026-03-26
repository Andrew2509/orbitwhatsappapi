<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // Only fetch outbound messages (sent messages)
        $query = Message::whereHas('device', fn($q) => $q->where('user_id', Auth::id()))
            ->where('direction', 'outbound')
            ->with(['device', 'contact']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('content', 'like', "%{$request->search}%")
                  ->orWhereHas('contact', fn($cq) => $cq->where('phone_number', 'like', "%{$request->search}%"));
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $messages = $query->latest()->paginate(20);
        
        // Get user's devices for filter dropdown
        $devices = Device::where('user_id', Auth::id())->get();
        
        // Get stats
        $stats = [
            'sent' => Message::whereHas('device', fn($q) => $q->where('user_id', Auth::id()))->where('status', 'sent')->count(),
            'pending' => Message::whereHas('device', fn($q) => $q->where('user_id', Auth::id()))->where('status', 'pending')->count(),
            'failed' => Message::whereHas('device', fn($q) => $q->where('user_id', Auth::id()))->where('status', 'failed')->count(),
        ];

        return view('messages.index', compact('messages', 'devices', 'stats'));
    }

    public function show(Message $message)
    {
        // Check ownership via device
        if ($message->device->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json($message->load(['device', 'contact']));
    }
}

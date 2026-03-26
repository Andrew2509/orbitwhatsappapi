<?php

namespace App\Http\Controllers;

use App\Models\AutoReply;
use App\Models\Device;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoReplyController extends Controller
{
    public function index()
    {
        $autoReplies = AutoReply::where('user_id', Auth::id())
            ->with(['device', 'template'])
            ->orderBy('priority', 'desc')
            ->get();

        $devices = Device::where('user_id', Auth::id())->where('status', 'connected')->get();
        
        // Get templates for dropdown
        $templates = Template::where(function ($q) {
            $q->where('user_id', Auth::id())
              ->orWhere('is_system', true);
        })->where('is_active', true)->get();

        return view('auto-reply.index', compact('autoReplies', 'devices', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'match_type' => 'required|in:exact,contains,starts_with,regex',
            'reply_type' => 'required|in:text,template',
            'reply_value' => 'required_if:reply_type,text|nullable|string',
            'template_id' => 'required_if:reply_type,template|nullable|exists:templates,id',
            'device_id' => 'nullable|exists:devices,id',
            'priority' => 'nullable|integer|min:0',
        ]);

        AutoReply::create([
            'user_id' => Auth::id(),
            'device_id' => $request->device_id,
            'keyword' => $request->keyword,
            'match_type' => $request->match_type,
            'reply_type' => $request->reply_type,
            'template_id' => $request->reply_type === 'template' ? $request->template_id : null,
            'reply_value' => $request->reply_type === 'text' ? $request->reply_value : null,
            'priority' => $request->priority ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('auto-reply.index')
            ->with('success', 'Auto reply rule created.');
    }

    public function update(Request $request, AutoReply $autoReply)
    {
        if ($autoReply->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'keyword' => 'required|string|max:255',
            'match_type' => 'required|in:exact,contains,starts_with,regex',
            'reply_type' => 'required|in:text,template',
            'reply_value' => 'required_if:reply_type,text|nullable|string',
            'template_id' => 'required_if:reply_type,template|nullable|exists:templates,id',
            'device_id' => 'nullable|exists:devices,id',
            'priority' => 'nullable|integer|min:0',
        ]);

        $autoReply->update([
            'device_id' => $request->device_id,
            'keyword' => $request->keyword,
            'match_type' => $request->match_type,
            'reply_type' => $request->reply_type,
            'template_id' => $request->reply_type === 'template' ? $request->template_id : null,
            'reply_value' => $request->reply_type === 'text' ? $request->reply_value : null,
            'priority' => $request->priority ?? 0,
        ]);

        return redirect()->route('auto-reply.index')
            ->with('success', 'Auto reply rule updated.');
    }

    public function destroy(AutoReply $autoReply)
    {
        if ($autoReply->user_id !== Auth::id()) {
            abort(403);
        }
        
        $autoReply->delete();

        return redirect()->route('auto-reply.index')
            ->with('success', 'Auto reply rule deleted.');
    }

    public function toggle(AutoReply $autoReply)
    {
        if ($autoReply->user_id !== Auth::id()) {
            abort(403);
        }
        
        $autoReply->update(['is_active' => !$autoReply->is_active]);

        return redirect()->route('auto-reply.index')
            ->with('success', $autoReply->is_active ? 'Rule activated.' : 'Rule deactivated.');
    }
}

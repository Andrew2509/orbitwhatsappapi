<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\WebhookLog;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function index()
    {
        $webhooks = Webhook::where('user_id', Auth::id())
            ->with(['logs' => fn($q) => $q->latest()->limit(5)])
            ->latest()
            ->get();

        // Get recent logs for all webhooks
        $recentLogs = WebhookLog::whereHas('webhook', fn($q) => $q->where('user_id', Auth::id()))
            ->with('webhook')
            ->latest()
            ->limit(20)
            ->get();

        // Available events
        $availableEvents = [
            'message.received' => 'Mengirim data saat ada chat masuk ke WhatsApp',
            'message.sent' => 'Notifikasi saat pesan berhasil dikirim',
            'message.delivered' => 'Notifikasi saat pesan terkirim ke HP penerima',
            'message.read' => 'Notifikasi saat pesan dibaca penerima',
            'message.failed' => 'Notifikasi saat pengiriman pesan gagal',
            'device.connected' => 'Notifikasi saat device terkoneksi',
            'device.disconnected' => 'Notifikasi saat device terputus',
            'device.battery' => 'Notifikasi level baterai HP (opsional)',
        ];

        return view('webhooks.index', compact('webhooks', 'recentLogs', 'availableEvents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'max_retries' => 'integer|min:0|max:10',
        ], [
            'url.required' => 'URL webhook harus diisi.',
            'url.url' => 'Format URL tidak valid.',
            'events.required' => 'Pilih minimal 1 event.',
        ]);

        Webhook::create([
            'user_id' => Auth::id(),
            'url' => $request->url,
            'events' => $request->events,
            'secret' => Str::random(32),
            'max_retries' => $request->max_retries ?? 3,
            'is_active' => true,
        ]);

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook berhasil dibuat! Secret key sudah di-generate otomatis.');
    }

    public function update(Request $request, Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'max_retries' => 'integer|min:0|max:10',
        ]);

        $webhook->update([
            'url' => $request->url,
            'events' => $request->events,
            'max_retries' => $request->max_retries ?? 3,
        ]);

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook berhasil diperbarui!');
    }

    public function destroy(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }
        
        $webhook->delete();

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook berhasil dihapus!');
    }

    public function toggle(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }
        
        $webhook->update(['is_active' => !$webhook->is_active]);

        return redirect()->route('webhooks.index')
            ->with('success', $webhook->is_active ? 'Webhook diaktifkan!' : 'Webhook dinonaktifkan!');
    }

    public function regenerateSecret(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }

        $webhook->update(['secret' => Str::random(32)]);

        return redirect()->route('webhooks.index')
            ->with('success', 'Secret key berhasil di-regenerate!');
    }

    public function test(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }

        $log = WebhookDispatcher::sendTest($webhook);

        if ($log->isSuccess()) {
            return back()->with('success', 'Test webhook berhasil dikirim! (Status: ' . $log->response_code . ')');
        }

        return back()->with('error', 'Test webhook gagal: ' . ($log->error_message ?? 'Unknown error'));
    }

    public function logs(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }

        $logs = $webhook->logs()->paginate(20);

        return view('webhooks.logs', compact('webhook', 'logs'));
    }

    public function showSecret(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'secret' => $webhook->secret,
        ]);
    }
}

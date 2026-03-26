<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    public function queue()
    {
        // Get active campaigns (broadcast queue)
        $campaigns = Campaign::with('user:id,name')
            ->whereIn('status', ['pending', 'running', 'paused'])
            ->withCount('campaignRecipients')
            ->latest()
            ->paginate(20);

        // Queue statistics
        $stats = [
            'pending' => Campaign::where('status', 'pending')->count(),
            'running' => Campaign::where('status', 'running')->count(),
            'completed_today' => Campaign::where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        return view('admin.system.queue', compact('campaigns', 'stats'));
    }

    public function webhooks(Request $request)
    {
        $query = WebhookLog::with(['webhook.user:id,name']);

        // Filter by status
        if ($request->status === 'failed') {
            $query->where('response_code', '>=', 400)
                ->orWhereNull('response_code');
        } elseif ($request->status === 'success') {
            $query->whereBetween('response_code', [200, 299]);
        }

        $logs = $query->latest()->paginate(50);

        // Statistics
        $stats = [
            'total' => WebhookLog::count(),
            'success' => WebhookLog::whereBetween('response_code', [200, 299])->count(),
            'failed' => WebhookLog::where('response_code', '>=', 400)
                ->orWhereNull('response_code')
                ->count(),
        ];

        return view('admin.system.webhooks', compact('logs', 'stats'));
    }

    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            // Get last 100 lines
            $lines = explode("\n", $content);
            $logs = array_slice($lines, -100);
            $logs = array_reverse($logs);
        }

        return view('admin.system.logs', compact('logs'));
    }
}

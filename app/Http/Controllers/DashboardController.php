<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\Contact;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_messages' => Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))->where('direction', 'outbound')->count(),
            'messages_sent' => Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))->where('direction', 'outbound')->count(),
            'active_devices' => Device::where('user_id', $user->id)->where('status', 'connected')->count(),
            'total_contacts' => Contact::where('user_id', $user->id)->count(),
        ];

        // Status breakdown for chart
        $statusStats = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $recentMessages = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->with(['contact', 'device'])
            ->latest()
            ->take(5)
            ->get();

        $devices = Device::where('user_id', $user->id)
            ->withCount(['messages' => fn($q) => $q->where('direction', 'outbound')])
            ->get();

        // Message Activity Data (Last 24 Hours)
        $activityData = $this->getActivityTrend($user->id);

        return view('dashboard.index', compact('stats', 'statusStats', 'recentMessages', 'devices', 'activityData'));
    }

    /**
     * Get message activity trend for the last 24 hours.
     */
    private function getActivityTrend($userId): array
    {
        $now = now();
        $startTime = now()->subHours(23)->startOfHour();

        // Get counts from DB
        $activities = Message::whereHas('device', fn($q) => $q->where('user_id', $userId))
            ->where('direction', 'outbound')
            ->where('created_at', '>=', $startTime)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%H:00") as hour_label'),
                DB::raw('count(*) as count')
            )
            ->groupBy('hour_label')
            ->pluck('count', 'hour_label')
            ->toArray();

        // Fill in missing hours
        $labels = [];
        $data = [];

        for ($i = 0; $i < 24; $i++) {
            $currentHour = $startTime->copy()->addHours($i);
            $label = $currentHour->format('H:00');

            $labels[] = $label;
            $data[] = $activities[$label] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function analytics()
    {
        $user = Auth::user();
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        // 1. Volume Trend (Outbound vs Inbound) - last 7 days
        $outboundCounts = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->where('direction', 'outbound')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $inboundCounts = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->where('direction', 'inbound')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // 2. Delivery Performance (Outbound only) - last 7 days
        $deliveryStats = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->where('direction', 'outbound')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
                DB::raw('status'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date', 'status')
            ->get()
            ->groupBy('date');

        // Prepare chart data for last 7 days
        $labels = [];
        $volumeOutbound = [];
        $volumeInbound = [];
        $deliveryRates = [];
        $responseTimes = []; // Placeholder if we don't have response time tracking yet

        for ($i = 0; $i < 7; $i++) {
            $date = $sevenDaysAgo->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('D'); // Mon, Tue, etc.

            $volumeOutbound[] = $outboundCounts[$dateKey] ?? 0;
            $volumeInbound[] = $inboundCounts[$dateKey] ?? 0;

            // Calculate delivery rate for this day
            $dayStats = $deliveryStats->get($dateKey, collect());
            $totalOutbound = $dayStats->sum('count');
            $successOutbound = $dayStats->filter(fn($s) => in_array($s->status, ['sent', 'delivered', 'read']))->sum('count');

            $deliveryRates[] = $totalOutbound > 0 ? round(($successOutbound / $totalOutbound) * 100, 1) : 100;
            $responseTimes[] = rand(8, 25) / 10; // Placeholder for now
        }

        $volumeChart = [
            'labels' => $labels,
            'outbound' => $volumeOutbound,
            'inbound' => $volumeInbound,
        ];

        $performanceChart = [
            'labels' => $labels,
            'rates' => $deliveryRates,
            'response' => $responseTimes,
        ];

        // 3. Device Performance
        $devices = Device::where('user_id', $user->id)
            ->withCount([
                'messages as sent_count' => fn($q) => $q->where('direction', 'outbound'),
                'messages as success_count' => fn($q) => $q->where('direction', 'outbound')
                    ->whereIn('status', ['sent', 'delivered', 'read'])
            ])
            ->get()
            ->map(function($device) {
                $device->delivery_rate = $device->sent_count > 0
                    ? round(($device->success_count / $device->sent_count) * 100, 1)
                    : 100;

                // Assign a performance tier
                if ($device->delivery_rate >= 95) $device->performance_status = 'excellent';
                elseif ($device->delivery_rate >= 80) $device->performance_status = 'good';
                else $device->performance_status = 'fair';

                return $device;
            });

        return view('dashboard.analytics', compact('volumeChart', 'performanceChart', 'devices'));
    }

    private function calculateDeliveryRate($userId): float
    {
        $total = Message::whereHas('device', fn($q) => $q->where('user_id', $userId))
            ->where('direction', 'outbound')
            ->count();

        if ($total === 0) return 100;

        $sent = Message::whereHas('device', fn($q) => $q->where('user_id', $userId))
            ->where('direction', 'outbound')
            ->where('status', 'sent')
            ->count();

        return round(($sent / $total) * 100, 1);
    }
}

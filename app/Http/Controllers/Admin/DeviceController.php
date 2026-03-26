<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceUsageLimit;
use App\Models\DeviceWarmup;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('user:id,name,email');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone_number', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($u) use ($request) {
                        $u->where('name', 'like', "%{$request->search}%")
                            ->orWhere('email', 'like', "%{$request->search}%");
                    });
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $devices = $query->latest()->paginate(20);

        // Get statistics
        $stats = [
            'total' => Device::count(),
            'connected' => Device::where('status', 'connected')->count(),
            'disconnected' => Device::where('status', 'disconnected')->count(),
            'pending' => Device::where('status', 'pending')->count(),
        ];

        return view('admin.devices.index', compact('devices', 'stats'));
    }

    public function forceLogout(Device $device)
    {
        // Update device status
        $device->update([
            'status' => 'disconnected',
            'session_data' => null,
        ]);

        // TODO: Call Node.js API to force logout the session

        return back()->with('success', "Device {$device->name} berhasil di-logout.");
    }

    public function logs(Request $request)
    {
        // TODO: Implement connection logs from Node.js
        $logs = collect(); // Placeholder

        return view('admin.devices.logs', compact('logs'));
    }

    /**
     * Device Usage Dashboard - Shows daily limits and warmup status
     */
    public function usageDashboard(Request $request)
    {
        $today = now()->toDateString();

        // Get devices with their usage and warmup status
        $query = Device::with(['user:id,name,email'])
            ->withCount('messages');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone_number', 'like', "%{$request->search}%");
            });
        }

        $devices = $query->where('status', 'connected')->get();

        // Enrich with usage data
        $devicesWithUsage = $devices->map(function ($device) use ($today) {
            $usageLimit = DeviceUsageLimit::where('device_id', $device->id)
                ->where('date', $today)
                ->first();

            $warmup = DeviceWarmup::where('device_id', $device->id)->first();

            return [
                'device' => $device,
                'usage' => $usageLimit ? [
                    'sent' => $usageLimit->messages_sent,
                    'limit' => $usageLimit->daily_limit,
                    'remaining' => $usageLimit->getRemainingMessages(),
                    'percentage' => $usageLimit->getUsagePercentage(),
                    'is_blocked' => $usageLimit->is_blocked,
                    'cooldown_until' => $usageLimit->cooldown_until,
                ] : [
                    'sent' => 0,
                    'limit' => config('whatsapp.daily_message_limit', 200),
                    'remaining' => config('whatsapp.daily_message_limit', 200),
                    'percentage' => 0,
                    'is_blocked' => false,
                    'cooldown_until' => null,
                ],
                'warmup' => $warmup ? [
                    'day' => $warmup->warmup_day,
                    'target' => $warmup->daily_target,
                    'progress' => $warmup->current_progress,
                    'is_complete' => $warmup->is_warmup_complete,
                    'description' => $warmup->getCurrentDayDescription(),
                ] : null,
            ];
        });

        // Statistics
        $stats = [
            'total_connected' => $devices->count(),
            'at_limit' => $devicesWithUsage->filter(fn($d) => $d['usage']['percentage'] >= 100)->count(),
            'warning_level' => $devicesWithUsage->filter(fn($d) => $d['usage']['percentage'] >= 80 && $d['usage']['percentage'] < 100)->count(),
            'in_warmup' => $devicesWithUsage->filter(fn($d) => $d['warmup'] && !$d['warmup']['is_complete'])->count(),
            'total_sent_today' => DeviceUsageLimit::where('date', $today)->sum('messages_sent'),
        ];

        return view('admin.devices.usage-dashboard', compact('devicesWithUsage', 'stats'));
    }

    /**
     * Reset daily limit for a specific device
     */
    public function resetLimit(Device $device)
    {
        $today = now()->toDateString();
        
        DeviceUsageLimit::where('device_id', $device->id)
            ->where('date', $today)
            ->update([
                'messages_sent' => 0,
                'is_blocked' => false,
                'cooldown_until' => null,
            ]);

        return back()->with('success', "Limit harian untuk {$device->name} berhasil direset.");
    }

    /**
     * Start warmup for a device
     */
    public function startWarmup(Device $device)
    {
        DeviceWarmup::startForDevice($device->id);

        return back()->with('success', "Warmup dimulai untuk {$device->name}.");
    }

    /**
     * Skip warmup for a device
     */
    public function skipWarmup(Device $device)
    {
        DeviceWarmup::where('device_id', $device->id)->update([
            'is_warmup_complete' => true,
            'completed_at' => now(),
        ]);

        return back()->with('success', "Warmup untuk {$device->name} dilewati.");
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // User Statistics
        $totalUsers = User::count();
        $activeUsers = User::whereHas('activeSubscription')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersWeek = User::whereDate('created_at', '>=', now()->subWeek())->count();

        // Message Statistics
        $totalMessagesToday = UsageLog::whereDate('date', today())->sum('messages_sent');
        $totalMessagesWeek = UsageLog::whereDate('date', '>=', now()->subWeek())->sum('messages_sent');
        $totalMessagesMonth = UsageLog::whereDate('date', '>=', now()->subMonth())->sum('messages_sent');

        // Revenue Statistics
        $revenueThisMonth = Invoice::paid()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');
        $revenueLastMonth = Invoice::paid()
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('total');
        $pendingPayments = Invoice::pending()->sum('total');
        $pendingCount = Invoice::pending()->count();

        // Device Statistics
        $totalDevices = Device::count();
        $connectedDevices = Device::where('status', 'connected')->count();

        // Subscription Statistics
        $subscriptionsByPlan = Subscription::active()
            ->select('plan_id', DB::raw('count(*) as total'))
            ->groupBy('plan_id')
            ->with('plan:id,name')
            ->get();

        // Traffic Data (last 7 days)
        $trafficData = UsageLog::select(
                DB::raw('DATE(date) as date'),
                DB::raw('SUM(messages_sent) as messages'),
                DB::raw('SUM(api_calls) as api_calls')
            )
            ->whereDate('date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent Activities
        $recentInvoices = Invoice::with('user:id,name,email', 'subscription.plan:id,name')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'newUsersToday',
            'newUsersWeek',
            'totalMessagesToday',
            'totalMessagesWeek',
            'totalMessagesMonth',
            'revenueThisMonth',
            'revenueLastMonth',
            'pendingPayments',
            'pendingCount',
            'totalDevices',
            'connectedDevices',
            'subscriptionsByPlan',
            'trafficData',
            'recentInvoices',
            'recentUsers'
        ));
    }
}

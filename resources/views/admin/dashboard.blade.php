@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Monitor kesehatan sistem dan aktivitas bisnis')

@section('content')
@php $nonce = config('app.csp_nonce'); @endphp
<style nonce="{{ $nonce }}">
    .chart-container-200 { height: 200px; position: relative; }
</style>
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalUsers) }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium">+{{ $newUsersToday }} hari ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Subscriptions</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($activeUsers) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $totalUsers > 0 ? round(($activeUsers/$totalUsers)*100) : 0 }}% dari total</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Messages Today -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Messages Today</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalMessagesToday) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($totalMessagesWeek) }} minggu ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue This Month -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue (Bulan Ini)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
                    @if($revenueLastMonth > 0)
                    <p class="text-xs {{ $revenueThisMonth >= $revenueLastMonth ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} mt-1 font-medium">
                        {{ $revenueThisMonth >= $revenueLastMonth ? '+' : '' }}{{ round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100) }}% vs bulan lalu
                    </p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Connected Devices -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Device Status</h3>
                <a href="{{ route('admin.devices.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        <span class="text-sm">Connected</span>
                        <span class="ml-auto font-semibold">{{ $connectedDevices }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-slate-300 rounded-full"></span>
                        <span class="text-sm">Total</span>
                        <span class="ml-auto font-semibold">{{ $totalDevices }}</span>
                    </div>
                </div>
                <div class="w-16 h-16">
                    <div class="relative w-full h-full">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="8" fill="none" class="text-slate-200 dark:text-slate-700"/>
                            <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="8" fill="none" class="text-emerald-500"
                                stroke-dasharray="{{ $totalDevices > 0 ? ($connectedDevices/$totalDevices)*176 : 0 }} 176"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold">
                            {{ $totalDevices > 0 ? round(($connectedDevices/$totalDevices)*100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Pending Payments</h3>
                <a href="{{ route('admin.transactions.pending') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Review</a>
            </div>
            <div class="text-center">
                <p class="text-4xl font-bold text-amber-500">{{ $pendingCount }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">menunggu approval</p>
                <p class="text-lg font-semibold mt-2">Rp {{ number_format($pendingPayments, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 hover:shadow-md transition-shadow">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-sm">Kelola Users</span>
                </a>
                <a href="{{ route('admin.plans.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="text-sm">Kelola Plans</span>
                </a>
                <a href="{{ route('admin.system.logs') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-sm">Error Logs</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Traffic Chart & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Traffic Chart -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Message Traffic (7 Hari Terakhir)</h3>
            <div class="chart-container-200">
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Recent Invoices</h3>
                <a href="{{ route('admin.transactions.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
            </div>
            <div class="space-y-3 max-h-[250px] overflow-y-auto">
                @forelse($recentInvoices as $invoice)
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50">
                    <div>
                        <p class="font-medium text-sm">{{ $invoice->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-500">{{ $invoice->invoice_number }} • {{ $invoice->subscription?->plan?->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-sm">{{ $invoice->formatted_total }}</p>
                        <span class="admin-badge {{ $invoice->status === 'paid' ? 'admin-badge-success' : ($invoice->status === 'pending' ? 'admin-badge-warning' : 'admin-badge-danger') }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-4">Belum ada invoice</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-slate-700">
                        <th class="pb-3 font-medium">User</th>
                        <th class="pb-3 font-medium">Email</th>
                        <th class="pb-3 font-medium">Role</th>
                        <th class="pb-3 font-medium">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($recentUsers as $user)
                    <tr>
                        <td class="py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-emerald-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-sm text-slate-500">{{ $user->email }}</td>
                        <td class="py-3">
                            <span class="admin-badge {{ $user->role === 'admin' ? 'admin-badge-info' : ($user->role === 'super_admin' ? 'admin-badge-danger' : 'admin-badge-success') }}">
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td class="py-3 text-sm text-slate-500">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ $csp_nonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trafficChart').getContext('2d');
    const trafficData = @json($trafficData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trafficData.map(d => d.date),
            datasets: [{
                label: 'Messages',
                data: trafficData.map(d => d.messages),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
            }, {
                label: 'API Calls',
                data: trafficData.map(d => d.api_calls),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                }
            }
        }
    });
});
</script>
@endpush
@endsection


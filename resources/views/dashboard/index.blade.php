{{-- [ignoring loop detection] --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
@php $nonce = config('app.csp_nonce'); @endphp
<style nonce="{{ $nonce }}">
    .chart-container-300 { height: 300px; position: relative; }
    .chart-container-200 { height: 200px; position: relative; }
</style>
<div class="space-y-6 animate-fade-in">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Messages -->
        <div class="card card-stat group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">Total Messages</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['total_messages']) }}</h3>
                    <p class="text-sm text-emerald-500 mt-2 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full"></span>
                        Lifetime activity
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sent Messages -->
        <div class="card card-stat group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">Messages Sent</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['messages_sent']) }}</h3>
                    <p class="text-sm text-blue-500 mt-2 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        Outbound traffic
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-blue-500/10 text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Devices -->
        <div class="card card-stat group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">Active Devices</p>
                    <h3 class="text-3xl font-bold">{{ $stats['active_devices'] }}</h3>
                    <p class="text-sm text-[var(--text-muted)] mt-2 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 {{ $stats['active_devices'] > 0 ? 'bg-emerald-500' : 'bg-red-500' }} rounded-full"></span>
                        {{ $stats['active_devices'] > 0 ? 'Instances online' : 'No devices connected' }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-purple-500/10 text-purple-500 group-hover:bg-purple-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Contacts -->
        <div class="card card-stat group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">Total Contacts</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['total_contacts']) }}</h3>
                    <p class="text-sm text-[var(--text-muted)] mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Saved contacts
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-amber-500/10 text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m12 5.197v-1a6 6 0 00-9-5.197"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Message Activity (Trend) -->
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-semibold text-lg">Message Activity</h3>
                <div class="text-xs text-[var(--text-muted)]">Last 24 hours</div>
            </div>
            <div class="chart-container-300">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="card">
            <h3 class="font-semibold text-lg mb-6">Message Status</h3>
            <div class="chart-container-200">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                @foreach(['sent' => 'blue', 'pending' => 'amber', 'failed' => 'red'] as $status => $color)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-{{ $color }}-500"></span>
                        <span class="text-sm capitalize">{{ $status }}</span>
                    </div>
                    <span class="text-sm font-medium">{{ number_format($statusStats[$status] ?? 0) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Data Table -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Messages -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-semibold text-lg">Recent Messages</h3>
                <a href="{{ route('messages.index') }}" class="text-sm text-emerald-500 hover:text-emerald-400">View all</a>
            </div>
            <div class="space-y-4">
                @forelse($recentMessages as $msg)
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-[var(--bg-secondary)] transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                        {{ strtoupper(substr($msg->contact->display_name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-medium text-sm truncate">{{ $msg->contact->display_name ?? $msg->contact->phone_number ?? 'Unknown' }}</p>
                            @php
                                $displayStatus = $msg->status === 'delivered' ? 'sent' : $msg->status;
                                $badgeClass = in_array($msg->status, ['delivered', 'sent']) ? 'info' : ($msg->status === 'pending' ? 'warning' : 'danger');
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ ucfirst($displayStatus) }}
                            </span>
                        </div>
                        <p class="text-sm text-[var(--text-secondary)] truncate mt-0.5">{{ $msg->content ?? '[Media message]' }}</p>
                        <p class="text-xs text-[var(--text-muted)] mt-1 flex items-center gap-2">
                            <span>{{ $msg->created_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span>{{ $msg->device->name ?? 'System' }}</span>
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-[var(--text-muted)]">No recent messages</div>
                @endforelse
            </div>
        </div>

        <!-- Device Overview -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-semibold text-lg">Device Overview</h3>
                <a href="{{ route('devices.index') }}" class="text-sm text-emerald-500 hover:text-emerald-400">Manage</a>
            </div>
            <div class="space-y-4">
                @forelse($devices as $device)
                <div class="flex items-center gap-4 p-3 rounded-lg border border-[var(--border-color)]">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $device->status === 'connected' ? 'from-emerald-400 to-emerald-600' : 'from-gray-400 to-gray-600' }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-medium">{{ $device->name }}</p>
                            <span class="w-2 h-2 rounded-full {{ $device->status === 'connected' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        </div>
                        <p class="text-sm text-[var(--text-secondary)]">{{ $device->phone_number ?? 'Not paired' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">{{ number_format($device->messages_count) }}</p>
                        <p class="text-xs text-[var(--text-muted)]">messages</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-[var(--text-muted)]">No devices found</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $csp_nonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#cbd5e1' : '#475569';
    const gridColor = isDark ? '#334155' : '#e2e8f0';

    // Activity Chart (Dynamic activity trend)
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($activityData['labels']) !!},
            datasets: [{
                label: 'Messages Sent',
                data: {!! json_encode($activityData['data']) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: gridColor }, ticks: { color: textColor } },
                x: { grid: { display: false }, ticks: { color: textColor } }
            }
        }
    });

    // Status Breakdown Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sent', 'Pending', 'Failed'],
            datasets: [{
                data: [
                    {{ $statusStats['sent'] ?? 0 }},
                    {{ $statusStats['pending'] ?? 0 }},
                    {{ $statusStats['failed'] ?? 0 }}
                ],
                backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush

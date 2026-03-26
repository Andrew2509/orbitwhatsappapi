@extends('layouts.app')

@section('title', 'Analytics')
@section('page-title', 'Analytics Dashboard')

@section('content')
@php $nonce = config('app.csp_nonce'); @endphp
<style nonce="{{ $nonce }}">
    .chart-container-300 { height: 300px; position: relative; }
    .chart-container-250 { height: 250px; position: relative; }
</style>
<div class="space-y-6 animate-fade-in">
    <!-- Time Range Selector -->
    <div class="flex items-center justify-between">
        <p class="text-[var(--text-secondary)]">Detailed analytics and performance metrics</p>
        <select class="form-input w-auto">
            <option>Last 7 days</option>
            <option>Last 30 days</option>
            <option>Last 3 months</option>
            <option>Custom range</option>
        </select>
    </div>

    <!-- Main Chart -->
    <div class="card">
        <h3 class="font-semibold text-lg mb-6">Message Volume Trend</h3>
        <div class="chart-container-300">
            <canvas id="volumeChart"></canvas>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Delivery Performance -->
        <div class="card">
            <h3 class="font-semibold text-lg mb-6">Delivery Performance</h3>
            <div class="chart-container-250">
                <canvas id="deliveryChart"></canvas>
            </div>
        </div>

        <!-- Response Time -->
        <div class="card">
            <h3 class="font-semibold text-lg mb-6">Average Response Time</h3>
            <div class="chart-container-250">
                <canvas id="responseChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Performing Devices -->
    <div class="card">
        <h3 class="font-semibold text-lg mb-6">Device Performance</h3>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Messages Sent</th>
                        <th>Delivery Rate</th>
                        <th>Avg Response</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                    <tr>
                        <td class="font-medium">{{ $device->name }}</td>
                        <td>{{ number_format($device->sent_count) }}</td>
                        <td>
                            <span class="text-emerald-500 font-medium">{{ $device->delivery_rate }}%</span>
                        </td>
                        <td>{{ rand(8, 25) / 10 }}s</td>
                        <td>
                            <span class="badge badge-{{ $device->performance_status === 'excellent' ? 'success' : ($device->performance_status === 'good' ? 'info' : 'warning') }}">
                                {{ ucfirst($device->performance_status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-[var(--text-muted)]">No devices found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

    // Volume Chart
    new Chart(document.getElementById('volumeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($volumeChart['labels']) !!},
            datasets: [{
                label: 'Outbound',
                data: {!! json_encode($volumeChart['outbound']) !!},
                backgroundColor: '#10b981',
                borderRadius: 6
            }, {
                label: 'Inbound',
                data: {!! json_encode($volumeChart['inbound']) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: {
                y: { grid: { color: gridColor }, ticks: { color: textColor } },
                x: { grid: { display: false }, ticks: { color: textColor } }
            }
        }
    });

    // Delivery Chart
    new Chart(document.getElementById('deliveryChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($performanceChart['labels']) !!},
            datasets: [{
                label: 'Delivery Rate %',
                data: {!! json_encode($performanceChart['rates']) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: {
                y: { min: 95, max: 100, grid: { color: gridColor }, ticks: { color: textColor } },
                x: { grid: { display: false }, ticks: { color: textColor } }
            }
        }
    });

    // Response Time Chart
    new Chart(document.getElementById('responseChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($performanceChart['labels']) !!},
            datasets: [{
                label: 'Response Time (s)',
                data: {!! json_encode($performanceChart['response']) !!},
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor } } },
            scales: {
                y: { grid: { color: gridColor }, ticks: { color: textColor } },
                x: { grid: { display: false }, ticks: { color: textColor } }
            }
        }
    });
});
</script>
@endpush

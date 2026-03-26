@extends('layouts.admin')

@section('title', 'Revenue Reports')
@section('page-title', 'Revenue Reports')
@section('page-subtitle', 'Laporan pendapatan bulanan')

@section('content')
@php $nonce = config('app.csp_nonce'); @endphp
<style nonce="{{ $nonce }}">
    .chart-container-300 { height: 300px; position: relative; }
</style>
<div class="space-y-6">
    <!-- Year Selector -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
        <form method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium">Tahun:</label>
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Revenue Bulanan {{ $year }}</h3>
            <div class="chart-container-300">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Plans -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Top Plans by Revenue</h3>
            <div class="space-y-4">
                @forelse($topPlans as $plan)
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50">
                    <div>
                        <p class="font-medium">{{ $plan->name }}</p>
                        <p class="text-sm text-slate-500">{{ $plan->count }} transaksi</p>
                    </div>
                    <p class="font-semibold">Rp {{ number_format($plan->revenue, 0, ',', '.') }}</p>
                </div>
                @empty
                <p class="text-slate-500 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Detail Bulanan</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200 dark:border-slate-700">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="pb-3 font-medium">Bulan</th>
                        <th class="pb-3 font-medium">Transaksi</th>
                        <th class="pb-3 font-medium">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @php
                        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    @endphp
                    @foreach($monthlyRevenue as $data)
                    <tr>
                        <td class="py-3">{{ $months[$data->month] }}</td>
                        <td class="py-3">{{ $data->count }}</td>
                        <td class="py-3 font-semibold">Rp {{ number_format($data->revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ config('app.csp_nonce') }}">
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = @json($monthlyRevenue);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // Fill in missing months with 0
    const data = Array(12).fill(0);
    monthlyData.forEach(d => {
        data[d.month - 1] = d.revenue;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection


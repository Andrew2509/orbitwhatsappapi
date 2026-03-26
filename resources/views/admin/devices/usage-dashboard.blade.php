@extends('layouts.admin')

@section('title', 'Device Usage Dashboard')
@section('page-title', 'Device Usage Dashboard')
@section('page-subtitle', 'Monitor penggunaan harian dan status warmup device')

@section('content')
@php $nonce = config('app.csp_nonce'); @endphp
<style nonce="{{ $nonce }}">
    .progress-bar-fill { height: 100%; border-radius: 9999px; transition: all 300ms; }
</style>
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Device Connected</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_connected'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pesan Hari Ini</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['total_sent_today']) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mencapai Limit</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['at_limit'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Warning Level</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['warning_level'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dalam Warmup</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_warmup'] }}</p>
        </div>
    </div>

    <!-- Search -->
    <div class="flex gap-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari device..."
                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
            <button type="submit" class="px-4 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-slate-600">
                Cari
            </button>
        </form>
    </div>

    <!-- Devices Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($devicesWithUsage as $item)
        @php
            $device = $item['device'];
            $usage = $item['usage'];
            $warmup = $item['warmup'];

            $progressColor = 'bg-emerald-500';
            if ($usage['percentage'] >= 80) $progressColor = 'bg-amber-500';
            if ($usage['percentage'] >= 100) $progressColor = 'bg-red-500';
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $device->name }}</h3>
                        <p class="text-sm text-gray-500 font-mono">{{ $device->phone_number }}</p>
                        <p class="text-xs text-gray-400">{{ $device->user->name ?? 'N/A' }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                        Connected
                    </span>
                </div>
            </div>

            <!-- Usage Progress -->
            <div class="p-4 space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Penggunaan Hari Ini</span>
                        <span class="font-semibold {{ $usage['percentage'] >= 100 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ $usage['sent'] }} / {{ $usage['limit'] }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5" x-data="{ width: {{ min($usage['percentage'], 100) }} }">
                        <div class="{{ $progressColor }} h-2.5 rounded-full transition-all" :style="'width: ' + width + '%'"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $usage['remaining'] }} sisa</p>
                </div>

                @if($usage['is_blocked'])
                <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <p class="text-sm text-red-600 dark:text-red-400">🚫 Device diblokir (cooldown)</p>
                    @if($usage['cooldown_until'])
                    <p class="text-xs text-red-500">Sampai: {{ \Carbon\Carbon::parse($usage['cooldown_until'])->format('H:i') }}</p>
                    @endif
                </div>
                @endif

                @if($warmup && !$warmup['is_complete'])
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-blue-600 dark:text-blue-400">🔥 Warmup Day {{ $warmup['day'] }}/7</span>
                        <span class="text-xs text-blue-500">{{ $warmup['progress'] }}/{{ $warmup['target'] }}</span>
                    </div>
                    <p class="text-xs text-blue-500 mt-1">{{ $warmup['description'] }}</p>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="px-4 pb-4 flex gap-2">
                <form action="{{ route('admin.devices.reset-limit', $device) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600"
                        onclick="return confirm('Reset limit untuk device ini?')">
                        Reset Limit
                    </button>
                </form>
                @if(!$warmup || $warmup['is_complete'])
                <form action="{{ route('admin.devices.start-warmup', $device) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/50">
                        Start Warmup
                    </button>
                </form>
                @else
                <form action="{{ route('admin.devices.skip-warmup', $device) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-800/50"
                        onclick="return confirm('Skip warmup untuk device ini?')">
                        Skip Warmup
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white dark:bg-slate-800 rounded-xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500">Tidak ada device yang terhubung</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

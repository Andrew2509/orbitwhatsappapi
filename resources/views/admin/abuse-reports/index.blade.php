@extends('layouts.admin')

@section('title', 'Abuse Reports')
@section('page-title', 'Abuse Reports')
@section('page-subtitle', 'Kelola laporan penyalahgunaan dari pihak eksternal')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Laporan</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Investigating</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['investigating'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Resolved</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor..." 
                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
            <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                <option value="">Semua Status</option>
                @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="reason" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                <option value="">Semua Alasan</option>
                @foreach($reasons as $key => $label)
                <option value="{{ $key }}" {{ request('reason') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-slate-600">
                Filter
            </button>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Nomor Dilaporkan</th>
                        <th class="px-6 py-4 font-medium">Pelapor</th>
                        <th class="px-6 py-4 font-medium">Alasan</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($reports as $report)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-6 py-4">
                            <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $report->reported_phone }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $report->reporter_name }}</p>
                                <p class="text-xs text-slate-500">{{ $report->reporter_email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300">
                                {{ $reasons[$report->reason] ?? $report->reason }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($report->status === 'pending')
                            <span class="admin-badge admin-badge-warning">Pending</span>
                            @elseif($report->status === 'investigating')
                            <span class="admin-badge admin-badge-info">Investigating</span>
                            @elseif($report->status === 'resolved')
                            <span class="admin-badge admin-badge-success">Resolved</span>
                            @else
                            <span class="admin-badge admin-badge-secondary">Dismissed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $report->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.abuse-reports.show', $report) }}" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($report->status === 'pending')
                                <form action="{{ route('admin.abuse-reports.investigate', $report) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg text-blue-500" title="Investigate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>Belum ada laporan abuse</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $reports->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

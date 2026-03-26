@extends('layouts.admin')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan Abuse')
@section('page-subtitle', 'Review dan selesaikan laporan')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Report Info -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Laporan #{{ $abuseReport->id }}</h3>
                <p class="text-sm text-gray-500">{{ $abuseReport->created_at->format('d M Y H:i') }}</p>
            </div>
            @if($abuseReport->status === 'pending')
            <span class="admin-badge admin-badge-warning">Pending</span>
            @elseif($abuseReport->status === 'investigating')
            <span class="admin-badge admin-badge-info">Investigating</span>
            @elseif($abuseReport->status === 'resolved')
            <span class="admin-badge admin-badge-success">Resolved</span>
            @else
            <span class="admin-badge admin-badge-secondary">Dismissed</span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Reported Phone -->
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <p class="text-sm font-medium text-red-600 dark:text-red-400 mb-1">Nomor Dilaporkan</p>
                <p class="text-xl font-mono font-bold text-red-700 dark:text-red-300">{{ $abuseReport->reported_phone }}</p>
                @if($relatedDevice)
                <p class="mt-2 text-xs text-red-500">⚠️ Terdaftar dalam sistem sebagai device user</p>
                @endif
            </div>

            <!-- Reporter Info -->
            <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pelapor</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $abuseReport->reporter_name }}</p>
                <p class="text-sm text-gray-500">{{ $abuseReport->reporter_email }}</p>
                @if($abuseReport->reporter_phone)
                <p class="text-sm text-gray-500">{{ $abuseReport->reporter_phone }}</p>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Alasan</p>
            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300">
                {{ \App\Models\AbuseReport::REASONS[$abuseReport->reason] ?? $abuseReport->reason }}
            </span>
        </div>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Deskripsi</p>
            <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-700/50 p-4 rounded-lg">{{ $abuseReport->description }}</p>
        </div>

        @if($abuseReport->evidence)
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Bukti</p>
            <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-700/50 p-4 rounded-lg">{{ $abuseReport->evidence }}</p>
        </div>
        @endif
    </div>

    <!-- Report History for this phone -->
    @if($reportHistory->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Riwayat Laporan untuk {{ $abuseReport->reported_phone }}
        </h3>
        <p class="text-sm text-amber-600 dark:text-amber-400 mb-4">⚠️ Nomor ini sudah dilaporkan {{ $reportHistory->count() + 1 }} kali</p>
        <div class="space-y-2">
            @foreach($reportHistory as $history)
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg text-sm">
                <span>{{ $history->reason }} - {{ Str::limit($history->description, 50) }}</span>
                <span class="text-gray-500">{{ $history->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Resolution Form -->
    @if(in_array($abuseReport->status, ['pending', 'investigating']))
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Selesaikan Laporan</h3>
        <form action="{{ route('admin.abuse-reports.resolve', $abuseReport) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan Resolusi</label>
                <textarea name="resolution_notes" rows="4" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                    placeholder="Tuliskan tindakan yang diambil..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" name="action" value="resolve" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    ✓ Resolve
                </button>
                <button type="submit" name="action" value="dismiss" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    ✗ Dismiss
                </button>
            </div>
        </form>
    </div>
    @else
    <!-- Resolution Info -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resolusi</h3>
        <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-700/50 p-4 rounded-lg">{{ $abuseReport->resolution_notes }}</p>
        <p class="mt-2 text-sm text-gray-500">
            Diselesaikan oleh {{ $abuseReport->resolvedBy?->name ?? 'Unknown' }} 
            pada {{ $abuseReport->resolved_at?->format('d M Y H:i') }}
        </p>
    </div>
    @endif

    <div class="flex gap-3">
        <a href="{{ route('admin.abuse-reports.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg">
            ← Kembali
        </a>
    </div>
</div>
@endsection

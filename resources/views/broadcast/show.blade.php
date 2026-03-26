@extends('layouts.app')

@section('title', 'Campaign Detail')
@section('page-title', $campaign->name)

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Back Link -->
    <a href="{{ route('broadcast.index') }}" class="inline-flex items-center gap-2 text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Broadcast
    </a>

    <!-- Campaign Header -->
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold">{{ $campaign->name }}</h2>
                <p class="text-[var(--text-muted)]">
                    {{ $campaign->message_type === 'template' ? 'Template: ' . ($campaign->template->name ?? '-') : 'Text Biasa' }}
                    @if($campaign->device) • Device: {{ $campaign->device->name }} @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColors = [
                        'completed' => 'bg-emerald-500',
                        'running' => 'bg-amber-500',
                        'scheduled' => 'bg-blue-500',
                        'paused' => 'bg-orange-500',
                        'draft' => 'bg-gray-500',
                        'cancelled' => 'bg-red-500',
                    ];
                @endphp
                <span class="px-4 py-2 {{ $statusColors[$campaign->status] ?? 'bg-gray-500' }} text-white rounded-full text-sm font-bold">
                    {{ ucfirst($campaign->status) }}
                </span>
                @if(in_array($campaign->status, ['draft', 'paused', 'scheduled']))
                <form action="{{ route('broadcast.start', $campaign) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Start Campaign</button>
                </form>
                @elseif($campaign->status === 'running')
                <form action="{{ route('broadcast.pause', $campaign) }}" method="POST">
                    @csrf
                    <button class="btn btn-secondary">Pause</button>
                </form>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex justify-between text-sm mb-2">
                <span>Progress: {{ $campaign->sent_count + $campaign->failed_count }} / {{ $campaign->total_recipients }}</span>
                <span class="font-bold">{{ $campaign->progress_percent }}%</span>
            </div>
            <div class="w-full h-4 bg-[var(--bg-tertiary)] rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500" 
                     style="width: {{ $campaign->progress_percent }}%"></div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-[var(--bg-secondary)] rounded-xl p-4 text-center">
                <div class="text-2xl font-bold">{{ number_format($campaign->total_recipients) }}</div>
                <div class="text-xs text-[var(--text-muted)]">Total</div>
            </div>
            <div class="bg-[var(--bg-secondary)] rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-emerald-500">{{ number_format($campaign->sent_count) }}</div>
                <div class="text-xs text-[var(--text-muted)]">Terkirim</div>
            </div>
            <div class="bg-[var(--bg-secondary)] rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-red-500">{{ number_format($campaign->failed_count) }}</div>
                <div class="text-xs text-[var(--text-muted)]">Gagal</div>
            </div>
            <div class="bg-[var(--bg-secondary)] rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-amber-500">{{ number_format($recipients->where('status', 'pending')->count()) }}</div>
                <div class="text-xs text-[var(--text-muted)]">Pending</div>
            </div>
            <div class="bg-[var(--bg-secondary)] rounded-xl p-4 text-center">
                <div class="text-2xl font-bold">{{ $campaign->current_batch }}</div>
                <div class="text-xs text-[var(--text-muted)]">Batch</div>
            </div>
        </div>
    </div>

    <!-- Safety Settings Info -->
    <div class="bg-amber-500/5 border border-amber-500/20 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="font-bold text-amber-500">Safety Settings</span>
        </div>
        <p class="text-sm text-[var(--text-muted)]">
            Delay: {{ $campaign->delay_min }}-{{ $campaign->delay_max }} detik • 
            Batch: {{ $campaign->batch_size }} pesan, istirahat {{ $campaign->batch_delay }} detik
        </p>
    </div>

    <!-- Recipients Table -->
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden">
        <div class="px-6 py-4 border-b border-[var(--border-color)]">
            <h3 class="font-bold">Daftar Penerima</h3>
        </div>
        <div class="overflow-x-auto max-h-96">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-[var(--bg-secondary)]">
                    <tr class="border-b border-[var(--border-color)]">
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">#</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Nomor</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Nama</th>
                        <th class="text-center py-3 px-5 font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Waktu Kirim</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recipients as $index => $recipient)
                    <tr class="border-b border-[var(--border-color)] hover:bg-[var(--bg-secondary)]/50">
                        <td class="py-3 px-5 text-[var(--text-muted)]">{{ $index + 1 }}</td>
                        <td class="py-3 px-5 font-mono">{{ $recipient->phone }}</td>
                        <td class="py-3 px-5">{{ $recipient->name ?: '-' }}</td>
                        <td class="py-3 px-5 text-center">
                            @php
                                $recipientStatusColors = [
                                    'sent' => 'bg-emerald-500/10 text-emerald-500',
                                    'delivered' => 'bg-blue-500/10 text-blue-500',
                                    'failed' => 'bg-red-500/10 text-red-500',
                                    'queued' => 'bg-amber-500/10 text-amber-500',
                                    'pending' => 'bg-gray-500/10 text-gray-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $recipientStatusColors[$recipient->status] ?? '' }}">
                                {{ ucfirst($recipient->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-[var(--text-muted)]">{{ $recipient->sent_at ? $recipient->sent_at->format('H:i:s') : '-' }}</td>
                        <td class="py-3 px-5 text-red-500 text-xs">{{ $recipient->error_message ? Str::limit($recipient->error_message, 30) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

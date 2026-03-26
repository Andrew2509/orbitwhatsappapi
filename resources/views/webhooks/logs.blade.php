@extends('layouts.app')

@section('title', 'Webhook Logs')
@section('page-title', 'Webhook Logs')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Back Button & Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('webhooks.index') }}" class="p-2 hover:bg-[var(--bg-secondary)] rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold">Log Pengiriman Webhook</h2>
            <code class="text-sm text-emerald-500">{{ $webhook->url }}</code>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4 text-center">
            <div class="text-2xl font-bold text-[var(--text-primary)]">{{ $logs->total() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Total</div>
        </div>
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4 text-center">
            <div class="text-2xl font-bold text-emerald-500">{{ $webhook->logs()->where('status', 'success')->count() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Sukses</div>
        </div>
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4 text-center">
            <div class="text-2xl font-bold text-red-500">{{ $webhook->logs()->where('status', 'failed')->count() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Gagal</div>
        </div>
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4 text-center">
            <div class="text-2xl font-bold">{{ round($webhook->logs()->avg('duration_ms') ?? 0) }}ms</div>
            <div class="text-xs text-[var(--text-muted)]">Avg Response</div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[var(--bg-secondary)]">
                    <tr>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Waktu</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Event</th>
                        <th class="text-center py-3 px-5 font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-center py-3 px-5 font-semibold text-[var(--text-muted)]">Response</th>
                        <th class="text-center py-3 px-5 font-semibold text-[var(--text-muted)]">Attempt</th>
                        <th class="text-right py-3 px-5 font-semibold text-[var(--text-muted)]">Duration</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-[var(--border-color)] hover:bg-[var(--bg-secondary)]/50" x-data="expandableRow">
                        <td class="py-3 px-5 text-[var(--text-muted)]">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="py-3 px-5">
                            <code class="text-xs px-2 py-1 bg-indigo-500/10 text-indigo-500 rounded">{{ $log->event }}</code>
                        </td>
                        <td class="py-3 px-5 text-center">
                            @if($log->status === 'success')
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-xs font-bold">Sukses</span>
                            @elseif($log->status === 'failed')
                            <span class="px-2 py-1 bg-red-500/10 text-red-500 rounded-full text-xs font-bold">Gagal</span>
                            @else
                            <span class="px-2 py-1 bg-amber-500/10 text-amber-500 rounded-full text-xs font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="py-3 px-5 text-center">
                            <span class="{{ $log->response_code >= 200 && $log->response_code < 300 ? 'text-emerald-500' : 'text-red-500' }} font-mono">
                                {{ $log->response_code ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-center">{{ $log->attempt }}</td>
                        <td class="py-3 px-5 text-right text-[var(--text-muted)]">{{ $log->duration_ms ?? '-' }}ms</td>
                        <td class="py-3 px-5">
                            <button @click="toggle()" class="text-indigo-500 hover:underline text-xs">
                                <span x-text="expanded ? 'Hide' : 'View'"></span>
                            </button>
                        </td>
                    </tr>
                    <tr x-show="expanded" x-collapse class="bg-[var(--bg-tertiary)]">
                        <td colspan="7" class="p-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-xs font-bold text-[var(--text-muted)] mb-2 uppercase">Payload Sent</h4>
                                    <pre class="text-xs bg-[var(--bg-secondary)] p-3 rounded-xl overflow-x-auto max-h-40"><code class="text-emerald-400">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-[var(--text-muted)] mb-2 uppercase">Response</h4>
                                    @if($log->error_message)
                                    <div class="text-xs text-red-500 bg-red-500/10 p-3 rounded-xl mb-2">
                                        <strong>Error:</strong> {{ $log->error_message }}
                                    </div>
                                    @endif
                                    @if($log->response_body)
                                    <pre class="text-xs bg-[var(--bg-secondary)] p-3 rounded-xl overflow-x-auto max-h-40"><code>{{ $log->response_body }}</code></pre>
                                    @else
                                    <p class="text-xs text-[var(--text-muted)]">No response body</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[var(--text-muted)]">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Belum ada log pengiriman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-[var(--border-color)]">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

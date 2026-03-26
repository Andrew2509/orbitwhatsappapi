@extends('layouts.app')

@section('title', 'Webhooks')
@section('page-title', 'Webhook Configuration')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="webhookManager">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <p class="text-[var(--text-secondary)]">Konfigurasi URL untuk menerima notifikasi real-time dari WhatsApp</p>
        </div>
        <button @click="openAddModal()" class="btn btn-primary shadow-lg shadow-indigo-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Webhook
        </button>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold">{{ $webhooks->where('is_active', true)->count() }}</div>
                    <div class="text-xs text-[var(--text-muted)]">Webhook Aktif</div>
                </div>
            </div>
        </div>
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold">{{ $recentLogs->where('status', 'success')->count() }}</div>
                    <div class="text-xs text-[var(--text-muted)]">Sukses (24 jam)</div>
                </div>
            </div>
        </div>
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold">{{ $recentLogs->where('status', 'failed')->count() }}</div>
                    <div class="text-xs text-[var(--text-muted)]">Gagal (24 jam)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Cards -->
    <div class="space-y-4">
        @forelse($webhooks as $webhook)
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-6 {{ !$webhook->is_active ? 'opacity-60' : '' }}">
            <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <code class="text-sm font-mono truncate text-emerald-500">{{ $webhook->url }}</code>
                        <button @click="copyToClipboard('{{ $webhook->url }}')" class="p-1 hover:bg-[var(--bg-secondary)] rounded transition-colors flex-shrink-0" title="Copy URL">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Events -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($webhook->events as $event)
                        <span class="px-2 py-0.5 text-[10px] font-mono bg-indigo-500/10 text-indigo-500 rounded border border-indigo-500/20">{{ $event }}</span>
                        @endforeach
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-4 text-xs text-[var(--text-muted)]">
                        <span>Last triggered: {{ $webhook->last_triggered_at ? $webhook->last_triggered_at->diffForHumans() : 'Never' }}</span>
                        <span>•</span>
                        <span>Retry: {{ $webhook->max_retries ?? 3 }}x</span>
                        <span>•</span>
                        <span>Failures: <span class="{{ ($webhook->failure_count ?? 0) > 0 ? 'text-red-500' : '' }}">{{ $webhook->failure_count ?? 0 }}</span></span>
                    </div>

                    <!-- Secret Key -->
                    <div class="mt-3 p-3 bg-[var(--bg-secondary)] rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-[var(--text-muted)]">Secret Key:</span>
                                <code class="text-xs font-mono" x-show="!showSecrets[{{ $webhook->id }}]">••••••••••••••••</code>
                                <code class="text-xs font-mono text-amber-500" x-show="showSecrets[{{ $webhook->id }}]" x-text="secrets[{{ $webhook->id }}]"></code>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="toggleSecret({{ $webhook->id }})" class="text-xs text-indigo-500 hover:underline">
                                    <span x-text="showSecrets[{{ $webhook->id }}] ? 'Hide' : 'Show'"></span>
                                </button>
                                <form action="{{ route('webhooks.regenerate-secret', $webhook) }}" method="POST" class="inline" onsubmit="return confirm('Regenerate secret key? Ini akan membuat signature lama tidak valid.')">
                                    @csrf
                                    <button type="submit" class="text-xs text-amber-500 hover:underline">Regenerate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 justify-end lg:justify-start">
                    <form action="{{ route('webhooks.test', $webhook) }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2.5 hover:bg-blue-500/10 text-blue-500 rounded-xl transition-colors" title="Test">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </button>
                    </form>

                    <a href="{{ route('webhooks.logs', $webhook) }}" class="p-2.5 hover:bg-purple-500/10 text-purple-500 rounded-xl transition-colors" title="Logs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </a>

                    <form action="{{ route('webhooks.toggle', $webhook) }}" method="POST">
                        @csrf
                        <button type="submit" class="relative inline-flex items-center cursor-pointer p-1">
                            <div class="w-11 h-6 bg-[var(--bg-tertiary)] rounded-full transition-colors {{ $webhook->is_active ? 'bg-emerald-500' : '' }}">
                                <div class="w-5 h-5 bg-white rounded-full shadow transform transition-transform mt-0.5 ml-0.5 {{ $webhook->is_active ? 'translate-x-5' : '' }}"></div>
                            </div>
                        </button>
                    </form>

                    <button @click="editWebhook({{ $webhook->id }}, '{{ $webhook->url }}', {{ json_encode($webhook->events) }}, {{ $webhook->max_retries ?? 3 }})" class="p-2.5 hover:bg-[var(--bg-secondary)] rounded-xl transition-colors" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>

                    <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" onsubmit="return confirm('Hapus webhook ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2.5 hover:bg-red-500/10 text-red-500 rounded-xl transition-colors" title="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] py-16 text-center">
            <div class="w-20 h-20 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-indigo-500">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Belum ada Webhook</h3>
            <p class="text-[var(--text-muted)] mb-6">Tambahkan webhook untuk menerima notifikasi real-time dari WhatsApp</p>
            <button @click="openAddModal()" class="btn btn-primary">Tambah Webhook</button>
        </div>
        @endforelse
    </div>

    <!-- Recent Logs Section -->
    @if($recentLogs->count() > 0)
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden">
        <div class="px-6 py-4 border-b border-[var(--border-color)] flex items-center justify-between">
            <h3 class="font-bold text-lg">Log Pengiriman Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[var(--bg-secondary)]">
                    <tr>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Waktu</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">Event</th>
                        <th class="text-left py-3 px-5 font-semibold text-[var(--text-muted)]">URL</th>
                        <th class="text-center py-3 px-5 font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-right py-3 px-5 font-semibold text-[var(--text-muted)]">Response</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs->take(10) as $log)
                    <tr class="border-b border-[var(--border-color)] hover:bg-[var(--bg-secondary)]/50">
                        <td class="py-3 px-5 text-[var(--text-muted)]">{{ $log->created_at->format('d M H:i:s') }}</td>
                        <td class="py-3 px-5"><code class="text-xs px-2 py-1 bg-indigo-500/10 text-indigo-500 rounded">{{ $log->event }}</code></td>
                        <td class="py-3 px-5 font-mono text-xs truncate max-w-[200px]">{{ $log->webhook->url ?? '-' }}</td>
                        <td class="py-3 px-5 text-center">
                            @if($log->status === 'success')
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-xs font-bold">✓</span>
                            @elseif($log->status === 'failed')
                            <span class="px-2 py-1 bg-red-500/10 text-red-500 rounded-full text-xs font-bold">✗</span>
                            @else
                            <span class="px-2 py-1 bg-amber-500/10 text-amber-500 rounded-full text-xs font-bold">⏳</span>
                            @endif
                        </td>
                        <td class="py-3 px-5 text-right">
                            <span class="{{ $log->response_code >= 200 && $log->response_code < 300 ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $log->response_code ?? '-' }}
                            </span>
                            @if($log->duration_ms)
                            <span class="text-[var(--text-muted)]">({{ $log->duration_ms }}ms)</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payload Examples Section -->
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden">
        <div class="px-6 py-4 border-b border-[var(--border-color)]">
            <h3 class="font-bold text-lg">Contoh Payload JSON</h3>
            <p class="text-sm text-[var(--text-muted)]">Data yang akan diterima oleh server Anda</p>
        </div>
        <div class="p-6 space-y-4" x-data="{ activeTab: 'received' }">
            <div class="flex flex-wrap gap-2 mb-4">
                <button @click="activeTab = 'received'" :class="activeTab === 'received' ? 'bg-indigo-500 text-white' : 'bg-[var(--bg-secondary)]'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">message.received</button>
                <button @click="activeTab = 'status'" :class="activeTab === 'status' ? 'bg-indigo-500 text-white' : 'bg-[var(--bg-secondary)]'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">message.delivered</button>
                <button @click="activeTab = 'device'" :class="activeTab === 'device' ? 'bg-indigo-500 text-white' : 'bg-[var(--bg-secondary)]'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">device.disconnected</button>
            </div>

            <div x-show="activeTab === 'received'" class="bg-[var(--bg-tertiary)] rounded-xl p-4 overflow-x-auto">
<pre class="text-sm font-mono text-emerald-400"><code>{
  "event": "message.received",
  "timestamp": "2024-12-28T10:30:00+07:00",
  "webhook_id": 1,
  "data": {
    "device_id": "8b2a-44c1",
    "from": "628123456789",
    "pushName": "Budi",
    "message": "Halo, berapa harganya?",
    "messageType": "text",
    "timestamp": 1672531200
  }
}</code></pre>
            </div>

            <div x-show="activeTab === 'status'" class="bg-[var(--bg-tertiary)] rounded-xl p-4 overflow-x-auto">
<pre class="text-sm font-mono text-blue-400"><code>{
  "event": "message.delivered",
  "timestamp": "2024-12-28T10:30:05+07:00",
  "webhook_id": 1,
  "data": {
    "device_id": "8b2a-44c1",
    "message_id": "3EB0198C91A236860C8CD7",
    "to": "628123456789",
    "status": "delivered",
    "timestamp": 1672531205
  }
}</code></pre>
            </div>

            <div x-show="activeTab === 'device'" class="bg-[var(--bg-tertiary)] rounded-xl p-4 overflow-x-auto">
<pre class="text-sm font-mono text-red-400"><code>{
  "event": "device.disconnected",
  "timestamp": "2024-12-28T10:35:00+07:00",
  "webhook_id": 1,
  "data": {
    "device_id": "8b2a-44c1",
    "device_name": "Device Utama",
    "reason": "Connection lost",
    "last_seen": "2024-12-28T10:34:55+07:00"
  }
}</code></pre>
            </div>

            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 mt-4">
                <p class="text-sm text-amber-500">
                    <strong>Signature Verification:</strong> Setiap request akan menyertakan header <code class="bg-amber-500/20 px-1 rounded">X-Webhook-Signature</code> berisi HMAC-SHA256 dari payload menggunakan Secret Key Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--bg-primary)] rounded-2xl shadow-2xl w-full max-w-lg border border-[var(--border-color)]" @click.away="closeModal()">
            <div class="px-6 py-4 border-b border-[var(--border-color)] flex items-center justify-between">
                <h3 class="text-lg font-bold" x-text="editingId ? 'Edit Webhook' : 'Tambah Webhook'"></h3>
                <button @click="closeModal()" class="p-1.5 hover:bg-[var(--bg-secondary)] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editingId ? '/webhooks/' + editingId : '{{ route('webhooks.store') }}'" method="POST" class="p-6 space-y-5">
                @csrf
                <template x-if="editingId">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1.5">Webhook URL</label>
                    <input type="url" name="url" x-model="form.url" class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl" placeholder="https://your-domain.com/webhook" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1.5">Retry Policy</label>
                    <select name="max_retries" x-model="form.max_retries" class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        <option value="0">Tidak retry</option>
                        <option value="1">1x retry</option>
                        <option value="3">3x retry (default)</option>
                        <option value="5">5x retry</option>
                        <option value="10">10x retry</option>
                    </select>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Jumlah percobaan ulang jika pengiriman gagal</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[var(--text-secondary)] mb-2">Events</label>
                    <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                        @foreach($availableEvents as $event => $description)
                        <label class="flex items-start gap-3 p-3 hover:bg-[var(--bg-secondary)] rounded-xl cursor-pointer transition-colors border border-transparent hover:border-[var(--border-color)]">
                            <input type="checkbox" name="events[]" value="{{ $event }}"
                                   :checked="form.events.includes('{{ $event }}')"
                                   @change="toggleEvent('{{ $event }}')"
                                   class="w-4 h-4 mt-0.5 rounded border-[var(--border-color)] text-indigo-500 focus:ring-indigo-500">
                            <div>
                                <code class="text-xs text-indigo-500">{{ $event }}</code>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $description }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="closeModal()" class="flex-1 px-4 py-3 bg-[var(--bg-secondary)] rounded-xl font-medium">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-bold">
                        <span x-text="editingId ? 'Update' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- Webhook logic moved to app.js for CSP compliance --}}
@endpush
@endsection

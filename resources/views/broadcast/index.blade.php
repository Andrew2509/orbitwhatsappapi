@extends('layouts.app')

@section('title', 'Broadcast')
@section('page-title', 'Broadcast / Blast Messages')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="broadcastManager">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <p class="text-[var(--text-secondary)]">Kirim pesan massal ke banyak penerima dengan fitur anti-banned.</p>
        </div>
        <button @click="openCreateModal()" class="btn btn-primary shadow-lg shadow-indigo-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Campaign Baru
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-emerald-500">{{ number_format($stats['completed']) }}</div>
            <div class="text-xs text-[var(--text-muted)]">Selesai</div>
        </div>
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-amber-500">{{ number_format($stats['running']) }}</div>
            <div class="text-xs text-[var(--text-muted)]">Berjalan</div>
        </div>
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-blue-500">{{ number_format($stats['scheduled']) }}</div>
            <div class="text-xs text-[var(--text-muted)]">Terjadwal</div>
        </div>
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-purple-500">{{ $campaigns->count() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Total Campaign</div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border-color)] bg-[var(--bg-secondary)]">
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Campaign</th>
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Penerima</th>
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Progress</th>
                        <th class="text-center py-4 px-5 font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-right py-4 px-5 font-semibold text-[var(--text-muted)]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr class="border-b border-[var(--border-color)] hover:bg-[var(--bg-secondary)]/50 transition-colors">
                        <td class="py-4 px-5">
                            <div class="font-bold">{{ $campaign->name }}</div>
                            <div class="text-xs text-[var(--text-muted)]">
                                {{ $campaign->message_type === 'template' ? 'Template' : 'Text' }}
                                @if($campaign->template) • {{ $campaign->template->name }} @endif
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <div class="text-lg font-bold">{{ number_format($campaign->total_recipients) }}</div>
                            <div class="text-xs text-[var(--text-muted)]">
                                ✓ {{ $campaign->sent_count }} • ✗ {{ $campaign->failed_count }}
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-32 h-2.5 bg-[var(--bg-tertiary)] rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500"
                                         style="width: {{ $campaign->progress_percent }}%"></div>
                                </div>
                                <span class="text-sm font-bold">{{ $campaign->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @php
                                $statusColors = [
                                    'completed' => 'bg-emerald-500/10 text-emerald-500',
                                    'running' => 'bg-amber-500/10 text-amber-500',
                                    'scheduled' => 'bg-blue-500/10 text-blue-500',
                                    'paused' => 'bg-orange-500/10 text-orange-500',
                                    'draft' => 'bg-gray-500/10 text-gray-400',
                                    'cancelled' => 'bg-red-500/10 text-red-500',
                                ];
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColors[$campaign->status] ?? '' }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('broadcast.show', $campaign) }}" class="p-2 hover:bg-indigo-500/10 text-indigo-500 rounded-xl transition-colors" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if(in_array($campaign->status, ['draft', 'paused', 'scheduled']))
                                <form action="{{ route('broadcast.start', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 hover:bg-emerald-500/10 text-emerald-500 rounded-xl transition-colors" title="Start">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                                @elseif($campaign->status === 'running')
                                <form action="{{ route('broadcast.pause', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 hover:bg-amber-500/10 text-amber-500 rounded-xl transition-colors" title="Pause">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                                @endif
                                @if(!in_array($campaign->status, ['completed', 'cancelled']))
                                <form action="{{ route('broadcast.cancel', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan campaign ini?')">
                                    @csrf
                                    <button type="submit" class="p-2 hover:bg-red-500/10 text-red-500 rounded-xl transition-colors" title="Cancel">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="w-20 h-20 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-indigo-500">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Belum ada Campaign</h3>
                            <p class="text-[var(--text-muted)] mb-6">Buat campaign pertama untuk mulai blast pesan.</p>
                            <button @click="openCreateModal()" class="btn btn-primary">Buat Campaign</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    <div x-show="showCreateModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/60 backdrop-blur-sm overflow-y-auto"
         x-cloak>
        <div class="bg-[var(--bg-primary)] rounded-2xl shadow-2xl w-full max-w-2xl border border-[var(--border-color)] my-8"
             @click.away="showCreateModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="px-6 py-4 border-b border-[var(--border-color)] flex items-center justify-between sticky top-0 bg-[var(--bg-primary)] rounded-t-2xl">
                <h3 class="text-lg font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">Buat Campaign Baru</h3>
                <button @click="showCreateModal = false" class="p-1.5 hover:bg-[var(--bg-secondary)] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('broadcast.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <!-- Campaign Name & Device -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Nama Campaign</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl" placeholder="Misal: Promo Tahun Baru">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Device Pengirim</label>
                        <select name="device_id" required class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                            <option value="">Pilih Device...</option>
                            @foreach($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->phone_number }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Message Type Toggle -->
                <div>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-2">Jenis Pesan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="form.message_type = 'text'"
                                :class="form.message_type === 'text' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-[var(--bg-secondary)] border-[var(--border-color)]'"
                                class="p-3 rounded-xl border-2 flex items-center justify-center gap-2 text-sm font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            Text + Spintax
                        </button>
                        <button type="button" @click="form.message_type = 'template'"
                                :class="form.message_type === 'template' ? 'bg-purple-500 text-white border-purple-500' : 'bg-[var(--bg-secondary)] border-[var(--border-color)]'"
                                class="p-3 rounded-xl border-2 flex items-center justify-center gap-2 text-sm font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Template
                        </button>
                    </div>
                    <input type="hidden" name="message_type" :value="form.message_type">
                </div>

                <!-- Custom Message (Text) -->
                <div x-show="form.message_type === 'text'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Isi Pesan (Spintax Supported)</label>
                    <textarea name="custom_message" rows="4" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl" placeholder="{Halo|Hai|Hi} @{{nama}}, ada promo spesial buat kamu!"></textarea>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Gunakan {opsi1|opsi2|opsi3} untuk variasi acak. Variabel: @{{nama}}, @{{phone}}</p>
                </div>

                <!-- Template Selector -->
                <div x-show="form.message_type === 'template'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Pilih Template</label>
                    <select name="template_id" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        <option value="">-- Pilih Template --</option>
                        @foreach($templates->groupBy('category') as $category => $categoryTemplates)
                        <optgroup label="{{ $category }}">
                            @foreach($categoryTemplates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>

                <!-- Recipients Source -->
                <div>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-2">Sumber Penerima</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" @click="form.recipients_type = 'csv'"
                                :class="form.recipients_type === 'csv' ? 'border-indigo-500 bg-indigo-500/10' : 'border-[var(--border-color)]'"
                                class="p-3 border-2 rounded-xl flex flex-col items-center gap-1 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-xs font-bold">CSV</span>
                        </button>
                        <button type="button" @click="form.recipients_type = 'all'"
                                :class="form.recipients_type === 'all' ? 'border-indigo-500 bg-indigo-500/10' : 'border-[var(--border-color)]'"
                                class="p-3 border-2 rounded-xl flex flex-col items-center gap-1 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-xs font-bold">Semua</span>
                        </button>
                        <button type="button" @click="form.recipients_type = 'label'"
                                :class="form.recipients_type === 'label' ? 'border-indigo-500 bg-indigo-500/10' : 'border-[var(--border-color)]'"
                                class="p-3 border-2 rounded-xl flex flex-col items-center gap-1 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span class="text-xs font-bold">Label</span>
                        </button>
                        <button type="button" @click="form.recipients_type = 'manual'"
                                :class="form.recipients_type === 'manual' ? 'border-indigo-500 bg-indigo-500/10' : 'border-[var(--border-color)]'"
                                class="p-3 border-2 rounded-xl flex flex-col items-center gap-1 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span class="text-xs font-bold">Manual</span>
                        </button>
                    </div>
                    <input type="hidden" name="recipients_type" :value="form.recipients_type">
                </div>

                <!-- CSV Upload -->
                <div x-show="form.recipients_type === 'csv'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Upload File CSV</label>
                    <input type="file" name="recipients_csv" accept=".csv,.txt" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Format: phone,nama,variabel1,variabel2... (baris pertama = header)</p>
                </div>

                <!-- Label Selector -->
                <div x-show="form.recipients_type === 'label'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Pilih Label</label>
                    <select name="recipients_label" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        <option value="">-- Pilih Label --</option>
                        @foreach($labels as $label)
                        <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Manual Input -->
                <div x-show="form.recipients_type === 'manual'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Nomor WhatsApp</label>
                    <textarea name="recipients_manual" rows="3" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl" placeholder="628123456789, 628234567890..."></textarea>
                </div>

                <!-- Safety Settings (Anti-Ban) -->
                <div class="p-4 bg-amber-500/5 border border-amber-500/20 rounded-2xl">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-bold text-amber-500 text-sm">Safety Settings (Anti-Banned)</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] mb-1">Delay Min (detik)</label>
                            <input type="number" name="delay_min" value="10" min="5" max="120" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] mb-1">Delay Max (detik)</label>
                            <input type="number" name="delay_max" value="30" min="5" max="300" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] mb-1">Batch Size</label>
                            <input type="number" name="batch_size" value="50" min="10" max="200" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] mb-1">Batch Delay (detik)</label>
                            <input type="number" name="batch_delay" value="300" min="60" max="1800" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                        </div>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-2">Jeda acak 10-30 detik. Setelah 50 pesan, istirahat 5 menit.</p>
                </div>

                <!-- Schedule -->
                <div>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Jadwalkan (Opsional)</label>
                    <input type="datetime-local" name="scheduled_at" class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl">
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="flex-1 px-4 py-3 bg-[var(--bg-secondary)] text-sm font-bold rounded-xl">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-bold rounded-xl shadow-lg">Buat Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- Broadcast logic moved to app.js for CSP compliance --}}
@endpush
@endsection

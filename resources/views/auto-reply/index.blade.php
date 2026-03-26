@extends('layouts.app')

@section('title', 'Auto Reply')
@section('page-title', 'Auto Reply / Chatbot')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="autoReplyManager">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <p class="text-[var(--text-secondary)]">Balas pesan otomatis berdasarkan kata kunci. Mendukung teks biasa atau template.</p>
        </div>
        <button @click="openCreateModal()" class="btn btn-primary shadow-lg shadow-indigo-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Auto Reply
        </button>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-indigo-500">{{ $autoReplies->count() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Total Rules</div>
        </div>
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-emerald-500">{{ $autoReplies->where('is_active', true)->count() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Active</div>
        </div>
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-amber-500">{{ $autoReplies->sum('triggered_count') }}</div>
            <div class="text-xs text-[var(--text-muted)]">Total Triggered</div>
        </div>
        <div class="bg-[var(--bg-secondary)] rounded-2xl p-4 border border-[var(--border-color)]">
            <div class="text-2xl font-bold text-purple-500">{{ $templates->count() }}</div>
            <div class="text-xs text-[var(--text-muted)]">Templates Ready</div>
        </div>
    </div>

    <!-- Auto Reply Rules Table -->
    <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border-color)] bg-[var(--bg-secondary)]">
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Kata Kunci</th>
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Tipe</th>
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Balasan</th>
                        <th class="text-left py-4 px-5 font-semibold text-[var(--text-muted)]">Device</th>
                        <th class="text-center py-4 px-5 font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-right py-4 px-5 font-semibold text-[var(--text-muted)]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($autoReplies as $rule)
                    <tr class="border-b border-[var(--border-color)] hover:bg-[var(--bg-secondary)]/50 transition-colors {{ !$rule->is_active ? 'opacity-50' : '' }}">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1.5 bg-indigo-500/10 text-indigo-500 rounded-lg font-mono text-sm font-bold">{{ $rule->keyword }}</span>
                                <span class="text-[10px] px-2 py-0.5 bg-[var(--bg-tertiary)] text-[var(--text-muted)] rounded uppercase">{{ $rule->match_type }}</span>
                            </div>
                            <div class="text-xs text-[var(--text-muted)] mt-1">Triggered {{ number_format($rule->triggered_count) }}x</div>
                        </td>
                        <td class="py-4 px-5">
                            @if($rule->reply_type === 'template')
                            <span class="inline-flex items-center gap-1 text-purple-500 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Template
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-emerald-500 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                Text
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 max-w-xs">
                            <div class="truncate text-[var(--text-secondary)]">
                                @if($rule->reply_type === 'template' && $rule->template)
                                    <span class="text-purple-400">[{{ $rule->template->name }}]</span> {{ Str::limit($rule->template->content, 50) }}
                                @else
                                    {{ Str::limit($rule->reply_value, 60) }}
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            @if($rule->device)
                            <span class="text-xs">{{ $rule->device->name }}</span>
                            @else
                            <span class="text-xs text-[var(--text-muted)]">Semua</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center">
                            <form action="{{ route('auto-reply.toggle', $rule) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="relative inline-flex items-center cursor-pointer group">
                                    <div class="w-11 h-6 rounded-full transition-colors {{ $rule->is_active ? 'bg-emerald-500' : 'bg-[var(--bg-tertiary)]' }}">
                                        <div class="w-5 h-5 bg-white rounded-full shadow transform transition-transform mt-0.5 ml-0.5 {{ $rule->is_active ? 'translate-x-5' : '' }}"></div>
                                    </div>
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="editRule({{ $rule->toJson() }})" class="p-2 hover:bg-amber-500/10 text-amber-500 rounded-xl transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('auto-reply.destroy', $rule) }}" method="POST" onsubmit="return confirm('Hapus rule ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-500/10 text-red-500 rounded-xl transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="w-20 h-20 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-indigo-500">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Belum ada Auto Reply</h3>
                            <p class="text-[var(--text-muted)] mb-6">Buat rule pertama untuk membalas pesan secara otomatis.</p>
                            <button @click="openCreateModal()" class="btn btn-primary">Tambah Auto Reply</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Rule Modal -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-cloak>
        <div class="bg-[var(--bg-primary)] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-[var(--border-color)]" @click.away="closeModal()">
            <div class="px-5 py-4 border-b border-[var(--border-color)] flex items-center justify-between">
                <h3 class="text-lg font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent"
                    x-text="editingRule ? 'Edit Auto Reply' : 'Tambah Auto Reply'"></h3>
                <button @click="closeModal()" class="p-1.5 hover:bg-[var(--bg-secondary)] rounded-lg transition-colors text-[var(--text-muted)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="editingRule ? '/auto-reply/' + editingRule.id : '{{ route('auto-reply.store') }}'" method="POST" class="p-5 space-y-4">
                @csrf
                <template x-if="editingRule">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Keyword & Match Type Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Kata Kunci</label>
                        <input type="text" name="keyword" x-model="form.keyword" required
                               class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                               placeholder="contoh: halo">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Tipe Pencocokan</label>
                        <select name="match_type" x-model="form.match_type" required
                                class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                            <option value="contains">Mengandung</option>
                            <option value="exact">Sama Persis</option>
                            <option value="starts_with">Diawali Dengan</option>
                            <option value="regex">Regex</option>
                        </select>
                    </div>
                </div>

                <!-- Reply Type Toggle -->
                <div>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-2">Jenis Balasan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="form.reply_type = 'text'"
                                :class="form.reply_type === 'text' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-[var(--bg-secondary)] border-[var(--border-color)] text-[var(--text-secondary)]'"
                                class="p-2.5 rounded-xl border-2 flex items-center justify-center gap-1.5 text-sm font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            Text
                        </button>
                        <button type="button" @click="form.reply_type = 'template'"
                                :class="form.reply_type === 'template' ? 'bg-purple-500 text-white border-purple-500' : 'bg-[var(--bg-secondary)] border-[var(--border-color)] text-[var(--text-secondary)]'"
                                class="p-2.5 rounded-xl border-2 flex items-center justify-center gap-1.5 text-sm font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Template
                        </button>
                    </div>
                    <input type="hidden" name="reply_type" :value="form.reply_type">
                </div>

                <!-- Reply Value (Text) -->
                <div x-show="form.reply_type === 'text'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Isi Balasan</label>
                    <textarea name="reply_value" x-model="form.reply_value" rows="3"
                              class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all resize-none"
                              placeholder="Ketik pesan balasan..."></textarea>
                </div>

                <!-- Template Selector -->
                <div x-show="form.reply_type === 'template'" x-transition>
                    <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Pilih Template</label>
                    <select name="template_id" x-model="form.template_id"
                            class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
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

                <!-- Device & Priority Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Device</label>
                        <select name="device_id" x-model="form.device_id"
                                class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                            <option value="">Semua</option>
                            @foreach($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-secondary)] mb-1.5">Prioritas</label>
                        <input type="number" name="priority" x-model="form.priority" min="0"
                               class="w-full px-3 py-2 text-sm bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                               placeholder="0">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="closeModal()" class="flex-1 px-4 py-2.5 bg-[var(--bg-secondary)] text-[var(--text-secondary)] text-sm font-bold rounded-xl hover:bg-[var(--bg-tertiary)] transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- Auto Reply logic moved to app.js for CSP compliance --}}
@endpush
@endsection

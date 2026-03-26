@extends('layouts.app')

@section('title', 'Templates')
@section('page-title', 'Message Templates')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="templateManager">
    <!-- Header & Search -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex-1 max-w-2xl">
            <form action="{{ route('templates.index') }}" method="GET" class="relative group">
                <input type="hidden" name="category" value="{{ request('category', 'all') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search templates by name or content..."
                       class="w-full pl-12 pr-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all group-hover:border-[var(--border-color-hover)]">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-muted)] group-hover:text-indigo-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openCreateModal()" class="btn btn-primary shadow-lg shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Template
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Filter -->
        <div class="w-full md:w-64 space-y-2">
            <h3 class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-4 px-3">Categories</h3>
            <a href="{{ route('templates.index', ['search' => request('search')]) }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl transition-all {{ request('category', 'all') === 'all' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/20' : 'hover:bg-[var(--bg-secondary)] text-[var(--text-secondary)]' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    All Templates
                </span>
                <span class="text-xs opacity-60">{{ $templates->count() }}</span>
            </a>

            @foreach($categories as $cat)
            <a href="{{ route('templates.index', ['category' => $cat->category, 'search' => request('search')]) }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl transition-all {{ request('category') === $cat->category ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/20' : 'hover:bg-[var(--bg-secondary)] text-[var(--text-secondary)]' }}">
                <span class="truncate pr-2">{{ $cat->category }}</span>
                <span class="text-xs opacity-60">{{ $cat->count }}</span>
            </a>
            @endforeach
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            @if($templates->count() > 0)
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @foreach($templates as $template)
                <div class="card bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-2xl p-5 hover:border-indigo-500/50 hover:shadow-xl hover:shadow-indigo-500/5 transition-all group flex flex-col h-full">
                    <div class="flex items-start justify-between mb-4">
                        <div class="space-y-1 pr-4 truncate">
                            <h4 class="font-bold text-lg truncate group-hover:text-indigo-500 transition-colors">{{ $template->name }}</h4>
                            <div class="flex items-center gap-2">
                                <span class="text-xs px-2 py-0.5 bg-indigo-500/10 text-indigo-500 rounded-md font-medium">
                                    {{ $template->category }}
                                </span>
                                @if($template->is_system)
                                <span class="text-[10px] px-1.5 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-md font-bold uppercase tracking-tighter">
                                    System
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-1">
                            @if(!$template->is_system)
                            <button @click="editTemplate({{ $template->toJson() }})" class="p-2 hover:bg-amber-500/10 text-amber-500 rounded-xl transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-500/10 text-red-500 rounded-xl transition-colors" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <div class="p-2 text-[var(--text-muted)]" title="System template cannot be deleted">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl p-4 mb-4 relative group/content max-h-48 overflow-y-auto">
                        <p class="text-sm text-[var(--text-secondary)] leading-relaxed whitespace-pre-wrap">{{ $template->content }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-[var(--text-muted)]">
                            Added {{ $template->created_at->diffForHumans() }}
                        </span>
                        <div class="flex items-center gap-2">
                            <button @click="copyToClipboard('{{ addslashes($template->content) }}')" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-lg text-xs font-bold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                                SALIN
                            </button>
                            <a href="{{ route('single-send.index', ['message' => $template->content]) }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 text-indigo-500 hover:bg-indigo-500 hover:text-white rounded-lg text-xs font-bold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                PAKAI
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-20 bg-[var(--bg-secondary)] rounded-3xl border-2 border-dashed border-[var(--border-color)]">
                <div class="w-20 h-20 bg-indigo-500/10 rounded-full flex items-center justify-center mb-6 text-indigo-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">No templates found</h3>
                <p class="text-[var(--text-muted)] text-center max-w-sm">We couldn't find any templates matching your search or category. Try clearing the filters.</p>
                <a href="{{ route('templates.index') }}" class="mt-6 text-indigo-500 font-bold hover:underline">Clear all filters</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-cloak>
        <div class="bg-[var(--bg-primary)] rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden border border-[var(--border-color)]" @click.away="closeModal()">
            <div class="px-8 py-6 border-b border-[var(--border-color)] flex items-center justify-between">
                <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent"
                    x-text="editingTemplate ? 'Update Template' : 'Create New Template'"></h3>
                <button @click="closeModal()" class="p-2 hover:bg-[var(--bg-secondary)] rounded-xl transition-colors text-[var(--text-muted)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="editingTemplate ? '/templates/' + editingTemplate.id : '{{ route('templates.store') }}'" method="POST" class="p-8 space-y-6">
                @csrf
                <template x-if="editingTemplate">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[var(--text-secondary)] mb-2 px-1">Template Name</label>
                        <input type="text" name="name" x-model="form.name" required
                               class="w-full px-5 py-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                               placeholder="e.g. Greeting Message">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[var(--text-secondary)] mb-2 px-1">Business Category</label>
                        <select name="category" x-model="form.category" required
                                class="w-full px-5 py-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                            <option value="Authentication">Authentication (OTP)</option>
                            <option value="Commerce">Commerce / Transaction</option>
                            <option value="Marketing">Marketing / Promotion</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Education">Education</option>
                            <option value="Other">Other Category</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[var(--text-secondary)] mb-2 px-1">Template Content</label>
                        <textarea name="content" x-model="form.content" required rows="5"
                                  class="w-full px-5 py-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all resize-none"
                                  placeholder="Type your message here... Use @{{name}} for dynamic data"></textarea>
                        <p class="mt-2 text-[10px] text-[var(--text-muted)] flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Gunakan double kurung kurawal @{{variable}} untuk variabel dinamis API
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" @click="closeModal()" class="flex-1 px-6 py-4 bg-[var(--bg-secondary)] text-[var(--text-secondary)] font-bold rounded-2xl hover:bg-[var(--bg-tertiary)] transition-all">
                        CANCEL
                    </button>
                    <button type="submit" class="flex-1 px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        SAVE TEMPLATE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- Template logic moved to app.js for CSP compliance --}}
@endpush
@endsection

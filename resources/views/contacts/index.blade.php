@extends('layouts.app')

@section('title', 'Contacts')
@section('page-title', 'Contact Book')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="contactManager">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form action="{{ route('contacts.index') }}" method="GET" class="flex items-center gap-4">
            <div class="relative flex-1 sm:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..." class="form-input pl-10">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
        <div class="flex gap-3">
            <button @click="openAddModal()" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Contact
            </button>
        </div>
    </div>

    <!-- Labels Filter -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('contacts.index') }}" class="badge {{ !request('label') ? 'badge-success' : 'bg-[var(--bg-secondary)]' }} cursor-pointer hover:opacity-80">
            All ({{ $contacts->total() }})
        </a>
        @foreach(['Customer', 'Lead', 'VIP', 'Supplier'] as $label)
        <a href="{{ route('contacts.index', ['label' => $label]) }}"
           class="badge {{ request('label') === $label ? 'badge-success' : 'bg-[var(--bg-secondary)]' }} cursor-pointer hover:opacity-80">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <!-- Contact Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($contacts as $contact)
        <div class="card hover:border-emerald-500/50 transition-colors {{ $contact->is_blocked ? 'opacity-60' : '' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br {{ $contact->is_blocked ? 'from-gray-400 to-gray-600' : 'from-emerald-400 to-emerald-600' }} flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr($contact->display_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="font-semibold truncate">{{ $contact->display_name }}</h4>
                        @if($contact->is_blocked)
                        <span class="badge badge-danger text-xs">Blocked</span>
                        @endif
                    </div>
                    <p class="text-sm text-[var(--text-secondary)]">{{ $contact->phone_number }}</p>
                </div>
                @if($contact->labels && count($contact->labels) > 0)
                <span class="badge badge-{{ $contact->labels[0] === 'VIP' ? 'warning' : ($contact->labels[0] === 'Lead' ? 'info' : 'success') }}">
                    {{ $contact->labels[0] }}
                </span>
                @endif
            </div>

            @if($contact->email || $contact->notes)
            <div class="mt-3 text-sm text-[var(--text-muted)]">
                @if($contact->email)
                <p>{{ $contact->email }}</p>
                @endif
                @if($contact->notes)
                <p class="truncate">{{ $contact->notes }}</p>
                @endif
            </div>
            @endif

            <div class="mt-4 pt-4 border-t border-[var(--border-color)] flex items-center justify-between">
                <span class="text-sm text-[var(--text-muted)]">
                    {{ $contact->messages_count ?? 0 }} messages
                </span>
                <div class="flex gap-1">
                    <button @click="editContact({{ $contact->toJson() }})"
                            class="p-2 hover:bg-[var(--bg-secondary)] rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                    <form action="{{ route('contacts.toggle-block', $contact) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 hover:bg-{{ $contact->is_blocked ? 'emerald' : 'yellow' }}-500/10 text-{{ $contact->is_blocked ? 'emerald' : 'yellow' }}-500 rounded-lg transition-colors" title="{{ $contact->is_blocked ? 'Unblock' : 'Block' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($contact->is_blocked)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                @endif
                            </svg>
                        </button>
                    </form>
                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Delete this contact?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 hover:bg-red-500/10 text-red-500 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-[var(--text-muted)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="text-lg font-medium mb-2">No contacts yet</h3>
            <p class="text-[var(--text-muted)] mb-4">Add your first contact to get started</p>
            <button @click="openAddModal()" class="btn btn-primary">Add Contact</button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($contacts->hasPages())
    <div class="flex justify-center">
        {{ $contacts->links() }}
    </div>
    @endif

    <!-- Add/Edit Contact Modal -->
    <div x-show="showModal" x-transition class="modal-overlay active" @click.self="closeModal()">
        <div class="modal-content" @click.stop>
            <h3 class="text-xl font-semibold mb-6" x-text="editingContact ? 'Edit Contact' : 'Add New Contact'"></h3>
            <form :action="editingContact ? '/contacts/' + editingContact.id : '{{ route('contacts.store') }}'" method="POST">
                @csrf
                <template x-if="editingContact">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" name="phone_number" x-model="form.phone_number" class="form-input" placeholder="+62 xxx-xxxx-xxxx" required>
                    </div>
                    <div>
                        <label class="form-label">Name</label>
                        <input type="text" name="name" x-model="form.name" class="form-input" placeholder="Contact name">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" x-model="form.email" class="form-input" placeholder="email@example.com">
                    </div>
                    <div>
                        <label class="form-label">Labels</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['Customer', 'Lead', 'VIP', 'Supplier'] as $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="labels[]" value="{{ $label }}"
                                       :checked="form.labels && form.labels.includes('{{ $label }}')"
                                       class="rounded border-[var(--border-color)] text-emerald-500 focus:ring-emerald-500">
                                <span class="text-sm">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Notes</label>
                        <textarea name="notes" x-model="form.notes" class="form-input" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="closeModal()" class="btn btn-secondary flex-1">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-1" x-text="editingContact ? 'Update Contact' : 'Save Contact'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- Contact Manager logic moved to app.js for CSP compliance --}}
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Message Logs')
@section('page-title', 'Message Logs')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="messageManager">
    <!-- Header Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form action="{{ route('messages.index') }}" method="GET" class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative flex-1 lg:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by phone or message..." class="form-input pl-10 pr-4">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Filter Toggle -->
            <button type="button" @click="showFilters = !showFilters" class="btn btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="hidden sm:inline">Filters</span>
            </button>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="flex items-center gap-3">
            <!-- Status Tabs -->
            <div class="flex bg-[var(--bg-secondary)] rounded-lg p-1">
                @foreach(['all' => 'All', 'sent' => 'Sent', 'pending' => 'Pending', 'failed' => 'Failed'] as $key => $label)
                <a href="{{ route('messages.index', array_merge(request()->query(), ['status' => $key])) }}"
                   class="px-3 py-1.5 text-sm font-medium rounded-md transition-all {{ request('status', 'all') === $key ? 'bg-[var(--bg-primary)] shadow-sm' : '' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filter Panel -->
    <div x-show="showFilters" x-transition class="card">
        <form action="{{ route('messages.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">

            <div>
                <label class="form-label">Device</label>
                <select name="device_id" class="form-input">
                    <option value="">All Devices</option>
                    @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                        {{ $device->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Direction</label>
                <select name="direction" class="form-input">
                    <option value="">All Directions</option>
                    <option value="outbound" {{ request('direction') === 'outbound' ? 'selected' : '' }}>Outbound</option>
                    <option value="inbound" {{ request('direction') === 'inbound' ? 'selected' : '' }}>Inbound</option>
                </select>
            </div>
            <div>
                <label class="form-label">Message Type</label>
                <select name="type" class="form-input">
                    <option value="">All Types</option>
                    <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Image</option>
                    <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Document</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">Apply</button>
                <a href="{{ route('messages.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-3 gap-4">
        <div class="card py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['sent']) }}</p>
                    <p class="text-sm text-[var(--text-muted)]">Sent</p>
                </div>
            </div>
        </div>
        <div class="card py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['pending']) }}</p>
                    <p class="text-sm text-[var(--text-muted)]">Pending</p>
                </div>
            </div>
        </div>
        <div class="card py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['failed']) }}</p>
                    <p class="text-sm text-[var(--text-muted)]">Failed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Contact</th>
                        <th>Message</th>
                        <th>Device</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                    <tr class="cursor-pointer hover:bg-[var(--bg-secondary)]"
                        @click="viewMessage({{ $message->toJson() }})">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white text-sm font-medium">
                                    {{ strtoupper(substr($message->contact->display_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $message->contact->display_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ $message->contact->phone_number ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="max-w-xs truncate text-sm">{{ $message->content ?? '[Media]' }}</p>
                            @if($message->type !== 'text')
                            <span class="text-xs text-[var(--text-muted)]">{{ ucfirst($message->type) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-[var(--text-secondary)]">{{ $message->device->name ?? '-' }}</span>
                        </td>
                        <td>
                            @php
                                $displayStatus = $message->status === 'delivered' ? 'sent' : $message->status;
                                $badgeClass = in_array($message->status, ['delivered', 'sent']) ? 'info' : ($message->status === 'pending' ? 'warning' : 'danger');
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ ucfirst($displayStatus) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm text-[var(--text-muted)]">{{ $message->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <button @click.stop="viewMessage({{ $message->toJson() }})" class="p-1.5 hover:bg-[var(--bg-secondary)] rounded-lg transition-colors" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-[var(--text-muted)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-[var(--text-muted)]">No messages found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-[var(--border-color)]">
            <p class="text-sm text-[var(--text-muted)]">
                Showing {{ $messages->firstItem() }}-{{ $messages->lastItem() }} of {{ $messages->total() }} messages
            </p>
            {{ $messages->links() }}
        </div>
        @endif
    </div>

    <!-- Message Detail Modal -->
    <div x-show="showMessageDetail"
         x-transition
         class="modal-overlay active"
         @click.self="showMessageDetail = false">
        <div class="modal-content max-w-lg" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold">Message Details</h3>
                <button @click="showMessageDetail = false" class="p-2 hover:bg-[var(--bg-secondary)] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <template x-if="selectedMessage">
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-[var(--bg-secondary)] rounded-xl">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white font-medium">
                            <span x-text="(selectedMessage.contact?.name || selectedMessage.contact?.phone_number || '?').charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <p class="font-semibold" x-text="selectedMessage.contact?.name || selectedMessage.contact?.phone_number || 'Unknown'"></p>
                            <p class="text-sm text-[var(--text-muted)]" x-text="selectedMessage.device?.name || ''"></p>
                        </div>
                        <div class="ml-auto">
                            <span class="badge"
                                  :class="{
                                      'badge-success': selectedMessage.status === 'delivered',
                                      'badge-info': selectedMessage.status === 'sent',
                                      'badge-warning': selectedMessage.status === 'pending',
                                      'badge-danger': selectedMessage.status === 'failed'
                                  }"
                                  x-text="selectedMessage.status ? selectedMessage.status.charAt(0).toUpperCase() + selectedMessage.status.slice(1) : ''">
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Message Content</label>
                        <div class="p-4 bg-[var(--bg-secondary)] rounded-xl">
                            <p class="text-sm whitespace-pre-wrap" x-text="selectedMessage.content || '[Media message]'"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Direction</label>
                            <p class="text-sm" x-text="selectedMessage.direction ? selectedMessage.direction.charAt(0).toUpperCase() + selectedMessage.direction.slice(1) : ''"></p>
                        </div>
                        <div>
                            <label class="form-label">Type</label>
                            <p class="text-sm" x-text="selectedMessage.type ? selectedMessage.type.charAt(0).toUpperCase() + selectedMessage.type.slice(1) : ''"></p>
                        </div>
                        <div>
                            <label class="form-label">Sent At</label>
                            <p class="text-sm" x-text="selectedMessage.sent_at || '-'"></p>
                        </div>
                        <div>
                            <label class="form-label">Message ID</label>
                            <p class="text-sm font-mono" x-text="'MSG-' + selectedMessage.id"></p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button @click="showMessageDetail = false" class="btn btn-primary flex-1">Close</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
{{-- Message Manager logic moved to app.js for CSP compliance --}}
@endpush
@endsection

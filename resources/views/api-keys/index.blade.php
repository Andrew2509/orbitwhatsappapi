@extends('layouts.app')

@section('title', 'API Keys')
@section('page-title', 'API Keys Management')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="apiKeyManager">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-[var(--text-secondary)]">Manage your API keys for authentication</p>
        <button @click="openCreateModal()" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Generate New Key
        </button>
    </div>

    <!-- Warning -->
    <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl flex gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-medium text-amber-500">Keep your API keys secure</p>
            <p class="text-sm text-[var(--text-secondary)]">Never share your API keys or commit them to version control. Use environment variables instead.</p>
        </div>
    </div>

    <!-- Show Newly Generated Key (from session) -->
    @if(session('new_key_generated'))
    <div class="card bg-emerald-500/10 border-emerald-500/20 border-2">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-emerald-500">New API Key Generated!</h4>
                <p class="text-sm text-[var(--text-secondary)]">Copy this key now. You won't be able to see it again.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl font-mono text-sm break-all">
            <span class="flex-1 text-emerald-400">{{ session('new_key_generated') }}</span>
            <button @click="copyToClipboard('{{ session('new_key_generated') }}')" class="btn btn-secondary py-1 px-3 text-xs">Copy</button>
        </div>
    </div>
    @endif

    <!-- API Keys List -->
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Environment</th>
                        <th>Key (Masked)</th>
                        <th>Last Used</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apiKeys as $apiKey)
                    <tr>
                        <td class="font-medium">{{ $apiKey->name }}</td>
                        <td>
                            <span class="badge badge-{{ $apiKey->environment === 'live' ? 'success' : 'warning' }}">
                                {{ strtoupper($apiKey->environment) }}
                            </span>
                        </td>
                        <td>
                            <code class="text-xs font-mono bg-[var(--bg-secondary)] px-2 py-1 rounded">
                                {{ $apiKey->masked_key }}
                            </code>
                        </td>
                        <td class="text-sm text-[var(--text-secondary)]">
                            {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $apiKey->is_active ? 'success' : 'danger' }}">
                                {{ $apiKey->is_active ? 'Active' : 'Revoked' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                @if($apiKey->is_active)
                                <form action="{{ route('api-keys.regenerate', $apiKey) }}" method="POST" onsubmit="return confirm('Regenerate key? Old key will stop working.')">
                                    @csrf
                                    <button type="submit" class="p-1.5 hover:bg-amber-500/10 text-amber-500 rounded-lg" title="Regenerate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('api-keys.revoke', $apiKey) }}" method="POST" onsubmit="return confirm('Revoke this key?')">
                                    @csrf
                                    <button type="submit" class="p-1.5 hover:bg-red-500/10 text-red-500 rounded-lg" title="Revoke">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('api-keys.destroy', $apiKey) }}" method="POST" onsubmit="return confirm('Delete this API key Permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 hover:bg-red-500/10 text-red-500 rounded-lg" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-[var(--text-muted)]">No API keys found. Generate a new one to start using the API.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Key Modal -->
    <div x-show="showCreateModal" x-transition class="modal-overlay active" @click.self="showCreateModal = false">
        <div class="modal-content max-w-md" @click.stop>
            <h3 class="text-xl font-semibold mb-6">Generate New API Key</h3>
            <form action="{{ route('api-keys.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Key Name</label>
                        <input type="text" name="name" class="form-input" placeholder="e.g., Production App" required>
                    </div>
                    <div>
                        <label class="form-label">Environment</label>
                        <select name="environment" class="form-input" required>
                            <option value="live">Live (Production)</option>
                            <option value="test">Test (Sandbox)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showCreateModal = false" class="btn btn-secondary flex-1">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-1">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- API Key logic moved to app.js for CSP compliance --}}
@endpush
@endsection

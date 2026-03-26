@extends('layouts.app')

@section('title', $application->name)
@section('page-title', $application->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    <!-- Back Link -->
    <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-2 text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to My Apps
    </a>

    <!-- Success Message -->
    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-emerald-500">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Header Card -->
    <div class="card">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($application->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $application->name }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="badge {{ $application->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $application->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-sm text-[var(--text-muted)]">
                            Created {{ $application->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('applications.edit', $application) }}" class="btn btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        @if($application->description)
        <p class="text-[var(--text-secondary)] mt-4 pt-4 border-t border-[var(--border-color)]">
            {{ $application->description }}
        </p>
        @endif
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card text-center">
            <p class="text-3xl font-bold text-indigo-500">{{ number_format($application->messages_count) }}</p>
            <p class="text-sm text-[var(--text-muted)]">Messages Sent</p>
        </div>
        <div class="card text-center">
            <p class="text-3xl font-bold text-purple-500">{{ $application->devices->count() }}</p>
            <p class="text-sm text-[var(--text-muted)]">Devices</p>
        </div>
        <div class="card text-center">
            <p class="text-sm font-medium text-[var(--text-secondary)]">{{ $application->last_used_at ? $application->last_used_at->format('M d, Y H:i') : 'Never' }}</p>
            <p class="text-sm text-[var(--text-muted)]">Last Used</p>
        </div>
    </div>

    <!-- Credentials -->
    <div class="card" x-data="{ showAppKey: false }">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Credentials
        </h3>

        <div class="space-y-4">
            <!-- App Key (Identifier) -->
            <div class="p-4 bg-[var(--bg-secondary)] rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-[var(--text-secondary)]">App Key</label>
                    <span class="text-xs text-[var(--text-muted)]">Application Identifier</span>
                </div>
                <div class="flex items-center gap-2">
                    <code class="flex-1 p-3 bg-[var(--bg-primary)] rounded-lg font-mono text-sm break-all"
                          x-text="showAppKey ? '{{ $application->app_key }}' : '{{ $application->masked_app_key }}'"></code>
                    <button @click="showAppKey = !showAppKey" class="p-2 hover:bg-[var(--bg-primary)] rounded-lg transition-colors" title="Toggle visibility">
                        <svg x-show="!showAppKey" class="w-5 h-5 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showAppKey" class="w-5 h-5 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                    <button onclick="navigator.clipboard.writeText('{{ $application->app_key }}')" class="p-2 hover:bg-[var(--bg-primary)] rounded-lg transition-colors" title="Copy">
                        <svg class="w-5 h-5 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- API Key (Authentication) -->
            <div class="p-4 bg-[var(--bg-secondary)] rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-[var(--text-secondary)]">API Key</label>
                    <span class="text-xs text-[var(--text-muted)]">Authentication Key</span>
                </div>
                @if($application->apiKey)
                <div class="flex items-center justify-between">
                    <div>
                        <code class="text-sm font-mono text-[var(--text-secondary)]">{{ $application->apiKey->masked_key }}</code>
                        <span class="text-xs text-[var(--text-muted)] ml-2">({{ $application->apiKey->name }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge {{ $application->apiKey->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $application->apiKey->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <a href="{{ route('api-keys.index') }}" class="text-sm text-indigo-500 hover:text-indigo-400">
                            Manage →
                        </a>
                    </div>
                </div>
                @else
                <div class="p-3 bg-amber-500/10 rounded-lg">
                    <p class="text-sm text-amber-500">No API key linked. <a href="{{ route('applications.edit', $application) }}" class="underline">Edit</a> to select one.</p>
                </div>
                @endif
            </div>

            <!-- Webhook URL -->
            @if($application->webhook_url)
            <div class="p-4 bg-[var(--bg-secondary)] rounded-lg">
                <label class="text-sm font-medium text-[var(--text-secondary)] mb-2 block">Webhook URL</label>
                <code class="block p-3 bg-[var(--bg-primary)] rounded-lg font-mono text-sm break-all">
                    {{ $application->webhook_url }}
                </code>
            </div>
            @endif
        </div>
    </div>

    <!-- Associated Devices -->
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Associated Devices
        </h3>

        @if($application->devices->count() > 0)
        <div class="space-y-2">
            @foreach($application->devices as $device)
            <div class="flex items-center justify-between p-3 bg-[var(--bg-secondary)] rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($device->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $device->name }}</p>
                        <p class="text-sm text-[var(--text-muted)]">{{ $device->phone_number ?? 'Not connected' }}</p>
                    </div>
                </div>
                <span class="badge {{ $device->status === 'connected' ? 'badge-success' : 'badge-danger' }}">
                    {{ ucfirst($device->status) }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-[var(--text-muted)] text-center py-6">No devices associated with this application.</p>
        @endif
    </div>

    <!-- Danger Zone -->
    <div class="card border-red-500/30">
        <h3 class="text-lg font-semibold mb-4 text-red-500 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Danger Zone
        </h3>
        <p class="text-sm text-[var(--text-secondary)] mb-4">
            Deleting this application will remove the configuration. The linked API key will not be deleted.
        </p>
        <form action="{{ route('applications.destroy', $application) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this application?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Application
            </button>
        </form>
    </div>
</div>
@endsection

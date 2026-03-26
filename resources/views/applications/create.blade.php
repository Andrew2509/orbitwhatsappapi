@extends('layouts.app')

@section('title', 'Create New App')
@section('page-title', 'Create New App')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    <!-- Back Link -->
    <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-2 text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to My Apps
    </a>

    <div class="card">
        <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Application Details
        </h3>

        <form action="{{ route('applications.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="form-label">Application Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" 
                       placeholder="My E-Commerce System" required>
                @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3" 
                          placeholder="Brief description of what this app does...">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- API Key Selection -->
            <div>
                <label class="form-label">API Key <span class="text-red-500">*</span></label>
                @if($apiKeys->count() > 0)
                <select name="api_key_id" class="form-input" required>
                    <option value="">Select an API Key</option>
                    @foreach($apiKeys as $apiKey)
                    <option value="{{ $apiKey->id }}" {{ old('api_key_id') == $apiKey->id ? 'selected' : '' }}>
                        {{ $apiKey->name }} ({{ $apiKey->masked_key }})
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Select an API key to authenticate requests from this application
                </p>
                @else
                <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                    <p class="text-sm text-amber-500">
                        No API keys available. <a href="{{ route('api-keys.index') }}" class="underline font-medium">Create an API key</a> first.
                    </p>
                </div>
                @endif
                @error('api_key_id')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Webhook URL -->
            <div>
                <label class="form-label">Webhook URL</label>
                <input type="url" name="webhook_url" value="{{ old('webhook_url') }}" class="form-input" 
                       placeholder="https://your-server.com/webhook">
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Receive message status updates at this URL
                </p>
                @error('webhook_url')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Associated Devices -->
            @if($devices->count() > 0)
            <div>
                <label class="form-label">Associated Devices</label>
                <p class="text-xs text-[var(--text-muted)] mb-3">
                    Select which devices this app can use to send messages
                </p>
                <div class="space-y-2 max-h-48 overflow-y-auto p-3 bg-[var(--bg-secondary)] rounded-lg">
                    @foreach($devices as $device)
                    <label class="flex items-center gap-3 p-2 rounded hover:bg-[var(--bg-primary)] cursor-pointer transition-colors">
                        <input type="checkbox" name="devices[]" value="{{ $device->id }}" 
                               class="w-4 h-4 rounded border-[var(--border-color)] text-indigo-500 focus:ring-indigo-500"
                               {{ in_array($device->id, old('devices', [])) ? 'checked' : '' }}>
                        <div class="flex items-center gap-2 flex-1">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($device->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ $device->name }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $device->phone_number ?? 'Not connected' }}</p>
                            </div>
                        </div>
                        <span class="badge {{ $device->status === 'connected' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($device->status) }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Submit -->
            <div class="flex items-center gap-3 pt-4 border-t border-[var(--border-color)]">
                @if($apiKeys->count() > 0)
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Application
                </button>
                @endif
                <a href="{{ route('applications.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

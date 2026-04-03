@extends('layouts.app')

@section('title', 'Devices')
@section('page-title', 'Device Manager')

@section('content')
<div class="space-y-6 animate-fade-in"
     x-data="deviceManager"
     x-init="init()">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[var(--text-secondary)]">Manage your WhatsApp devices and connections</p>
            <p class="text-sm mt-1" :class="serviceOnline ? 'text-emerald-500' : 'text-red-500'">
                <span class="inline-block w-2 h-2 rounded-full mr-1" :class="serviceOnline ? 'bg-emerald-500' : 'bg-red-500'"></span>
                WhatsApp Service: <span x-text="serviceOnline ? 'Online' : 'Offline'"></span>
            </p>
        </div>
        <button @click="openAddModal()" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Device
        </button>
    </div>

    <!-- Device Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
        @forelse($devices as $device)
        <div class="card group relative overflow-hidden">
            <!-- Status Indicator Bar -->
            <div class="absolute top-0 left-0 right-0 h-1 {{ $device->status === 'connected' ? 'bg-emerald-500' : ($device->status === 'waiting_qr' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>

            <div class="flex items-start gap-4 pt-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-500/20">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-lg">{{ $device->name }}</h3>
                        <span class="badge badge-{{ $device->status === 'connected' ? 'success' : ($device->status === 'waiting_qr' ? 'warning' : 'danger') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $device->status === 'connected' ? 'bg-emerald-500' : ($device->status === 'waiting_qr' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                        </span>
                    </div>
                    <p class="text-[var(--text-secondary)] mt-1">{{ $device->phone_number ?? 'Not connected' }}</p>
                    <div class="mt-2 flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-[var(--bg-secondary)] border border-[var(--border-color)] w-fit">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">ID:</span>
                        <span class="text-[11px] font-mono font-medium text-emerald-500">{{ $device->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t border-[var(--border-color)]">
                <div>
                    <p class="text-2xl font-bold">{{ number_format($device->messages_sent) }}</p>
                    <p class="text-sm text-[var(--text-muted)]">Total Messages</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-[var(--text-secondary)]">{{ $device->last_connected_at ? $device->last_connected_at->diffForHumans() : 'Never' }}</p>
                    <p class="text-sm text-[var(--text-muted)]">Last Active</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 mt-6 pt-4 border-t border-[var(--border-color)]">
                @if($device->status === 'connected')
                <form action="{{ route('devices.logout', $device) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-danger w-full text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
                @else
                <button @click="scanDevice({{ $device->id }})" class="btn btn-primary flex-1 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Connect
                </button>
                @endif
                <form action="{{ route('devices.destroy', $device) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this device?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <!-- No devices yet -->
        @endforelse

        <!-- Add Device Card -->
        <div @click="openAddModal()"
             class="card border-2 border-dashed border-[var(--border-color)] hover:border-emerald-500 cursor-pointer transition-colors group flex flex-col items-center justify-center min-h-[280px]">
            <div class="w-16 h-16 rounded-2xl bg-[var(--bg-secondary)] group-hover:bg-emerald-500/10 flex items-center justify-center transition-colors">
                <svg class="w-8 h-8 text-[var(--text-muted)] group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <p class="mt-4 font-medium text-[var(--text-secondary)] group-hover:text-emerald-500 transition-colors">Add New Device</p>
            <p class="mt-1 text-sm text-[var(--text-muted)]">Connect a WhatsApp number</p>
        </div>
    </div>

    <!-- Add Device Modal -->
    <div x-show="showAddModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-overlay active"
         @click.self="closeModal()">
        <div class="modal-content max-w-md" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold" 
                    x-text="step === 'name' ? 'Add Device' : 
                          (step === 'method' ? 'Choose Connection Method' : 
                          (step === 'qr' ? 'Scan QR Code' : 
                          (step === 'pairing' ? 'Link with Phone Number' : 'Connecting...')))">
                </h3>
                <button @click="closeModal()" class="p-2 hover:bg-[var(--bg-secondary)] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Step 1: Device Name -->
            <div x-show="step === 'name'">
                <form @submit.prevent="createDevice()">
                    <div class="mb-6">
                        <label class="form-label">Device Name</label>
                        <input type="text" x-model="deviceName" class="form-input" placeholder="e.g., Customer Service" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-full" :disabled="loading">
                        <span x-show="!loading">Continue</span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Step 2: Choose Method -->
            <div x-show="step === 'method'" class="space-y-4">
                <p class="text-[var(--text-secondary)] mb-4">Select how you want to connect your device:</p>
                
                <button @click="chooseMethod('qr')" class="w-full flex items-center gap-4 p-4 rounded-xl border border-[var(--border-color)] hover:border-emerald-500 hover:bg-emerald-500/5 transition-all text-left group">
                    <div class="w-12 h-12 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Scan QR Code</p>
                        <p class="text-sm text-[var(--text-muted)]">Scan a QR code with your phone</p>
                    </div>
                </button>

                <button @click="chooseMethod('pairing')" class="w-full flex items-center gap-4 p-4 rounded-xl border border-[var(--border-color)] hover:border-emerald-500 hover:bg-emerald-500/5 transition-all text-left group">
                    <div class="w-12 h-12 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Link with Phone Number</p>
                        <p class="text-sm text-[var(--text-muted)]">Connect using an 8-character code</p>
                    </div>
                </button>
            </div>
            <!-- Step 3: QR Code -->
            <div x-show="step === 'qr'">
                <!-- QR Code Display -->
                <div class="bg-white p-6 rounded-xl flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-64 h-64 mx-auto bg-gray-100 rounded-lg flex items-center justify-center mb-4 overflow-hidden">
                            <template x-if="qrCode">
                                <img :src="qrCode" class="w-full h-full object-contain" alt="QR Code">
                            </template>
                            <template x-if="!qrCode && !qrError">
                                <div class="flex flex-col items-center gap-3 p-8">
                                    <div class="relative">
                                        <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-medium text-gray-700">Generating QR Code...</p>
                                        <p class="text-xs text-gray-400 mt-1">This may take up to 10 seconds</p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="qrError">
                                <div class="flex flex-col items-center gap-2 text-red-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <p class="text-sm" x-text="qrError"></p>
                                    <button @click="fetchQR()" class="btn btn-secondary btn-sm mt-2">Retry</button>
                                </div>
                            </template>
                        </div>
                        <p class="text-gray-600 text-sm">Scan this QR code with WhatsApp</p>
                        <p class="text-gray-400 text-xs mt-1">QR code refreshes automatically</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-4 p-3 rounded-lg" :class="connected ? 'bg-emerald-500/10 text-emerald-600' : 'bg-yellow-500/10 text-yellow-600'">
                    <div class="flex items-center gap-2">
                        <template x-if="!connected">
                            <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                            </svg>
                        </template>
                        <template x-if="connected">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-text="connected ? 'Connected! Redirecting...' : 'Waiting for scan...'"></span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-6 p-4 bg-[var(--bg-secondary)] rounded-xl">
                    <p class="font-medium mb-2">How to connect:</p>
                    <ol class="text-sm text-[var(--text-secondary)] space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            Open WhatsApp on your phone
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            Go to Settings → Linked Devices
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            Tap "Link a Device" and scan this QR
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Step 3: Pairing Code -->
            <div x-show="step === 'pairing'">
                <div x-show="!pairingCode">
                    <form @submit.prevent="getPairingCode()">
                        <div class="mb-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" x-model="phone" class="form-input" placeholder="e.g., 628123456789" required>
                            <p class="text-xs text-[var(--text-muted)] mt-1">Enter with country code (e.g., 62...)</p>
                        </div>
                        <button type="submit" class="btn btn-primary w-full" :disabled="loading">
                            <span x-show="!loading">Get Code</span>
                            <span x-show="loading" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Requesting...
                            </span>
                        </button>
                    </form>
                </div>

                <template x-if="pairingCode">
                    <div class="text-center">
                        <div class="mb-6">
                            <p class="text-[var(--text-secondary)] mb-4">Enter this code on your phone:</p>
                            <div class="flex items-center justify-center flex-wrap gap-2">
                                <template x-for="(char, index) in (pairingCode || '').split('')">
                                    <div x-show="char !== '-'" 
                                         class="w-10 h-14 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-xl flex items-center justify-center text-2xl font-bold text-emerald-500 shadow-sm" 
                                         x-text="char">
                                    </div>
                                    <div x-show="char === '-'" class="flex items-center justify-center text-2xl font-bold text-[var(--text-muted)] mx-1">
                                        -
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-4 p-3 rounded-lg" :class="connected ? 'bg-emerald-500/10 text-emerald-600' : 'bg-yellow-500/10 text-yellow-600'">
                            <div class="flex items-center justify-center gap-2">
                                <template x-if="!connected">
                                    <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                </template>
                                <template x-if="connected">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                                <span x-text="connected ? 'Connected! Redirecting...' : 'Waiting for connection...'"></span>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="mt-6 p-4 bg-[var(--bg-secondary)] rounded-xl text-left">
                            <p class="font-medium mb-2">How to connect:</p>
                            <ol class="text-sm text-[var(--text-secondary)] space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                                    Open WhatsApp → Settings → Linked Devices
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                                    Tap "Link a Device"
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                                    Tap "Link with phone number instead" at the bottom
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
                                    Enter the 8-character code shown above
                                </li>
                            </ol>
                        </div>
                        
                        <button @click="pairingCode = null" class="btn btn-secondary w-full mt-4">
                            Try another number
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Device Manager logic moved to app.js for CSP compliance --}}
@endpush
@endsection

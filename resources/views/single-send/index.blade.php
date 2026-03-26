@extends('layouts.app')

@section('title', 'Single Send')
@section('page-title', 'Single Send - Manual Message Testing')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="singleSendManager">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-[var(--text-secondary)]">Send a single message manually for testing or quick communication</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-emerald-500">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-red-500">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Column -->
        <div class="card">
            <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Compose Message
            </h3>

            <form action="{{ route('single-send.send') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Device Selector -->
                <div>
                    <label class="form-label">Select Device <span class="text-red-500">*</span></label>
                    @if($devices->count() > 0)
                    <select name="device_id" x-model="form.device_id" class="form-input" required>
                        <option value="">Choose connected device...</option>
                        @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->phone_number ?? 'Unknown' }})</option>
                        @endforeach
                    </select>
                    @else
                    <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-lg text-sm text-amber-500">
                        No connected devices. <a href="{{ route('devices.index') }}" class="underline">Connect a device</a> first.
                    </div>
                    @endif
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="form-label">Recipient Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" x-model="form.phone" class="form-input"
                           placeholder="628xxxxxxxxxx" required
                           @input="formatPhone()">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Format: 628xxxxxxxxxx (without + or spaces)</p>
                </div>

                <!-- Message Type -->
                <div>
                    <label class="form-label">Message Type</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-secondary)] transition-colors"
                               :class="{ 'border-emerald-500 bg-emerald-500/10': form.type === 'text' }">
                            <input type="radio" name="type" value="text" x-model="form.type" class="sr-only">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <span class="text-sm font-medium">Text</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-secondary)] transition-colors"
                               :class="{ 'border-emerald-500 bg-emerald-500/10': form.type === 'image' }">
                            <input type="radio" name="type" value="image" x-model="form.type" class="sr-only">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Image</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-secondary)] transition-colors"
                               :class="{ 'border-emerald-500 bg-emerald-500/10': form.type === 'document' }">
                            <input type="radio" name="type" value="document" x-model="form.type" class="sr-only">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Document</span>
                        </label>
                    </div>
                </div>

                <!-- Media Upload (conditional) -->
                <div x-show="form.type !== 'text'" x-transition>
                    <label class="form-label">Upload File</label>
                    <div class="border-2 border-dashed border-[var(--border-color)] rounded-lg p-4 text-center hover:border-emerald-500/50 transition-colors">
                        <input type="file" name="media_file"
                               :accept="form.type === 'image' ? 'image/*' : '.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip'"
                               @change="handleFileSelect($event)"
                               class="hidden" id="mediaFileInput">
                        <label for="mediaFileInput" class="cursor-pointer">
                            <svg class="w-8 h-8 mx-auto text-[var(--text-muted)] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-[var(--text-muted)]">
                                <span class="text-emerald-500 font-medium">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-[var(--text-muted)] mt-1" x-text="form.type === 'image' ? 'PNG, JPG, GIF up to 16MB' : 'PDF, DOC, XLS, ZIP up to 16MB'"></p>
                        </label>
                    </div>
                    <!-- File Preview -->
                    <div x-show="form.fileName" class="mt-2 p-2 bg-[var(--bg-secondary)] rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm flex-1 truncate" x-text="form.fileName"></span>
                        <button type="button" @click="clearFile()" class="text-red-500 hover:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- OR divider -->
                    <div class="flex items-center gap-3 my-3">
                        <div class="flex-1 h-px bg-[var(--border-color)]"></div>
                        <span class="text-xs text-[var(--text-muted)]">OR</span>
                        <div class="flex-1 h-px bg-[var(--border-color)]"></div>
                    </div>

                    <!-- Media URL -->
                    <label class="form-label">Media URL</label>
                    <input type="url" name="media_url" x-model="form.media_url" class="form-input"
                           placeholder="https://example.com/image.jpg">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Direct link to the media file</p>
                </div>

                <!-- Message Content -->
                <div>
                    <label class="form-label">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" x-model="form.message" class="form-input" rows="5"
                              placeholder="Type your message here..." required></textarea>
                    <p class="text-xs text-[var(--text-muted)] mt-1">
                        <span x-text="form.message.length"></span> / 4096 characters
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-full py-3" :disabled="!canSend">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Send Message
                </button>
            </form>
        </div>

        <!-- Preview Column - Phone Mockup -->
        <div class="card flex flex-col items-center">
            <h3 class="text-lg font-semibold mb-6 flex items-center gap-2 self-start">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Live Preview
            </h3>

            <!-- Phone Frame -->
            <div class="relative w-72">
                <!-- Phone Body -->
                <div class="bg-gray-900 rounded-[2.5rem] p-3 shadow-2xl">
                    <!-- Screen -->
                    <div class="bg-[#0b141a] rounded-[2rem] overflow-hidden">
                        <!-- Status Bar -->
                        <div class="h-7 bg-[#1f2c34] flex items-center justify-between px-4">
                            <span class="text-[10px] text-white/60">9:41</span>
                            <div class="flex items-center gap-1">
                                <div class="w-4 h-2 border border-white/60 rounded-sm">
                                    <div class="w-3/4 h-full bg-white/60"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Header -->
                        <div class="h-14 bg-[#1f2c34] flex items-center gap-3 px-4 border-b border-[#2a3942]">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-sm font-bold">
                                <span x-text="form.phone ? form.phone.charAt(0) : '?'">?</span>
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium" x-text="form.phone || 'Recipient'">Recipient</p>
                                <p class="text-[10px] text-gray-400">online</p>
                            </div>
                        </div>

                        <!-- Chat Body -->
                        <div class="h-80 bg-[#0b141a] p-4 overflow-y-auto" :style="'background-image: url(\'data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.02\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\');'">

                            <!-- Empty State -->
                            <div x-show="!form.message" class="h-full flex items-center justify-center">
                                <p class="text-gray-500 text-sm text-center">Start typing to see<br>your message preview</p>
                            </div>

                            <!-- Message Bubble -->
                            <div x-show="form.message" class="flex justify-end" x-transition>
                                <div class="max-w-[85%]">
                                    <!-- Media Preview -->
                                    <div x-show="form.type === 'image' && (form.imagePreview || form.media_url)" class="mb-1">
                                        <div class="w-48 h-32 bg-gray-700 rounded-lg overflow-hidden">
                                            <img x-show="form.imagePreview" :src="form.imagePreview" class="w-full h-full object-cover" alt="Preview">
                                            <div x-show="!form.imagePreview && form.media_url" class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="form.type === 'document' && (form.fileName || form.media_url)" class="mb-1">
                                        <div class="bg-[#025144] rounded-lg p-3 flex items-center gap-3">
                                            <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-white text-xs truncate max-w-[120px]" x-text="form.fileName || 'Document'"></span>
                                        </div>
                                    </div>
                                    <!-- Text Bubble -->
                                    <div class="bg-[#005c4b] rounded-lg rounded-tr-none px-3 py-2 text-white text-sm whitespace-pre-wrap break-words" x-text="form.message"></div>
                                    <div class="text-[10px] text-gray-400 text-right mt-1">
                                        <span>Now</span>
                                        <svg class="w-3 h-3 inline ml-1 text-blue-400" fill="currentColor" viewBox="0 0 16 15">
                                            <path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88a.32.32 0 01-.484.032l-1.837-1.83a.366.366 0 00-.52.006l-.423.433a.364.364 0 00.006.514l2.641 2.634a.429.429 0 00.618-.002l6.036-7.81a.366.366 0 00-.063-.51zm-5.21 7.14l-.008-.007.008.007z"/>
                                            <path d="M12.85 3.316l-.478-.372a.365.365 0 00-.51.063L6.501 9.88a.32.32 0 01-.484.032L4.18 8.082a.366.366 0 00-.52.006l-.423.433a.364.364 0 00.006.514l2.641 2.634a.429.429 0 00.618-.002l6.036-7.81a.366.366 0 00-.063-.51z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Input Bar -->
                        <div class="h-14 bg-[#1f2c34] flex items-center gap-2 px-3">
                            <div class="flex-1 bg-[#2a3942] rounded-full px-4 py-2">
                                <span class="text-gray-400 text-sm">Type a message</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Home Indicator -->
                <div class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1/3 h-1 bg-white/30 rounded-full"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Single Send Manager logic moved to app.js for CSP compliance --}}
@endpush
@endsection

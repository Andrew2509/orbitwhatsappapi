@extends('layouts.app')

@section('title', 'Security')
@section('page-title', 'Security Settings')

@section('content')
<div class="max-w-4xl space-y-6 animate-fade-in">
    
    <!-- Profile Header Card -->
    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        
        <div class="relative flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold">Security Settings</h2>
                <p class="text-white/80">Manage your account security and sessions</p>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] overflow-hidden sticky top-6">
                <nav class="p-2">
                    <a href="{{ route('settings.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-secondary)] hover:bg-[var(--bg-secondary)] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('settings.security') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-500/10 text-amber-500 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Security
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-secondary)] hover:bg-[var(--bg-secondary)] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Notifications
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-secondary)] hover:bg-[var(--bg-secondary)] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Preferences
                    </a>
                </nav>
                
                <div class="border-t border-[var(--border-color)] p-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-500/10 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="md:col-span-2 space-y-6">
            @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 px-4 py-3 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <!-- Two-Factor Authentication -->
            <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Two-Factor Authentication</h3>
                            <p class="text-sm text-[var(--text-muted)]">Add extra security with 2FA</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-14 h-7 bg-[var(--bg-tertiary)] peer-checked:bg-emerald-500 rounded-full peer after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-[22px] after:w-[22px] after:transition-all peer-checked:after:translate-x-7 shadow-inner"></div>
                    </label>
                </div>
                <div class="mt-4 p-4 bg-purple-500/5 border border-purple-500/20 rounded-xl">
                    <p class="text-sm text-[var(--text-secondary)]">
                        <strong>Not enabled.</strong> Enable 2FA to add an extra layer of security. You'll need to enter a code from your authenticator app when signing in.
                    </p>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Active Sessions</h3>
                        <p class="text-sm text-[var(--text-muted)]">{{ $sessions->count() }} session aktif</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    @forelse($sessions as $session)
                    <div class="flex items-center justify-between p-4 {{ $session->is_current ? 'bg-emerald-500/5 border border-emerald-500/20' : 'bg-[var(--bg-secondary)]' }} rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl {{ $session->is_current ? 'bg-emerald-500/10 text-emerald-500' : ($session->is_mobile ? 'bg-blue-500/10 text-blue-500' : 'bg-purple-500/10 text-purple-500') }} flex items-center justify-center">
                                @if($session->is_mobile)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold">{{ $session->platform }} • {{ $session->browser }}</p>
                                <p class="text-sm text-[var(--text-muted)]">{{ $session->ip_address }} • {{ $session->is_current ? 'Active now' : $session->last_active }}</p>
                            </div>
                        </div>
                        @if($session->is_current)
                        <span class="px-3 py-1.5 bg-emerald-500/10 text-emerald-500 rounded-full text-xs font-bold">Current</span>
                        @else
                        <form action="{{ route('settings.revoke-session', $session->id) }}" method="POST" onsubmit="return confirm('Logout session ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-red-500 border border-red-500/30 rounded-xl hover:bg-red-500/10 transition-colors text-sm font-medium">
                                Revoke
                            </button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <p class="text-center text-[var(--text-muted)] py-4">Tidak ada session aktif</p>
                    @endforelse
                </div>
                
                @if($sessions->where('is_current', false)->count() > 0)
                <div class="mt-6 pt-6 border-t border-[var(--border-color)]">
                    <form action="{{ route('settings.logout-others') }}" method="POST" onsubmit="return confirm('Logout dari semua session lain?')">
                        @csrf
                        <button type="submit" class="w-full py-3 text-red-500 border border-red-500/30 rounded-xl hover:bg-red-500/10 transition-colors font-bold">
                            Logout All Other Sessions
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- API Keys -->
            <div class="bg-[var(--bg-primary)] rounded-2xl border border-[var(--border-color)] p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">API Keys</h3>
                            <p class="text-sm text-[var(--text-muted)]">Manage your API access tokens</p>
                        </div>
                    </div>
                    <a href="{{ route('api-keys.index') }}" class="px-4 py-2 bg-indigo-500/10 text-indigo-500 rounded-xl hover:bg-indigo-500/20 transition-colors text-sm font-medium">
                        Manage Keys
                    </a>
                </div>
                <p class="text-sm text-[var(--text-muted)]">
                    API keys allow external applications to access your WhatsApp API. Keep them secure and never share them publicly.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

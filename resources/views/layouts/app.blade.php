<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="mainLayout" :class="{ 'dark': darkMode }">
<head>
    <script nonce="{{ config('app.csp_nonce') }}" data-cookieconsent="ignore">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            'ad_storage': 'denied',
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'analytics_storage': 'denied',
            'functionality_storage': 'denied',
            'personalization_storage': 'denied',
            'security_storage': 'granted',
            'wait_for_update': 500
        });
        gtag('set', 'ads_data_redaction', true);
        gtag('set', 'developer_id.dMWZhNz', true);
    </script>
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-blockingmode="auto" data-cbid="e34b0360-5226-4315-9b8e-52f41f19761b" type="text/javascript" nonce="{{ config('app.csp_nonce') }}"></script>
    <!-- Google tag (gtag.js) -->
    <script nonce="{{ config('app.csp_nonce') }}" src="https://www.googletagmanager.com/gtag/js?id=G-WFGB15WM4T"></script>
    <script nonce="{{ config('app.csp_nonce') }}" data-cookieconsent="ignore">
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-WFGB15WM4T');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="whatsapp-service-url" content="{{ config('services.whatsapp.url') }}">

    <title>@yield('title', 'Dashboard') - {{ get_setting('site_name', config('app.name', 'WhatsApp API')) }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Chart.js -->
    <script nonce="{{ config('app.csp_nonce') }}" src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-9nhczxUqK87bcKHh20fSQcTGD4qq5GhayNYSYWqwBkINBhOfQLg/P5HG5lF1urn4" crossorigin="anonymous"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[var(--bg-primary)] text-[var(--text-primary)]">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">

            <!-- Top Header -->
            <header class="sticky top-0 z-20 bg-[var(--bg-primary)] border-b border-[var(--border-color)] px-4 lg:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-[var(--bg-secondary)] transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-[var(--bg-secondary)] transition-colors" title="Toggle Dark Mode">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </button>

                        <!-- Notifications -->
                        <button class="p-2 rounded-lg hover:bg-[var(--bg-secondary)] transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-lg hover:bg-[var(--bg-secondary)] transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-lg shadow-lg py-1 z-50">
                                <a href="{{ route('settings.profile') }}" class="block px-4 py-2 text-sm hover:bg-[var(--bg-secondary)]">Profile</a>
                                <a href="{{ route('settings.security') }}" class="block px-4 py-2 text-sm hover:bg-[var(--bg-secondary)]">Security</a>
                                <hr class="my-1 border-[var(--border-color)]">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-[var(--bg-secondary)]">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-[var(--border-color)] px-4 lg:px-6 py-4">
                <div class="flex items-center justify-between text-sm text-[var(--text-muted)]">
                    <span>{{ get_setting('footer_text', '&copy; ' . date('Y') . ' WhatsApp API Connect. All rights reserved.') }}</span>
                    <span>v1.0.0</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150"></div>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="mainLayout" :class="{ 'dark': darkMode }">
<head>
    <script nonce="{{ $csp_nonce }}">
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
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-blockingmode="auto" data-cbid="e34b0360-5226-4315-9b8e-52f41f19761b" type="text/javascript" nonce="{{ $csp_nonce }}"></script>
    <!-- Google tag (gtag.js) -->
    <script nonce="{{ $csp_nonce }}" src="https://www.googletagmanager.com/gtag/js?id=G-WFGB15WM4T"></script>
    <script nonce="{{ $csp_nonce }}">
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-WFGB15WM4T');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ get_setting('site_name', config('app.name', 'WhatsApp API')) }} Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Chart.js -->
    <script nonce="{{ $csp_nonce }}" src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-9nhczxUqK87bcKHh20fSQcTGD4qq5GhayNYSYWqwBkINBhOfQLg/P5HG5lF1urn4" crossorigin="anonymous"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimal custom CSS - Navigation items */
        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
            color: #94a3b8;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .admin-nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .admin-nav-item.active {
            background: linear-gradient(to right, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #34d399;
            font-weight: 500;
            border-left: 2px solid #34d399;
        }
        .admin-nav-item svg {
            width: 1.25rem;
            height: 1.25rem;
            opacity: 0.7;
            flex-shrink: 0;
        }
        .admin-nav-item.active svg {
            opacity: 1;
        }

        /* Badges */
        .admin-badge {
            padding: 0.125rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }
        .admin-badge-success { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
        .admin-badge-warning { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .admin-badge-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .admin-badge-info { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        .dark .admin-badge-success { background-color: rgba(16, 185, 129, 0.2); color: #34d399; }
        .dark .admin-badge-warning { background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .dark .admin-badge-danger { background-color: rgba(239, 68, 68, 0.2); color: #f87171; }
        .dark .admin-badge-info { background-color: rgba(59, 130, 246, 0.2); color: #60a5fa; }

        /* Card component */
        .admin-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .dark .admin-card {
            background-color: #1e293b;
            border-color: #334155;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-white transition-colors duration-300">
    <div class="flex min-h-screen">
        <!-- Admin Sidebar -->
        @include('admin.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">

            <!-- Top Header -->
            <header class="sticky top-0 z-20 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-4 lg:px-6 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">@yield('page-title', 'Admin Dashboard')</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@yield('page-subtitle', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- System Status -->
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">System Online</span>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Toggle Dark Mode">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </button>

                        <!-- Notifications -->
                        <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- Admin Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst(Auth::user()->role ?? 'admin') }}</p>
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg py-1 z-50">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                        </svg>
                                        User Dashboard
                                    </span>
                                </a>
                                <a href="{{ route('settings.profile') }}" class="block px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">Profile</a>
                                <hr class="my-1 border-slate-200 dark:border-slate-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-slate-100 dark:hover:bg-slate-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
                @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg">
                    {{ session('error') }}
                </div>
                @endif
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 dark:border-slate-700 px-4 lg:px-6 py-4 bg-white dark:bg-slate-800">
                <div class="flex items-center justify-between text-sm text-slate-500 dark:text-slate-400">
                    <span>&copy; {{ date('Y') }} Orbit WhatsApp API - Admin Panel</span>
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

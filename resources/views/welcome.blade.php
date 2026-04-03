<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ get_setting('meta_description', 'Orbit WhatsApp API adalah platform pengiriman pesan WhatsApp massal.') }}">
    <title>{{ get_setting('site_name', 'Orbit WhatsApp API') }} - {{ get_setting('site_tagline', 'Solusi API WhatsApp Terpercaya') }}</title>

    <!-- Scripts & Styles from Original Design -->
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Space+Grotesk:wght@500;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

    <!-- Original Tracking Scripts (Preserved) -->
    <script nonce="{{ config('app.csp_nonce') }}" data-cookieconsent="ignore">
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
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
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-blockingmode="auto"
        data-cbid="e34b0360-5226-4315-9b8e-52f41f19761b" type="text/javascript" nonce="{{ config('app.csp_nonce') }}">
    </script>
    <script nonce="{{ config('app.csp_nonce') }}" src="https://www.googletagmanager.com/gtag/js?id=G-WFGB15WM4T"></script>
    <script nonce="{{ config('app.csp_nonce') }}" data-cookieconsent="ignore">
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-WFGB15WM4T');
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 transition-colors duration-300 antialiased selection:bg-primary selection:text-white">
    <nav class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <a href="/" class="flex items-center gap-3 group">
                        <img src="{{ get_setting('site_logo', asset('Image/logo-wa-api-black.png')) }}" alt="{{ get_setting('site_name', 'Orbit API') }} Logo" class="h-10 w-auto dark:hidden">
                        <img src="{{ get_setting('site_logo_white', asset('Image/logo-wa-api-white.png')) }}" alt="{{ get_setting('site_name', 'Orbit API') }} Logo" class="h-10 w-auto hidden dark:block">
                        <span class="font-display font-bold text-xl tracking-tight text-slate-900 dark:text-white">{{ get_setting('site_name', 'Orbit Whatsapp API') }}</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:shadow-glow px-2 py-1 rounded-md" href="#features">Features</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:shadow-glow px-2 py-1 rounded-md" href="#pricing">Pricing</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:shadow-glow px-2 py-1 rounded-md" href="{{ route('docs.index') }}">API Docs</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:shadow-glow px-2 py-1 rounded-md" href="#contact">Contact</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-slate-700 dark:hover:bg-slate-200 transition-all shadow-sm hover:shadow-glow hover:-translate-y-0.5 group" href="{{ route('dashboard') }}">
                            Dashboard <span class="hidden group-hover:inline-block transition-transform duration-300 group-hover:translate-x-1 ml-1">→</span>
                        </a>
                    @else
                        <a class="text-sm font-semibold text-primary hover:text-primary-dark transition-colors" href="{{ route('login') }}">Log In</a>
                        @if (Route::has('register'))
                            <a class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-slate-700 dark:hover:bg-slate-200 transition-all shadow-sm hover:shadow-glow hover:-translate-y-0.5 group" href="{{ route('register') }}">
                                Get Started <span class="hidden group-hover:inline-block transition-transform duration-300 group-hover:translate-x-1 ml-1">→</span>
                            </a>
                        @endif
                    @endauth
                </div>
                <div class="md:hidden flex items-center">
                    <button class="text-slate-600 dark:text-slate-300 hover:text-primary"><span class="material-icons-round">menu</span></button>
                </div>
            </div>
        </div>
    </nav>

    <header class="relative pt-20 pb-32 overflow-hidden">
        <div class="absolute inset-0 grid-bg opacity-[0.07] dark:opacity-[0.1] pointer-events-none"></div>
        <div class="scan-line"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/20 rounded-full blur-3xl opacity-50 pointer-events-none animate-pulse-slow"></div>
        <div class="absolute top-40 -left-20 w-72 h-72 bg-blue-500/10 dark:bg-blue-500/20 rounded-full blur-3xl opacity-50 pointer-events-none animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center space-x-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full px-4 py-1.5 shadow-sm group hover:border-primary/50 transition-colors cursor-default">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors">Trusted by 500+ Businesses</span>
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-display font-bold text-slate-900 dark:text-white leading-tight">
                        {{ explode(' ', get_setting('site_tagline', 'Integrasi WhatsApp API Tercepat untuk Bisnis Anda'), 2)[0] ?? 'Integrasi' }} 
                        <span class="relative inline-block">
                            <span class="absolute inset-0 bg-primary/20 blur-lg transform skew-x-12"></span>
                            <span class="relative text-transparent bg-clip-text bg-gradient-to-r from-primary to-emerald-400">
                                {{ explode(' ', get_setting('site_tagline', 'Integrasi WhatsApp API Tercepat untuk Bisnis Anda'), 2)[1] ?? 'WhatsApp API' }}
                            </span>
                        </span>
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-lg">
                        {{ get_setting('meta_description', 'Kirim notifikasi, OTP, dan blast pesan secara otomatis dengan infrastruktur enterprise-grade yang stabil dan aman.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            <a class="group relative inline-flex justify-center items-center px-8 py-4 bg-primary text-white rounded-xl font-bold text-lg hover:bg-emerald-400 transition-all duration-300 shadow-glow hover:shadow-glow-emerald hover:scale-105 active:scale-95 overflow-hidden" href="{{ route('dashboard') }}">
                                <span class="relative z-10 flex items-center">Dashboard <span class="material-icons-round ml-2 text-xl group-hover:translate-x-1.5 transition-transform duration-300">arrow_forward</span></span>
                            </a>
                        @else
                            <a class="group relative inline-flex justify-center items-center px-8 py-4 bg-primary text-white rounded-xl font-bold text-lg hover:bg-emerald-400 transition-all duration-300 shadow-glow hover:shadow-glow-emerald hover:scale-105 active:scale-95 overflow-hidden" href="{{ route('register') }}">
                                <span class="relative z-10 flex items-center">Coba Gratis Sekarang <span class="material-icons-round ml-2 text-xl group-hover:translate-x-1.5 transition-transform duration-300">arrow_forward</span></span>
                            </a>
                        @endauth
                        <a class="group inline-flex justify-center items-center px-8 py-4 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-lg hover:bg-slate-50 dark:hover:bg-slate-750 hover:border-primary/50 transition-all duration-300 hover:shadow-[0_0_15px_-3px_rgba(16,185,129,0.3)] hover:-translate-y-0.5 active:scale-95" href="{{ route('docs.index') }}">
                            <span class="material-icons-round mr-2 text-xl text-primary group-hover:scale-110 transition-transform duration-300">description</span> Lihat Dokumentasi
                        </a>
                    </div>
                    <div class="flex items-center space-x-6 text-sm font-medium text-slate-500 dark:text-slate-400 pt-4">
                        <div class="flex items-center group">
                            <span class="material-icons-round text-primary mr-1.5 text-lg group-hover:scale-110 transition-transform">check_circle</span>
                            Free Trial
                        </div>
                        <div class="flex items-center group">
                            <span class="material-icons-round text-primary mr-1.5 text-lg group-hover:scale-110 transition-transform">check_circle</span>
                            No Credit Card
                        </div>
                        <div class="flex items-center group">
                            <span class="material-icons-round text-primary mr-1.5 text-lg group-hover:scale-110 transition-transform">verified_user</span>
                            SSL Secured
                        </div>
                    </div>
                </div>
                <div class="relative group perspective-1000">
                    <div class="animate-float">
                        <div class="absolute inset-0 bg-primary/20 blur-3xl rounded-full transform rotate-3 scale-90 -z-10 animate-pulse-slow"></div>
                        <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden relative z-10 ring-1 ring-slate-900/5 dark:ring-white/10 group-hover:shadow-glow-lg transition-shadow duration-500">
                            <div class="h-12 bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex items-center px-4 space-x-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div><div class="w-3 h-3 rounded-full bg-amber-400"></div><div class="w-3 h-3 rounded-full bg-green-400"></div>
                                <div class="ml-4 px-3 py-1 bg-white dark:bg-slate-800 rounded text-xs text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex-1 text-center font-mono flex justify-between items-center group/url">
                                    <span class="opacity-0 w-4"></span>
                                    <span>dashboard.orbitwaapi.site</span>
                                    <span class="material-icons-round text-[10px] text-green-500 opacity-0 group-hover/url:opacity-100 transition-opacity">lock</span>
                                </div>
                            </div>
                            <div class="p-6 space-y-6 relative">
                                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CjxwYXRoIGQ9Ik0gMSAwIEwgMCAwIEwgMCAxIiBmaWxsPSJub25lIiBzdHJva2U9InJnYmEoMjAwLDIwMCwyMDAsMC4wNSkiIHN0cm9rZS13aWR0aD0iMSIvPgo8L3N2Zz4=')] opacity-30 pointer-events-none"></div>
                                <div class="grid grid-cols-3 gap-4 relative z-10">
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-700 text-center hover:border-primary/30 transition-colors group/stat">
                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Messages</p>
                                        <div class="flex items-center justify-center space-x-1">
                                            <p class="text-2xl font-display font-bold text-slate-900 dark:text-white">{{ $formattedMessages ?? '12.4K' }}</p>
                                            <span class="flex h-2 w-2 relative">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-emerald-500 font-mono mt-1 opacity-0 group-hover/stat:opacity-100 transition-opacity">+12% vs last week</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-700 text-center relative overflow-hidden group/stat">
                                        <div class="absolute top-0 right-0 w-2 h-2 bg-primary rounded-full m-2 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Success Rate</p>
                                        <p class="text-2xl font-display font-bold text-emerald-500">{{ $successRate ?? '99.8' }}%</p>
                                        <div class="w-full h-1 bg-slate-200 dark:bg-slate-700 rounded-full mt-2 overflow-hidden">
                                            <div class="h-full bg-emerald-500 w-[{{ $successRate ?? '99.8' }}%] shadow-[0_0_10px_rgba(16,185,129,0.5)] relative">
                                                <div class="absolute inset-0 bg-white/30 w-full animate-[shimmer_2s_infinite]"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-700 text-center hover:border-primary/30 transition-colors">
                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Devices</p>
                                        <p class="text-2xl font-display font-bold text-slate-900 dark:text-white">5</p>
                                        <div class="flex justify-center space-x-1 mt-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" style="animation-delay: 0.2s"></div>
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" style="animation-delay: 0.4s"></div>
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" style="animation-delay: 0.6s"></div>
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" style="animation-delay: 0.8s"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 border border-slate-100 dark:border-slate-700 relative overflow-hidden group/message hover:shadow-md transition-shadow">
                                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent transform -translate-x-full group-hover/message:translate-x-full transition-transform duration-1000"></div>
                                    <div class="flex items-start space-x-4 relative z-10">
                                        <div class="h-10 w-10 bg-primary rounded-full flex items-center justify-center text-white flex-shrink-0 shadow-lg shadow-emerald-500/30 group-hover/message:scale-110 transition-transform duration-300">
                                            <span class="material-icons-round">whatsapp</span>
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover/message:text-primary transition-colors">OTP Verification</p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">+62 812 3456 ****</p>
                                                </div>
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded-full font-medium border border-green-200 dark:border-green-800 flex items-center gap-1">
                                                    <span class="material-icons-round text-[10px]">done_all</span> Delivered
                                                </span>
                                            </div>
                                            <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300 shadow-sm group-hover/message:border-primary/20 transition-colors">
                                                <p>Kode OTP Anda adalah <strong class="text-slate-900 dark:text-white font-mono bg-slate-100 dark:bg-slate-700 px-1 rounded">849201</strong>. Jangan berikan kode ini kepada siapapun.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-12 top-24 bg-surface-light dark:bg-surface-dark p-3 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 flex items-center space-x-3 animate-bounce shadow-glow-lg backdrop-blur-md bg-opacity-90 dark:bg-opacity-90" style="animation-duration: 4s;">
                        <div class="bg-green-100 dark:bg-green-900/30 p-1.5 rounded-md relative overflow-hidden">
                            <div class="absolute inset-0 bg-emerald-400/20 animate-pulse"></div>
                            <span class="material-icons-round text-primary text-xl relative z-10">security</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">Enterprise Security</p>
                            <p class="text-[10px] text-slate-500">ISO 27001 Certified</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-24 bg-surface-light dark:bg-surface-dark border-y border-slate-200 dark:border-slate-800" id="features">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-3 block">Fitur Unggulan</span>
                <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-6">Solusi Komunikasi Terpadu untuk Skala Enterprise</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400">Dirancang untuk kehandalan tinggi dengan fitur yang memudahkan integrasi ke sistem yang sudah ada.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="group p-8 bg-background-light dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all duration-300 hover:shadow-glow-lg hover:-translate-y-2">
                    <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons-round text-primary text-3xl">bolt</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">API Super Cepat</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Latency rendah dengan infrastruktur server yang dioptimalkan untuk pengiriman pesan massal dan real-time.</p>
                </div>
                <div class="group p-8 bg-background-light dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all duration-300 hover:shadow-glow-lg hover:-translate-y-2">
                    <div class="w-14 h-14 bg-blue-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons-round text-blue-500 text-3xl">terminal</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">Dokumentasi Lengkap</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">SDK dan contoh kode dalam berbagai bahasa pemrograman (Node.js, PHP, Python) untuk integrasi tanpa hambatan.</p>
                </div>
                <div class="group p-8 bg-background-light dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all duration-300 hover:shadow-glow-lg hover:-translate-y-2">
                    <div class="w-14 h-14 bg-purple-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons-round text-purple-500 text-3xl">bar_chart</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">Analitik Real-time</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Pantau status pengiriman, tingkat keberhasilan, dan penggunaan kuota secara langsung melalui dashboard interaktif.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 relative overflow-hidden" id="pricing">
        <div class="absolute inset-0 grid-bg opacity-[0.03] dark:opacity-[0.05] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-3 block">Harga Layanan</span>
                <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-6">Pilih Paket yang Sesuai dengan Kebutuhan Bisnis Anda</h2>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button class="px-6 py-2 rounded-lg text-sm font-bold bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white">Monthly</button>
                    <button class="px-6 py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Yearly (Save 20%)</button>
                </div>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($plans as $plan)
                <div class="relative p-8 rounded-2xl border transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group {{ $plan->is_featured ? 'bg-slate-900 text-white border-primary shadow-glow-emerald z-10 scale-105 h-full' : 'bg-surface-light dark:bg-surface-dark border-slate-200 dark:border-slate-800' }}">
                    @if($plan->is_featured)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white text-[10px] font-bold py-1 px-4 rounded-full tracking-widest uppercase shadow-lg z-20">Recommended</div>
                    @endif
                    <div class="mb-8">
                        <h3 class="text-lg font-bold {{ $plan->is_featured ? 'text-white' : 'text-slate-900 dark:text-white' }} mb-2">{{ $plan->name }}</h3>
                        <div class="flex items-baseline space-x-1">
                            @if($plan->price == 0)
                            <span class="text-4xl font-display font-bold {{ $plan->is_featured ? 'text-white' : 'text-slate-900 dark:text-white' }}">Gratis</span>
                            @else
                            <span class="text-sm font-bold {{ $plan->is_featured ? 'text-slate-400' : 'text-slate-500' }}">Rp</span>
                            <span class="text-4xl font-display font-bold {{ $plan->is_featured ? 'text-white' : 'text-slate-900 dark:text-white' }}">{{ number_format($plan->price, 0, ',', '.') }}</span>
                            <span class="text-sm font-medium {{ $plan->is_featured ? 'text-slate-400' : 'text-slate-500' }}">/bln</span>
                            @endif
                        </div>
                        <p class="text-sm {{ $plan->is_featured ? 'text-slate-400' : 'text-slate-500' }} mt-4">{{ $plan->description }}</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        @if($plan->features)
                            @foreach($plan->features as $feature => $enabled)
                                @if($enabled)
                                <li class="flex items-center text-sm {{ $plan->is_featured ? 'text-slate-300' : 'text-slate-600 dark:text-slate-300' }}">
                                    <span class="material-icons-round text-primary mr-3 text-lg">check_circle</span> {{ is_string($enabled) ? $enabled : ucwords(str_replace('_', ' ', $feature)) }}
                                </li>
                                @endif
                            @endforeach
                        @endif
                        <li class="flex items-center text-sm {{ $plan->is_featured ? 'text-slate-300' : 'text-slate-600 dark:text-slate-300' }}">
                            <span class="material-icons-round text-primary mr-3 text-lg">check_circle</span>
                            {{ $plan->isUnlimited('devices') ? 'Unlimited' : $plan->max_devices }} Devices
                        </li>
                        <li class="flex items-center text-sm {{ $plan->is_featured ? 'text-slate-300' : 'text-slate-600 dark:text-slate-300' }}">
                            <span class="material-icons-round text-primary mr-3 text-lg">check_circle</span>
                            {{ $plan->isUnlimited('messages') ? 'Unlimited' : number_format($plan->max_messages_per_day, 0, ',', '.') }} Msg/Day
                        </li>
                    </ul>
                    <a class="block text-center py-3 px-6 rounded-xl font-bold {{ $plan->is_featured ? 'bg-primary text-white hover:bg-emerald-400 shadow-glow hover:shadow-glow-emerald' : 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-slate-700' }} transition-all"
                       href="{{ Auth::check() ? route('billing.plans') : route('register') }}">
                       {{ $plan->price == 0 ? 'Mulai Gratis' : 'Pilih Paket' }}
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact, FAQ, CTA, and Footer sections follow -->
    <section class="py-24 bg-surface-light dark:bg-surface-dark border-y border-slate-200 dark:border-slate-800 relative overflow-hidden" id="contact">
        <div class="absolute inset-0 grid-bg opacity-[0.05] dark:opacity-[0.1] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-primary font-bold tracking-widest uppercase text-sm mb-3 block">Hubungi Kami</span>
                    <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-6">Siap Mengakselerasi Bisnis Anda?</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">Tim kami siap membantu Anda memilih solusi yang tepat. Konsultasikan kebutuhan API WhatsApp Anda sekarang.</p>
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-700 group hover:border-primary/30 transition-colors">
                            <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                <span class="material-icons-round">email</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">Email Sales</p>
                                <p class="text-sm text-slate-500">{{ get_setting('support_email', 'sales@orbitwaapi.site') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-700 group hover:border-primary/30 transition-colors">
                            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center text-green-500 group-hover:scale-110 transition-transform">
                                <span class="material-icons-round">whatsapp</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">WhatsApp Support</p>
                                <p class="text-sm text-slate-500">{{ get_setting('support_whatsapp', '+62 821-2222-3333') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 relative group overflow-hidden">
                    @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
                        <span class="material-icons-round">check_circle</span>
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                        <span class="material-icons-round">error</span>
                        {{ session('error') }}
                    </div>
                    @endif
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6 relative z-10">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                                <input name="name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="John Doe" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Email Bisnis</label>
                                <input name="email" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="john@company.com" type="email" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Subjek</label>
                            <select name="subject" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                <option value="Inquiry Paket Enterprise">Inquiry Paket Enterprise</option>
                                <option value="Bantuan Teknis">Bantuan Teknis</option>
                                <option value="Kemitraan">Kemitraan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Pesan</label>
                            <textarea name="message" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all h-32" placeholder="Bagaimana kami bisa membantu Anda?"></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-primary text-white rounded-xl font-bold text-lg hover:bg-emerald-400 transition-all shadow-glow hover:shadow-glow-emerald active:scale-[0.98]">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-background-light dark:bg-background-dark" id="faq">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-3 block">FAQ</span>
                <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-6">Pertanyaan yang Sering Diajukan</h2>
            </div>
            <div class="space-y-4">
                @foreach($faqs as $faq)
                <details class="group p-6 bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-primary/50 transition-all cursor-pointer">
                    <summary class="flex justify-between items-center font-bold text-lg text-slate-900 dark:text-white">
                        {{ $faq['question'] }}
                        <span class="material-icons-round text-primary transition-transform group-open:rotate-180 rotate-icon">expand_more</span>
                    </summary>
                    <div class="mt-4 text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ $faq['answer'] }}
                    </div>
                </details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-gradient-to-r from-primary/90 to-emerald-600/90 rounded-[2.5rem] p-12 lg:p-20 overflow-hidden text-center shadow-2xl shadow-primary/20 group">
                <div class="absolute inset-0 grid-bg opacity-10 pointer-events-none"></div>
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-300/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="relative z-10 max-w-2xl mx-auto space-y-8">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold text-white leading-tight">Mulai Transformasi Digital Komunikasi Anda Hari Ini</h2>
                    <p class="text-emerald-50 leading-relaxed text-lg">Bergabunglah dengan ribuan developer yang telah memilih Orbit API untuk kebutuhan integrasi WhatsApp mereka.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                        <a class="px-10 py-4 bg-white text-primary rounded-xl font-bold text-lg hover:bg-emerald-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 active:scale-95" href="{{ route('register') }}">Daftar Sekarang</a>
                        <a class="px-10 py-4 bg-primary-dark/30 text-white border border-white/20 rounded-xl font-bold text-lg hover:bg-primary-dark/50 transition-all backdrop-blur-sm active:scale-95" href="{{ route('docs.index') }}">Lihat Demo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-background-dark text-slate-300 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1">
                    <div class="flex items-center space-x-3 mb-8">
                        <img src="{{ asset('Image/logo-wa-api-white.png') }}" alt="Orbit API Logo" class="h-10 w-auto">
                        <span class="font-display font-bold text-xl text-white">Orbit Whatsapp API</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-400">Penyedia layanan WhatsApp API terpercaya untuk integrasi sistem otomatisasi pesan bisnis global.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-widest text-xs">Produk</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a class="hover:text-primary transition-colors" href="#">Features</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Pricing</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Documentation</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">API Status</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-widest text-xs">Perusahaan</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a class="hover:text-primary transition-colors" href="#">About Us</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Blog</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Careers</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-widest text-xs">Bantuan</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a class="hover:text-primary transition-colors" href="#">Help Center</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Community</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Contact Support</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">System Health</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-xs text-slate-500">{{ get_setting('footer_text', '© 2026 Orbit WhatsApp API. All rights reserved. Registered PT Multi Solusi Digital.') }}</p>
                <div class="flex space-x-6">
                    <a class="text-slate-500 hover:text-white transition-colors" href="#"><span class="material-icons-round">facebook</span></a>
                    <a class="text-slate-500 hover:text-white transition-colors" href="#"><span class="material-icons-round">language</span></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi - {{ config('app.name', 'Orbit WhatsApp API') }}</title>

    <!-- Meta Data -->
    <meta name="description"
        content="Platform WhatsApp API terdepan untuk bisnis. Kirim notifikasi, OTP, dan blast pesan secara otomatis dengan infrastruktur yang stabil dan aman.">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Tailwind CSS (CDN for standalone pages if not using Vite for this specific design) -->
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark transition-colors duration-200">
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-b border-gray-100 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('Image/logo-wa-api-black.png') }}" alt="Orbit API Logo" class="h-12 w-auto">
                    <span class="text-lg font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Orbit WhatsApp API</span>
                </div>

                <!-- Nav Menu -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}#features" class="text-xs text-gray-600 hover:text-emerald-600 transition">Features</a>
                    <a href="{{ route('home') }}#pricing" class="text-xs text-gray-600 hover:text-emerald-600 transition">Pricing</a>
                    <a href="{{ route('docs.index') }}" class="text-xs text-gray-600 hover:text-emerald-600 transition">API Docs</a>
                    <a href="{{ route('home') }}#contact" class="text-xs text-gray-600 hover:text-emerald-600 transition">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Log In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-xs font-medium text-white px-4 py-1.5 rounded-lg">Get Started Free</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <header
        class="pt-32 pb-12 sm:pt-40 sm:pb-16 bg-gradient-to-b from-emerald-50 to-transparent dark:from-emerald-900/20 dark:to-transparent">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div
                class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-medium mb-6">
                <span class="w-2 h-2 rounded-full bg-primary mr-2"></span>
                Legal &amp; Compliance
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900 dark:text-white mb-6">
                Kebijakan Privasi
            </h1>
            <p class="text-lg text-text-muted-light dark:text-text-muted-dark max-w-2xl mx-auto">
                Kami berkomitmen untuk melindungi privasi dan data Anda. Pelajari bagaimana kami mengumpulkan,
                menggunakan, dan melindungi informasi Anda.
            </p>
            <div class="mt-4 text-sm text-text-muted-light dark:text-text-muted-dark">
                Terakhir diperbarui: {{ date('d F Y') }}
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div
            class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 sm:p-12 prose prose-emerald dark:prose-invert max-w-none">
            <section class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                        <span class="material-icons text-sm">info</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white m-0">1. Pendahuluan</h2>
                </div>
                <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                    Selamat datang di Orbit WhatsApp API ("kami", "kita", atau "milik kami"). Kebijakan Privasi ini
                    menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan mengamankan informasi
                    pribadi Anda saat Anda menggunakan situs web kami dan layanan API WhatsApp kami ("Layanan").
                </p>
                <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                    Dengan mengakses atau menggunakan Layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi
                    sesuai dengan kebijakan ini.
                </p>
            </section>

            <section class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                        <span class="material-icons text-sm">folder_open</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white m-0">2. Informasi yang Kami Kumpulkan
                    </h2>
                </div>
                <p class="text-text-muted-light dark:text-text-muted-dark mb-4">
                    Kami mengumpulkan beberapa jenis informasi untuk berbagai tujuan guna menyediakan dan meningkatkan
                    Layanan kami kepada Anda:
                </p>
                <ul class="space-y-4 list-none pl-0">
                    <li class="flex gap-4">
                        <span class="material-icons text-primary text-sm mt-1">check_circle</span>
                        <div>
                            <strong class="text-gray-900 dark:text-white block">Informasi Pribadi</strong>
                            <span class="text-text-muted-light dark:text-text-muted-dark">Saat mendaftar, kami mungkin
                                meminta informasi pengenal pribadi tertentu seperti alamat email, nama, nomor telepon,
                                dan data bisnis.</span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-icons text-primary text-sm mt-1">check_circle</span>
                        <div>
                            <strong class="text-gray-900 dark:text-white block">Data Penggunaan</strong>
                            <span class="text-text-muted-light dark:text-text-muted-dark">Kami dapat mengumpulkan
                                informasi tentang bagaimana Layanan diakses dan digunakan, termasuk alamat IP komputer
                                Anda, jenis browser, versi browser, halaman yang Anda kunjungi, dan stempel
                                waktu.</span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-icons text-primary text-sm mt-1">check_circle</span>
                        <div>
                            <strong class="text-gray-900 dark:text-white block">Data Pesan</strong>
                            <span class="text-text-muted-light dark:text-text-muted-dark">Untuk memfasilitasi pengiriman
                                pesan melalui WhatsApp API, sistem kami memproses data pesan secara sementara. Kami
                                tidak menyimpan isi pesan Anda lebih lama dari yang diperlukan untuk transmisi.</span>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                        <span class="material-icons text-sm">settings_suggest</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white m-0">3. Penggunaan Informasi</h2>
                </div>
                <p class="text-text-muted-light dark:text-text-muted-dark">Orbit WhatsApp API menggunakan data yang
                    dikumpulkan untuk berbagai tujuan:</p>
                <ul
                    class="list-disc pl-5 space-y-2 text-text-muted-light dark:text-text-muted-dark marker:text-primary">
                    <li>Untuk menyediakan dan memelihara Layanan kami</li>
                    <li>Untuk memberi tahu Anda tentang perubahan pada Layanan kami</li>
                    <li>Untuk memungkinkan Anda berpartisipasi dalam fitur interaktif Layanan kami</li>
                    <li>Untuk memberikan dukungan pelanggan</li>
                    <li>Untuk mengumpulkan analisis atau informasi berharga sehingga kami dapat meningkatkan Layanan
                        kami</li>
                    <li>Untuk memantau penggunaan Layanan kami</li>
                    <li>Untuk mendeteksi, mencegah, dan mengatasi masalah teknis</li>
                </ul>
            </section>

            <section class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                        <span class="material-icons text-sm">security</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white m-0">4. Keamanan Data</h2>
                </div>
                <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                    Keamanan data Anda penting bagi kami, tetapi ingat bahwa tidak ada metode transmisi melalui
                    Internet, atau metode penyimpanan elektronik yang 100% aman. Meskipun kami berusaha menggunakan cara
                    yang dapat diterima secara komersial untuk melindungi Data Pribadi Anda, kami tidak dapat menjamin
                    keamanannya secara mutlak.
                </p>
                <div
                    class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800/50">
                    <p class="text-sm text-emerald-800 dark:text-emerald-200 m-0">
                        <strong class="font-semibold">Enkripsi End-to-End:</strong> Kami mematuhi protokol keamanan
                        standar industri dan memastikan bahwa integrasi dengan WhatsApp tetap mematuhi standar enkripsi
                        end-to-end yang diberlakukan oleh Meta.
                    </p>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                        <span class="material-icons text-sm">contact_support</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white m-0">5. Hubungi Kami</h2>
                </div>
                <p class="text-text-muted-light dark:text-text-muted-dark mb-6">
                    Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami:
                </p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <a class="flex items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-colors group"
                        href="mailto:privacy@orbitwaapi.site">
                        <span class="material-icons text-gray-400 group-hover:text-primary mr-3">email</span>
                        <div>
                            <div class="text-sm text-text-muted-light dark:text-text-muted-dark">Email</div>
                            <div class="font-medium text-gray-900 dark:text-white">privacy@orbitwaapi.site</div>
                        </div>
                    </a>
                    <a class="flex items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-colors group"
                        href="#" target="_blank">
                        <span class="material-icons text-gray-400 group-hover:text-primary mr-3">chat</span>
                        <div>
                            <div class="text-sm text-text-muted-light dark:text-text-muted-dark">WhatsApp Support</div>
                            <div class="font-medium text-gray-900 dark:text-white">+62 812 3456 7890</div>
                        </div>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <x-public-footer />
</body>

</html>

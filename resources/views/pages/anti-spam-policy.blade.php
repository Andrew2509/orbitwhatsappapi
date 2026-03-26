<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Kebijakan Anti-Spam - {{ config('app.name', 'Orbit WhatsApp API') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
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
                    <span
                        class="text-lg font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Orbit
                        WhatsApp API</span>
                </div>

                <!-- Nav Menu -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}#features"
                        class="text-xs text-gray-600 hover:text-emerald-600 transition">Features</a>
                    <a href="{{ route('home') }}#pricing"
                        class="text-xs text-gray-600 hover:text-emerald-600 transition">Pricing</a>
                    <a href="{{ route('docs.index') }}"
                        class="text-xs text-gray-600 hover:text-emerald-600 transition">API Docs</a>
                    <a href="{{ route('home') }}#contact"
                        class="text-xs text-gray-600 hover:text-emerald-600 transition">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Log
                            In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-xs font-medium text-white px-4 py-1.5 rounded-lg">Get
                                Started Free</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-32 pb-12 bg-gradient-to-b from-emerald-50 to-white dark:from-slate-900 dark:to-background-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div
                class="inline-flex items-center space-x-2 bg-emerald-100 dark:bg-emerald-900/30 text-primary px-3 py-1 rounded-full text-sm font-medium mb-6">
                <span class="material-icons-round text-sm">gavel</span>
                <span>Legal &amp; Compliance</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white tracking-tight mb-4">
                Kebijakan Anti-Spam
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Kami berkomitmen untuk menjaga kualitas komunikasi di platform WhatsApp. Pelajari standar dan aturan
                pengiriman pesan di Orbit.
            </p>
            <p class="mt-4 text-sm text-slate-500 dark:text-slate-500">
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="sticky top-28 space-y-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 pl-3">Daftar Isi</p>
                    <a class="group flex items-center px-3 py-2 text-sm font-medium text-primary bg-emerald-50 dark:bg-emerald-900/20 rounded-md border-l-4 border-primary"
                        href="#pendahuluan">
                        Pendahuluan
                    </a>
                    <a class="group flex items-center px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md border-l-4 border-transparent transition-all"
                        href="#definisi-spam">
                        Apa itu Spam?
                    </a>
                    <a class="group flex items-center px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md border-l-4 border-transparent transition-all"
                        href="#persetujuan-pengguna">
                        Persetujuan (Opt-In)
                    </a>
                    <a class="group flex items-center px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md border-l-4 border-transparent transition-all"
                        href="#konten-dilarang">
                        Konten yang Dilarang
                    </a>
                    <a class="group flex items-center px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md border-l-4 border-transparent transition-all"
                        href="#pemblokiran">
                        Sanksi &amp; Pemblokiran
                    </a>
                    <a class="group flex items-center px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md border-l-4 border-transparent transition-all"
                        href="#pelaporan">
                        Pelaporan Pelanggaran
                    </a>
                    <div class="pt-8">
                        <div
                            class="bg-slate-50 dark:bg-surface-dark rounded-xl p-6 border border-slate-100 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Butuh Bantuan?</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Tim support kami siap membantu
                                pertanyaan seputar policy.</p>
                            <a class="text-sm font-medium text-primary hover:text-primary-dark flex items-center"
                                href="{{ route('home') }}#contact">
                                Hubungi Support <span class="material-icons-round text-base ml-1">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </aside>
            <main class="lg:col-span-9">
                <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">
                    <section class="mb-12 scroll-mt-28" id="pendahuluan">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                            <span
                                class="bg-emerald-100 dark:bg-emerald-900/50 text-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-lg font-bold">1</span>
                            Pendahuluan
                        </h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Orbit WhatsApp API menerapkan kebijakan <strong>Zero Tolerance</strong> terhadap segala
                            bentuk spam. Sebagai penyedia layanan resmi WhatsApp Business API, kami terikat pada aturan
                            ketat Meta Platforms, Inc. untuk melindungi pengalaman pengguna akhir WhatsApp. Kebijakan
                            ini mengatur penggunaan layanan kami untuk memastikan komunikasi yang sehat, relevan, dan
                            diinginkan oleh penerima.
                        </p>
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="material-icons-round text-yellow-500">warning</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        Pelanggaran terhadap kebijakan ini dapat mengakibatkan penangguhan akun secara
                                        permanen tanpa pengembalian dana.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="mb-12 scroll-mt-28" id="definisi-spam">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                            <span
                                class="bg-emerald-100 dark:bg-emerald-900/50 text-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-lg font-bold">2</span>
                            Apa itu Spam?
                        </h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            Dalam konteks Orbit WhatsApp API, spam didefinisikan sebagai pesan elektronik yang tidak
                            diminta, tidak relevan, atau dikirimkan secara massal kepada penerima yang tidak memberikan
                            persetujuan eksplisit.
                        </p>
                        <ul class="space-y-3 list-none pl-0">
                            <li class="flex items-start">
                                <span class="material-icons-round text-red-500 mt-1 mr-2">cancel</span>
                                <span class="text-slate-600 dark:text-slate-300">Pesan promosi agresif tanpa interaksi
                                    sebelumnya.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-round text-red-500 mt-1 mr-2">cancel</span>
                                <span class="text-slate-600 dark:text-slate-300">Mengirim pesan ke nomor yang didapat
                                    dari pihak ketiga (jual beli database).</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-round text-red-500 mt-1 mr-2">cancel</span>
                                <span class="text-slate-600 dark:text-slate-300">Pesan penipuan, phishing, atau konten
                                    menyesatkan.</span>
                            </li>
                        </ul>
                    </section>
                    <section class="mb-12 scroll-mt-28" id="persetujuan-pengguna">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                            <span
                                class="bg-emerald-100 dark:bg-emerald-900/50 text-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-lg font-bold">3</span>
                            Persetujuan (Opt-In)
                        </h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            Anda <strong>WAJIB</strong> mendapatkan persetujuan (opt-in) dari setiap kontak sebelum
                            mengirimkan pesan pertama melalui API kami. Persetujuan ini harus dilakukan secara aktif,
                            bukan pasif.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-white dark:bg-surface-dark p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                <h3 class="font-semibold text-primary mb-2 flex items-center">
                                    <span class="material-icons-round mr-2">check_circle</span> Metode yang Diizinkan
                                </h3>
                                <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-2">
                                    <li>• Checkbox di formulir pendaftaran website.</li>
                                    <li>• Pelanggan menghubungi Anda duluan di WhatsApp.</li>
                                    <li>• Form fisik dengan tanda tangan persetujuan.</li>
                                </ul>
                            </div>
                            <div
                                class="bg-white dark:bg-surface-dark p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                <h3 class="font-semibold text-red-500 mb-2 flex items-center">
                                    <span class="material-icons-round mr-2">remove_circle</span> Metode Dilarang
                                </h3>
                                <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-2">
                                    <li>• Scraping nomor dari internet.</li>
                                    <li>• Mengasumsikan persetujuan dari pembelian lama.</li>
                                    <li>• Membeli daftar kontak.</li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    <section class="mb-12 scroll-mt-28" id="konten-dilarang">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                            <span
                                class="bg-emerald-100 dark:bg-emerald-900/50 text-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-lg font-bold">4</span>
                            Konten yang Dilarang
                        </h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            Selain spam, jenis konten berikut dilarang keras untuk dikirimkan melalui Orbit WhatsApp
                            API, sesuai dengan Commerce Policy WhatsApp:
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div class="bg-slate-50 dark:bg-surface-dark p-4 rounded-lg text-center">
                                <span class="material-icons-round text-3xl text-slate-400 mb-2">casino</span>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Perjudian</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-surface-dark p-4 rounded-lg text-center">
                                <span class="material-icons-round text-3xl text-slate-400 mb-2">medication</span>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Obat Ilegal</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-surface-dark p-4 rounded-lg text-center">
                                <span class="material-icons-round text-3xl text-slate-400 mb-2">18_up_rating</span>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Konten Dewasa</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-surface-dark p-4 rounded-lg text-center">
                                <span class="material-icons-round text-3xl text-slate-400 mb-2">liquor</span>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Alkohol &amp;
                                    Tembakau</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-surface-dark p-4 rounded-lg text-center">
                                <span class="material-icons-round text-3xl text-slate-400 mb-2">monetization_on</span>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Pinjaman Ilegal</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-surface-dark p-4 rounded-lg text-center">
                                <span class="material-icons-round text-3xl text-slate-400 mb-2">dangerous</span>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Ujaran Kebencian</p>
                            </div>
                        </div>
                    </section>
                    <section class="mb-12 scroll-mt-28" id="pemblokiran">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                            <span
                                class="bg-emerald-100 dark:bg-emerald-900/50 text-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-lg font-bold">5</span>
                            Sanksi &amp; Konsekuensi
                        </h2>
                        <div
                            class="bg-white dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Prosedur Penanganan
                                    Pelanggaran</h3>
                            </div>
                            <div class="p-6">
                                <ol class="relative border-l border-slate-200 dark:border-slate-700 ml-3 space-y-8">
                                    <li class="mb-10 ml-6">
                                        <span
                                            class="absolute flex items-center justify-center w-8 h-8 bg-emerald-100 rounded-full -left-4 ring-8 ring-white dark:ring-surface-dark dark:bg-emerald-900">
                                            <span
                                                class="material-icons-round text-primary text-sm">notifications</span>
                                        </span>
                                        <h3
                                            class="flex items-center mb-1 text-lg font-semibold text-slate-900 dark:text-white">
                                            Peringatan Pertama</h3>
                                        <p class="mb-2 text-base font-normal text-slate-500 dark:text-slate-400">Jika
                                            terdeteksi tingkat blokir tinggi dari user, kami akan mengirimkan notifikasi
                                            peringatan dan meminta peninjauan template pesan.</p>
                                    </li>
                                    <li class="mb-10 ml-6">
                                        <span
                                            class="absolute flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full -left-4 ring-8 ring-white dark:ring-surface-dark dark:bg-orange-900">
                                            <span class="material-icons-round text-orange-500 text-sm">pause</span>
                                        </span>
                                        <h3 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">
                                            Pembatasan Akun Sementara</h3>
                                        <p class="text-base font-normal text-slate-500 dark:text-slate-400">Jika
                                            pelanggaran berlanjut, limit pengiriman pesan harian akan diturunkan secara
                                            drastis oleh sistem WhatsApp secara otomatis.</p>
                                    </li>
                                    <li class="ml-6">
                                        <span
                                            class="absolute flex items-center justify-center w-8 h-8 bg-red-100 rounded-full -left-4 ring-8 ring-white dark:ring-surface-dark dark:bg-red-900">
                                            <span class="material-icons-round text-red-500 text-sm">block</span>
                                        </span>
                                        <h3 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Terminasi
                                            Permanen</h3>
                                        <p class="text-base font-normal text-slate-500 dark:text-slate-400">Akun akan
                                            diblokir permanen jika ditemukan pelanggaran berat atau berulang. Nomor
                                            telepon bisnis Anda akan dibanned dari ekosistem WhatsApp.</p>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </section>
                    <section class="mb-12 scroll-mt-28" id="pelaporan">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                            <span
                                class="bg-emerald-100 dark:bg-emerald-900/50 text-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-lg font-bold">6</span>
                            Pelaporan Pelanggaran
                        </h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            Jika Anda menerima pesan spam yang berasal dari infrastruktur Orbit, atau mencurigai salah
                            satu klien kami melakukan pelanggaran, harap laporkan segera kepada kami.
                        </p>
                        <div class="bg-slate-900 dark:bg-black rounded-xl p-8 text-center relative overflow-hidden">
                            <div
                                class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-primary/20 blur-3xl">
                            </div>
                            <div class="relative z-10">
                                <h3 class="text-xl font-bold text-white mb-2">Laporkan Penyalahgunaan</h3>
                                <p class="text-slate-300 mb-6 max-w-lg mx-auto">Sertakan bukti screenshot dan nomor
                                    pengirim agar tim investigasi kami dapat menindaklanjuti dalam 1x24 jam.</p>
                                <a class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-slate-900 bg-primary hover:bg-emerald-400 transition-colors"
                                    href="mailto:admin@orbitapi.com">
                                    <span class="material-icons-round mr-2">email</span>
                                    admin@orbitapi.com
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
    <section class="bg-primary py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Siap Mengirim Pesan Secara Etis &amp; Efektif?</h2>
            <p class="text-emerald-50 mb-8 text-lg">Bergabunglah dengan ratusan bisnis yang tumbuh dengan komunikasi
                pelanggan yang sehat menggunakan Orbit API.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if (Route::has('register'))
                    <a class="bg-white text-primary hover:bg-slate-100 px-8 py-3 rounded-lg font-bold shadow-lg transition-colors"
                        href="{{ route('register') }}">
                        Mulai Sekarang - Gratis
                    </a>
                @endif
                <a class="border-2 border-white text-white hover:bg-emerald-600 px-8 py-3 rounded-lg font-bold transition-colors"
                    href="{{ route('docs.index') }}">
                    Baca Dokumentasi
                </a>
            </div>
        </div>
    </section>
    <footer class="bg-gray-900 dark:bg-black text-white pt-16 pb-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 lg:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('Image/logo-wa-api-white.png') }}" alt="Orbit API Logo"
                            class="h-10 w-auto">
                        <span class="font-bold text-xl tracking-tight">Orbit API</span>
                    </div>
                    <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                        Platform WhatsApp API terdepan untuk bisnis Indonesia. Solusi lengkap untuk automasi komunikasi
                        bisnis Anda.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-4">Product</h3>
                    <ul class="space-y-3">
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="{{ route('home') }}#features">Features</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="{{ route('home') }}#pricing">Pricing</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="{{ route('docs.index') }}">API Docs</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="#">Changelog</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-4">Company</h3>
                    <ul class="space-y-3">
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors" href="#">About
                                Us</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="#">Blog</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="#">Careers</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="{{ route('home') }}#contact">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-4">Legal</h3>
                    <ul class="space-y-3">
                        <li><a class="text-white text-sm font-medium"
                                href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="{{ route('terms-of-service') }}">Terms of Service</a></li>
                        <li><a class="text-gray-400 hover:text-white text-sm transition-colors"
                                href="{{ route('anti-spam-policy') }}">Anti-Spam Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} Orbit API. All rights reserved.</p>
                <div class="flex space-x-4">
                    <a class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition-all"
                        href="#">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z">
                            </path>
                        </svg>
                    </a>
                    <a class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition-all"
                        href="#">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <script nonce="{{ config('app.csp_nonce') }}">
        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('aside nav a');
            window.addEventListener('scroll', () => {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (pageYOffset >= (sectionTop - 150)) {
                        current = section.getAttribute('id');
                    }
                });
                navLinks.forEach(a => {
                    a.classList.remove('text-primary', 'bg-emerald-50', 'dark:bg-emerald-900/20',
                        'border-primary');
                    a.classList.add('text-slate-600', 'border-transparent', 'hover:text-primary');
                    if (a.getAttribute('href').includes(current)) {
                        a.classList.add('text-primary', 'bg-emerald-50', 'dark:bg-emerald-900/20',
                            'border-primary');
                        a.classList.remove('text-slate-600', 'border-transparent',
                            'hover:text-primary');
                    }
                });
            });
        });
    </script>

</body>

</html>

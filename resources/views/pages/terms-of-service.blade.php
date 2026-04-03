<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Terms of Service - {{ config('app.name', 'Orbit WhatsApp API') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-sans antialiased transition-colors duration-200">
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
    <div class="bg-primary/5 dark:bg-primary/10 py-16 sm:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl mb-4">
                Ketentuan Layanan
            </h1>
            <p class="text-lg text-muted-light dark:text-muted-dark max-w-2xl mx-auto">
                Harap baca dokumen ini dengan seksama sebelum menggunakan layanan Orbit WhatsApp API. Dokumen ini
                mengatur penggunaan Anda atas platform kami.
            </p>
            <div
                class="mt-6 inline-flex items-center text-sm text-muted-light dark:text-muted-dark bg-surface-light dark:bg-surface-dark px-4 py-2 rounded-full shadow-sm border border-gray-200 dark:border-gray-700">
                <span class="material-icons-round text-primary mr-2 text-base">update</span>
                Terakhir diperbarui: {{ date('d F Y') }}
            </div>
        </div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div
            class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-xl p-8 sm:p-12 border border-gray-100 dark:border-gray-800">
            <div
                class="prose prose-lg prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-muted-light dark:prose-p:text-muted-dark prose-li:text-muted-light dark:prose-li:text-muted-dark prose-strong:text-gray-900 dark:prose-strong:text-white max-w-none">
                <h3>1. Pendahuluan</h3>
                <p>
                    Selamat datang di Orbit WhatsApp API. Dengan mendaftar atau menggunakan layanan kami ("Layanan"),
                    Anda setuju untuk terikat oleh Ketentuan Layanan ini ("Ketentuan"). Jika Anda tidak setuju dengan
                    bagian mana pun dari ketentuan ini, maka Anda tidak boleh mengakses Layanan.
                </p>
                <h3>2. Definisi</h3>
                <ul class="marker:text-primary">
                    <li><strong>"Akun"</strong> berarti akun unik yang dibuat untuk Anda guna mengakses Layanan kami.
                    </li>
                    <li><strong>"Perusahaan"</strong> (disebut sebagai "Kami", "Kita", atau "Milik Kami" dalam
                        Perjanjian ini) mengacu pada Orbit Tech Indonesia.</li>
                    <li><strong>"Layanan"</strong> mengacu pada Website dan API WhatsApp Gateway yang disediakan oleh
                        Orbit.</li>
                    <li><strong>"Anda"</strong> berarti individu yang mengakses atau menggunakan Layanan, atau
                        perusahaan, atau badan hukum lain atas nama mana individu tersebut mengakses atau menggunakan
                        Layanan.</li>
                </ul>
                <h3>3. Tanggung Jawab Pengguna</h3>
                <p>
                    Sebagai pengguna layanan Orbit WhatsApp API, Anda bertanggung jawab penuh atas penggunaan akun Anda.
                    Anda setuju untuk:
                </p>
                <div class="bg-primary/5 dark:bg-primary/10 rounded-lg p-6 my-6 border-l-4 border-primary">
                    <ul class="list-none pl-0 space-y-3 my-0">
                        <li class="flex items-start">
                            <span class="material-icons-round text-primary mr-3 mt-1 text-sm">check_circle</span>
                            <span>Menjaga kerahasiaan kredensial API dan kata sandi akun Anda.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-round text-primary mr-3 mt-1 text-sm">check_circle</span>
                            <span>Mematuhi semua kebijakan WhatsApp/Meta yang berlaku terkait pengiriman pesan
                                bisnis.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-round text-primary mr-3 mt-1 text-sm">check_circle</span>
                            <span>Hanya mengirim pesan kepada penerima yang telah memberikan persetujuan
                                (opt-in).</span>
                        </li>
                    </ul>
                </div>
                <h3>4. Konten yang Dilarang</h3>
                <p>
                    Anda dilarang keras menggunakan Layanan untuk mengirimkan, menyimpan, atau memproses konten yang:
                </p>
                <ul class="marker:text-red-500">
                    <li>Melanggar hukum yang berlaku di Indonesia maupun hukum internasional.</li>
                    <li>Mengandung unsur penipuan, phishing, atau spamming.</li>
                    <li>Menyebarkan ujaran kebencian, diskriminasi, atau konten kekerasan.</li>
                    <li>Melanggar hak kekayaan intelektual pihak lain.</li>
                    <li>Mendistribusikan malware, virus, atau kode berbahaya lainnya.</li>
                </ul>
                <h3>5. Pembayaran dan Berlangganan</h3>
                <p>
                    Layanan tertentu disediakan berdasarkan langganan berbayar. Anda akan ditagih di muka secara
                    berulang dan berkala (misalnya bulanan atau tahunan). Jika pembayaran gagal atau tertunda, kami
                    berhak menangguhkan akses Anda ke Layanan hingga pembayaran diselesaikan.
                </p>
                <h3>6. Batasan Tanggung Jawab</h3>
                <p>
                    Sejauh diizinkan oleh hukum yang berlaku, Orbit WhatsApp API tidak akan bertanggung jawab atas
                    kerugian tidak langsung, insidental, khusus, konsekuensial, atau hukuman, termasuk namun tidak
                    terbatas pada, kehilangan keuntungan, data, penggunaan, goodwill, atau kerugian tidak berwujud
                    lainnya, yang diakibatkan oleh:
                </p>
                <ol>
                    <li>Akses Anda ke atau penggunaan atau ketidakmampuan untuk mengakses atau menggunakan Layanan;</li>
                    <li>Perilaku atau konten pihak ketiga mana pun di Layanan;</li>
                    <li>Konten apa pun yang diperoleh dari Layanan; dan</li>
                    <li>Akses, penggunaan, atau perubahan transmisi atau konten Anda yang tidak sah.</li>
                </ol>
                <h3>7. Penghentian Layanan</h3>
                <p>
                    Kami dapat mengakhiri atau menangguhkan akun Anda segera, tanpa pemberitahuan atau kewajiban
                    sebelumnya, dengan alasan apa pun, termasuk namun tidak terbatas pada jika Anda melanggar Ketentuan.
                    Setelah pengakhiran, hak Anda untuk menggunakan Layanan akan segera berhenti.
                </p>
                <h3>8. Perubahan Ketentuan</h3>
                <p>
                    Kami berhak, atas kebijakan kami sendiri, untuk mengubah atau mengganti Ketentuan ini setiap saat.
                    Jika revisi bersifat material, kami akan berusaha memberikan pemberitahuan setidaknya 30 hari
                    sebelum ketentuan baru berlaku. Apa yang dimaksud dengan perubahan material akan ditentukan atas
                    kebijakan kami sendiri.
                </p>
                <hr class="border-gray-200 dark:border-gray-700 my-8" />
                <h3>Hubungi Kami</h3>
                <p>
                    Jika Anda memiliki pertanyaan tentang Ketentuan Layanan ini, silakan hubungi kami:
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mt-6 not-prose">
                    <a class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm"
                        href="mailto:admin@orbitwaapi.site">
                        <span class="material-icons-round mr-2 text-primary">email</span>
                        admin@orbitwaapi.site
                    </a>
                    <a class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-primary hover:bg-primary-dark shadow-sm transition-all hover:-translate-y-0.5"
                        href="{{ route('home') }}#contact">
                        <span class="material-icons-round mr-2">chat</span>
                        Hubungi Support
                    </a>
                </div>
            </div>
        </div>
    </div>
    <x-public-footer />

</body>

</html>

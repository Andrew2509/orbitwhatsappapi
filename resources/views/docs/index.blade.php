<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Dokumentasi API Orbit - Integrasi WhatsApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Space+Grotesk:wght@500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&amp;display=swap" rel="stylesheet"/>
    <style>
        .code-preview::-webkit-scrollbar { height: 8px; width: 8px; }
        .code-preview::-webkit-scrollbar-track { background: #1e293b; border-radius: 4px; }
        .code-preview::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .code-preview::-webkit-scrollbar-thumb:hover { background: #475569; }
        .endpoint-badge { font-size: 0.7rem; font-weight: 800; padding: 0.1rem 0.4rem; border-radius: 4px; text-transform: uppercase; }
        .method-get { background: #e0f2fe; color: #0369a1; }
        .method-post { background: #ecfdf5; color: #047857; }
        .nav-link-active { background: #10B9811a; color: #10B981; border-left: 4px solid #10B981; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-primary selection:text-white">
<nav class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-8">
                <a class="flex items-center space-x-3" href="{{ route('home') }}">
                    <img src="{{ asset('Image/logo-wa-api-black.png') }}" alt="Orbit API Logo" class="h-8 w-auto">
                    <span class="font-display font-bold text-lg tracking-tight text-slate-900">Orbit API <span class="text-slate-400 font-normal ml-1 text-sm">Docs</span></span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-1 text-sm font-medium text-slate-600">
                    <a class="px-3 py-2 hover:text-primary transition-colors" href="#intro">Pengenalan</a>
                    <a class="px-3 py-2 text-primary border-b-2 border-primary" href="#endpoints">Endpoints</a>
                    <a class="px-3 py-2 hover:text-primary transition-colors" href="#scalar">Scalar API</a>
                    <a class="px-3 py-2 hover:text-primary transition-colors" href="#code-samples">Implementasi</a>
                </div>
                <div class="h-6 w-px bg-slate-200 mx-2 hidden md:block"></div>
                @auth
                    <a class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-800 transition-colors shadow-sm" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="text-sm font-semibold text-slate-700 hover:text-primary transition-colors" href="{{ route('login') }}">Log In</a>
                    <a class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-800 transition-colors shadow-sm" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="max-w-screen-2xl mx-auto flex items-start">
    <aside class="hidden lg:block w-72 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto border-r border-slate-200 bg-white p-6 pb-20">
        <nav class="space-y-8">
            <div>
                <h5 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Panduan Memulai</h5>
                <ul class="space-y-1">
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#intro">1. Pengenalan API</a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#get-key">2. Mendapatkan API Key</a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#base-url">3. Base URL</a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#auth">4. Autentikasi</a></li>
                </ul>
            </div>
            <div>
                <h5 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Autentikasi & Akun</h5>
                <ul class="space-y-1">
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#auth-register">Daftar Akun <span class="endpoint-badge method-post">POST</span></a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#auth-login">Login Token <span class="endpoint-badge method-post">POST</span></a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#auth-me">Profil Saya <span class="endpoint-badge method-get">GET</span></a></li>
                </ul>
            </div>
            <div>
                <h5 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Pesan WhatsApp</h5>
                <ul class="space-y-1">
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#send-msg">Kirim Pesan <span class="endpoint-badge method-post">POST</span></a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#msg-history">Riwayat Pesan <span class="endpoint-badge method-get">GET</span></a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#msg-status">Cek Status <span class="endpoint-badge method-get">GET</span></a></li>
                </ul>
            </div>
            <div>
                <h5 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Manajemen Device</h5>
                <ul class="space-y-1">
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#device-list">Daftar Device <span class="endpoint-badge method-get">GET</span></a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 justify-between items-center" href="#device-detail">Detail Device <span class="endpoint-badge method-get">GET</span></a></li>
                </ul>
            </div>
            <div>
                <h5 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Development Tools</h5>
                <ul class="space-y-1">
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#curl">Contoh cURL</a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#scalar">Scalar API Documentation</a></li>
                    <li><a class="block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900" href="#code-samples">Contoh Implementasi</a></li>
                </ul>
            </div>
        </nav>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">

            <!-- 1. PENGENALAN API -->
            <section class="mb-24 scroll-mt-24" id="intro">
                <div class="flex items-center space-x-2 text-sm text-primary mb-4 font-medium">
                    <span>Dokumentasi</span>
                    <span class="material-icons-round text-xs">chevron_right</span>
                    <span>Pengenalan</span>
                </div>
                <h1 class="text-4xl font-display font-bold text-slate-900 mb-6">1. Pengenalan WhatsApp Bot API</h1>
                <p class="text-lg text-slate-600 leading-relaxed mb-8">
                    WhatsApp Bot API Orbit adalah layanan antarmuka pemrograman aplikasi (API) RESTful yang memungkinkan developer untuk mengintegrasikan fungsionalitas WhatsApp ke dalam sistem mereka dengan cepat dan stabil. Konsepnya serupa dengan layanan WhatsApp API populer seperti Fonnte.
                </p>
                <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-6 text-xl">Kegunaan Utama API Kami:</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-emerald-100 p-2 rounded-lg text-primary"><span class="material-icons-round text-xl">chat</span></div>
                            <div><p class="font-bold text-slate-800">Kirim Pesan Otomatis</p><p class="text-sm text-slate-500">Kirim balasan chat atau broadcast otomatis kepada pelanggan.</p></div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 p-2 rounded-lg text-blue-500"><span class="material-icons-round text-xl">notifications</span></div>
                            <div><p class="font-bold text-slate-800">Notifikasi Sistem</p><p class="text-sm text-slate-500">Berikan update status pesanan atau transaksi secara real-time.</p></div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-amber-100 p-2 rounded-lg text-amber-500"><span class="material-icons-round text-xl">lock</span></div>
                            <div><p class="font-bold text-slate-800">Verifikasi OTP</p><p class="text-sm text-slate-500">Kirim kode verifikasi rahasia yang aman via WhatsApp.</p></div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-100 p-2 rounded-lg text-purple-500"><span class="material-icons-round text-xl">campaign</span></div>
                            <div><p class="font-bold text-slate-800">Pesan Promosi</p><p class="text-sm text-slate-500">Kelola kampanye marketing WhatsApp untuk meningkatkan konversi.</p></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. MENDAPATKAN API KEY -->
            <section class="mb-24 scroll-mt-24" id="get-key">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-8">2. Cara Mendapatkan API Key</h2>
                <p class="text-slate-600 mb-8">Ikuti langkah-langkah berikut untuk mendapatkan otorisasi akses API:</p>
                <div class="grid md:grid-cols-4 gap-4">
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">1</div>
                        <p class="text-sm font-bold text-slate-700">Login</p>
                        <p class="text-[10px] text-slate-500 mt-1">Masuk ke Dashboard Orbit</p>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">2</div>
                        <p class="text-sm font-bold text-slate-700">Menu API</p>
                        <p class="text-[10px] text-slate-500 mt-1">Buka menu API Keys di sidebar</p>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">3</div>
                        <p class="text-sm font-bold text-slate-700">Generate</p>
                        <p class="text-[10px] text-slate-500 mt-1">Klik tombol Generate New Key</p>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">4</div>
                        <p class="text-sm font-bold text-slate-700">Copy</p>
                        <p class="text-[10px] text-slate-500 mt-1">Salin Token untuk digunakan</p>
                    </div>
                </div>
            </section>

            <!-- 3. BASE URL -->
            <section class="mb-24 scroll-mt-24" id="base-url">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">3. Base URL</h2>
                <p class="text-slate-600 mb-6">Gunakan alamat URL dasar berikut untuk setiap pemanggilan endpoint API:</p>
                <div class="bg-slate-900 rounded-2xl p-6 flex items-center justify-between border border-emerald-500/30 shadow-glow">
                    <code class="text-emerald-400 font-mono text-xl">{{ url('/api/v1') }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ url('/api/v1') }}')" class="p-2 bg-slate-800 text-slate-400 rounded-lg hover:text-white transition-colors" title="Salin URL">
                        <span class="material-icons-round text-sm">content_copy</span>
                    </button>
                </div>
            </section>

            <!-- 4. AUTENTIKASI -->
            <section class="mb-24 scroll-mt-24" id="auth">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">4. Autentikasi API</h2>
                <p class="text-slate-600 mb-8 leading-relaxed">
                    Setiap request harus menyertakan API Key dalam Header Authorization dengan format **Bearer Token**.
                </p>
                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 font-bold text-slate-700 text-sm">Contoh Header HTTP</div>
                    <div class="p-6">
                        <pre class="bg-slate-900 p-6 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto leading-loose"><code>GET /v1/devices HTTP/1.1
Host: api.orbitapi.com
<span class="text-yellow-400">Authorization:</span> Bearer YOUR_SECRET_API_KEY
Content-Type: application/json</code></pre>
                    </div>
                </div>
            </section>

            <!-- 5. ENDPOINT REFERENSI -->
            <div id="endpoints" class="border-t border-slate-200 pt-20">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-10">5. Referensi Endpoint API</h2>

                <!-- AUTH SECTIONS -->
                <div class="mb-20 space-y-20">
                    <h3 class="text-xl font-bold text-slate-800 border-l-4 border-primary pl-4">Autentikasi & Akun</h3>
                    
                    <section class="scroll-mt-24" id="auth-register">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-post px-3 py-1 text-sm font-bold rounded-lg shadow-sm">POST</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/auth/register</code>
                        </div>
                        <p class="text-slate-600 mb-8">Membuat akun baru untuk mendapatkan akses ke dashboard dan API.</p>
                        <div class="grid lg:grid-cols-2 gap-8">
                            <pre class="bg-[#0b141a] p-6 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "rahasia123",
  "password_confirmation": "rahasia123"
}</code></pre>
                            <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "message": "User successfully registered",
  "user": { "id": 1, "name": "Budi", "email": "budi@..." }
}</code></pre>
                        </div>
                    </section>

                    <section class="scroll-mt-24" id="auth-login">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-post px-3 py-1 text-sm font-bold rounded-lg shadow-sm">POST</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/auth/login</code>
                        </div>
                        <p class="text-slate-600 mb-8">Login untuk mendapatkan JWT Bearer Token.</p>
                        <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "access_token": "eyJhbGciOiJIUz...",
  "token_type": "bearer",
  "expires_in": 3600
}</code></pre>
                    </section>
                </div>

                <!-- MESSAGES SECTION -->
                <div class="mb-20 space-y-20">
                    <h3 class="text-xl font-bold text-slate-800 border-l-4 border-primary pl-4">Pesan WhatsApp</h3>

                    <section class="scroll-mt-24" id="send-msg">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-post px-3 py-1 text-sm font-bold rounded-lg shadow-sm">POST</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/v1/messages/send</code>
                        </div>
                        <p class="text-slate-600 mb-8 leading-relaxed">Mengirimkan pesan teks atau media (gambar) ke nomor WhatsApp.</p>

                        <h4 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">Parameter (JSON Body)</h4>
                        <div class="overflow-hidden border border-slate-100 rounded-2xl mb-10">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50"><tr><th class="px-6 py-4 text-left font-bold text-slate-500">Nama</th><th class="px-6 py-4 text-left font-bold text-slate-500">Tipe</th><th class="px-6 py-4 text-left font-bold text-slate-500">Wajib</th><th class="px-6 py-4 text-left font-bold text-slate-500">Keterangan</th></tr></thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <tr><td class="px-6 py-4 font-mono text-primary font-bold">to</td><td class="px-6 py-4">string</td><td class="px-6 py-4 text-red-500 font-bold">Ya</td><td class="px-6 py-4 text-slate-500">Nomor tujuan (format internasional: 628...)</td></tr>
                                    <tr><td class="px-6 py-4 font-mono text-primary font-bold">message</td><td class="px-6 py-4">string</td><td class="px-6 py-4 text-red-500 font-bold">Ya</td><td class="px-6 py-4 text-slate-500">Isi pesan teks</td></tr>
                                    <tr><td class="px-6 py-4 font-mono text-primary font-bold">file</td><td class="px-6 py-4">string (URL)</td><td class="px-6 py-4 italic text-slate-400">Opsional</td><td class="px-6 py-4 text-slate-500">URL publik gambar untuk dikirim</td></tr>
                                    <tr><td class="px-6 py-4 font-mono text-primary font-bold">device_id</td><td class="px-6 py-4">int</td><td class="px-6 py-4 italic text-slate-400">Opsional</td><td class="px-6 py-4 text-slate-500">ID Device spesifik</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="grid lg:grid-cols-2 gap-8">
                            <pre class="bg-[#0b141a] p-6 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "to": "6285218574781",
  "message": "Halo dari Orbit API! 🚀",
  "file": "https://orbit.../logo.png"
}</code></pre>
                            <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "message_status": "Success",
  "data": {
    "message_id": "MSG-30005-20260314",
    "status": "sent"
  }
}</code></pre>
                        </div>
                    </section>

                    <section class="scroll-mt-24" id="msg-history">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-get px-3 py-1 text-sm font-bold rounded-lg shadow-sm">GET</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/v1/messages</code>
                        </div>
                        <p class="text-slate-600 mb-8 leading-relaxed">Melihat daftar riwayat pesan yang telah dikirim.</p>
                        <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "data": [
    { "id": 1, "external_id": "MSG-30001-20260314", "status": "read" }
  ],
  "total": 120
}</code></pre>
                    </section>

                    <section class="scroll-mt-24" id="msg-status">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-get px-3 py-1 text-sm font-bold rounded-lg shadow-sm">GET</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/v1/messages/{id}/status</code>
                        </div>
                        <p class="text-slate-600 mb-8 leading-relaxed">Cek detail status pengiriman pesan tertentu.</p>
                        <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "message_id": "MSG-30005-20260314",
  "status": "delivered",
  "sent_at": "2026-03-14T17:37:26Z",
  "delivered_at": "2026-03-14T17:37:30Z"
}</code></pre>
                    </section>
                </div>

                <!-- DEVICES SECTION -->
                <div class="mb-20 space-y-20">
                    <h3 class="text-xl font-bold text-slate-800 border-l-4 border-primary pl-4">Manajemen Device</h3>

                    <section class="scroll-mt-24" id="device-list">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-get px-3 py-1 text-sm font-bold rounded-lg shadow-sm">GET</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/v1/devices</code>
                        </div>
                        <p class="text-slate-600 mb-8">Melihat daftar perangkat WhatsApp Anda.</p>
                        <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>[
  { "id": 1, "name": "Device Utama", "status": "connected" }
]</code></pre>
                    </section>

                    <section class="scroll-mt-24" id="device-detail">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="endpoint-badge method-get px-3 py-1 text-sm font-bold rounded-lg shadow-sm">GET</span>
                            <code class="text-lg font-mono font-bold text-slate-800">/v1/devices/{id}</code>
                        </div>
                        <p class="text-slate-600 mb-8">Melihat detail spesifik dari satu perangkat.</p>
                        <pre class="bg-[#0b141a] p-6 rounded-2xl text-blue-300 font-mono text-xs overflow-x-auto border border-slate-800"><code>{
  "id": 1,
  "name": "Samsung S21",
  "phone_number": "62812345678",
  "status": "connected"
}</code></pre>
                    </section>
                </div>
            </div>

            <!-- 6. cURL EXAMPLES -->
            <section class="mb-24 scroll-mt-24 border-t border-slate-200 pt-20" id="curl">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-8">6. Contoh Request Menggunakan cURL</h2>
                <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
                    <p class="text-slate-600 text-sm mb-6">Gunakan perintah terminal berikut untuk pengujian cepat tanpa software tambahan:</p>
<pre class="bg-slate-900 p-8 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto leading-relaxed shadow-lg"><code>curl -X POST {{ url('/api/v1/messages/send') }} \
-H "Authorization: Bearer YOUR_API_KEY" \
-H "Content-Type: application/json" \
-d '{
  "to": "628123456789",
  "message": "Test cURL Orbit"
}'</code></pre>
                </div>
            </section>

            <!-- 7. SCALAR API DOCS -->
            <section class="mb-24 scroll-mt-24 border-t border-slate-200 pt-20" id="scalar">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-8">7. Scalar API Documentation</h2>

                <div class="grid md:grid-cols-2 gap-8 mb-10">
                    <div class="space-y-6">
                        <p class="text-slate-600 leading-relaxed">
                            Gunakan file spesifikasi API kami untuk mengimpor seluruh endpoint ke aplikasi API client favorit Anda (seperti NativeRest atau Postman) tanpa perlu mengetik manual.
                        </p>
                        <div class="flex flex-col space-y-4">
                            <div class="flex items-start space-x-3 text-sm text-slate-700">
                                <span class="w-6 h-6 bg-primary/10 text-primary flex items-center justify-center rounded-full text-xs font-bold shrink-0 mt-0.5">1</span>
                                <div>
                                    <p class="font-bold">Unduh Spesifikasi API</p>
                                    <p class="text-xs text-slate-500">Klik tombol di bawah untuk mendapatkan file <code>openapi.json</code>.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 text-sm text-slate-700">
                                <span class="w-6 h-6 bg-primary/10 text-primary flex items-center justify-center rounded-full text-xs font-bold shrink-0 mt-0.5">2</span>
                                <div>
                                    <p class="font-bold">Scroll ke Scalar API</p>
                                    <p class="text-xs text-slate-500">Scroll ke bawah untuk melihat antarmuka Scalar yang sudah kami sediakan.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 text-sm text-slate-700">
                                <span class="w-6 h-6 bg-primary/10 text-primary flex items-center justify-center rounded-full text-xs font-bold shrink-0 mt-0.5">3</span>
                                <div>
                                    <p class="font-bold">PENTING: Impor File JSON</p>
                                    <p class="text-xs text-slate-500">Pastikan Anda mengklik tombol <b>Import</b> pada Scalar di bawah, lalu pilih file JSON yang baru saja diunduh agar seluruh endpoint muncul.</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ asset('openapi.json') }}" download class="inline-flex items-center space-x-2 bg-slate-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-800 transition-all shadow-glow hover:shadow-glow-emerald">
                            <span class="material-icons-round">download</span>
                            <span>Download JSON untuk Import</span>
                        </a>
                    </div>
                    <div class="bg-emerald-50/50 rounded-3xl p-8 border border-emerald-100 flex flex-col justify-center items-center text-center">
                        <div class="bg-emerald-100 text-primary p-3 rounded-2xl mb-4">
                            <span class="material-icons-round text-3xl">auto_awesome</span>
                        </div>
                        <p class="font-bold text-slate-800 text-lg">Auto-Populate Ready</p>
                        <p class="text-sm text-slate-600 mt-2 max-w-[250px]">File ini sudah dilengkapi dengan contoh body JSON sehingga Anda tidak perlu mengetik lagi secara manual.</p>
                    </div>
                </div>

                <div id="scalar-container" class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-xl shadow-slate-200/50 relative">
                    <button id="fullscreen-btn" class="absolute top-4 right-4 z-[110] bg-white/90 backdrop-blur-sm border border-slate-200 p-2 rounded-xl text-slate-600 hover:text-primary transition-all shadow-lg hover:shadow-xl" title="Penuhi Layar">
                        <span class="material-icons-round">fullscreen</span>
                    </button>
                    <div class="relative w-full overflow-hidden" style="padding-top: 60%;"> <!-- Slightly taller for Scalar -->
                        <iframe
                            id="scalar-iframe"
                            src="https://client.scalar.com/@local/default/document/orbit-whatsapp-api-(fixed)/overview"
                            class="absolute top-0 left-0 w-full h-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-800 text-sm flex items-start space-x-3">
                    <span class="material-icons-round text-emerald-500">info</span>
                    <p>Dokumentasi Scalar memberikan antarmuka interaktif yang lebih lengkap untuk mencoba setiap endpoint API kami secara langsung.</p>
                </div>
            </section>

            <!-- 8. IMPLEMENTASI BAHASA PEMROGRAMAN -->
            <section class="mb-24 scroll-mt-24 border-t border-slate-200 pt-20" id="code-samples">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-8">8. Contoh Implementasi Sederhana</h2>

                <div class="space-y-6">
                    <div class="flex flex-wrap gap-2">
                        <button id="btn-php" class="lang-btn px-6 py-2 rounded-xl text-sm font-bold transition-all bg-primary text-white">PHP (Laravel/Native)</button>
                        <button id="btn-js" class="lang-btn px-6 py-2 rounded-xl text-sm font-bold transition-all bg-slate-200 text-slate-600">JavaScript (Fetch/Node)</button>
                        <button id="btn-py" class="lang-btn px-6 py-2 rounded-xl text-sm font-bold transition-all bg-slate-200 text-slate-600">Python (Requests)</button>
                    </div>

                    <div id="code-php" class="lang-code animate-in fade-in slide-in-from-top-2 duration-300">
<pre class="bg-slate-900 p-8 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto leading-relaxed border border-white/10 shadow-xl"><code><span class="text-slate-500">// Menggunakan Laravel Http Client</span>
use Illuminate\Support\Facades\Http;

$response = Http::withToken('YOUR_API_KEY')
    ->post('{{ url('/api/v1/messages/send') }}', [
        'to' => '6285218574781',
        'message' => 'Halo dari Orbit API! 🚀'
    ]);

return $response->json();</code></pre>
                    </div>

                    <div id="code-js" class="lang-code hidden animate-in fade-in slide-in-from-top-2 duration-300">
<pre class="bg-slate-900 p-8 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto leading-relaxed border border-white/10 shadow-xl"><code><span class="text-slate-500">// Menggunakan Fetch API</span>
const response = await fetch('{{ url('/api/v1/messages/send') }}', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer YOUR_API_KEY',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        to: "6285218574781",
        message: "Hello dari JavaScript!"
    })
});

const result = await response.json();
console.log(result);</code></pre>
                    </div>

                    <div id="code-py" class="lang-code hidden animate-in fade-in slide-in-from-top-2 duration-300">
<pre class="bg-slate-900 p-8 rounded-2xl text-emerald-400 font-mono text-xs overflow-x-auto leading-relaxed border border-white/10 shadow-xl"><code><span class="text-slate-500"># Menggunakan library 'requests'</span>
import requests

url = "{{ url('/api/v1/messages/send') }}"
headers = {
    "Authorization": "Bearer YOUR_API_KEY",
    "Content-Type": "application/json"
}
data = {
    "to": "6285218574781",
    "message": "Hello from Python!"
}

response = requests.post(url, headers=headers, json=data)
print(response.json())</code></pre>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <div class="mt-32 pt-10 border-t border-slate-200 text-center text-slate-400 text-xs">
                <p>© {{ date('Y') }} WhatsApp Bot API Orbit. Semua hak cipta dilindungi undang-undang.</p>
            </div>
        </div>
    </main>
</div>

<!-- Scripts (Alpine and Custom) -->
<script nonce="{{ $csp_nonce }}">
    document.addEventListener('DOMContentLoaded', () => {
        // Fullscreen Toggle
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', () => window.toggleFullscreen());
        }

        // Language Switcher Logic
        const langBtns = document.querySelectorAll('.lang-btn');
        const langCodes = document.querySelectorAll('.lang-code');

        langBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const lang = btn.id.replace('btn-', '');

                // Update buttons
                langBtns.forEach(b => {
                    b.classList.remove('bg-primary', 'text-white');
                    b.classList.add('bg-slate-200', 'text-slate-600');
                });
                btn.classList.add('bg-primary', 'text-white');
                btn.classList.remove('bg-slate-200', 'text-slate-600');

                // Update code blocks
                langCodes.forEach(code => code.classList.add('hidden'));
                const targetCode = document.getElementById('code-' + lang);
                if (targetCode) targetCode.classList.remove('hidden');
            });
        });

        // Scroll Spy
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('aside nav a');
            let current = "";

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('nav-link-active');
                if (link.getAttribute('href').includes(current) && current !== "") {
                    link.classList.add('nav-link-active');
                }
            });
        });
    });
</script>
</body>
</html>

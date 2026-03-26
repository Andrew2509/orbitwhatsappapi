<!DOCTYPE html>
<html lang="id">
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporkan Penyalahgunaan - OrbitWA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Laporkan Penyalahgunaan</h1>
                <p class="text-gray-500 mt-2">Bantu kami menjaga layanan tetap bersih dari spam dan penipuan</p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('abuse.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">Informasi Pelapor</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="reporter_name" value="{{ old('reporter_name') }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="reporter_email" value="{{ old('reporter_email') }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp (Opsional)</label>
                            <input type="text" name="reporter_phone" value="{{ old('reporter_phone') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="628123456789">
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <h3 class="font-semibold text-gray-900 mb-3">Nomor yang Dilaporkan</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp *</label>
                        <input type="text" name="reported_phone" value="{{ old('reported_phone') }}" required
                            class="w-full px-4 py-2 rounded-lg border border-red-300 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="628123456789">
                        <p class="text-xs text-gray-500 mt-1">Masukkan nomor yang mengirim spam/penipuan kepada Anda</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pelaporan *</label>
                    <select name="reason" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Pilih alasan...</option>
                        @foreach($reasons as $key => $label)
                        <option value="{{ $key }}" {{ old('reason') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                    <textarea name="description" rows="4" required minlength="20"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Jelaskan apa yang terjadi...">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimal 20 karakter</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti (Opsional)</label>
                    <textarea name="evidence" rows="3"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Link screenshot, isi pesan, dll...">{{ old('evidence') }}</textarea>
                </div>

                <div class="flex items-start">
                    <input type="checkbox" required id="terms" class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan.
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                    Kirim Laporan
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-6">
                Laporan akan ditinjau oleh tim kami dalam 1-3 hari kerja.
            </p>
        </div>
    </div>
</body>
</html>

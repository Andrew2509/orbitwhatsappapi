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
    <title>Terima Kasih - OrbitWA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12">
    <div class="max-w-md mx-auto px-4 text-center">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Terima Kasih!</h1>
            <p class="text-gray-600 mb-6">
                Laporan Anda telah kami terima dan akan segera ditinjau oleh tim kami.
                Kami akan mengambil tindakan yang diperlukan.
            </p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500">
                    Tim kami akan meninjau laporan dalam <strong>1-3 hari kerja</strong>.
                </p>
            </div>
            <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>

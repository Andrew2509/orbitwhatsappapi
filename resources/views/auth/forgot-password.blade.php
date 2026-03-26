<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script nonce="{{ config('app.csp_nonce') ?? '' }}">
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
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-blockingmode="auto" data-cbid="e34b0360-5226-4315-9b8e-52f41f19761b" type="text/javascript" nonce="{{ config('app.csp_nonce') ?? '' }}"></script>
    <!-- Google tag (gtag.js) -->
    <script nonce="{{ config('app.csp_nonce') ?? '' }}" src="https://www.googletagmanager.com/gtag/js?id=G-WFGB15WM4T"></script>
    <script nonce="{{ config('app.csp_nonce') ?? '' }}">
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-WFGB15WM4T');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - {{ config('app.name', 'Orbit WhatsApp API') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script nonce="{{ config('app.csp_nonce') ?? '' }}" type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
        import { getAuth, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('forgot-password-form');
            const submitBtn = document.getElementById('submit-btn');
            const statusMessage = document.getElementById('status-message');
            const errorMessage = document.getElementById('error-message');
            const loadingIcon = document.getElementById('loading-spinner');
            const buttonText = document.getElementById('button-text');

            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const email = document.getElementById('email').value;
                    
                    // Reset UI
                    statusMessage.classList.add('hidden');
                    errorMessage.classList.add('hidden');
                    submitBtn.disabled = true;
                    loadingIcon.classList.remove('hidden');
                    buttonText.innerText = 'Sending...';

                    try {
                        await sendPasswordResetEmail(auth, email);
                        
                        statusMessage.innerText = 'Kami telah mengirimkan tautan reset kata sandi ke email Anda melalui Firebase.';
                        statusMessage.classList.remove('hidden');
                        form.reset();
                    } catch (error) {
                        console.error('Firebase Reset Error:', error);
                        let msg = 'Gagal mengirim email reset. ';
                        
                        if (error.code === 'auth/user-not-found') {
                            msg += 'Email tidak terdaftar.';
                        } else if (error.code === 'auth/invalid-email') {
                            msg += 'Format email tidak valid.';
                        } else {
                            msg += error.message;
                        }
                        
                        errorMessage.innerText = msg;
                        errorMessage.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                        loadingIcon.classList.add('hidden');
                        buttonText.innerText = "{{ __('Email Password Reset Link') }}";
                    }
                });
            }
        });
    </script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%);
        }

        .input-field {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            background: #fff;
        }

        .input-field:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            color: white;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>

<body class="antialiased">
    <div class="h-screen flex overflow-hidden">
        <!-- Left Side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center p-6 lg:p-10 bg-white overflow-y-auto">
            <div class="max-w-sm mx-auto w-full">
                <!-- Logo -->
                <div class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('Image/logo-wa-api-black.png') }}" alt="Orbit API" class="h-12">
                    <span class="text-lg font-bold text-gray-800">Orbit WhatsApp API</span>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Forgot Password</h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </p>
                </div>

                <!-- Session Status (Laravel fallback) -->
                @if (session('status'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-xs font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Firebase Status Messages -->
                <div id="status-message" class="hidden mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-medium"></div>
                <div id="error-message" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs font-medium"></div>

                <!-- Password Reset Form -->
                <form id="forgot-password-form" method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="input-field" placeholder="user@company.com" required autofocus>
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submit-btn" class="btn-primary mt-2">
                        <svg id="loading-spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg id="envelope-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span id="button-text">{{ __('Email Password Reset Link') }}</span>
                    </button>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to login
                        </a>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-8 space-y-3">
                    <p class="text-center text-xs text-gray-500">
                        © {{ date('Y') }} Orbit API. Powered by Laravel & Tailwind CSS.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Logo & Text -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg flex-col justify-center items-center p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2">
            </div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2">
            </div>

            <div class="relative z-10 max-w-sm text-center">
                <!-- Globe / Icon -->
                <div class="w-32 h-32 mx-auto mb-6 relative">
                    <div class="absolute inset-0 bg-white/20 rounded-full backdrop-blur-sm"></div>
                    <div class="absolute inset-3 bg-gradient-to-br from-cyan-400 to-emerald-400 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-white mb-3">
                    Secure Account Recovery
                </h2>
                <div class="text-white/80 text-sm">
                    <p class="italic">"Get back to building amazing WhatsApp experiences."</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

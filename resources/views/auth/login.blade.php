<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <title>Login - {{ config('app.name', 'Orbit WhatsApp API') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script nonce="{{ $csp_nonce }}" type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

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
        const provider = new GoogleAuthProvider();

        window.signInWithGoogle = async () => {
            try {
                const result = await signInWithPopup(auth, provider);
                const user = result.user;
                const idToken = await user.getIdToken();

                const response = await fetch("{{ route('auth.firebase.login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id_token: idToken })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = data.redirect || '/dashboard';
                } else {
                    alert('Login failed: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Firebase Auth Error:', error);
                alert('Error during Google Sign-in: ' + error.message);
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const googleBtn = document.getElementById('google-login-btn');
            if (googleBtn) {
                googleBtn.addEventListener('click', window.signInWithGoogle);
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

        .btn-social {
            flex: 1;
            padding: 8px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-social:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #9ca3af;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
    </style>
</head>

<body class="antialiased">
    <div class="h-screen flex overflow-hidden">
        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center p-6 lg:p-10 bg-white overflow-y-auto">
            <div class="max-w-sm mx-auto w-full">
                <!-- Logo -->
                <div class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('Image/logo-wa-api-black.png') }}" alt="Orbit API" class="h-12">
                    <span class="text-lg font-bold text-gray-800">Orbit WhatsApp API</span>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-700 rounded-lg text-xs">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
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

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="input-field" placeholder="••••••••"
                            required>
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-3.5 h-3.5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                            <span class="text-xs text-gray-600">Remember Me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Forgot Password?</a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Sign In
                    </button>

                    <!-- Divider -->
                    <div class="divider">Or continue with</div>

                    <!-- Social Login -->
                    <div class="flex gap-3">
                        <button type="button" class="btn-social" id="google-login-btn">
                            <svg class="w-4 h-4" viewBox="0 0 24 24">
                                <path fill="#4285F4"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Google
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-5 space-y-3">
                    <div class="flex justify-center">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="text-[10px] font-medium text-gray-600">SSL Secured</span>
                        </div>
                    </div>
                    <p class="text-center text-xs text-gray-500">
                        © {{ date('Y') }} Orbit API. Powered by Laravel & Tailwind CSS.
                    </p>
                    <p class="text-center text-xs text-gray-500">
                        Don't have an account? <a href="{{ route('register') }}"
                            class="text-emerald-600 font-medium hover:underline">Sign up</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Logo & Text -->
        <div
            class="hidden lg:flex lg:w-1/2 gradient-bg flex-col justify-center items-center p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2">
            </div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2">
            </div>

            <div class="relative z-10 max-w-sm text-center">
                <!-- Globe -->
                <div class="w-32 h-32 mx-auto mb-6 relative">
                    <div class="absolute inset-0 bg-white/20 rounded-full backdrop-blur-sm"></div>
                    <div
                        class="absolute inset-3 bg-gradient-to-br from-cyan-400 to-emerald-400 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div
                        class="absolute -top-2 -right-2 w-10 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-white mb-3">
                    Join 1000+ developers<br>sending millions of messages.
                </h2>
                <div class="text-white/80 text-sm">
                    <p class="italic">"Our team scaled instantly. The reliability is unmatched."</p>
                    <p class="mt-1 font-medium text-xs">- Sarah J., CTO at TechFlow</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

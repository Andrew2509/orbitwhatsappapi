<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <title>Register - {{ config('app.name', 'Orbit WhatsApp API') }}</title>
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

        window.signInWithGoogle = () => {
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
                    alert('Registration failed: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Firebase Auth Error:', error);
                alert('Error during Google Sign-in: ' + error.message);
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const googleBtn = document.getElementById('google-register-btn');
            if (googleBtn) {
                googleBtn.addEventListener('click', window.signInWithGoogle);
            }
        });
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        html, body { height: 100%; overflow: hidden; }
        body { display: flex; flex-direction: column; }
        main { flex: 1; overflow: hidden; }

        .input-field {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            background: #fff;
        }
        .input-field:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
        }

        .btn-signup {
            background: #0d9488;
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-signup:hover { background: #0f766e; }

        .btn-social {
            flex: 1;
            padding: 7px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-social.white { background: #fff; color: #374151; }
        .btn-social.white:hover { background: #f9fafb; }
        .btn-social.dark { background: #1f2937; color: #fff; border-color: #1f2937; }
        .btn-social.dark:hover { background: #374151; }

        .strength-bar {
            height: 4px;
            flex: 1;
            border-radius: 2px;
            background: #e5e7eb;
        }
        .strength-1 { background: #ef4444; }
        .strength-2 { background: #f59e0b; }
        .strength-3 { background: #10b981; }
        .strength-4 { background: #10b981; }

        .gradient-side {
            background: linear-gradient(135deg, #1d7253 0%, #075a3f 50%, #047857 100%);
        }

        /* Floating animations */
        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(10px, -15px) rotate(5deg); }
            50% { transform: translate(-5px, -25px) rotate(-3deg); }
            75% { transform: translate(-15px, -10px) rotate(3deg); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-15px, 10px) rotate(-5deg); }
            66% { transform: translate(10px, 20px) rotate(5deg); }
        }
        @keyframes float3 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, -20px); }
        }
        @keyframes float4 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-10px, 15px) rotate(-3deg); }
            75% { transform: translate(15px, -10px) rotate(3deg); }
        }
        @keyframes float5 {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(-10px, -15px); }
            66% { transform: translate(5px, 10px); }
        }
        .animate-float-1 { animation: float1 6s ease-in-out infinite; }
        .animate-float-2 { animation: float2 7s ease-in-out infinite; }
        .animate-float-3 { animation: float3 5s ease-in-out infinite; }
        .animate-float-4 { animation: float4 8s ease-in-out infinite; }
        .animate-float-5 { animation: float5 6.5s ease-in-out infinite; }
        .float-1-pos { top: 15%; left: 25%; }
        .float-2-pos { top: 10%; right: 25%; }
        .float-3-pos { top: 45%; left: 18%; }
        .float-4-pos { bottom: 25%; right: 20%; }
        .float-5-pos { top: 35%; right: 18%; }
    </style>
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-teal-600">Orbit API</span>
                </div>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="#" class="text-xs text-gray-600 hover:text-gray-900">Documentation</a>
                    <a href="#" class="text-xs text-gray-600 hover:text-gray-900">Pricing</a>
                    <a href="#" class="text-xs text-gray-600 hover:text-gray-900">Support</a>
                    <a href="{{ route('login') }}" class="text-xs text-teal-600 font-medium">Log In</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1 flex items-center">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-4 w-full">
            <div class="flex gap-10 justify-center items-start">
                <!-- Left: Form -->
                <div class="w-full lg:w-1/2 max-w-md" x-data="registerForm()">
                    <h1 class="text-xl font-bold text-gray-900 mb-4">Create your developer account</h1>

                    <form method="POST" action="{{ route('register') }}" class="space-y-3">
                        @csrf

                        <!-- Work Email -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Work Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="input-field" placeholder="yourname@company.com" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden Name field (auto-generate from email) -->
                        <input type="hidden" name="name" value="{{ old('name', 'User') }}">

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input :type="showPwd ? 'text' : 'password'" name="password"
                                       class="input-field pr-10" required @input="checkStrength($event.target.value)">
                                <button type="button" @click="showPwd = !showPwd"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Strength bars -->
                            <div class="flex gap-1 mt-1">
                                <div class="strength-bar" :class="strength >= 1 ? 'strength-' + strength : ''"></div>
                                <div class="strength-bar" :class="strength >= 2 ? 'strength-' + strength : ''"></div>
                                <div class="strength-bar" :class="strength >= 3 ? 'strength-' + strength : ''"></div>
                                <div class="strength-bar" :class="strength >= 4 ? 'strength-' + strength : ''"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">
                                <span class="font-medium" x-text="strengthLabel"></span>: 8+ characters, uppercase, number, symbol
                            </p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden confirm password -->
                        <input type="hidden" name="password_confirmation" x-ref="confirmPwd">

                        <!-- Divider -->
                        <div class="flex items-center gap-3 text-gray-400 text-xs">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span>OR</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        <!-- Social buttons -->
                        <div class="flex gap-3">
                            <button type="button" class="btn-social white" id="google-register-btn">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Sign up with Google
                            </button>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start gap-2">
                            <input type="checkbox" name="terms" id="terms" required
                                   class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 mt-0.5">
                            <label for="terms" class="text-xs text-gray-600 leading-relaxed">
                                I agree to the <a href="#" class="text-teal-600 hover:underline">Terms & Privacy Policy</a>, including the WhatsApp <a href="#" class="text-teal-600 hover:underline">anti-spam policy</a>.
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center gap-4">
                            <button type="submit" class="btn-signup" @click="$refs.confirmPwd.value = $el.form.password.value">
                                Sign Up
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                            <span class="text-xs text-gray-600">
                                Already have an account? <a href="{{ route('login') }}" class="text-teal-600 font-medium hover:underline">Log In</a>
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="flex gap-2 p-2.5 bg-cyan-50 border border-cyan-200 rounded-lg">
                            <svg class="w-4 h-4 text-cyan-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-cyan-800">
                                Please verify your email to complete registration.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Right: Illustration -->
                <div class="hidden lg:block lg:w-2/5">
                    <div class="gradient-side rounded-2xl p-6 h-full relative overflow-hidden flex flex-col justify-between">
                        <!-- Illustration with floating icons -->
                        <div class="flex-1 flex items-center justify-center relative">
                            <!-- Floating chat icons with animation -->
                            <div class="absolute w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center animate-float-1 float-1-pos">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div class="absolute w-16 h-16 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center animate-float-2 float-2-pos">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                            </div>
                            <div class="absolute w-12 h-12 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center animate-float-3 float-3-pos">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                            </div>
                            <div class="absolute w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center animate-float-4 float-4-pos">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="absolute w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center animate-float-5 float-5-pos">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <!-- Main globe image -->
                            <img src="{{ asset('Image/register-illustration.png') }}" alt="API Illustration" class="max-w-64 max-h-64 object-contain drop-shadow-xl relative z-10">
                        </div>
                        <!-- Quote section -->
                        <div class="mt-4">
                            <svg class="w-8 h-8 text-white/30 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                            </svg>
                            <p class="text-base text-white font-medium leading-relaxed mb-2">
                                API Connect has streamlined our data integration process significantly.
                            </p>
                            <p class="text-sm text-white/80">- Global Tech Solutions</p>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <a href="#" class="text-xs text-teal-600 hover:underline">Need help?</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-2">
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-500">© {{ date('Y') }} Orbit API</p>
                <div class="flex gap-6">
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-700">Privacy Policy</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-700">Terms of Service</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-700">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script nonce="{{ $csp_nonce }}">
    function registerForm() {
        return {
            showPwd: false,
            strength: 0,
            strengthLabel: 'Weak',
            checkStrength(pwd) {
                let s = 0;
                if (pwd.length >= 8) s++;
                if (/[A-Z]/.test(pwd)) s++;
                if (/[0-9]/.test(pwd)) s++;
                if (/[^A-Za-z0-9]/.test(pwd)) s++;
                this.strength = s;
                this.strengthLabel = s >= 4 ? 'Strong' : s >= 3 ? 'Good' : s >= 2 ? 'Fair' : 'Weak';
            }
        }
    }
    </script>
</body>

</html>

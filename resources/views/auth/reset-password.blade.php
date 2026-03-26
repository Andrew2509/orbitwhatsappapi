<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ config('app.name', 'Orbit WhatsApp API') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script nonce="{{ config('app.csp_nonce') ?? '' }}" type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
        import { getAuth, verifyPasswordResetCode, confirmPasswordReset, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

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

        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const oobCode = urlParams.get('oobCode');
        let userEmail = '';

        document.addEventListener('DOMContentLoaded', async () => {
            const formContainer = document.getElementById('form-container');
            const invalidContainer = document.getElementById('invalid-container');
            const successContainer = document.getElementById('success-container');
            const loadingOverlay = document.getElementById('loading-overlay');
            
            const resetForm = document.getElementById('reset-password-form');
            const submitBtn = document.getElementById('submit-btn');
            const errorMessage = document.getElementById('error-message');
            const loadingSpinner = document.getElementById('loading-spinner');
            const buttonText = document.getElementById('button-text');

            if (!oobCode) {
                loadingOverlay.classList.add('hidden');
                invalidContainer.classList.remove('hidden');
                return;
            }

            try {
                // Verify the reset code first
                userEmail = await verifyPasswordResetCode(auth, oobCode);
                document.getElementById('display-email').innerText = userEmail;
                loadingOverlay.classList.add('hidden');
                formContainer.classList.remove('hidden');
            } catch (error) {
                console.error('Code Verification Error:', error);
                loadingOverlay.classList.add('hidden');
                invalidContainer.classList.remove('hidden');
            }

            if (resetForm) {
                resetForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('password_confirmation').value;

                    if (password !== confirmPassword) {
                        errorMessage.innerText = 'Kata sandi tidak cocok.';
                        errorMessage.classList.remove('hidden');
                        return;
                    }

                    if (password.length < 6) {
                        errorMessage.innerText = 'Kata sandi minimal 6 karakter.';
                        errorMessage.classList.remove('hidden');
                        return;
                    }

                    // UI State
                    errorMessage.classList.add('hidden');
                    submitBtn.disabled = true;
                    loadingSpinner.classList.remove('hidden');
                    buttonText.innerText = 'Processing...';

                    try {
                        // 1. Confirm reset in Firebase
                        await confirmPasswordReset(auth, oobCode, password);
                        
                        // 2. Sign in to get ID Token for sync
                        buttonText.innerText = 'Syncing...';
                        const userCredential = await signInWithEmailAndPassword(auth, userEmail, password);
                        const idToken = await userCredential.user.getIdToken();

                        // 3. Sync with local database
                        const syncResponse = await fetch("{{ route('auth.firebase.sync-password') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                id_token: idToken,
                                password: password
                            })
                        });

                        const syncData = await syncResponse.json();
                        
                        if (syncData.success) {
                            formContainer.classList.add('hidden');
                            successContainer.classList.remove('hidden');
                        } else {
                            throw new Error(syncData.message || 'Gagal sinkronisasi data.');
                        }
                    } catch (error) {
                        console.error('Reset Confirmation Error:', error);
                        errorMessage.innerText = 'Gagal mereset kata sandi: ' + error.message;
                        errorMessage.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                        loadingSpinner.classList.add('hidden');
                        buttonText.innerText = 'Reset Password';
                    }
                });
            }
        });
    </script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        html, body { height: 100%; overflow: hidden; }
        .gradient-bg { background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%); }
        .input-field {
            width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb;
            border-radius: 8px; font-size: 14px; transition: all 0.2s; background: #fff;
        }
        .input-field:focus {
            outline: none; border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .btn-primary {
            width: 100%; padding: 10px; background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            color: white; font-weight: 600; border-radius: 8px; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 14px; transition: all 0.2s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }
    </style>
</head>

<body class="antialiased">
    <div class="h-screen flex overflow-hidden">
        <!-- Left Side -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center p-6 lg:p-10 bg-white overflow-y-auto">
            <div class="max-w-sm mx-auto w-full">
                <!-- Logo -->
                <div class="flex items-center gap-2 mb-8">
                    <img src="{{ asset('Image/logo-wa-api-black.png') }}" alt="Orbit API" class="h-12">
                    <span class="text-lg font-bold text-gray-800">Orbit WhatsApp API</span>
                </div>

                <!-- Loading State -->
                <div id="loading-overlay" class="text-center py-10">
                    <div class="animate-spin inline-block w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full mb-4"></div>
                    <p class="text-sm text-gray-500 italic">Memverifikasi kode reset...</p>
                </div>

                <!-- Form Container -->
                <div id="form-container" class="hidden">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Buat Kata Sandi Baru</h2>
                        <p class="text-sm text-gray-500 mt-2">
                            Reset kata sandi untuk <span id="display-email" class="font-semibold text-gray-700 underline"></span>
                        </p>
                    </div>

                    <div id="error-message" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs font-medium"></div>

                    <form id="reset-password-form" class="space-y-4">
                        <div>
                            <label for="password" class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" id="password" class="input-field" placeholder="••••••••" required autofocus>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                            <input type="password" id="password_confirmation" class="input-field" placeholder="••••••••" required>
                        </div>

                        <button type="submit" id="submit-btn" class="btn-primary mt-2">
                            <svg id="loading-spinner" class="hidden animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="button-text">Reset Password</span>
                        </button>
                    </form>
                </div>

                <!-- Success State -->
                <div id="success-container" class="hidden text-center py-6">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Password Berhasil Diubah!</h2>
                    <p class="text-sm text-gray-500 mb-6">Anda sekarang dapat masuk kembali ke akun Orbit API Anda menggunakan kata sandi baru.</p>
                    <a href="{{ route('login') }}" class="btn-primary">Kembali ke Login</a>
                </div>

                <!-- Invalid Code State -->
                <div id="invalid-container" class="hidden text-center py-6">
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Tautan Tidak Valid</h2>
                    <p class="text-sm text-gray-500 mb-6">Tautan reset kata sandi mungkin sudah kedaluwarsa atau sudah pernah digunakan sebelumnya.</p>
                    <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Minta tautan baru</a>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg flex-col justify-center items-center p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative z-10 max-w-sm text-center">
                <div class="w-32 h-32 mx-auto mb-6 relative">
                    <div class="absolute inset-0 bg-white/20 rounded-full backdrop-blur-sm"></div>
                    <div class="absolute inset-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Update your credentials</h2>
                <p class="text-white/80 text-sm italic">"Keamanan akun Anda adalah prioritas utama kami."</p>
            </div>
        </div>
    </div>
</body>

</html>

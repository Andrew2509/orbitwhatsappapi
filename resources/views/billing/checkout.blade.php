@extends('layouts.app')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran Invoice')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    @if(session('success'))
    <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg">
        {{ session('info') }}
    </div>
    @endif

    <!-- Invoice Summary -->
    <div class="card">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold mb-1">Invoice {{ $invoice->invoice_number }}</h2>
                <p class="text-[var(--text-muted)]">Dibuat: {{ $invoice->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span class="badge {{ $invoice->status_badge }}">
                {{ $invoice->status_label }}
            </span>
        </div>

        <div class="border-t border-b py-4 mb-4">
            <div class="flex justify-between mb-2">
                <span class="text-[var(--text-muted)]">Paket</span>
                <span class="font-semibold">{{ $plan->name }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-[var(--text-muted)]">Periode</span>
                <span>1 Bulan</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-[var(--text-muted)]">Subtotal</span>
                <span>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-[var(--text-muted)]">PPN (11%)</span>
                <span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex justify-between text-lg font-bold">
            <span>Total</span>
            <span class="text-emerald-600">{{ $invoice->formatted_total }}</span>
        </div>

        @if($invoice->due_date)
        <p class="text-sm text-[var(--text-muted)] mt-4">
            Batas pembayaran: <span class="font-medium">{{ $invoice->due_date->format('d M Y') }}</span>
        </p>
        @endif
    </div>

    @if($invoice->isPending())
    <!-- Midtrans Payment -->
    <div class="card text-center">
        <h3 class="font-semibold text-lg mb-4">Metode Pembayaran Otomatis</h3>

        @if(isset($paymentError))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-600 dark:text-red-400">
            {{ $paymentError }}
        </div>
        @else
        <p class="text-[var(--text-muted)] mb-6">Klik tombol di bawah untuk membayar menggunakan Midtrans (QRIS, VA, E-Wallet, Kartu Kredit).</p>

        <button id="pay-button" class="w-full py-4 px-6 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-emerald-500/20 transition-all flex items-center justify-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Bayar Sekarang
        </button>
        @endif

        <div class="mt-6 flex items-center justify-center gap-4 grayscale opacity-60">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-6">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" class="h-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Logo_ovo_purple.svg" alt="OVO" class="h-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" alt="DANA" class="h-4">
        </div>
    </div>

    @push('scripts')
    @php
        $snapUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script nonce="{{ config('app.csp_nonce') }}" src="{{ $snapUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script nonce="{{ $csp_nonce }}">
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const snapToken = '{{ $snapToken ?? "" }}';
            let snapIsOpening = false;

            console.log('Payment script initialized.', {
                buttonFound: !!payButton,
                tokenPresent: !!snapToken
            });

            if (payButton && snapToken) {
                payButton.addEventListener('click', function () {
                    if (snapIsOpening) {
                        console.log('Snap is already opening/open.');
                        return;
                    }

                    console.log('Initiating Snap payment with token:', snapToken);
                    snapIsOpening = true;
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memuat Pembayaran...</span>';

                    window.snap.pay(snapToken, {
                        onSuccess: function (result) {
                            console.log('Payment successful:', result);
                            window.location.reload();
                        },
                        onPending: function (result) {
                            console.log('Payment pending:', result);
                            window.location.reload();
                        },
                        onError: function (result) {
                            console.error('Payment error:', result);
                            alert("Pembayaran gagal! Silakan coba lagi.");
                            snapIsOpening = false;
                            payButton.disabled = false;
                            payButton.innerHTML = 'Bayar Sekarang';
                        },
                        onClose: function () {
                            console.log('User closed the popup without finishing the payment');
                            snapIsOpening = false;
                            payButton.disabled = false;
                            payButton.innerHTML = 'Bayar Sekarang';
                        }
                    });
                });
            } else if (!snapToken && !document.querySelector('.bg-red-50')) {
                // Only log if we haven't already displayed a UI error message
                console.warn('Snap token is missing. Payment button will not be interactive.');
            }
        });
    </script>
    @endpush
    @elseif($invoice->isPaid())
    <div class="card bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-800 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-emerald-700 dark:text-emerald-400">Pembayaran Berhasil!</h3>
                <p class="text-sm text-emerald-600 dark:text-emerald-500">
                    Dibayar pada {{ $invoice->paid_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('billing.index') }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] transition">
            ← Kembali ke Billing
        </a>
    </div>
</div>
@endsection

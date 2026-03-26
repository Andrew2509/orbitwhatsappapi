@extends('layouts.app')

@section('title', 'Pilih Paket')
@section('page-title', 'Pilih Paket Berlangganan')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold mb-2">Pilih Paket Sesuai Kebutuhan Anda</h2>
        <p class="text-[var(--text-muted)]">Upgrade kapan saja untuk mendapatkan fitur lebih lengkap</p>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
        @foreach($plans as $plan)
        <div class="card relative {{ $plan->is_featured ? 'ring-2 ring-emerald-500' : '' }} {{ $currentPlan && $currentPlan->id === $plan->id ? 'bg-emerald-50 dark:bg-emerald-900/20' : '' }}">
            <!-- Featured Badge -->
            @if($plan->is_featured)
            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span class="px-3 py-1 bg-emerald-500 text-white text-xs font-medium rounded-full">
                    Most Popular
                </span>
            </div>
            @endif

            <!-- Current Plan Badge -->
            @if($currentPlan && $currentPlan->id === $plan->id)
            <div class="absolute -top-3 right-4">
                <span class="px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded-full">
                    Paket Anda
                </span>
            </div>
            @endif

            <div class="text-center pt-4">
                <h3 class="text-xl font-bold mb-2">{{ $plan->name }}</h3>
                <p class="text-sm text-[var(--text-muted)] mb-4">{{ $plan->description }}</p>
                <div class="mb-6">
                    <span class="text-3xl font-extrabold">{{ $plan->formatted_price }}</span>
                    @if(!$plan->isFree())
                    <span class="text-[var(--text-muted)]">/bulan</span>
                    @endif
                </div>
            </div>

            <!-- Features List -->
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">
                        {{ $plan->isUnlimited('devices') ? 'Unlimited' : $plan->max_devices }} Device
                    </span>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">
                        {{ $plan->isUnlimited('messages') ? 'Unlimited' : number_format($plan->max_messages_per_day) }} Pesan/hari
                    </span>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">
                        {{ $plan->isUnlimited('contacts') ? 'Unlimited' : number_format($plan->max_contacts) }} Kontak
                    </span>
                </li>
                @foreach($plan->features ?? [] as $feature => $enabled)
                <li class="flex items-center gap-2">
                    @if($enabled)
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    @else
                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    @endif
                    <span class="text-sm {{ !$enabled ? 'text-gray-400' : '' }}">
                        {{ ucwords(str_replace('_', ' ', $feature)) }}
                    </span>
                </li>
                @endforeach
            </ul>

            <!-- CTA Button -->
            @if($currentPlan && $currentPlan->id === $plan->id)
            <button disabled class="w-full py-3 px-4 bg-gray-300 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
                Paket Aktif
            </button>
            @elseif($plan->isFree())
            <button disabled class="w-full py-3 px-4 bg-gray-200 text-gray-600 rounded-lg font-semibold cursor-not-allowed">
                Paket Gratis
            </button>
            @else
            <form action="{{ route('billing.subscribe', $plan) }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3 px-4 {{ $plan->is_featured ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-gray-800 hover:bg-gray-900 text-white dark:bg-white dark:text-gray-800 dark:hover:bg-gray-100' }} rounded-lg font-semibold transition">
                    Pilih {{ $plan->name }}
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="max-w-3xl mx-auto mt-12">
        <h3 class="text-xl font-bold text-center mb-6">Pertanyaan Umum</h3>
        <div class="space-y-4">
            <div class="card">
                <h4 class="font-semibold mb-2">Bagaimana cara pembayaran?</h4>
                <p class="text-sm text-[var(--text-muted)]">Saat ini kami menerima pembayaran via transfer bank. Setelah transfer, upload bukti pembayaran dan tim kami akan memverifikasi dalam 1x24 jam.</p>
            </div>
            <div class="card">
                <h4 class="font-semibold mb-2">Bisakah upgrade atau downgrade paket?</h4>
                <p class="text-sm text-[var(--text-muted)]">Ya, Anda dapat mengubah paket kapan saja. Saat upgrade, Anda langsung mendapat akses fitur baru. Saat downgrade, perubahan berlaku di periode berikutnya.</p>
            </div>
            <div class="card">
                <h4 class="font-semibold mb-2">Apakah ada refund?</h4>
                <p class="text-sm text-[var(--text-muted)]">Kami menyediakan refund proporsional untuk pembatalan dalam 7 hari pertama. Hubungi support untuk informasi lebih lanjut.</p>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-8">
        <a href="{{ route('billing.index') }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] transition">
            ← Kembali ke Billing
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Billing')
@section('page-title', 'Subscription & Billing')

@section('content')
@php $nonce = config('app.csp_nonce'); @endphp
<style nonce="{{ $nonce }}">
    .progress-bar-fill { height: 100%; border-radius: 9999px; transition: all 300ms; }
</style>
<div class="space-y-6 animate-fade-in">
    <!-- Current Plan -->
    @if($subscription && $subscription->isActive())
    <div class="card bg-gradient-to-br from-emerald-500 to-emerald-600 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-emerald-100 text-sm mb-1">Paket Aktif</p>
                <h2 class="text-3xl font-bold">{{ $plan->name }}</h2>
                <div class="flex items-center gap-4 mt-2">
                    <p class="text-emerald-100">
                        <span class="font-medium">{{ $subscription->daysRemaining() }}</span> hari tersisa
                    </p>
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs">
                        Berakhir: {{ $subscription->ends_at->format('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('billing.plans') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition">
                    Upgrade
                </a>
                <form action="{{ route('billing.auto-renew') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 {{ $subscription->auto_renew ? 'bg-emerald-700' : 'bg-white/20' }} hover:bg-white/30 rounded-lg font-medium transition">
                        Auto-Renew: {{ $subscription->auto_renew ? 'ON' : 'OFF' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="card bg-gradient-to-br from-gray-600 to-gray-700 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-gray-300 text-sm mb-1">Paket Saat Ini</p>
                <h2 class="text-3xl font-bold">Free Plan</h2>
                <p class="text-gray-300 mt-2">Upgrade untuk mendapatkan lebih banyak fitur</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('billing.plans') }}" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 rounded-lg font-semibold transition">
                    Lihat Paket
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Usage Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Pesan Hari Ini
            </h3>
            <div class="flex items-end gap-2 mb-3">
                <span class="text-3xl font-bold">{{ number_format($todayUsage->messages_sent) }}</span>
                <span class="text-[var(--text-muted)]">/ {{ $maxMessages }}</span>
            </div>
            <div class="w-full h-2 bg-[var(--bg-secondary)] rounded-full" x-data="{ p: {{ $messagesPercent }} }">
                <div class="progress-bar-fill bg-emerald-500" :style="'width: ' + p + '%'"></div>
            </div>
        </div>
        <div class="card">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Device Aktif
            </h3>
            <div class="flex items-end gap-2 mb-3">
                <span class="text-3xl font-bold">{{ $devicesCount }}</span>
                <span class="text-[var(--text-muted)]">/ {{ $maxDevices }}</span>
            </div>
            <div class="w-full h-2 bg-[var(--bg-secondary)] rounded-full" x-data="{ p: {{ $devicesPercent }} }">
                <div class="progress-bar-fill bg-blue-500" :style="'width: ' + p + '%'"></div>
            </div>
        </div>
        <div class="card">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Kontak
            </h3>
            <div class="flex items-end gap-2 mb-3">
                <span class="text-3xl font-bold">{{ number_format($contactsCount) }}</span>
                <span class="text-[var(--text-muted)]">/ {{ $maxContacts }}</span>
            </div>
            <div class="w-full h-2 bg-[var(--bg-secondary)] rounded-full" x-data="{ p: {{ $contactsPercent }} }">
                <div class="progress-bar-fill bg-purple-500" :style="'width: ' + p + '%'"></div>
            </div>
        </div>
    </div>

    <!-- Features Available -->
    @if($plan)
    <div class="card">
        <h3 class="font-semibold text-lg mb-4">Fitur Tersedia</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($plan->features ?? [] as $feature => $enabled)
            <div class="flex items-center gap-2 p-3 rounded-lg {{ $enabled ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-gray-100 dark:bg-gray-800' }}">
                @if($enabled)
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                @else
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                @endif
                <span class="text-sm {{ $enabled ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500' }}">
                    {{ ucwords(str_replace('_', ' ', $feature)) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Invoice History -->
    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-lg">Riwayat Invoice</h3>
            @if($invoices->count() > 0)
            <a href="{{ route('billing.invoices') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                Lihat Semua →
            </a>
            @endif
        </div>

        @if($invoices->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="font-mono text-sm">{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="font-semibold">{{ $invoice->formatted_total }}</td>
                        <td>
                            <span class="badge {{ $invoice->status_badge }}">
                                {{ $invoice->status_label }}
                            </span>
                        </td>
                        <td>
                            @if($invoice->isPending())
                            <a href="{{ route('billing.checkout', $invoice) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                Bayar
                            </a>
                            @else
                            <a href="{{ route('billing.invoice.download', $invoice) }}" class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                                Download
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-[var(--text-muted)]">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Belum ada invoice</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('billing.plans') }}" class="card hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold group-hover:text-emerald-600 transition">Upgrade Paket</h4>
                    <p class="text-sm text-[var(--text-muted)]">Tingkatkan limit dan akses fitur premium</p>
                </div>
            </div>
        </a>
        <a href="{{ route('billing.invoices') }}" class="card hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold group-hover:text-blue-600 transition">Riwayat Pembayaran</h4>
                    <p class="text-sm text-[var(--text-muted)]">Lihat dan download semua invoice</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

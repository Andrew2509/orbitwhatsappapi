@extends('layouts.admin')

@section('title', 'Promo Stats: {{ $promoCode->code }}')
@section('page-title', 'Statistik: {{ $promoCode->code }}')
@section('page-subtitle', 'Detail penggunaan kode promo')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.promo-codes.index') }}" class="text-emerald-600 hover:text-blue-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Promo Info Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold font-mono text-emerald-600">{{ $promoCode->code }}</h2>
                    @if($promoCode->is_active && $promoCode->isValid())
                    <span class="admin-badge admin-badge-success">Active</span>
                    @else
                    <span class="admin-badge admin-badge-danger">Inactive</span>
                    @endif
                </div>
                @if($promoCode->description)
                <p class="text-slate-500 mt-1">{{ $promoCode->description }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-emerald-500">{{ $promoCode->getFormattedDiscount() }}</p>
                <p class="text-sm text-slate-500">{{ $promoCode->discount_type === 'percentage' ? 'Persentase' : 'Nominal Tetap' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
            <div>
                <p class="text-sm text-slate-500">Penggunaan</p>
                <p class="font-semibold">
                    {{ $stats['times_used'] }}
                    @if($promoCode->usage_limit)
                    / {{ $promoCode->usage_limit }}
                    @else
                    / ∞
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Sisa Kuota</p>
                <p class="font-semibold">{{ $stats['remaining'] ?? 'Unlimited' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Diskon Diberikan</p>
                <p class="font-semibold text-red-500">Rp {{ number_format($stats['total_discount_given'], 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Revenue</p>
                <p class="font-semibold text-emerald-500">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700 text-sm">
            <div>
                <span class="text-slate-500">Mulai:</span>
                <span class="font-medium">{{ $promoCode->starts_at?->format('d M Y H:i') ?? 'Segera' }}</span>
            </div>
            <div>
                <span class="text-slate-500">Berakhir:</span>
                <span class="font-medium {{ $promoCode->expires_at?->isPast() ? 'text-red-500' : '' }}">
                    {{ $promoCode->expires_at?->format('d M Y H:i') ?? 'Tidak ada batas' }}
                </span>
            </div>
            <div>
                <span class="text-slate-500">Min. Pembelian:</span>
                <span class="font-medium">Rp {{ number_format($promoCode->min_purchase, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Copy Link Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold mb-2">🔗 Link Promo Otomatis</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Bagikan link ini ke user. Kode promo akan otomatis terpasang saat checkout.</p>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ url('/register?promo=' . $promoCode->code) }}" id="promoLink"
                class="flex-1 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-mono">
            <button onclick="copyLink()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700 transition">
                Copy
            </button>
        </div>
    </div>

    <!-- Usage History -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Riwayat Penggunaan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Invoice</th>
                        <th class="px-6 py-4 font-medium">Diskon</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($promoCode->usages as $usage)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-emerald-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr($usage->user->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $usage->user->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $usage->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm">
                            {{ $usage->invoice?->invoice_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-red-500">
                            -Rp {{ number_format($usage->discount_applied, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $usage->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            Belum ada yang menggunakan kode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script nonce="{{ $csp_nonce }}">
function copyLink() {
    const input = document.getElementById('promoLink');
    input.select();
    document.execCommand('copy');
    alert('Link berhasil disalin!');
}
</script>
@endsection


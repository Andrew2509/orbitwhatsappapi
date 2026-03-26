@extends('layouts.admin')

@section('title', 'Promo Codes')
@section('page-title', 'Promo Codes')
@section('page-subtitle', 'Kelola kode promo dan diskon')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Promo</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expired</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Digunakan</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['total_usage'] }}</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <div></div>
        <a href="{{ route('admin.promo-codes.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Promo Code
        </a>
    </div>

    <!-- Promo Codes Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Kode</th>
                        <th class="px-6 py-4 font-medium">Diskon</th>
                        <th class="px-6 py-4 font-medium">Penggunaan</th>
                        <th class="px-6 py-4 font-medium">Masa Berlaku</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($promoCodes as $promo)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-mono font-bold text-emerald-600">{{ $promo->code }}</p>
                                @if($promo->description)
                                <p class="text-xs text-slate-500">{{ Str::limit($promo->description, 40) }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-emerald-600">{{ $promo->getFormattedDiscount() }}</span>
                            @if($promo->discount_type === 'percentage' && $promo->max_discount)
                            <p class="text-xs text-slate-500">Max: Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-medium">{{ $promo->times_used }}</span>
                                @if($promo->usage_limit)
                                <span class="text-slate-500">/ {{ $promo->usage_limit }}</span>
                                @else
                                <span class="text-slate-500">/ ∞</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($promo->expires_at)
                                @if($promo->expires_at->isPast())
                                <span class="text-red-500">Expired</span>
                                @elseif($promo->isExpiringSoon())
                                <span class="text-amber-500">{{ $promo->expires_at->format('d M Y') }}</span>
                                <p class="text-xs text-amber-500">Segera berakhir!</p>
                                @else
                                <span>{{ $promo->expires_at->format('d M Y') }}</span>
                                @endif
                            @else
                            <span class="text-slate-400">Tidak ada batas</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($promo->is_active && $promo->isValid())
                            <span class="admin-badge admin-badge-success">Active</span>
                            @elseif(!$promo->is_active)
                            <span class="admin-badge admin-badge-danger">Inactive</span>
                            @else
                            <span class="admin-badge admin-badge-warning">Invalid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.promo-codes.show', $promo) }}" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg" title="View Statistics">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.promo-codes.toggle', $promo) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg" title="{{ $promo->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if($promo->is_active)
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        @else
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @endif
                                    </button>
                                </form>
                                @if($promo->times_used == 0)
                                <form action="{{ route('admin.promo-codes.destroy', $promo) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus kode promo ini?')" class="p-2 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg text-red-500" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <p>Belum ada kode promo</p>
                                <a href="{{ route('admin.promo-codes.create') }}" class="mt-2 text-emerald-600 hover:text-blue-700">Buat kode promo pertama →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($promoCodes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $promoCodes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection


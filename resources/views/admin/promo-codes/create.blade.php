@extends('layouts.admin')

@section('title', 'Create Promo Code')
@section('page-title', 'Create Promo Code')
@section('page-subtitle', 'Tambah kode promo baru')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.promo-codes.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Info -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Informasi Dasar</h3>

            <div>
                <label class="block text-sm font-medium mb-2">Kode Promo <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input type="text" name="code" id="codeInput" value="{{ old('code') }}" required
                        class="flex-1 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500 uppercase font-mono"
                        placeholder="DISKONHEMAT">
                    <button type="button" @click="generateCode()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                        Generate
                    </button>
                </div>
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Deskripsi (Internal)</label>
                <input type="text" name="description" value="{{ old('description') }}"
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"
                    placeholder="Promo khusus Harbolnas untuk paket Pro">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded">
                <label for="is_active" class="text-sm font-medium">Aktifkan kode ini</label>
            </div>
        </div>

        <!-- Discount Type -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Jenis Diskon</h3>

            <div class="grid grid-cols-2 gap-4" x-data="promoCodeForm('{{ old('discount_type', 'percentage') }}', '{{ route('admin.promo-codes.generate-code') }}')">
                <label class="cursor-pointer">
                    <input type="radio" name="discount_type" value="percentage" x-model="discountType" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 dark:border-slate-700 rounded-lg peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition">
                        <div class="font-medium">Persentase (%)</div>
                        <p class="text-xs text-slate-500">Potongan berdasarkan persen</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="discount_type" value="fixed" x-model="discountType" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 dark:border-slate-700 rounded-lg peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition">
                        <div class="font-medium">Nominal Tetap (Rp)</div>
                        <p class="text-xs text-slate-500">Potongan dengan jumlah pasti</p>
                    </div>
                </label>

                <div class="col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            <span x-show="discountType === 'percentage'">Nilai Diskon (%)</span>
                            <span x-show="discountType === 'fixed'">Nilai Diskon (Rp)</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="discount_value" value="{{ old('discount_value', 10) }}" min="0" step="0.01" required
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    </div>
                    <div x-show="discountType === 'percentage'">
                        <label class="block text-sm font-medium mb-2">Maksimum Potongan (Rp)</label>
                        <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"
                            placeholder="25000">
                        <p class="text-xs text-slate-500 mt-1">Batas atas diskon (opsional)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Restrictions -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Aturan Batasan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Minimum Pembelian (Rp)</label>
                    <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" min="0"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"
                        placeholder="0">
                    <p class="text-xs text-slate-500 mt-1">0 = tidak ada minimum</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Limit Penggunaan Total</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"
                        placeholder="100">
                    <p class="text-xs text-slate-500 mt-1">Kosongkan untuk unlimited</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Limit per User <span class="text-red-500">*</span></label>
                    <input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', 1) }}" min="1" required
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                </div>
            </div>
        </div>

        <!-- Validity -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Masa Berlaku</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Tanggal Mulai</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <p class="text-xs text-slate-500 mt-1">Kosongkan untuk segera aktif</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tanggal Berakhir</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <p class="text-xs text-slate-500 mt-1">Kosongkan untuk tidak ada batas</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Berlaku untuk Paket</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($plans as $plan)
                    <label class="flex items-center gap-2 p-3 border border-gray-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <input type="checkbox" name="applicable_plans[]" value="{{ $plan->id }}" class="rounded">
                        <span class="text-sm">{{ $plan->name }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-2">Jika tidak dipilih, promo berlaku untuk semua paket</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.promo-codes.index') }}" class="px-6 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700">
                Simpan Promo
            </button>
        </div>
    </form>
</div>

@section('scripts')
{{-- Code generation logic moved to app.js --}}
@endsection
@endsection


@extends('layouts.admin')

@section('title', 'Create Plan')
@section('page-title', 'Create New Plan')
@section('page-subtitle', 'Tambah paket langganan baru')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Detail Paket</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" min="0" required 
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="3" 
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Billing Period</label>
                    <select name="billing_period" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" 
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Limit & Quota</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Max Devices</label>
                    <input type="number" name="max_devices" value="{{ old('max_devices', 1) }}" min="-1" 
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <p class="text-xs text-slate-500 mt-1">-1 untuk unlimited</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Max Pesan / Hari</label>
                    <input type="number" name="max_messages_per_day" value="{{ old('max_messages_per_day', 100) }}" min="-1" 
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <p class="text-xs text-slate-500 mt-1">-1 untuk unlimited</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Max Kontak</label>
                    <input type="number" name="max_contacts" value="{{ old('max_contacts', 100) }}" min="-1" 
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <p class="text-xs text-slate-500 mt-1">-1 untuk unlimited</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 space-y-6">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-4">Fitur</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_basic_messaging" value="1" checked class="rounded">
                    <span class="text-sm">Basic Messaging</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_broadcast" value="1" class="rounded">
                    <span class="text-sm">Broadcast</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_auto_reply" value="1" class="rounded">
                    <span class="text-sm">Auto Reply</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_webhook" value="1" class="rounded">
                    <span class="text-sm">Webhook</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_api_access" value="1" checked class="rounded">
                    <span class="text-sm">API Access</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_priority_support" value="1" class="rounded">
                    <span class="text-sm">Priority Support</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_dedicated_ip" value="1" class="rounded">
                    <span class="text-sm">Dedicated IP</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="feature_account_manager" value="1" class="rounded">
                    <span class="text-sm">Account Manager</span>
                </label>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    <span class="text-sm font-medium">Active</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" value="1" class="rounded">
                    <span class="text-sm font-medium">Featured (Most Popular)</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.plans.index') }}" class="px-6 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700">
                Simpan Paket
            </button>
        </div>
    </form>
</div>
@endsection


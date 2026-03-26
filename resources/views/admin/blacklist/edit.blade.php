@extends('layouts.admin')

@section('title', 'Edit Kata Blacklist')
@section('page-title', 'Edit Kata Blacklist')
@section('page-subtitle', 'Ubah pengaturan kata blacklist')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('admin.blacklist.update', $blacklist) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="word" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kata/Frasa</label>
                    <input type="text" name="word" id="word" value="{{ old('word', $blacklist->word) }}" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    @error('word')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select name="category" id="category" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category', $blacklist->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Severity</label>
                    <select name="severity" id="severity" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        <option value="block" {{ old('severity', $blacklist->severity) == 'block' ? 'selected' : '' }}>Block - Pesan akan diblokir</option>
                        <option value="warning" {{ old('severity', $blacklist->severity) == 'warning' ? 'selected' : '' }}>Warning - Hanya peringatan</option>
                    </select>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan (Opsional)</label>
                    <input type="text" name="reason" id="reason" value="{{ old('reason', $blacklist->reason) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', $blacklist->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <a href="{{ route('admin.blacklist.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

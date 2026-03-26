@extends('layouts.admin')

@section('title', 'Website Settings')
@section('page-title', 'Website Settings')
@section('page-subtitle', 'Manage global configuration and site information')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.system.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach($settings as $group => $items)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-sm">{{ ucfirst($group) }} Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                @foreach($items as $setting)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-4 border-b border-gray-100 dark:border-slate-700 last:border-0 last:pb-0">
                    <div class="md:col-span-1">
                        <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ $setting->description ?: ucfirst(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <p class="mt-1 text-xs text-gray-400">Key: <code class="bg-gray-100 dark:bg-slate-900 px-1 rounded">{{ $setting->key }}</code></p>
                    </div>
                    
                    <div class="md:col-span-2">
                        @if($setting->type === 'textarea')
                        <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3" 
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm">{{ $setting->value }}</textarea>
                        @elseif($setting->type === 'boolean')
                        <div class="flex items-center">
                            <input type="checkbox" name="{{ $setting->key }}" id="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}
                                class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                        </div>
                        @elseif($setting->type === 'file')
                        <input type="file" name="{{ $setting->key }}" id="{{ $setting->key }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        @else
                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm">
                        @endif

                        @if($group === 'branding' && str_contains($setting->key, 'logo'))
                        <div class="mt-3 p-3 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-dashed border-gray-200 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-2">Live Preview</p>
                            <div class="flex items-center justify-center p-4 bg-white dark:bg-slate-800 rounded shadow-inner min-h-[60px]">
                                <img src="{{ $setting->value }}" alt="Preview" class="max-h-12 w-auto object-contain">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex justify-end gap-3 pt-4">
            <button type="reset" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                Reset
            </button>
            <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

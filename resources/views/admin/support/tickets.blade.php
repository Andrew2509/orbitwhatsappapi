@extends('layouts.admin')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')
@section('page-subtitle', 'Kelola pertanyaan dan keluhan user')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex gap-2">
            <a href="{{ route('admin.support.tickets') }}" class="px-4 py-2 {{ request()->routeIs('admin.support.tickets') ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700' }} rounded-lg transition hover:shadow-sm">
                Tickets
            </a>
            <a href="{{ route('admin.support.announcements') }}" class="px-4 py-2 {{ request()->routeIs('admin.support.announcements') ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700' }} rounded-lg transition hover:shadow-sm">
                Announcements
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold mb-2">Support Tickets</h3>
        <p class="text-slate-500 mb-4">Fitur support ticket akan segera tersedia.</p>
        <p class="text-sm text-slate-400">Anda bisa menggunakan email support untuk sementara.</p>
    </div>
</div>
@endsection


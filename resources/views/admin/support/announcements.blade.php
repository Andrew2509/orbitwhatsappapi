@extends('layouts.admin')

@section('title', 'Announcements')
@section('page-title', 'Admin Announcements')
@section('page-subtitle', 'Kirim pengumuman ke semua user')

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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold mb-2">Announcements</h3>
        <p class="text-slate-500 mb-4">Fitur announcement akan segera tersedia.</p>
        <p class="text-sm text-slate-400">Anda bisa mengirim pengumuman maintenance ke semua dashboard user.</p>
    </div>
</div>
@endsection


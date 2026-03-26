@extends('layouts.admin')

@section('title', 'Error Logs')
@section('page-title', 'System Logs')
@section('page-subtitle', 'Monitor error dan log sistem')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex gap-2">
            <a href="{{ route('admin.system.queue') }}" class="px-4 py-2 {{ request()->routeIs('admin.system.queue') ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700' }} rounded-lg transition hover:shadow-sm">
                Broadcast Queue
            </a>
            <a href="{{ route('admin.system.webhooks') }}" class="px-4 py-2 {{ request()->routeIs('admin.system.webhooks') ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700' }} rounded-lg transition hover:shadow-sm">
                Webhook Logs
            </a>
            <a href="{{ route('admin.system.logs') }}" class="px-4 py-2 {{ request()->routeIs('admin.system.logs') ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700' }} rounded-lg transition hover:shadow-sm">
                Error Logs
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Laravel Log (Last 100 lines)</h3>
        </div>
        <div class="p-4 bg-slate-900 text-slate-100 font-mono text-sm max-h-[600px] overflow-y-auto">
            @forelse($logs as $log)
            <div class="py-1 border-b border-slate-800 {{ str_contains($log, 'ERROR') ? 'text-red-400' : (str_contains($log, 'WARNING') ? 'text-amber-400' : '') }}">
                {{ $log }}
            </div>
            @empty
            <p class="text-slate-500">No logs found</p>
            @endforelse
        </div>
    </div>
</div>
@endsection


@extends('layouts.admin')

@section('title', 'Webhook Logs')
@section('page-title', 'Webhook Logs')
@section('page-subtitle', 'Monitor webhook deliveries')

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

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Success</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['success'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">URL</th>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Response</th>
                        <th class="px-6 py-4 font-medium">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono truncate max-w-xs">{{ $log->webhook?->url ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $log->webhook?->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="admin-badge {{ $log->response_code >= 200 && $log->response_code < 300 ? 'admin-badge-success' : 'admin-badge-danger' }}">
                                {{ $log->response_code ?? 'Error' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 truncate max-w-xs">{{ Str::limit($log->response_body, 50) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $log->created_at?->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada webhook logs</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection


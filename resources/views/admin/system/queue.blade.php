@extends('layouts.admin')

@section('title', 'Broadcast Queue')
@section('page-title', 'Broadcast Queue')
@section('page-subtitle', 'Monitor antrean broadcast')

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
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Running</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['running'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed Today</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['completed_today'] }}</p>
        </div>
    </div>

    <!-- Queue Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Campaign</th>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Recipients</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ $campaign->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $campaign->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $campaign->campaign_recipients_count }}</td>
                        <td class="px-6 py-4">
                            <span class="admin-badge {{ $campaign->status === 'running' ? 'admin-badge-info' : ($campaign->status === 'pending' ? 'admin-badge-warning' : 'admin-badge-success') }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $campaign->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada campaign dalam antrean</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($campaigns->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $campaigns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection


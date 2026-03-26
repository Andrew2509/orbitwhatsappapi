@extends('layouts.admin')

@section('title', 'Devices')
@section('page-title', 'Device Monitor')
@section('page-subtitle', 'Monitor semua device yang terhubung')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Devices</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Connected</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['connected'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Disconnected</p>
            <p class="text-2xl font-bold text-gray-400 dark:text-gray-500">{{ $stats['disconnected'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari device atau user..." 
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
            </div>
            <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                <option value="">Semua Status</option>
                <option value="connected" {{ request('status') === 'connected' ? 'selected' : '' }}>Connected</option>
                <option value="disconnected" {{ request('status') === 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700 transition">Filter</button>
        </form>
    </div>

    <!-- Devices Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Device</th>
                        <th class="px-6 py-4 font-medium">Owner</th>
                        <th class="px-6 py-4 font-medium">Phone</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Last Active</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($devices as $device)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $device->status === 'connected' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $device->status === 'connected' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ $device->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $device->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-mono">{{ $device->phone_number ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="admin-badge {{ $device->status === 'connected' ? 'admin-badge-success' : ($device->status === 'pending' ? 'admin-badge-warning' : 'admin-badge-danger') }}">
                                {{ ucfirst($device->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $device->last_active_at?->diffForHumans() ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($device->status === 'connected')
                            <form action="{{ route('admin.devices.force-logout', $device) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                    Force Logout
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada device ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $devices->links() }}
        </div>
    </div>
</div>
@endsection


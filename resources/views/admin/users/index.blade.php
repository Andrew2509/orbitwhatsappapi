@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page-title', 'User Management')
@section('page-subtitle', 'Kelola semua pengguna platform')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500">
            </div>
            <select name="role" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                <option value="">Semua Role</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
            <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700 transition">Filter</button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Plan</th>
                        <th class="px-6 py-4 font-medium">Devices</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Joined</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-emerald-600 flex items-center justify-center text-white font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="admin-badge {{ $user->role === 'admin' ? 'admin-badge-info' : ($user->role === 'super_admin' ? 'admin-badge-danger' : 'admin-badge-success') }}">
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $user->activeSubscription?->plan?->name ?? 'Free' }}
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $user->devices_count }}</td>
                        <td class="px-6 py-4">
                            @if($user->is_suspended)
                            <span class="admin-badge admin-badge-danger">Suspended</span>
                            @else
                            <span class="admin-badge admin-badge-success">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($user->role !== 'super_admin')
                                <form action="{{ route('admin.users.impersonate', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition" title="Impersonate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </button>
                                </form>
                                @if($user->is_suspended)
                                <form action="{{ route('admin.users.unsuspend', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition text-green-600" title="Unsuspend">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <button type="button" 
                                    class="suspend-btn p-2 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition text-red-600" 
                                    title="Suspend"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">Tidak ada user ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-w-md mx-4 border border-gray-200 dark:border-slate-700 shadow-xl">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Suspend User</h3>
        <form id="suspendForm" method="POST">
            @csrf
            <p class="text-sm text-slate-500 mb-4">Anda akan suspend user: <span id="suspendUserName" class="font-medium text-gray-900 dark:text-white"></span></p>
            <textarea name="reason" rows="3" required placeholder="Alasan suspend..."
                class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white mb-4 transition focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-lg shadow-red-500/20">Suspend</button>
            </div>
        </form>
    </div>
</div>

<script nonce="{{ $csp_nonce }}">
function openSuspendModal(userId, userName) {
    const modal = document.getElementById('suspendModal');
    const form = document.getElementById('suspendForm');
    const nameSpan = document.getElementById('suspendUserName');
    
    if (modal && form && nameSpan) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        nameSpan.textContent = userName;
        form.action = `/admin/users/${userId}/suspend`;
    }
}

function closeSuspendModal() {
    const modal = document.getElementById('suspendModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Add event listeners to all suspend buttons
document.addEventListener('DOMContentLoaded', function() {
    const suspendButtons = document.querySelectorAll('.suspend-btn');
    suspendButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            openSuspendModal(userId, userName);
        });
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSuspendModal();
    });
});
</script>
@endsection


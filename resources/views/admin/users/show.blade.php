@extends('layouts.admin')

@section('title', 'User Detail')
@section('page-title', $user->name)
@section('page-subtitle', $user->email)

@section('content')
<div class="space-y-6">
    <!-- User Info Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="flex items-start gap-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-emerald-600 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <span class="admin-badge {{ $user->role === 'admin' ? 'admin-badge-info' : ($user->role === 'super_admin' ? 'admin-badge-danger' : 'admin-badge-success') }}">
                        {{ ucfirst($user->role ?? 'user') }}
                    </span>
                    @if($user->is_suspended)
                    <span class="admin-badge admin-badge-danger">Suspended</span>
                    @endif
                </div>
                <p class="text-slate-500">{{ $user->email }}</p>
                <p class="text-sm text-slate-400 mt-2">Member since {{ $user->created_at->format('d M Y') }}</p>
            </div>
            <div class="flex gap-2">
                @if($user->role !== 'super_admin')
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.users.impersonate', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                            Impersonate
                        </button>
                    </form>

                    @if($user->is_suspended)
                    <form action="{{ route('admin.users.unsuspend', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Unsuspend
                        </button>
                    </form>
                    @else
                    <button type="button" 
                        class="suspend-btn px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->name }}">
                        Suspend
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subscription Info -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Subscription</h3>
            @if($user->activeSubscription)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-slate-500">Plan</span>
                    <span class="font-medium">{{ $user->activeSubscription->plan->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status</span>
                    <span class="admin-badge admin-badge-success">Active</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Expires</span>
                    <span class="font-medium">{{ $user->activeSubscription->ends_at?->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Auto-Renew</span>
                    <span>{{ $user->activeSubscription->auto_renew ? 'Yes' : 'No' }}</span>
                </div>
            </div>
            @else
            <p class="text-slate-500">Free Plan</p>
            @endif
        </div>

        <!-- Usage This Month -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Usage (Bulan Ini)</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-slate-500">Messages</span>
                    <span class="font-medium">{{ number_format($usageThisMonth->messages ?? 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">API Calls</span>
                    <span class="font-medium">{{ number_format($usageThisMonth->api_calls ?? 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Devices</span>
                    <span class="font-medium">{{ $user->devices->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Invoices</span>
                    <span class="font-medium">{{ $user->invoices->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Paid</span>
                    <span class="font-medium">Rp {{ number_format($user->invoices->where('status', 'paid')->sum('total'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Recent Invoices</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200 dark:border-slate-700">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="pb-3 font-medium">Invoice</th>
                        <th class="pb-3 font-medium">Plan</th>
                        <th class="pb-3 font-medium">Amount</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($user->invoices as $invoice)
                    <tr>
                        <td class="py-3 font-mono text-sm">{{ $invoice->invoice_number }}</td>
                        <td class="py-3">{{ $invoice->subscription?->plan?->name ?? '-' }}</td>
                        <td class="py-3 font-semibold">{{ $invoice->formatted_total }}</td>
                        <td class="py-3">
                            <span class="admin-badge {{ $invoice->status === 'paid' ? 'admin-badge-success' : ($invoice->status === 'pending' ? 'admin-badge-warning' : 'admin-badge-danger') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-sm text-slate-500">{{ $invoice->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">Belum ada invoice</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Devices -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Devices</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($user->devices as $device)
            <div class="p-4 rounded-lg border border-gray-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $device->status === 'connected' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $device->status === 'connected' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium">{{ $device->name }}</p>
                        <p class="text-xs text-slate-500">{{ $device->phone_number ?? 'No number' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-slate-500 col-span-full text-center py-6">Belum ada device</p>
            @endforelse
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
            ← Kembali
        </a>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-md mx-4 border border-gray-200 dark:border-slate-700 shadow-xl">
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


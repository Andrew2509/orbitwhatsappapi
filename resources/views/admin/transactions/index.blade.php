@extends('layouts.admin')

@section('title', 'Transactions')
@section('page-title', 'Transactions')
@section('page-subtitle', 'Kelola semua transaksi pembayaran')

@section('content')
<div class="space-y-6" x-data="transactionManager">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($stats['paid'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($stats['pending'], 0, ',', '.') }}</p>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['pending_count'] }} transaksi</span>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($stats['failed'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-md transition-shadow">
            <a href="{{ route('admin.transactions.reports') }}" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Lihat Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-slate-700">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice atau user..."
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
            </div>
            <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700 transition">Filter</button>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Invoice</th>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Plan</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-6 py-4 font-mono text-sm">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium">{{ $invoice->user->name ?? '-' }}</p>
                                <p class="text-sm text-slate-500">{{ $invoice->user->email ?? '' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->subscription?->plan?->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-semibold">{{ $invoice->formatted_total }}</td>
                        <td class="px-6 py-4">
                            <span class="admin-badge {{ $invoice->status === 'paid' ? 'admin-badge-success' : ($invoice->status === 'pending' ? 'admin-badge-warning' : 'admin-badge-danger') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $invoice->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                @if($invoice->payment_proof)
                                <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition" title="Lihat Bukti Transfer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                                @endif

                                @if($invoice->status === 'pending')
                                <form action="{{ route('admin.transactions.approve', $invoice) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-sm bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                                <button @click="openReject({{ $invoice->id }})" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition" title="Reject">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif
                                <form action="{{ route('admin.transactions.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300 rounded-lg hover:bg-red-100 hover:text-red-700 dark:hover:bg-red-900/50 dark:hover:text-red-400 transition" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">Tidak ada transaksi ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="rejectModalOpen" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center" x-transition style="display: none;">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-w-md mx-4 border border-gray-200 dark:border-slate-700 shadow-xl" @click.away="closeReject()">
            <h3 class="text-lg font-semibold mb-4">Reject Payment</h3>
            <form :action="rejectAction" method="POST">
                @csrf
                <textarea name="reason" rows="3" required placeholder="Alasan penolakan..."
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 mb-4"></textarea>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="closeReject()" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

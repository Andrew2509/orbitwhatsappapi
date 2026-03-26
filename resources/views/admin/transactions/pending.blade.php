@extends('layouts.admin')

@section('title', 'Pending Payments')
@section('page-title', 'Pending Payments')
@section('page-subtitle', 'Pembayaran menunggu approval')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Invoice</th>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Plan</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Bukti Transfer</th>
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
                            @if($invoice->payment_proof)
                            <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="text-emerald-600 hover:text-blue-700 text-sm">
                                Lihat Bukti
                            </a>
                            @else
                            <span class="text-slate-400 text-sm">Belum upload</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $invoice->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <form action="{{ route('admin.transactions.approve', $invoice) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-sm bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition">
                                        Approve
                                    </button>
                                </form>
                                <button onclick="openRejectModal({{ $invoice->id }})" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>Tidak ada pembayaran pending</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-w-md mx-4 border border-gray-200 dark:border-slate-700 shadow-xl">
        <h3 class="text-lg font-semibold mb-4">Reject Payment</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" required placeholder="Alasan penolakan..."
                class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 mb-4"></textarea>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>

<script nonce="{{ $csp_nonce }}">
function openRejectModal(invoiceId) {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
    document.getElementById('rejectForm').action = `/admin/transactions/${invoiceId}/reject`;
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}
</script>
@endsection


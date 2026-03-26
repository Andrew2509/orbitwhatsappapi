@extends('layouts.app')

@section('title', 'Riwayat Invoice')
@section('page-title', 'Riwayat Invoice')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">Semua Invoice</h2>
            <p class="text-sm text-[var(--text-muted)]">Riwayat pembayaran langganan Anda</p>
        </div>
        <a href="{{ route('billing.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Invoice Table -->
    <div class="card">
        @if($invoices->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Paket</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="font-mono text-sm">{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->created_at->format('d M Y') }}</td>
                        <td>{{ $invoice->subscription?->plan?->name ?? '-' }}</td>
                        <td class="font-semibold">{{ $invoice->formatted_total }}</td>
                        <td>
                            <span class="badge {{ $invoice->status_badge }}">
                                {{ $invoice->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                @if($invoice->isPending())
                                <a href="{{ route('billing.checkout', $invoice) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                    Bayar
                                </a>
                                @endif
                                <a href="{{ route('billing.invoice.download', $invoice) }}" class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
        @else
        <div class="text-center py-12 text-[var(--text-muted)]">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-semibold mb-2">Belum Ada Invoice</h3>
            <p class="mb-4">Anda belum memiliki riwayat invoice</p>
            <a href="{{ route('billing.plans') }}" class="btn btn-primary">
                Lihat Paket
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #555; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; color: #555; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td{ border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        .status-paid { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h1 style="margin: 0; font-size: 24px;">INVOICE</h1>
                            </td>
                            <td>
                                Invoice #: {{ $invoice->invoice_number }}<br>
                                Created: {{ $invoice->created_at->format('d M Y') }}<br>
                                Due: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Orbit Whatsapp API</strong><br>
                                support@orbit.com
                            </td>
                            <td>
                                <strong>{{ $invoice->user->name }}</strong><br>
                                {{ $invoice->user->email }}<br>
                                {{ $invoice->company_name ?? '' }}<br>
                                {{ $invoice->billing_address ?? '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Description</td>
                <td>Price</td>
            </tr>
            <tr class="item">
                <td>{{ $invoice->subscription->plan->name }} Plan (1 Month)</td>
                <td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
             <tr class="item">
                <td>Tax (11%)</td>
                <td>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Total: Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <br>

        <div style="margin-top: 20px;">
            Status:
            <span class="status-{{ $invoice->status }}">
                @if($invoice->status == 'paid')
                    PAID on {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y H:i') : '' }}
                @else
                    {{ strtoupper($invoice->status) }}
                @endif
            </span>
        </div>

        @if($invoice->payment_proof)
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <h3>Payment Proof</h3>
        @if($invoice->payment_proof)
            <div style="margin-top: 20px;">
                <p><strong>Bukti Pembayaran:</strong></p>
                <img src="{{ $invoice->payment_proof }}" style="max-width: 300px; border: 1px solid #ddd;">
            </div>
        @endif
        </div>
        @endif

    </div>
</body>
</html>

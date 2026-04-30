<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 11px;
        color: #2c1020;
        background: #fff;
        padding: 32px;
    }

    /* ── Header ── */
    .receipt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 16px;
        border-bottom: 2px solid #5e2039;
        margin-bottom: 20px;
    }

    .brand-block .brand-name {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: .14em;
        color: #5e2039;
        text-transform: uppercase;
    }

    .brand-block .brand-sub {
        font-size: 9.5px;
        color: #9e7286;
        margin-top: 2px;
        letter-spacing: .04em;
    }

    .receipt-meta { text-align: right; }

    .receipt-meta .receipt-label {
        font-size: 9px;
        color: #9e7286;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .receipt-meta .receipt-num {
        font-size: 14px;
        font-weight: 700;
        color: #5e2039;
        margin-top: 2px;
    }

    .receipt-meta .receipt-date {
        font-size: 10px;
        color: #9e7286;
        margin-top: 2px;
    }

    /* ── Section heading ── */
    .section-label {
        font-size: 9px;
        font-weight: 700;
        color: #9e7286;
        text-transform: uppercase;
        letter-spacing: .1em;
        margin-bottom: 8px;
        padding-bottom: 4px;
        border-bottom: 1px solid #f0e6ec;
    }

    /* ── Info grid ── */
    .info-grid {
        display: flex;
        gap: 32px;
        margin-bottom: 20px;
    }

    .info-col { flex: 1; }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 10.5px;
        border-bottom: 1px solid #faf3f6;
    }

    .info-row:last-child { border-bottom: none; }
    .info-key   { color: #9e7286; }
    .info-val   { font-weight: 600; color: #2c1020; text-align: right; }

    /* ── Products table ── */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    thead th {
        background: #5e2039;
        color: #fff;
        padding: 7px 10px;
        font-size: 9.5px;
        font-weight: 600;
        text-align: left;
        letter-spacing: .04em;
    }

    thead th:last-child { text-align: right; }
    thead th:nth-child(2),
    thead th:nth-child(3) { text-align: center; }

    tbody tr:nth-child(even) { background: #fdf5f8; }

    tbody td {
        padding: 7px 10px;
        font-size: 10.5px;
        border-bottom: 1px solid #f5edf1;
        vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }

    td.center { text-align: center; }
    td.right  { text-align: right; }
    td.muted  { color: #9e7286; }

    /* ── Totals ── */
    .totals-wrap {
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1.5px solid #f0e6ec;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        padding: 3px 0;
        color: #9e7286;
    }

    .total-row.grand {
        font-size: 13px;
        font-weight: 700;
        color: #2c1020;
        padding-top: 8px;
        margin-top: 6px;
        border-top: 1.5px solid #5e2039;
    }

    /* ── This payment highlight ── */
    .this-payment {
        background: #f5e6ed;
        border-radius: 8px;
        padding: 12px 14px;
        margin: 16px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .this-payment-label {
        font-size: 10px;
        color: #9e7286;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .this-payment-amount {
        font-size: 18px;
        font-weight: 700;
        color: #5e2039;
    }

    .this-payment-method {
        font-size: 10px;
        color: #9e7286;
        margin-top: 2px;
    }

    /* ── Balance ── */
    .balance-box {
        background: #fff8f0;
        border: 1px solid #fde5c3;
        border-radius: 6px;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        margin-bottom: 20px;
    }

    .balance-box.settled {
        background: #f0faf0;
        border-color: #c3e6cb;
    }

    .balance-key   { color: #7a4d00; }
    .balance-val   { font-weight: 700; color: #7a4d00; }
    .settled-key   { color: #2e7d32; }
    .settled-val   { font-weight: 700; color: #2e7d32; }

    /* ── Footer ── */
    .receipt-footer {
        margin-top: 24px;
        padding-top: 12px;
        border-top: 1px solid #f0e6ec;
        text-align: center;
        font-size: 9px;
        color: #c4a0b0;
        line-height: 1.6;
    }
</style>
</head>
<body>

{{-- ── Header ── --}}
<div class="receipt-header">
    <div class="brand-block">
        <div class="brand-name">SKINETIQUE</div>
        <div class="brand-sub">Official Payment Receipt</div>
    </div>
    <div class="receipt-meta">
        <div class="receipt-label">Receipt No.</div>
        <div class="receipt-num">{{ $payment->receipt->receipt_num }}</div>
        <div class="receipt-date">Issued: {{ $payment->receipt->issued_at->format('F d, Y \a\t h:i A') }}</div>
    </div>
</div>

{{-- ── Order & Customer Info ── --}}
<div class="info-grid">
    <div class="info-col">
        <div class="section-label">Order Details</div>
        <div class="info-row">
            <span class="info-key">Order ID</span>
            <span class="info-val">{{ $payment->order->order_label }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">Order Date</span>
            <span class="info-val">{{ $payment->order->order_date->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">Delivery</span>
            <span class="info-val">{{ $payment->order->deliveryMethod->label }}</span>
        </div>
    </div>
    <div class="info-col">
        <div class="section-label">Customer</div>
        <div class="info-row">
            <span class="info-key">Name</span>
            <span class="info-val">{{ $payment->order->customer->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">Contact</span>
            <span class="info-val">{{ $payment->order->customer->contact_num }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">Address</span>
            <span class="info-val">{{ $payment->order->customer->address }}</span>
        </div>
    </div>
</div>

{{-- ── Products ── --}}
<div class="section-label">Items Ordered</div>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th style="text-align:center;">Qty</th>
            <th style="text-align:right;">Unit Price</th>
            <th style="text-align:right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payment->order->orderLines as $line)
            <tr>
                <td>{{ $line->product->name }}</td>
                <td class="center">{{ $line->quantity }}</td>
                <td class="right muted">PHP {{ number_format($line->unit_price, 2) }}</td>
                <td class="right">PHP {{ number_format($line->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="totals-wrap">
    <div class="total-row">
        <span>Subtotal</span>
        <span>PHP {{ number_format($payment->order->subtotal, 2) }}</span>
    </div>
    <div class="total-row">
        <span>Delivery Fee</span>
        <span>{{ $payment->order->delivery_fee > 0 ? 'PHP ' . number_format($payment->order->delivery_fee, 2) : 'Free' }}</span>
    </div>
    <div class="total-row grand">
        <span>Order Total</span>
        <span>PHP {{ number_format($payment->order->total, 2) }}</span>
    </div>
</div>

{{-- ── This Payment ── --}}
<div class="this-payment">
    <div>
        <div class="this-payment-label">Amount Paid (This Receipt)</div>
        <div class="this-payment-method">via {{ $payment->paymentMethod->name }} · {{ $payment->payment_date->format('M d, Y') }}</div>
    </div>
    <div class="this-payment-amount">PHP {{ number_format($payment->amount, 2) }}</div>
</div>

{{-- ── Remaining balance ── --}}
@php
    $totalPaid      = $payment->order->payments->sum('amount');
    $totalRemaining = $payment->order->total - $totalPaid;
@endphp

@if ($totalRemaining <= 0)
    <div class="balance-box settled">
        <span class="settled-key">Status</span>
        <span class="settled-val">Fully Paid — Thank you!</span>
    </div>
@else
    <div class="balance-box">
        <span class="balance-key">Remaining Balance</span>
        <span class="balance-val">PHP {{ number_format($totalRemaining, 2) }}</span>
    </div>
@endif

{{-- ── Payment history ── --}}
@if ($payment->order->payments->count() > 1)
    <div class="section-label" style="margin-top: 16px;">Full Payment History</div>
    <table>
        <thead>
            <tr>
                <th>Receipt No.</th>
                <th>Date</th>
                <th>Method</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payment->order->payments->sortBy('payment_date') as $p)
                <tr @if ($p->id === $payment->id) style="font-weight:700;" @endif>
                    <td>{{ $p->receipt->receipt_num ?? '—' }}</td>
                    <td>{{ $p->payment_date->format('M d, Y') }}</td>
                    <td>{{ $p->paymentMethod->name }}</td>
                    <td class="right">PHP {{ number_format($p->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- ── Footer ── --}}
<div class="receipt-footer">
    SKINETIQUE · Davao City, Philippines<br>
    This is an official receipt generated by the SKINETIQUE Records Management System.<br>
    Thank you for your purchase!
</div>

</body>
</html>
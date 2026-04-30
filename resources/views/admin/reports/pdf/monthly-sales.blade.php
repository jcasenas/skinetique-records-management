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
    }

    /* ── Header ── */
    .header {
        background: #5e2039;
        color: #fff;
        padding: 22px 32px 18px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }

    .brand { font-size: 22px; font-weight: 700; letter-spacing: .12em; }
    .report-title { font-size: 13px; opacity: .85; margin-top: 3px; }
    .header-right { text-align: right; font-size: 10px; opacity: .75; line-height: 1.6; }

    /* ── Summary row ── */
    .summary-row {
        display: flex;
        gap: 16px;
        margin: 0 32px 22px;
    }

    .summary-box {
        flex: 1;
        background: #f8e1eb;
        border-radius: 8px;
        padding: 14px 16px;
        border-left: 3px solid #7b2d4e;
    }

    .summary-box-label { font-size: 10px; color: #9e7286; font-weight: 600; margin-bottom: 4px; }
    .summary-box-value { font-size: 18px; font-weight: 700; color: #5e2039; }

    /* ── Section title ── */
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: #5e2039;
        margin: 0 32px 10px;
        padding-bottom: 6px;
        border-bottom: 1.5px solid #f0e6ec;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    /* ── Table ── */
    table {
        width: calc(100% - 64px);
        margin: 0 32px 24px;
        border-collapse: collapse;
    }

    thead th {
        background: #5e2039;
        color: #fff;
        padding: 9px 12px;
        font-size: 10px;
        font-weight: 600;
        text-align: left;
        letter-spacing: .04em;
    }

    tbody tr:nth-child(even) { background: #fdf5f8; }

    tbody td {
        padding: 8px 12px;
        font-size: 10px;
        color: #2c1020;
        border-bottom: 1px solid #f5edf1;
        vertical-align: top;
    }

    .badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 600;
    }

    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-partial  { background: #cfe2ff; color: #0a58ca; }
    .badge-paid     { background: #d1e7dd; color: #0f5132; }

    /* ── Footer ── */
    .footer {
        margin: 0 32px;
        padding-top: 12px;
        border-top: 1px solid #f0e6ec;
        font-size: 9px;
        color: #9e7286;
        display: flex;
        justify-content: space-between;
    }

    .totals-row {
        background: #5e2039 !important;
        color: #fff;
    }

    .totals-row td {
        font-weight: 700;
        color: #fff !important;
        border: none;
        font-size: 10px;
    }

    .empty { text-align: center; padding: 24px; color: #9e7286; font-style: italic; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="brand">SKINETIQUE</div>
        <div class="report-title">Monthly Sales Report &mdash; {{ $monthName }}</div>
    </div>
    <div class="header-right">
        Generated: {{ now()->format('F d, Y') }}<br>
        Period: {{ $monthName }}
    </div>
</div>

{{-- Summary boxes --}}
<div class="summary-row">
    <div class="summary-box">
        <div class="summary-box-label">Total Orders</div>
        <div class="summary-box-value">{{ $totalOrders }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Revenue</div>
        <div class="summary-box-value">PHP {{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Subtotal</div>
        <div class="summary-box-value">PHP {{ number_format($totalSubtotal, 2) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Delivery Fees</div>
        <div class="summary-box-value">PHP {{ number_format($totalDelivery, 2) }}</div>
    </div>
</div>

{{-- Payment status breakdown --}}
<div class="section-title">Payment Status Breakdown</div>
<div class="summary-row">
    @foreach (['pending' => 'Pending', 'partial' => 'Partial', 'fully_paid' => 'Fully Paid'] as $key => $label)
        <div class="summary-box">
            <div class="summary-box-label">{{ $label }}</div>
            <div class="summary-box-value">{{ $byStatus->get($key, 0) }}</div>
        </div>
    @endforeach
</div>

{{-- Orders table --}}
<div class="section-title">Order Details</div>

@if ($orders->isEmpty())
    <p class="empty">No orders found for {{ $monthName }}.</p>
@else
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Delivery Method</th>
                <th>Date</th>
                <th>Subtotal</th>
                <th>Delivery Fee</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->order_label }}</td>
                    <td>{{ $order->customer->full_name }}</td>
                    <td>{{ $order->deliveryMethod->label }}</td>
                    <td>{{ $order->order_date->format('m/d/Y') }}</td>
                    <td>PHP {{ number_format($order->subtotal, 2) }}</td>
                    <td>PHP {{ number_format($order->delivery_fee, 2) }}</td>
                    <td>PHP {{ number_format($order->total, 2) }}</td>
                    <td>
                        <span class="badge {{ match($order->payment_status) {
                            'pending'    => 'badge-pending',
                            'partial'    => 'badge-partial',
                            'fully_paid' => 'badge-paid',
                            default      => '',
                        } }}">
                            {{ $order->payment_status === 'fully_paid' ? 'Paid' : ucfirst($order->payment_status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
            <tr class="totals-row">
                <td colspan="4">TOTAL</td>
                <td>PHP {{ number_format($totalSubtotal, 2) }}</td>
                <td>PHP {{ number_format($totalDelivery, 2) }}</td>
                <td>PHP {{ number_format($totalRevenue, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endif

<div class="footer">
    <span>SKINETIQUE Records Management System</span>
    <span>Confidential &mdash; For internal use only</span>
</div>

</body>
</html>
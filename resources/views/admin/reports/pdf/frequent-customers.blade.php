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

    .header {
        background: #5e2039;
        color: #fff;
        padding: 22px 32px 18px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .brand        { font-size: 22px; font-weight: 700; letter-spacing: .12em; }
    .report-title { font-size: 13px; opacity: .85; margin-top: 3px; }
    .header-right { text-align: right; font-size: 10px; opacity: .75; line-height: 1.6; }

    .summary-row { display: flex; gap: 16px; margin: 0 32px 22px; }

    .summary-box {
        flex: 1;
        background: #f8e1eb;
        border-radius: 8px;
        padding: 14px 16px;
        border-left: 3px solid #7b2d4e;
    }

    .summary-box-label { font-size: 10px; color: #9e7286; font-weight: 600; margin-bottom: 4px; }
    .summary-box-value { font-size: 18px; font-weight: 700; color: #5e2039; }

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
        padding: 9px 12px;
        font-size: 10px;
        color: #2c1020;
        border-bottom: 1px solid #f5edf1;
        vertical-align: middle;
    }

    .rank-cell { font-size: 13px; font-weight: 700; text-align: center; width: 32px; }
    .rank-1 { color: #b8860b; }
    .rank-2 { color: #808080; }
    .rank-3 { color: #8b4513; }

    .totals-row td { font-weight: 700; background: #f5e6ed; border-top: 2px solid #7b2d4e; }

    .footer {
        margin: 0 32px;
        padding-top: 12px;
        border-top: 1px solid #f0e6ec;
        font-size: 9px;
        color: #9e7286;
        display: flex;
        justify-content: space-between;
    }

    .empty { text-align: center; padding: 24px; color: #9e7286; font-style: italic; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="brand">SKINETIQUE</div>
        <div class="report-title">Frequent Customers Report &mdash; {{ $year }}</div>
    </div>
    <div class="header-right">
        Generated: {{ now()->format('F d, Y') }}<br>
        Period: January &mdash; December {{ $year }}
    </div>
</div>

<div class="summary-row">
    <div class="summary-box">
        <div class="summary-box-label">Customers with Orders</div>
        <div class="summary-box-value">{{ $customers->count() }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Orders</div>
        <div class="summary-box-value">{{ number_format($grandTotalOrders) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Revenue</div>
        <div class="summary-box-value">PHP {{ number_format($grandTotalSpent, 2) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Avg. Orders / Customer</div>
        <div class="summary-box-value">
            {{ $customers->count() > 0 ? number_format($grandTotalOrders / $customers->count(), 1) : '0' }}
        </div>
    </div>
</div>

<div class="section-title">Customer Rankings</div>

@if ($customers->isEmpty())
    <p class="empty">No order data found for {{ $year }}.</p>
@else
    <table>
        <thead>
            <tr>
                <th style="width:32px">Rank</th>
                <th>Customer</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Orders</th>
                <th>Avg. Order Value</th>
                <th>Last Order</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $i => $row)
                <tr>
                    <td class="rank-cell {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : '')) }}">
                        {{ $i + 1 }}
                    </td>
                    <td><strong>{{ $row->customer->full_name ?? '—' }}</strong></td>
                    <td>{{ $row->customer->address ?? '—' }}</td>
                    <td>{{ $row->customer->contact_num ?? '—' }}</td>
                    <td>{{ number_format($row->order_count) }}</td>
                    <td>PHP {{ number_format($row->avg_order_value, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->last_order_date)->format('m/d/Y') }}</td>
                    <td>PHP {{ number_format($row->total_spent, 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals-row">
                <td colspan="4">TOTAL</td>
                <td>{{ number_format($grandTotalOrders) }}</td>
                <td></td>
                <td></td>
                <td>PHP {{ number_format($grandTotalSpent, 2) }}</td>
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
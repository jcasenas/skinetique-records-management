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

    .summary-row   { display: flex; gap: 16px; margin: 0 32px 22px; }

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

    .rank-cell {
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        width: 32px;
    }

    .rank-1 { color: #b8860b; }
    .rank-2 { color: #808080; }
    .rank-3 { color: #8b4513; }

    .bar-wrap { width: 80px; height: 8px; background: #f0e6ec; border-radius: 99px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 6px; }
    .bar-fill  { height: 100%; background: #7b2d4e; border-radius: 99px; }

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
        <div class="report-title">Bestselling Products Report &mdash; {{ $year }}</div>
    </div>
    <div class="header-right">
        Generated: {{ now()->format('F d, Y') }}<br>
        Period: January &mdash; December {{ $year }}
    </div>
</div>

<div class="summary-row">
    <div class="summary-box">
        <div class="summary-box-label">Total Products Ranked</div>
        <div class="summary-box-value">{{ $products->count() }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Units Sold</div>
        <div class="summary-box-value">{{ number_format($grandTotalQty) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Revenue</div>
        <div class="summary-box-value">PHP {{ number_format($grandTotalRevenue, 2) }}</div>
    </div>
</div>

<div class="section-title">Product Rankings</div>

@if ($products->isEmpty())
    <p class="empty">No sales data found for {{ $year }}.</p>
@else
    <table>
        <thead>
            <tr>
                <th style="width:32px">Rank</th>
                <th>Product</th>
                <th>Supplier</th>
                <th>Units Sold</th>
                <th>Share</th>
                <th>Orders</th>
                <th>Avg. Price</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $i => $row)
                @php
                    $sharePct = $grandTotalQty > 0 ? round(($row->total_qty / $grandTotalQty) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="rank-cell {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : '')) }}">
                        {{ $i + 1 }}
                    </td>
                    <td><strong>{{ $row->product->name ?? '—' }}</strong></td>
                    <td>{{ $row->product->supplier->name ?? '—' }}</td>
                    <td>{{ number_format($row->total_qty) }}</td>
                    <td>
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width:{{ $sharePct }}%"></div>
                        </div>
                        {{ $sharePct }}%
                    </td>
                    <td>{{ number_format($row->order_count) }}</td>
                    <td>PHP {{ number_format($row->avg_price, 2) }}</td>
                    <td>PHP {{ number_format($row->total_revenue, 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals-row">
                <td colspan="3">TOTAL</td>
                <td>{{ number_format($grandTotalQty) }}</td>
                <td>100%</td>
                <td></td>
                <td></td>
                <td>PHP {{ number_format($grandTotalRevenue, 2) }}</td>
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
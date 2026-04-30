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
        padding: 8px 12px;
        font-size: 10px;
        color: #2c1020;
        border-bottom: 1px solid #f5edf1;
        vertical-align: middle;
    }

    .totals-row td { font-weight: 700; background: #f5e6ed !important; border-top: 2px solid #7b2d4e; }

    .two-col { display: flex; gap: 20px; margin: 0 32px 24px; }
    .two-col table { width: 100%; margin: 0; }
    .two-col-wrap { flex: 1; }
    .two-col-wrap .section-title { margin: 0 0 10px; }

    .rank-cell { font-weight: 700; text-align: center; }
    .rank-1 { color: #b8860b; }
    .rank-2 { color: #808080; }
    .rank-3 { color: #8b4513; }

    .month-bar-wrap { width: 60px; height: 7px; background: #f0e6ec; border-radius: 99px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 4px; }
    .month-bar-fill  { height: 100%; background: #7b2d4e; border-radius: 99px; }

    .footer {
        margin: 0 32px;
        padding-top: 12px;
        border-top: 1px solid #f0e6ec;
        font-size: 9px;
        color: #9e7286;
        display: flex;
        justify-content: space-between;
    }

    .empty { text-align: center; padding: 16px; color: #9e7286; font-style: italic; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="brand">SKINETIQUE</div>
        <div class="report-title">Annual Business Summary &mdash; {{ $year }}</div>
    </div>
    <div class="header-right">
        Generated: {{ now()->format('F d, Y') }}<br>
        Period: January &mdash; December {{ $year }}
    </div>
</div>

{{-- Top-level summary --}}
<div class="summary-row">
    <div class="summary-box">
        <div class="summary-box-label">Annual Revenue</div>
        <div class="summary-box-value">PHP {{ number_format($annualRevenue, 2) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Orders</div>
        <div class="summary-box-value">{{ number_format($annualOrders) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Avg. Monthly Revenue</div>
        <div class="summary-box-value">
            PHP {{ $monthlySales->count() > 0 ? number_format($annualRevenue / 12, 2) : '0.00' }}
        </div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Peak Month</div>
        @php
            $peak = $monthlySales->sortByDesc('revenue')->first();
            $peakLabel = $peak ? now()->setDate($year, $peak->month, 1)->format('F') : '—';
        @endphp
        <div class="summary-box-value">{{ $peakLabel }}</div>
    </div>
</div>

{{-- Monthly breakdown --}}
<div class="section-title">Monthly Revenue Breakdown</div>

@php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp

<table>
    <thead>
        <tr>
            <th>Month</th>
            <th>Orders</th>
            <th>Subtotal</th>
            <th>Delivery</th>
            <th>Revenue</th>
            <th style="width:120px">Share of Year</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($months as $i => $label)
            @php $row = $monthlySales->get($i + 1); @endphp
            <tr>
                <td><strong>{{ $label }}</strong></td>
                <td>{{ $row ? number_format($row->order_count) : '—' }}</td>
                <td>{{ $row ? 'PHP ' . number_format($row->subtotal, 2) : '—' }}</td>
                <td>{{ $row ? 'PHP ' . number_format($row->delivery_fee, 2) : '—' }}</td>
                <td>{{ $row ? 'PHP ' . number_format($row->revenue, 2) : '—' }}</td>
                <td>
                    @if ($row && $annualRevenue > 0)
                        @php $pct = round(($row->revenue / $annualRevenue) * 100, 1); @endphp
                        <div class="month-bar-wrap">
                            <div class="month-bar-fill" style="width:{{ $pct }}%"></div>
                        </div>
                        {{ $pct }}%
                    @else
                        —
                    @endif
                </td>
            </tr>
        @endforeach
        <tr class="totals-row">
            <td>TOTAL</td>
            <td>{{ number_format($annualOrders) }}</td>
            <td>PHP {{ number_format($monthlySales->sum('subtotal'), 2) }}</td>
            <td>PHP {{ number_format($monthlySales->sum('delivery_fee'), 2) }}</td>
            <td>PHP {{ number_format($annualRevenue, 2) }}</td>
            <td>100%</td>
        </tr>
    </tbody>
</table>

{{-- Side-by-side: top products + top customers --}}
<div class="two-col">

    <div class="two-col-wrap">
        <div class="section-title">Top 5 Products</div>
        @if ($topProducts->isEmpty())
            <p class="empty">No data.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:28px">#</th>
                        <th>Product</th>
                        <th>Units</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProducts as $i => $row)
                        <tr>
                            <td class="rank-cell {{ $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'')) }}">{{ $i+1 }}</td>
                            <td>{{ $row->product->name ?? '—' }}</td>
                            <td>{{ number_format($row->total_qty) }}</td>
                            <td>PHP {{ number_format($row->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="two-col-wrap">
        <div class="section-title">Top 5 Customers</div>
        @if ($topCustomers->isEmpty())
            <p class="empty">No data.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:28px">#</th>
                        <th>Customer</th>
                        <th>Orders</th>
                        <th>Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topCustomers as $i => $row)
                        <tr>
                            <td class="rank-cell {{ $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'')) }}">{{ $i+1 }}</td>
                            <td>{{ $row->customer->full_name ?? '—' }}</td>
                            <td>{{ number_format($row->order_count) }}</td>
                            <td>PHP {{ number_format($row->total_spent, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

<div class="footer">
    <span>SKINETIQUE Records Management System</span>
    <span>Confidential &mdash; For internal use only</span>
</div>

</body>
</html>
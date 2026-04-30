@extends('admin.layout')

@section('title', 'Business Reports')

@push('styles')
<style>
    /* ── Summary cards ── */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 36px;
    }

    .summary-card {
        background: #fff;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        border-left: 4px solid var(--primary);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .summary-card.accent-warn  { border-left-color: #c47a1e; }
    .summary-card.accent-warn .summary-card-value { color: #c47a1e; }
    .summary-card.accent-purple { border-left-color: #6a1b9a; }
    .summary-card.accent-purple .summary-card-value { color: #6a1b9a; }

    .summary-card-label { font-size: 13px; color: var(--muted); font-weight: 500; }
    .summary-card-value { font-size: 30px; font-weight: 700; color: var(--primary); line-height: 1.1; }
    .summary-card-sub   { font-size: 12px; color: var(--muted); }

    /* ── Section title ── */
    .section-heading {
        font-size: 17px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-heading::after {
        content: '';
        flex: 1;
        height: 1.5px;
        background: #f0e6ec;
        border-radius: 99px;
    }

    /* ── Report cards ── */
    .report-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 36px;
    }

    .report-card {
        background: #fff;
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: box-shadow .2s, transform .2s;
    }

    .report-card:hover {
        box-shadow: 0 6px 24px rgba(94,32,57,.12);
        transform: translateY(-2px);
    }

    .report-card-top { display: flex; align-items: flex-start; gap: 14px; }

    .report-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: #f5e6ed;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .report-icon svg { width: 22px; height: 22px; stroke: var(--primary); }

    .report-card-info { flex: 1; }
    .report-card-title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .report-card-desc  { font-size: 13px; color: var(--muted); line-height: 1.5; }

    .report-filters {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-select {
        padding: 7px 28px 7px 10px;
        border: 1.5px solid #e8d5dd;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        color: var(--text);
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237b2d4e' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 8px center;
        appearance: none;
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
    }

    .filter-select:focus { border-color: var(--primary); }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        text-decoration: none;
        transition: opacity .15s, transform .1s;
        white-space: nowrap;
    }

    .btn-export:hover  { opacity: .88; }
    .btn-export:active { transform: scale(.97); }
    .btn-export svg { width: 15px; height: 15px; stroke: #fff; }

    /* ── Preview tables ── */
    .preview-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 36px;
    }

    .preview-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
    }

    .preview-card-header {
        padding: 16px 20px;
        border-bottom: 1.5px solid #f0e6ec;
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .preview-table { width: 100%; border-collapse: collapse; }

    .preview-table th {
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-align: left;
        border-bottom: 1px solid #f5edf1;
        white-space: nowrap;
    }

    .preview-table td {
        padding: 10px 16px;
        font-size: 13px;
        color: var(--text);
        border-bottom: 1px solid #f9f3f6;
    }

    .preview-table tr:last-child td { border-bottom: none; }
    .preview-table tr:hover td { background: #fdf5f8; }

    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px; height: 22px;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
    }

    .rank-1     { background: #ffd700; color: #6b4c00; }
    .rank-2     { background: #e0e0e0; color: #444; }
    .rank-3     { background: #cd7f32; color: #fff; }
    .rank-other { background: #f5e6ed; color: var(--primary); }

    .empty-preview { padding: 32px; text-align: center; font-size: 13px; color: var(--muted); }

    /* ── Monthly mini-table ── */
    .monthly-table-wrap {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
        margin-bottom: 36px;
    }

    .monthly-table-header {
        padding: 16px 20px;
        border-bottom: 1.5px solid #f0e6ec;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .monthly-table-title { font-size: 14px; font-weight: 700; color: var(--text); }

    /* ── Inventory health section ── */
    .inventory-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 36px;
    }

    /* Reason badge pills */
    .reason-pill {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .reason-pill.damaged    { background: #fce8ef; color: #c0392b; }
    .reason-pill.lost       { background: #fff8e1; color: #856404; }
    .reason-pill.expired    { background: #f0e6ff; color: #6a3aad; }
    .reason-pill.correction { background: #e3f2fd; color: #1565c0; }

    /* Adjustment breakdown mini-grid */
    .adj-breakdown {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 16px 20px;
    }

    .adj-reason-card {
        background: #faf5f7;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .adj-reason-label { font-size: 12px; font-weight: 600; color: var(--muted); }
    .adj-reason-count { font-size: 20px; font-weight: 700; color: var(--text); }
    .adj-reason-units { font-size: 11px; color: var(--muted); }

    .adj-reason-card.damaged    { background: #fce8ef; }
    .adj-reason-card.damaged    .adj-reason-count { color: #c0392b; }
    .adj-reason-card.lost       { background: #fff8e1; }
    .adj-reason-card.lost       .adj-reason-count { color: #856404; }
    .adj-reason-card.expired    { background: #f0e6ff; }
    .adj-reason-card.expired    .adj-reason-count { color: #6a3aad; }
    .adj-reason-card.correction { background: #e3f2fd; }
    .adj-reason-card.correction .adj-reason-count { color: #1565c0; }
</style>
@endpush

@section('content')

    <h1 class="page-title">Business Reports</h1>

    {{-- ── Summary strip — 3 original + 2 new ── --}}
    <div class="summary-grid">
        <div class="summary-card">
            <span class="summary-card-label">Total Revenue ({{ $currentYear }})</span>
            <span class="summary-card-value">₱{{ number_format($totalRevenue, 2) }}</span>
            <span class="summary-card-sub">From archived (fulfilled) orders</span>
        </div>
        <div class="summary-card">
            <span class="summary-card-label">Total Orders ({{ $currentYear }})</span>
            <span class="summary-card-value">{{ number_format($totalOrders) }}</span>
            <span class="summary-card-sub">Fulfilled orders this year</span>
        </div>
        <div class="summary-card">
            <span class="summary-card-label">Total Customers</span>
            <span class="summary-card-value">{{ number_format($totalCustomers) }}</span>
            <span class="summary-card-sub">All registered customers</span>
        </div>
        <div class="summary-card accent-purple">
            <span class="summary-card-label">Units Returned ({{ $currentYear }})</span>
            <span class="summary-card-value">{{ number_format($totalReturnedUnits) }}</span>
            <span class="summary-card-sub">
                {{ number_format($totalReturnTransactions) }} {{ Str::plural('transaction', $totalReturnTransactions) }}
                · ₱{{ number_format($totalRefunded, 2) }} refunded
            </span>
        </div>
        <div class="summary-card accent-warn">
            <span class="summary-card-label">Units Adjusted Out ({{ $currentYear }})</span>
            <span class="summary-card-value">{{ number_format($totalAdjustedUnits) }}</span>
            <span class="summary-card-sub">
                {{ number_format($totalAdjustmentTransactions) }} {{ Str::plural('adjustment', $totalAdjustmentTransactions) }}
                · damaged, lost, or expired
            </span>
        </div>
        {{-- Sixth card: net inventory loss = adjustments + returns (units that left without generating revenue) --}}
        <div class="summary-card" style="border-left-color:#888; opacity:.85;">
            <span class="summary-card-label">Total Inventory Loss ({{ $currentYear }})</span>
            <span class="summary-card-value" style="color:#555; font-size:26px;">
                {{ number_format($totalReturnedUnits + $totalAdjustedUnits) }} units
            </span>
            <span class="summary-card-sub">Returns + adjustments combined</span>
        </div>
    </div>

    {{-- ── Export report cards ── --}}
    <div class="section-heading">Generate & Export Reports</div>

    <div class="report-cards">

        {{-- Monthly Sales --}}
        <div class="report-card">
            <div class="report-card-top">
                <div class="report-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8"  y1="2" x2="8"  y2="6"/>
                        <line x1="3"  y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="report-card-info">
                    <div class="report-card-title">Monthly Sales Report</div>
                    <div class="report-card-desc">Full breakdown of orders, revenue, delivery fees, and payment status for any given month.</div>
                </div>
            </div>
            <div class="report-filters">
                <select class="filter-select" id="ms-year">
                    @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                        <option value="{{ $y }}" {{ $y === $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select class="filter-select" id="ms-month">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $m === $currentMonth ? 'selected' : '' }}>
                            {{ now()->setDate($currentYear, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
                <a href="#" class="btn-export" onclick="exportReport('monthly-sales', ['ms-year', 'ms-month']); return false;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Bestselling Products --}}
        <div class="report-card">
            <div class="report-card-top">
                <div class="report-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div class="report-card-info">
                    <div class="report-card-title">Bestselling Products</div>
                    <div class="report-card-desc">Ranked list of products by units sold, total revenue, and order frequency for the year.</div>
                </div>
            </div>
            <div class="report-filters">
                <select class="filter-select" id="bs-year">
                    @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                        <option value="{{ $y }}" {{ $y === $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <a href="#" class="btn-export" onclick="exportReport('bestsellers', ['bs-year']); return false;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Frequent Customers --}}
        <div class="report-card">
            <div class="report-card-top">
                <div class="report-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="report-card-info">
                    <div class="report-card-title">Frequent Customers</div>
                    <div class="report-card-desc">Customers ranked by order count, total spending, and average order value for the year.</div>
                </div>
            </div>
            <div class="report-filters">
                <select class="filter-select" id="fc-year">
                    @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                        <option value="{{ $y }}" {{ $y === $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <a href="#" class="btn-export" onclick="exportReport('frequent-customers', ['fc-year']); return false;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Annual Summary --}}
        <div class="report-card">
            <div class="report-card-top">
                <div class="report-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6"  y1="20" x2="6"  y2="14"/>
                    </svg>
                </div>
                <div class="report-card-info">
                    <div class="report-card-title">Annual Summary</div>
                    <div class="report-card-desc">Full-year overview combining monthly revenue, top products, and top customers in one report.</div>
                </div>
            </div>
            <div class="report-filters">
                <select class="filter-select" id="as-year">
                    @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                        <option value="{{ $y }}" {{ $y === $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <a href="#" class="btn-export" onclick="exportReport('annual-summary', ['as-year']); return false;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

    </div>

    {{-- ── Data previews ── --}}
    <div class="section-heading">Quick Preview</div>

    <div class="preview-wrap">

        {{-- Top 5 Bestsellers --}}
        <div class="preview-card">
            <div class="preview-card-header">Top 5 Bestselling Products ({{ $currentYear }})</div>
            @if ($bestsellers->isEmpty())
                <div class="empty-preview">No sales data yet.</div>
            @else
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bestsellers as $i => $row)
                            <tr>
                                <td>
                                    <span class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td>{{ $row->product->name ?? '—' }}</td>
                                <td>{{ number_format($row->total_qty) }}</td>
                                <td>₱{{ number_format($row->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Top 5 Frequent Customers --}}
        <div class="preview-card">
            <div class="preview-card-header">Top 5 Frequent Customers ({{ $currentYear }})</div>
            @if ($frequentCustomers->isEmpty())
                <div class="empty-preview">No order data yet.</div>
            @else
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($frequentCustomers as $i => $row)
                            <tr>
                                <td>
                                    <span class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td>{{ $row->customer->full_name ?? '—' }}</td>
                                <td>{{ number_format($row->order_count) }}</td>
                                <td>₱{{ number_format($row->total_spent, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- ── Monthly sales mini table ── --}}
    <div class="monthly-table-wrap">
        <div class="monthly-table-header">
            <span class="monthly-table-title">Monthly Breakdown ({{ $currentYear }})</span>
        </div>
        <table class="preview-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                @foreach ($months as $i => $label)
                    @php $row = $monthlySales->get($i + 1); @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $row ? number_format($row->order_count) : '—' }}</td>
                        <td>{{ $row ? '₱' . number_format($row->revenue, 2) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ══ INVENTORY HEALTH SECTION (NEW) ══ --}}
    <div class="section-heading">Inventory Health ({{ $currentYear }})</div>

    <div class="inventory-grid">

        {{-- Stock Adjustments breakdown by reason --}}
        <div class="preview-card">
            <div class="preview-card-header">
                <span>Stock Adjustments by Reason</span>
                <span style="font-size:12px; color:var(--muted); font-weight:500;">
                    {{ number_format($totalAdjustedUnits) }} units total
                </span>
            </div>
            @if ($totalAdjustmentTransactions === 0)
                <div class="empty-preview">No stock adjustments recorded this year.</div>
            @else
                <div class="adj-breakdown">
                    @foreach (['damaged', 'lost', 'expired', 'correction'] as $reason)
                        @php $row = $adjustmentsByReason->get($reason); @endphp
                        <div class="adj-reason-card {{ $reason }}">
                            <span class="adj-reason-label">{{ ucfirst($reason) }}</span>
                            <span class="adj-reason-count">{{ $row ? number_format($row->count) : '0' }}</span>
                            <span class="adj-reason-units">
                                {{ $row ? number_format($row->total_units) . ' units' : '0 units' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Returns preview --}}
        <div class="preview-card">
            <div class="preview-card-header">
                <span>Recent Returns</span>
                <span style="font-size:12px; color:var(--muted); font-weight:500;">
                    {{ number_format($totalReturnTransactions) }} this year
                    · ₱{{ number_format($totalRefunded, 2) }} refunded
                </span>
            </div>
            @if ($recentReturns->isEmpty())
                <div class="empty-preview">No returns recorded this year.</div>
            @else
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Refund</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentReturns as $ret)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $ret->order) }}"
                                       style="color:var(--primary); text-decoration:none; font-weight:600;">
                                        {{ $ret->order->order_label ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $ret->product->name ?? '—' }}</td>
                                <td>{{ $ret->quantity }}</td>
                                <td>
                                    @if ($ret->refund_amount > 0)
                                        ₱{{ number_format($ret->refund_amount, 2) }}
                                    @else
                                        <span style="color:var(--muted);">—</span>
                                    @endif
                                </td>
                                <td style="color:var(--muted); font-size:12px;">
                                    {{ $ret->return_date->format('M d') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

@endsection

@push('scripts')
<script>
    function exportReport(type, fieldIds) {
        const base = '{{ url("admin/reports/export") }}';
        const url  = new URL(base + '/' + type, window.location.origin);

        fieldIds.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            const param = id.split('-').slice(1).join('_');
            url.searchParams.set(param, el.value);
        });

        window.location.href = url.toString();
    }
</script>
@endpush
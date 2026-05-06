@extends('admin.layout')

@section('title', 'Home')

@push('styles')
<style>
    /* ── Layout ── */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 28px;
        align-items: start;
    }

    /* ── LEFT: KPIs ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 32px;
    }

    .kpi-card {
        background: #f8e1eb;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: var(--shadow);
        transition: transform .2s ease;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
    }

    .kpi-card:hover { transform: translateY(-3px); }

    .kpi-label {
        font-size: 13px;
        color: var(--muted);
        font-weight: 500;
        margin-bottom: 8px;
        align-self: flex-start;
    }

    .kpi-value {
        font-size: 40px;
        font-weight: 700;
        color: var(--primary);
        line-height: 1.05;
    }

    .kpi-sub {
        font-size: 11px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* ── Section label ── */
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    /* ── Shortcuts ── */
    .shortcut-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .shortcut-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--text);
        padding: 20px 12px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(94,32,57,.06);
        border: 1.5px solid transparent;
        transition: transform .2s ease, border-color .2s, box-shadow .2s;
    }

    .shortcut-card:hover {
        transform: translateY(-4px);
        border-color: #f2d6e0;
        box-shadow: 0 6px 20px rgba(94,32,57,.10);
    }

    .shortcut-icon {
        width: 56px; height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5e6ed;
        border-radius: 50%;
    }

    .shortcut-icon svg {
        width: 26px; height: 26px;
        stroke: var(--primary);
        stroke-width: 1.8;
    }

    .shortcut-label {
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        line-height: 1.3;
        color: var(--text);
    }

    /* ── Charts section ── */
    .charts-section {
        margin-top: 32px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .chart-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(94,32,57,.06);
        padding: 20px 22px 18px;
    }

    .chart-card-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .chart-card-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    .chart-card-sub {
        font-size: 11px;
        color: var(--muted);
    }

    /* Bar chart */
    .bar-chart {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 110px;
    }

    .bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        height: 100%;
        justify-content: flex-end;
    }

    .bar-value-label {
        font-size: 9px;
        color: var(--muted);
        font-weight: 600;
        white-space: nowrap;
    }

    .bar-fill {
        width: 100%;
        background: linear-gradient(180deg, #a04065 0%, #7b2d4e 100%);
        border-radius: 5px 5px 0 0;
        min-height: 3px;
        transition: opacity .15s;
        position: relative;
    }

    .bar-fill:hover { opacity: .8; }
    .bar-fill.zero  { background: #f0e6ec; }

    .bar-label {
        font-size: 10px;
        color: var(--muted);
        font-weight: 500;
        margin-top: 6px;
        white-space: nowrap;
    }

    /* Donut chart */
    .donut-wrap {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .donut-svg { flex-shrink: 0; }

    .donut-legend {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text);
    }

    .legend-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-count {
        margin-left: auto;
        font-weight: 700;
        font-size: 13px;
        color: var(--text);
    }

    /* Horizontal bar chart for top products */
    .hbar-chart {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .hbar-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .hbar-name {
        font-size: 11px;
        color: var(--text);
        font-weight: 500;
        width: 110px;
        flex-shrink: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hbar-track {
        flex: 1;
        height: 10px;
        background: #f5e6ed;
        border-radius: 99px;
        overflow: hidden;
    }

    .hbar-fill {
        height: 100%;
        background: linear-gradient(90deg, #7b2d4e 0%, #a04065 100%);
        border-radius: 99px;
        transition: width .6s cubic-bezier(.4,0,.2,1);
    }

    .hbar-qty {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        width: 30px;
        text-align: right;
        flex-shrink: 0;
    }

    .chart-empty {
        font-size: 13px;
        color: var(--muted);
        text-align: center;
        padding: 20px 0;
    }

    /* ── RIGHT: Activity feed ── */
    .activity-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
        position: sticky;
        top: 24px;
    }

    .activity-header {
        padding: 18px 22px 14px;
        border-bottom: 1.5px solid #f0e6ec;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .activity-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .notif-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f8e1eb;
        color: var(--primary);
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .notif-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--primary);
        animation: pulse 1.8s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .5; transform: scale(.8); }
    }

    .activity-list { max-height: 520px; overflow-y: auto; }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 22px;
        border-bottom: 1px solid #f9f3f6;
        transition: background .12s;
    }

    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #fdf5f8; }

    .activity-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .activity-icon svg { width: 16px; height: 16px; }

    .icon-order      { background: #e8f5e9; }
    .icon-order      svg { stroke: #2e7d32; }
    .icon-payment    { background: #e3f2fd; }
    .icon-payment    svg { stroke: #1565c0; }
    .icon-stock      { background: #fff8e1; }
    .icon-stock      svg { stroke: #f57f17; }
    .icon-adjustment { background: #fff3e0; }
    .icon-adjustment svg { stroke: #e65100; }
    .icon-return     { background: #f3e5f5; }
    .icon-return     svg { stroke: #6a1b9a; }

    .activity-body  { flex: 1; min-width: 0; }

    .activity-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }

    .activity-detail {
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .activity-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 3px;
        flex-shrink: 0;
    }

    .activity-amount { font-size: 13px; font-weight: 600; color: var(--primary); }
    .activity-amount.negative { color: #856404; }

    .activity-time {
        font-size: 11px;
        color: var(--muted);
        white-space: nowrap;
    }

    .activity-empty {
        padding: 40px 22px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }

    /* Pending payments banner */
    .pending-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #fff8f0;
        border: 1.5px solid #fde5c3;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .pending-banner-left { display: flex; align-items: center; gap: 10px; }

    .pending-banner-icon {
        width: 36px; height: 36px;
        background: #fde5c3;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pending-banner-icon svg { width: 18px; height: 18px; stroke: #c47a1e; }
    .pending-banner-text { font-size: 13px; font-weight: 600; color: #7a4d1e; }
    .pending-banner-sub  { font-size: 12px; color: #c47a1e; }

    .btn-banner {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #c47a1e;
        color: #fff;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        font-family: inherit;
        transition: opacity .15s;
        white-space: nowrap;
    }

    .btn-banner:hover { opacity: .88; }
</style>
@endpush

@section('content')

    <h1 class="page-title">Home</h1>

    {{-- ── Pending payments alert banner ── --}}
    @if ($pendingPaymentsCount > 0)
        <div class="pending-banner">
            <div class="pending-banner-left">
                <div class="pending-banner-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div>
                    <div class="pending-banner-text">
                        {{ $pendingPaymentsCount }} order{{ $pendingPaymentsCount > 1 ? 's' : '' }} awaiting payment
                    </div>
                    <div class="pending-banner-sub">These orders have pending or partial balances</div>
                </div>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn-banner">
                View Payments →
            </a>
        </div>
    @endif

    <div class="dashboard-grid">

        {{-- ════════ LEFT COLUMN ════════ --}}
        <div>

            {{-- KPI Cards --}}
            <div class="section-title">Today's Overview</div>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <span class="kpi-label">Orders Today</span>
                    <span class="kpi-value">{{ $ordersToday }}</span>
                    <span class="kpi-sub">Active orders placed today</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">Earned Today</span>
                    <span class="kpi-value">₱{{ number_format($earnedToday, 2) }}</span>
                    <span class="kpi-sub">Total value of today's orders</span>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="section-title" style="margin-top:4px;">Quick Actions</div>
            <div class="shortcut-grid">

                <a href="{{ route('admin.orders.index', ['open' => 1]) }}" class="shortcut-card">
                    <div class="shortcut-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            <line x1="12" y1="11" x2="12" y2="17"/>
                            <line x1="9"  y1="14" x2="15" y2="14"/>
                        </svg>
                    </div>
                    <span class="shortcut-label">Create New Order</span>
                </a>

                <a href="{{ route('admin.stocks.index') }}" class="shortcut-card">
                    <div class="shortcut-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <span class="shortcut-label">Manage Stocks</span>
                </a>

                <a href="{{ route('admin.payments.index') }}" class="shortcut-card">
                    <div class="shortcut-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="6" width="20" height="12" rx="2"/>
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M6 12h.01M18 12h.01"/>
                        </svg>
                    </div>
                    <span class="shortcut-label">Pending Payments</span>
                </a>

            </div>

            {{-- ════ CHARTS ════ --}}
            <div class="charts-section">

                {{-- ── Chart 1: Monthly Revenue (last 6 months) ── --}}
                <div class="chart-card">
                    <div class="chart-card-header">
                        <span class="chart-card-title">Monthly Revenue</span>
                        <span class="chart-card-sub">Last 6 months · fulfilled orders</span>
                    </div>

                    @php
                        $maxRev = $monthlyRevenue->max('revenue') ?: 1;
                    @endphp

                    @if ($maxRev <= 1)
                        <div class="chart-empty">No fulfilled orders in the last 6 months.</div>
                    @else
                        <div class="bar-chart" id="revenueChart">
                            @foreach ($monthlyRevenue as $point)
                                @php
                                    $heightPct = $maxRev > 0 ? round(($point['revenue'] / $maxRev) * 100) : 0;
                                    $isZero    = $point['revenue'] == 0;
                                @endphp
                                <div class="bar-col">
                                    @if (! $isZero)
                                        <span class="bar-value-label">
                                            ₱{{ $point['revenue'] >= 1000
                                                ? number_format($point['revenue'] / 1000, 1) . 'k'
                                                : number_format($point['revenue'], 0) }}
                                        </span>
                                    @else
                                        <span class="bar-value-label" style="color:transparent;">0</span>
                                    @endif
                                    <div
                                        class="bar-fill {{ $isZero ? 'zero' : '' }}"
                                        style="height: {{ max($heightPct, 3) }}%;"
                                        title="₱{{ number_format($point['revenue'], 2) }}">
                                    </div>
                                    <span class="bar-label">{{ $point['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Chart 2: Orders by Status ── --}}
                <div class="chart-card">
                    <div class="chart-card-header">
                        <span class="chart-card-title">Active Orders by Status</span>
                        <span class="chart-card-sub">Unarchived orders</span>
                    </div>

                    @php
                        $totalStatusOrders = array_sum($statusCounts);
                    @endphp

                    @if ($totalStatusOrders === 0)
                        <div class="chart-empty">No active orders at the moment.</div>
                    @else
                        @php
                            // Donut segments
                            $colors  = ['#856404', '#1565c0', '#2e7d32'];
                            $labels  = ['Pending', 'Partial', 'Fully Paid'];
                            $values  = array_values($statusCounts);
                            $cx = 54; $cy = 54; $r = 40; $stroke = 18;
                            $circumference = 2 * M_PI * $r;
                            $offset = 0;
                            $segments = [];
                            foreach ($values as $i => $v) {
                                $dash = $totalStatusOrders > 0 ? ($v / $totalStatusOrders) * $circumference : 0;
                                $segments[] = [
                                    'dash'   => $dash,
                                    'gap'    => $circumference - $dash,
                                    'offset' => $offset,
                                    'color'  => $colors[$i],
                                    'label'  => $labels[$i],
                                    'value'  => $v,
                                ];
                                $offset += $dash;
                            }
                        @endphp

                        <div class="donut-wrap">
                            <svg class="donut-svg" width="108" height="108" viewBox="0 0 108 108">
                                {{-- Background ring --}}
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                                    fill="none" stroke="#f5e6ed" stroke-width="{{ $stroke }}"/>
                                {{-- Segments --}}
                                @foreach ($segments as $seg)
                                    @if ($seg['value'] > 0)
                                        <circle
                                            cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                                            fill="none"
                                            stroke="{{ $seg['color'] }}"
                                            stroke-width="{{ $stroke }}"
                                            stroke-dasharray="{{ round($seg['dash'], 2) }} {{ round($seg['gap'], 2) }}"
                                            stroke-dashoffset="{{ round(-$seg['offset'], 2) }}"
                                            transform="rotate(-90 {{ $cx }} {{ $cy }})"
                                            style="transition: stroke-dasharray .4s ease;">
                                        </circle>
                                    @endif
                                @endforeach
                                {{-- Centre label --}}
                                <text x="{{ $cx }}" y="{{ $cy - 4 }}"
                                    text-anchor="middle" font-size="16" font-weight="700" fill="#3a1a28">
                                    {{ $totalStatusOrders }}
                                </text>
                                <text x="{{ $cx }}" y="{{ $cy + 12 }}"
                                    text-anchor="middle" font-size="9" fill="#9e7286">
                                    orders
                                </text>
                            </svg>

                            <div class="donut-legend">
                                @foreach ($segments as $seg)
                                    <div class="legend-item">
                                        <span class="legend-dot" style="background:{{ $seg['color'] }};"></span>
                                        <span>{{ $seg['label'] }}</span>
                                        <span class="legend-count">{{ $seg['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── Chart 3: Top 5 Products by Units Sold ── --}}
                <div class="chart-card">
                    <div class="chart-card-header">
                        <span class="chart-card-title">Top Products</span>
                        <span class="chart-card-sub">By units sold · fulfilled orders</span>
                    </div>

                    @if ($topProducts->isEmpty())
                        <div class="chart-empty">No sales data yet.</div>
                    @else
                        @php $maxQty = $topProducts->max('qty') ?: 1; @endphp
                        <div class="hbar-chart">
                            @foreach ($topProducts as $row)
                                <div class="hbar-row">
                                    <span class="hbar-name" title="{{ $row['name'] }}">{{ $row['name'] }}</span>
                                    <div class="hbar-track">
                                        <div class="hbar-fill"
                                             style="width: {{ round(($row['qty'] / $maxQty) * 100) }}%;">
                                        </div>
                                    </div>
                                    <span class="hbar-qty">{{ $row['qty'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>{{-- /charts-section --}}

        </div>{{-- /left column --}}

        {{-- ════════ RIGHT COLUMN: Activity Feed ════════ --}}
        <div class="activity-card">
            <div class="activity-header">
                <span class="activity-title">Recent Activity</span>
                @if ($pendingPaymentsCount > 0)
                    <span class="notif-badge">
                        <span class="notif-dot"></span>
                        {{ $pendingPaymentsCount }} pending
                    </span>
                @endif
            </div>

            <div class="activity-list">
                @forelse ($recentActivity as $event)
                    <div class="activity-item">

                        <div class="activity-icon icon-{{ $event['type'] }}">
                            @if ($event['type'] === 'order')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                            @elseif ($event['type'] === 'payment')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="6" width="20" height="12" rx="2"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            @elseif ($event['type'] === 'stock')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                </svg>
                            @elseif ($event['type'] === 'adjustment')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="1 4 1 10 7 10"/>
                                    <path d="M3.51 15a9 9 0 1 0 .49-4"/>
                                </svg>
                            @endif
                        </div>

                        <div class="activity-body">
                            <div class="activity-label">{{ $event['label'] }}</div>
                            <div class="activity-detail" title="{{ $event['detail'] }}">{{ $event['detail'] }}</div>
                        </div>

                        <div class="activity-right">
                            @if ($event['amount'])
                                <span class="activity-amount {{ str_starts_with($event['amount'], '−') ? 'negative' : '' }}">
                                    {{ $event['amount'] }}
                                </span>
                            @endif
                            <span class="activity-time">
                                {{ $event['time'] ? \Carbon\Carbon::parse($event['time'])->diffForHumans() : '—' }}
                            </span>
                        </div>

                    </div>
                @empty
                    <div class="activity-empty">
                        No activity yet. Orders, payments, and stock records will appear here.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection
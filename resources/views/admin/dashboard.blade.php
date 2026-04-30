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

    /* ── Shortcuts ── */
    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 16px;
        letter-spacing: .01em;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-size: 12px;
        color: var(--muted);
    }

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
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5e6ed;
        border-radius: 50%;
    }

    .shortcut-icon svg {
        width: 26px;
        height: 26px;
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

    /* Notification badge */
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
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--primary);
        animation: pulse 1.8s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .5; transform: scale(.8); }
    }

    /* Activity list */
    .activity-list {
        max-height: 520px;
        overflow-y: auto;
    }

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

    /* Type icon */
    .activity-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .activity-icon svg { width: 16px; height: 16px; }

    /* Original three */
    .icon-order      { background: #e8f5e9; }
    .icon-order      svg { stroke: #2e7d32; }
    .icon-payment    { background: #e3f2fd; }
    .icon-payment    svg { stroke: #1565c0; }
    .icon-stock      { background: #fff8e1; }
    .icon-stock      svg { stroke: #f57f17; }

    /* New two */
    .icon-adjustment { background: #fff3e0; }
    .icon-adjustment svg { stroke: #e65100; }
    .icon-return     { background: #f3e5f5; }
    .icon-return     svg { stroke: #6a1b9a; }

    .activity-body { flex: 1; min-width: 0; }

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

    .activity-amount {
        font-size: 13px;
        font-weight: 600;
        color: var(--primary);
    }

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

    .pending-banner-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pending-banner-icon {
        width: 36px;
        height: 36px;
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
        </div>

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

                        {{-- Type icon --}}
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
                                {{-- return --}}
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
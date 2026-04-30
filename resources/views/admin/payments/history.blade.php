@extends('admin.layout')

@section('title', 'Payment History')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .search-wrap {
        position: relative;
        flex: 1;
        max-width: 360px;
    }

    .search-wrap input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1.5px solid #e8d5dd;
        border-radius: 10px;
        background: #fff;
        font-size: 14px;
        font-family: inherit;
        color: var(--text);
        outline: none;
        transition: border-color .2s;
    }

    .search-wrap input:focus { border-color: var(--primary); }

    .search-wrap svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        stroke: var(--muted);
        pointer-events: none;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: opacity .15s, transform .1s;
        white-space: nowrap;
    }

    .btn:active { transform: scale(.97); }
    .btn-secondary { background: #f5edf1; color: var(--primary); }
    .btn-secondary:hover { background: #ecdbe3; }

    .flash { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 20px; }

    .tab-bar {
        display: flex;
        gap: 0;
        margin-bottom: 24px;
        border-bottom: 2px solid #f0e6ec;
    }

    .tab-link {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        color: var(--muted);
        text-decoration: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: color .15s, border-color .15s;
    }

    .tab-link.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-link:hover { color: var(--text); }

    .table-wrap {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
        margin-bottom: 24px;
    }

    table { width: 100%; border-collapse: collapse; }

    thead th {
        padding: 14px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        text-align: left;
        border-bottom: 1.5px solid #f0e6ec;
        white-space: nowrap;
    }

    tbody tr { transition: background .12s; }
    tbody tr:hover { background: #fdf5f8; }

    tbody td {
        padding: 13px 20px;
        font-size: 14px;
        color: var(--text);
        border-bottom: 1px solid #f5edf1;
        vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }

    .empty-row td { text-align: center; color: var(--muted); padding: 48px 20px; font-size: 14px; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-paid { background: #d1f5e0; color: #1a7a42; }

    /* ── Payment entries ── */
    .payment-entries { display: flex; flex-direction: column; gap: 6px; }

    .payment-entry {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text);
    }

    .method-tag {
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
        background: #f5edf1;
        padding: 2px 8px;
        border-radius: 99px;
    }

    /* Receipt download button — matches show.blade style */
    .btn-receipt {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        background: #f5e6ed;
        color: var(--primary);
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
        margin-left: auto;
        flex-shrink: 0;
    }

    .btn-receipt:hover { background: #ecdbe3; }
    .btn-receipt svg { width: 11px; height: 11px; stroke: var(--primary); }

    .pagination {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: var(--muted);
    }

    .pagination a { color: var(--text); text-decoration: none; font-weight: 500; transition: color .15s; }
    .pagination a:hover { color: var(--primary); }
    .pagination .disabled { color: #ccc; pointer-events: none; }
</style>
@endpush

@section('content')

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="page-header">
        <h1 class="page-title" style="margin-bottom:0">Payment History</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.payments.history') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search orders..." value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Back to Payments
        </a>
    </div>

    <div class="tab-bar">
        <a href="{{ route('admin.payments.index') }}" class="tab-link">Pending &amp; Partial</a>
        <a href="{{ route('admin.payments.history') }}" class="tab-link active">Fully Paid</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Order Total</th>
                    <th>Order Date</th>
                    <th>Payment(s)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paidOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               style="font-weight:600; color:var(--text); text-decoration:none;">
                                {{ $order->order_label }}
                            </a>
                        </td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>₱{{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->order_date->format('M d, Y') }}</td>
                        <td>
                            <div class="payment-entries">
                                @forelse ($order->payments as $payment)
                                    <div class="payment-entry">
                                        <span>₱{{ number_format($payment->amount, 2) }}</span>
                                        <span class="method-tag">{{ $payment->paymentMethod->name }}</span>
                                        <span style="font-size:12px; color:var(--muted);">
                                            {{ $payment->payment_date->format('M d, Y') }}
                                        </span>
                                        {{-- Receipt download button, shown if receipt exists ── --}}
                                        @if ($payment->receipt)
                                            <a href="{{ route('admin.payments.receipt', $payment) }}"
                                               class="btn-receipt"
                                               title="Download receipt {{ $payment->receipt->receipt_num }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <polyline points="7 10 12 15 17 10"/>
                                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                                </svg>
                                                {{ $payment->receipt->receipt_num }}
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <span style="color:var(--muted); font-size:13px;">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-paid">Fully Paid</span>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">No fully paid orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        @if ($paidOrders->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $paidOrders->previousPageUrl() }}">← Previous</a>
        @endif

        <span>Page {{ $paidOrders->currentPage() }} of {{ $paidOrders->lastPage() }}</span>

        @if ($paidOrders->hasMorePages())
            <a href="{{ $paidOrders->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>

@endsection
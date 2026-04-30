@extends('admin.layout')

@section('title', 'Order Archives')

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

    tbody tr { transition: background .12s; cursor: pointer; }
    tbody tr:hover { background: #fdf5f8; }

    tbody td {
        padding: 14px 20px;
        font-size: 14px;
        color: var(--text);
        border-bottom: 1px solid #f5edf1;
        vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }

    .empty-row td {
        text-align: center;
        color: var(--muted);
        padding: 48px 20px;
        font-size: 14px;
        cursor: default;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: var(--muted);
    }

    .pagination a {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
        transition: color .15s;
    }

    .pagination a:hover { color: var(--primary); }
    .pagination .disabled { color: #ccc; pointer-events: none; }
</style>
@endpush

@section('content')

    <div class="page-header">
        <h1 class="page-title" style="margin-bottom:0">Order Archives</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.orders.archives') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Back to Orders
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Delivery Method</th>
                    <th>Total</th>
                    <th>Order Date</th>
                    <th>Archived At</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                        <td>{{ $order->order_label }}</td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>{{ $order->deliveryMethod->label }}</td>
                        <td>₱{{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->order_date->format('m/d/Y') }}</td>
                        <td>{{ $order->archived_at->format('m/d/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               onclick="event.stopPropagation()"
                               style="font-size:12px; color:var(--muted); text-decoration:none; white-space:nowrap;">
                                View →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="7">No archived orders.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        @if ($orders->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $orders->previousPageUrl() }}">← Previous</a>
        @endif

        <span>Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>

        @if ($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>

@endsection
@extends('admin.layout')

@section('title', 'Order ' . $order->order_label)

@push('styles')
<style>
    /* ── Back bar ── */
    .back-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--muted);
        text-decoration: none;
        transition: color .15s;
    }

    .back-link:hover { color: var(--primary); }

    .order-heading {
        font-size: 26px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.02em;
        margin: 0;
    }

    /* ── Layout grid ── */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
    }

    /* ── Cards ── */
    .detail-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .detail-card:last-child { margin-bottom: 0; }

    .card-header {
        padding: 16px 22px;
        border-bottom: 1.5px solid #f0e6ec;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .card-body { padding: 20px 22px; }

    /* ── Info rows ── */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 8px 0;
        border-bottom: 1px solid #f9f3f6;
        gap: 16px;
    }

    .info-row:last-child { border-bottom: none; }

    .info-label {
        font-size: 13px;
        color: var(--muted);
        font-weight: 500;
        white-space: nowrap;
    }

    .info-value {
        font-size: 13px;
        color: var(--text);
        font-weight: 600;
        text-align: right;
    }

    /* ── Badges ── */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-partial  { background: #cfe2ff; color: #0a58ca; }
    .badge-paid     { background: #d1e7dd; color: #0f5132; }

    /* ── Order lines table ── */
    table { width: 100%; border-collapse: collapse; }

    thead th {
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-align: left;
        border-bottom: 1.5px solid #f0e6ec;
        white-space: nowrap;
    }

    thead th:last-child { text-align: right; }

    tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: var(--text);
        border-bottom: 1px solid #f9f3f6;
        vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fdf5f8; }

    td.amount { text-align: right; font-weight: 600; }
    td.muted  { color: var(--muted); font-size: 12px; }

    /* ── Totals block ── */
    .totals-block {
        padding: 16px 22px;
        border-top: 1.5px solid #f0e6ec;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: var(--muted);
    }

    .total-row.grand {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        padding-top: 8px;
        border-top: 1px solid #f0e6ec;
        margin-top: 4px;
    }

    /* ── Payment history items ── */
    .payment-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f9f3f6;
    }

    .payment-item:last-child { border-bottom: none; }

    .payment-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: #e3f2fd;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .payment-icon svg { width: 17px; height: 17px; stroke: #1565c0; }

    .payment-info { flex: 1; min-width: 0; }
    .payment-method { font-size: 13px; font-weight: 600; color: var(--text); }
    .payment-date   { font-size: 12px; color: var(--muted); margin-top: 1px; }

    .payment-right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
    .payment-amount { font-size: 14px; font-weight: 700; color: var(--primary); }

    .btn-receipt {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: #f5e6ed;
        color: var(--primary);
        border-radius: 7px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
    }

    .btn-receipt:hover { background: #ecdbe3; }
    .btn-receipt svg { width: 12px; height: 12px; stroke: var(--primary); }

    /* ── Return items ── */
    .return-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f9f3f6;
    }

    .return-item:last-child { border-bottom: none; }

    .return-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: #fff8e1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .return-icon svg { width: 17px; height: 17px; stroke: #856404; }

    .return-info { flex: 1; min-width: 0; }
    .return-product { font-size: 13px; font-weight: 600; color: var(--text); }
    .return-meta    { font-size: 12px; color: var(--muted); margin-top: 2px; line-height: 1.5; }

    .return-right { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; flex-shrink: 0; }
    .return-qty    { font-size: 13px; font-weight: 700; color: #856404; }
    .return-refund { font-size: 12px; color: var(--muted); }

    /* ── Balance bar ── */
    .balance-section { padding: 16px 22px; }

    .balance-label {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .balance-bar-bg {
        height: 8px;
        background: #f0e6ec;
        border-radius: 99px;
        overflow: hidden;
    }

    .balance-bar-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 99px;
        transition: width .4s ease;
    }

    .balance-amounts {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-top: 8px;
    }

    .balance-paid      { color: #2e7d32; font-weight: 600; }
    .balance-remaining { color: var(--primary); font-weight: 600; }

    /* ── Empty state ── */
    .empty-state {
        padding: 32px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }

    /* ── Flash ── */
    .flash       { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 16px; }
    .flash-error { background: #fce8ef; border-left-color: var(--primary); color: var(--primary); }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: opacity .15s;
        white-space: nowrap;
    }

    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }
    .btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
    .btn-outline:hover { background: #f5e6ed; }

    /* ── Modal ── */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(60,20,40,.45);
        z-index: 200;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-backdrop.open { display: flex; }

    .modal {
        background: #fff;
        border-radius: 18px;
        padding: 36px 40px;
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 8px 48px rgba(94,32,57,.18);
    }

    .modal-title { font-size: 20px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.02em; }
    .modal-sub   { font-size: 13px; color: var(--muted); margin-bottom: 24px; line-height: 1.5; }

    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: 13px; font-weight: 600; color: var(--text); }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px 14px;
        border: 1.5px solid #e8d5dd;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        color: var(--text);
        background: #fff;
        outline: none;
        transition: border-color .2s;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: var(--primary); }

    .form-group input[readonly] { background: #f9f3f6; color: var(--muted); cursor: default; }

    .return-hint {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f0e6ec;
    }

    .btn-ghost {
        background: none;
        border: none;
        font-size: 14px;
        font-weight: 600;
        color: var(--muted);
        cursor: pointer;
        padding: 10px 18px;
        border-radius: 10px;
        font-family: inherit;
        transition: background .15s;
    }

    .btn-ghost:hover { background: #f5edf1; color: var(--text); }
</style>
@endpush

@section('content')

    {{-- ── Back bar ── --}}
    <div class="back-bar">
        @if (session('error'))
            <div class="flash flash-error" style="width:100%; margin-bottom:0;">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="flash" style="width:100%; margin-bottom:0;">{{ session('success') }}</div>
        @endif

        <a href="{{ route('admin.orders.index') }}" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Back to Orders
        </a>
        <span style="color:#e8d5dd;">/</span>
        <h1 class="order-heading">{{ $order->order_label }}</h1>
        <span class="badge {{ match($order->payment_status) {
            'pending'    => 'badge-pending',
            'partial'    => 'badge-partial',
            'fully_paid' => 'badge-paid',
            default      => ''
        } }}" style="margin-left:4px;">
            {{ $order->payment_status === 'fully_paid' ? 'Paid' : ucfirst($order->payment_status) }}
        </span>
    </div>

    <div class="detail-grid">

        {{-- ════════ LEFT: Order details + line items ════════ --}}
        <div>

            {{-- Order Info --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">Order Information</span>
                    <span style="font-size:12px; color:var(--muted);">
                        Placed {{ $order->order_date->format('F d, Y') }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Order ID</span>
                        <span class="info-value">{{ $order->order_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Customer</span>
                        <span class="info-value">{{ $order->customer->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact</span>
                        <span class="info-value">{{ $order->customer->contact_num }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address</span>
                        <span class="info-value">{{ $order->customer->address }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Delivery Method</span>
                        <span class="info-value">{{ $order->deliveryMethod->label }}</span>
                    </div>
                </div>
            </div>

            {{-- Order Lines --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">Products Ordered</span>
                    <span style="font-size:12px; color:var(--muted);">
                        {{ $order->orderLines->count() }} {{ Str::plural('item', $order->orderLines->count()) }}
                    </span>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Unit Price</th>
                            <th style="text-align:right;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderLines as $line)
                            @php
                                $returned = $order->returns
                                    ->where('product_id', $line->product_id)
                                    ->sum('quantity');
                            @endphp
                            <tr>
                                <td style="font-weight:600;">
                                    {{ $line->product->name }}
                                    @if ($returned > 0)
                                        <span style="font-size:11px; font-weight:600; color:#856404; background:#fff8e1; border-radius:6px; padding:1px 7px; margin-left:6px;">
                                            {{ $returned }} returned
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:center; color:var(--muted);">{{ $line->quantity }}</td>
                                <td class="amount" style="color:var(--muted);">₱{{ number_format($line->unit_price, 2) }}</td>
                                <td class="amount">₱{{ number_format($line->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="totals-block">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>Delivery Fee</span>
                        <span>{{ $order->delivery_fee > 0 ? '₱' . number_format($order->delivery_fee, 2) : 'Free (Pickup)' }}</span>
                    </div>
                    @if ($order->returns->sum('refund_amount') > 0)
                        <div class="total-row" style="color:#856404;">
                            <span>Total Refunded</span>
                            <span>− ₱{{ number_format($order->returns->sum('refund_amount'), 2) }}</span>
                        </div>
                    @endif
                    <div class="total-row grand">
                        <span>Grand Total</span>
                        <span>₱{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ════════ RIGHT: Payment + Returns + Archive ════════ --}}
        <div>

            {{-- Balance summary --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">Payment Summary</span>
                </div>
                <div class="balance-section">
                    @php
                        $pct = $order->total > 0
                            ? min(100, round(($totalPaid / $order->total) * 100))
                            : 0;
                    @endphp
                    <div class="balance-label">
                        <span>Payment progress</span>
                        <span>{{ $pct }}%</span>
                    </div>
                    <div class="balance-bar-bg">
                        <div class="balance-bar-fill" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="balance-amounts">
                        <span class="balance-paid">₱{{ number_format($totalPaid, 2) }} paid</span>
                        @if ($totalRemaining > 0)
                            <span class="balance-remaining">₱{{ number_format($totalRemaining, 2) }} remaining</span>
                        @else
                            <span style="color:#2e7d32; font-weight:600; font-size:12px;">Fully settled ✓</span>
                        @endif
                    </div>
                </div>
                <div style="padding: 0 22px 16px;">
                    <div class="info-row">
                        <span class="info-label">Order Total</span>
                        <span class="info-value">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Paid</span>
                        <span class="info-value" style="color:#2e7d32;">₱{{ number_format($totalPaid, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Remaining</span>
                        <span class="info-value" style="color:{{ $totalRemaining > 0 ? 'var(--primary)' : '#2e7d32' }};">
                            ₱{{ number_format($totalRemaining, 2) }}
                        </span>
                    </div>
                </div>

                @if ($order->payment_status !== 'fully_paid')
                    <div style="padding: 0 22px 20px;">
                        <a href="{{ route('admin.payments.index') }}"
                           class="btn btn-primary"
                           style="width:100%; justify-content:center;">
                            Record Payment
                        </a>
                    </div>
                @endif
            </div>

            {{-- Payment history --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">Payment History</span>
                    <span style="font-size:12px; color:var(--muted);">
                        {{ $order->payments->count() }} {{ Str::plural('payment', $order->payments->count()) }}
                    </span>
                </div>

                @if ($order->payments->isEmpty())
                    <div class="empty-state">No payments recorded yet.</div>
                @else
                    <div class="card-body" style="padding: 8px 22px;">
                        @foreach ($order->payments->sortByDesc('payment_date') as $payment)
                            <div class="payment-item">
                                <div class="payment-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="6" width="20" height="12" rx="2"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-method">{{ $payment->paymentMethod->name }}</div>
                                    <div class="payment-date">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                        @if ($payment->receipt)
                                            · {{ $payment->receipt->receipt_num }}
                                        @endif
                                    </div>
                                </div>
                                <div class="payment-right">
                                    <span class="payment-amount">₱{{ number_format($payment->amount, 2) }}</span>
                                    @if ($payment->receipt)
                                        <a href="{{ route('admin.payments.receipt', $payment) }}"
                                           class="btn-receipt"
                                           title="Download receipt">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="7 10 12 15 17 10"/>
                                                <line x1="12" y1="15" x2="12" y2="3"/>
                                            </svg>
                                            Receipt
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ══ RETURNS CARD ══ --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">Returns</span>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:12px; color:var(--muted);">
                            {{ $order->returns->count() }} {{ Str::plural('return', $order->returns->count()) }}
                        </span>
                        @if ($order->payment_status !== 'pending')
                            <button
                                onclick="document.getElementById('returnModal').classList.add('open')"
                                style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; background:#fff8e1; color:#856404; border:1.5px solid #fde5a0; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s;"
                                onmouseover="this.style.background='#fde8a0'"
                                onmouseout="this.style.background='#fff8e1'">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="1 4 1 10 7 10"/>
                                    <path d="M3.51 15a9 9 0 1 0 .49-4"/>
                                </svg>
                                Record Return
                            </button>
                        @else
                            <span style="font-size:12px; color:var(--muted); font-style:italic;">
                                Requires payment first
                            </span>
                        @endif
                    </div>
                </div>

                @if ($order->returns->isEmpty())
                    <div class="empty-state">
                        @if ($order->payment_status === 'pending')
                            Returns cannot be recorded until at least one payment has been made.
                        @else
                            No returns recorded for this order.
                        @endif
                    </div>
                @else
                    <div class="card-body" style="padding: 8px 22px;">
                        @foreach ($order->returns->sortByDesc('return_date') as $ret)
                            <div class="return-item">
                                <div class="return-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="1 4 1 10 7 10"/>
                                        <path d="M3.51 15a9 9 0 1 0 .49-4"/>
                                    </svg>
                                </div>
                                <div class="return-info">
                                    <div class="return-product">{{ $ret->product->name }}</div>
                                    <div class="return-meta">
                                        {{ $ret->return_date->format('M d, Y') }}
                                        · by {{ $ret->employee->full_name }}
                                        @if ($ret->reason)
                                            <br>{{ $ret->reason }}
                                        @endif
                                    </div>
                                </div>
                                <div class="return-right">
                                    <span class="return-qty">−{{ $ret->quantity }} unit{{ $ret->quantity > 1 ? 's' : '' }}</span>
                                    @if ($ret->refund_amount > 0)
                                        <span class="return-refund">₱{{ number_format($ret->refund_amount, 2) }} refund</span>
                                    @else
                                        <span class="return-refund">No refund</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Archive order — only when fully paid and not yet archived --}}
            @if ($order->payment_status === 'fully_paid' && ! $order->archived_at)
                <div class="detail-card" style="border: 1.5px solid #f0e6ec;">
                    <div class="card-header">
                        <span class="card-title">Archive Order</span>
                        <span style="font-size:11px; background:#fff3cd; color:#856404; padding:2px 8px; border-radius:10px; font-weight:600;">
                            Irreversible
                        </span>
                    </div>
                    <div class="card-body" style="font-size:13px; color:var(--muted); line-height:1.7;">
                        <p style="margin-bottom:12px;">
                            Archiving marks this order as fulfilled. It will be moved to the
                            <strong style="color:var(--text);">Archives</strong> and product
                            stock quantities will be deducted automatically.
                        </p>
                        <p style="margin-bottom:16px; font-size:12px; color:#c0392b;">
                            This action cannot be undone. Only archive once the order has been delivered.
                        </p>
                        <button
                            class="btn btn-primary"
                            style="width:100%; justify-content:center; background:#5e2039;"
                            onclick="document.getElementById('archiveModal').classList.add('open')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="21 8 21 21 3 21 3 8"/>
                                <rect x="1" y="3" width="22" height="5"/>
                                <line x1="10" y1="12" x2="14" y2="12"/>
                            </svg>
                            Archive This Order
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══ RETURN MODAL ══ --}}
    <div class="modal-backdrop" id="returnModal">
        <div class="modal">
            <div class="modal-title">Record Return</div>
            <div class="modal-sub">
                Stock will be restored automatically when the return is recorded.
                Only products from this order can be returned.
            </div>

            <form method="POST" action="{{ route('admin.orders.returns.store', $order) }}">
                @csrf

                <div class="form-group">
                    <label>Product <span style="color:#c0392b">*</span></label>
                    <select name="product_id" id="returnProductSelect" required onchange="updateReturnHint()">
                        <option value="" disabled selected>Select a product from this order</option>
                        @foreach ($order->orderLines as $line)
                            @php
                                $alreadyReturned = $order->returns
                                    ->where('product_id', $line->product_id)
                                    ->sum('quantity');
                                $returnable = $line->quantity - $alreadyReturned;
                            @endphp
                            @if ($returnable > 0)
                                <option
                                    value="{{ $line->product_id }}"
                                    data-ordered="{{ $line->quantity }}"
                                    data-returned="{{ $alreadyReturned }}"
                                    data-returnable="{{ $returnable }}"
                                    data-price="{{ $line->unit_price }}">
                                    {{ $line->product->name }}
                                    ({{ $returnable }} returnable)
                                </option>
                            @else
                                <option value="{{ $line->product_id }}" disabled>
                                    {{ $line->product->name }} (fully returned)
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Quantity to Return <span style="color:#c0392b">*</span></label>
                    <input
                        type="number"
                        name="quantity"
                        id="returnQty"
                        min="1"
                        placeholder="e.g. 1"
                        required
                        oninput="updateReturnHint()">
                    <span class="return-hint" id="returnHint"></span>
                </div>

                <div class="form-group">
                    <label>Reason <span style="font-weight:400; color:var(--muted);">(optional)</span></label>
                    <textarea
                        name="reason"
                        rows="2"
                        placeholder="e.g. Customer received damaged item"
                        maxlength="255"></textarea>
                </div>

                <div class="form-group">
                    <label>Refund Amount (₱) <span style="color:#c0392b">*</span></label>
                    <input
                        type="number"
                        name="refund_amount"
                        id="returnRefund"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        value="0"
                        required>
                    <span class="return-hint" id="refundHint" style="color:var(--muted);"></span>
                </div>

                <div class="form-group">
                    <label>Return Date <span style="color:#c0392b">*</span></label>
                    <input
                        type="date"
                        name="return_date"
                        value="{{ now()->toDateString() }}"
                        max="{{ now()->toDateString() }}"
                        required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-ghost" onclick="document.getElementById('returnModal').classList.remove('open')">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#856404;">Record Return</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Archive confirmation modal ── --}}
    @if ($order->payment_status === 'fully_paid' && ! $order->archived_at)
        <div class="modal-backdrop" id="archiveModal" style="display:none; position:fixed; inset:0; background:rgba(60,20,40,.45); z-index:200; align-items:center; justify-content:center; padding:20px;">
            <div style="background:#fff; border-radius:18px; padding:36px 40px; width:100%; max-width:420px; box-shadow:0 8px 48px rgba(94,32,57,.18);">
                <div style="font-size:20px; font-weight:700; margin-bottom:12px; color:var(--text);">
                    Archive {{ $order->order_label }}?
                </div>
                <p style="font-size:13px; color:var(--muted); line-height:1.7; margin-bottom:8px;">
                    This will permanently mark the order as fulfilled and move it to Archives.
                </p>
                <p style="font-size:13px; color:var(--muted); line-height:1.7; margin-bottom:24px;">
                    Stock quantities will be deducted:
                </p>
                <ul style="font-size:13px; color:var(--text); line-height:1.8; margin-bottom:24px; padding-left:20px;">
                    @foreach ($order->orderLines as $line)
                        @php
                            $alreadyReturned = $order->returns
                                ->where('product_id', $line->product_id)
                                ->sum('quantity');
                            $netQty = max(0, $line->quantity - $alreadyReturned);
                        @endphp
                        @if ($netQty > 0)
                            <li>
                                {{ $line->product->name }} — <strong>−{{ $netQty }}</strong> units
                                @if ($alreadyReturned > 0)
                                    <span style="font-size:11px; color:var(--muted); font-weight:400;">
                                        ({{ $line->quantity }} ordered − {{ $alreadyReturned }} returned)
                                    </span>
                                @endif
                            </li>
                        @else
                            <li style="color:var(--muted);">
                                {{ $line->product->name }} — <strong>0</strong> units
                                <span style="font-size:11px; font-weight:400;">(fully returned)</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div style="display:flex; justify-content:flex-end; gap:12px; padding-top:16px; border-top:1px solid #f0e6ec;">
                    <button
                        type="button"
                        style="background:none; border:none; font-size:14px; font-weight:600; color:var(--muted); cursor:pointer; padding:10px 18px; border-radius:10px; font-family:inherit; transition:background .15s;"
                        onclick="document.getElementById('archiveModal').classList.remove('open')"
                        onmouseover="this.style.background='#f5edf1'"
                        onmouseout="this.style.background='none'">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.orders.archive', $order) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="background:#5e2039;">
                            Yes, Archive
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            const archiveModal = document.getElementById('archiveModal');
            const observer = new MutationObserver(() => {
                archiveModal.style.display = archiveModal.classList.contains('open') ? 'flex' : 'none';
            });
            observer.observe(archiveModal, { attributes: true, attributeFilter: ['class'] });
            archiveModal.addEventListener('click', function (e) {
                if (e.target === this) this.classList.remove('open');
            });
        </script>
    @endif

    {{-- Return modal backdrop close + refund hint JS --}}
    <script>
        document.getElementById('returnModal').addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('open');
        });

        function updateReturnHint() {
            const sel     = document.getElementById('returnProductSelect');
            const qtyEl   = document.getElementById('returnQty');
            const hint    = document.getElementById('returnHint');
            const refund  = document.getElementById('returnRefund');
            const refHint = document.getElementById('refundHint');

            if (!sel.value) { hint.textContent = ''; return; }

            const opt        = sel.options[sel.selectedIndex];
            const returnable = parseInt(opt.dataset.returnable) || 0;
            const price      = parseFloat(opt.dataset.price)    || 0;
            const qty        = parseInt(qtyEl.value)             || 0;

            hint.textContent = `Max returnable: ${returnable} unit${returnable !== 1 ? 's' : ''}`;

            if (qty > 0 && qty <= returnable) {
                const suggested = (qty * price).toFixed(2);
                refHint.textContent = `Suggested refund for ${qty} unit${qty !== 1 ? 's' : ''}: ₱${suggested}`;
                // Only auto-fill if the field is still 0 / empty
                if (!refund.value || parseFloat(refund.value) === 0) {
                    refund.value = suggested;
                }
            } else {
                refHint.textContent = '';
            }
        }
    </script>

@endsection
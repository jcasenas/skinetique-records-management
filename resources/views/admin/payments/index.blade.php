@extends('admin.layout')

@section('title', 'Payments')

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

    /* ── Buttons ── */
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
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }
    .btn-secondary { background: #f5edf1; color: var(--primary); }
    .btn-secondary:hover { background: #ecdbe3; }
    .btn-sm { padding: 7px 14px; font-size: 13px; border-radius: 8px; }

    /* ── Flash ── */
    .flash       { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 20px; }
    .flash-error { background: #fce8ef; border-left-color: var(--primary); color: var(--primary); }

    /* ── Tab bar ── */
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

    .tab-link.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-link:hover { color: var(--text); }

    /* ── Table ── */
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

    .empty-row td {
        text-align: center;
        color: var(--muted);
        padding: 48px 20px;
        font-size: 14px;
    }

    /* ── Balance bar ── */
    .balance-wrap { display: flex; flex-direction: column; gap: 4px; min-width: 140px; }

    .balance-bar-bg {
        height: 6px;
        background: #f0e6ec;
        border-radius: 99px;
        overflow: hidden;
    }

    .balance-bar-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 99px;
        transition: width .3s;
    }

    .balance-text { font-size: 12px; color: var(--muted); }

    /* ── Badge ── */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-partial { background: #cfe2ff; color: #0a58ca; }

    /* ── Pagination ── */
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

    /* ══════════════════════════════
       MODAL (shared styles)
    ══════════════════════════════ */
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
        box-shadow: 0 8px 48px rgba(94,32,57,.18);
    }

    .modal-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.02em; }
    .modal-subtitle { font-size: 13px; color: var(--muted); margin-bottom: 24px; }

    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: 13px; font-weight: 600; color: var(--text); }

    .form-group input,
    .form-group select {
        padding: 10px 14px;
        border: 1.5px solid #e8d5dd;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        color: var(--text);
        background: #fff;
        outline: none;
        transition: border-color .2s;
    }

    .form-group input:focus,
    .form-group select:focus { border-color: var(--primary); }

    .form-group input[readonly] { background: #f9f3f6; color: var(--muted); cursor: default; }

    /* Amount field locked state */
    .form-group input:disabled {
        background: #f5edf1;
        color: var(--muted);
        cursor: not-allowed;
        opacity: .7;
    }

    .remaining-hint {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* Warning hint shown before order is selected */
    .amount-locked-hint {
        font-size: 12px;
        color: #c47a1e;
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

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash flash-error">{{ $errors->first() }}</div>
    @endif

    <div class="page-header">
        <h1 class="page-title" style="margin-bottom:0">Payments</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.payments.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search orders..." value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <button class="btn btn-primary" onclick="openAddPayment()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Payment
        </button>

        <a href="{{ route('admin.payments.history') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="10"/>
            </svg>
            Payment History
        </a>
    </div>

    {{-- ── Tab bar ── --}}
    <div class="tab-bar">
        <a href="{{ route('admin.payments.index') }}" class="tab-link active">Pending &amp; Partial</a>
        <a href="{{ route('admin.payments.history') }}" class="tab-link">Fully Paid</a>
    </div>

    {{-- ── Pending / Partial orders table ── --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Order Total</th>
                    <th>Amount Paid</th>
                    <th>Remaining</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingOrders as $order)
                    @php
                        $paid      = $order->payments->sum('amount');
                        $remaining = $order->total - $paid;
                        $pct       = $order->total > 0 ? min(100, round(($paid / $order->total) * 100)) : 0;
                    @endphp
                    <tr>
                        <td>{{ $order->order_label }}</td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>₱{{ number_format($order->total, 2) }}</td>
                        <td>₱{{ number_format($paid, 2) }}</td>
                        <td>₱{{ number_format($remaining, 2) }}</td>
                        <td>
                            <div class="balance-wrap">
                                <div class="balance-bar-bg">
                                    <div class="balance-bar-fill" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="balance-text">{{ $pct }}% paid</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $order->payment_status === 'pending' ? 'badge-pending' : 'badge-partial' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <button
                                class="btn btn-primary btn-sm"
                                onclick="openPayment(
                                    {{ $order->id }},
                                    {{ json_encode($order->order_label) }},
                                    {{ json_encode($order->customer->full_name) }},
                                    {{ $remaining }}
                                )">
                                Record Payment
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="8">No pending or partial payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        @if ($pendingOrders->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $pendingOrders->previousPageUrl() }}">← Previous</a>
        @endif

        <span>Page {{ $pendingOrders->currentPage() }} of {{ $pendingOrders->lastPage() }}</span>

        @if ($pendingOrders->hasMorePages())
            <a href="{{ $pendingOrders->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>

@endsection

@push('scripts')

{{-- ══════════════════════════════
     "RECORD PAYMENT" MODAL
     Opens from the per-row "Record Payment" button.
     Order is already known — amount field is immediately usable.
══════════════════════════════ --}}
<div class="modal-backdrop" id="paymentModal">
    <div class="modal">
        <div class="modal-title">Record Payment</div>
        <div class="modal-subtitle" id="paymentSubtitle"></div>

        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf
            <input type="hidden" name="order_id" id="paymentOrderId">

            <div class="form-group">
                <label>Order</label>
                <input type="text" id="paymentOrderLabel" readonly>
            </div>

            <div class="form-group">
                <label>Customer</label>
                <input type="text" id="paymentCustomerName" readonly>
            </div>

            <div class="form-group">
                <label>Payment Method <span style="color:#c0392b">*</span></label>
                <select name="payment_method_id" required>
                    <option value="" disabled selected>Select method</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Amount (₱) <span style="color:#c0392b">*</span></label>
                <input type="number" name="amount" id="paymentAmount" min="0.01" step="0.01" placeholder="0.00" required>
                <span class="remaining-hint" id="remainingHint"></span>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="text" value="{{ now()->format('m/d/Y') }}" readonly>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closePayment()">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════
     "ADD PAYMENT" MODAL
     Opens from the header "Add Payment" button.
     Amount field is DISABLED until an order is selected,
     preventing entry before a max is known.
══════════════════════════════ --}}
<div class="modal-backdrop" id="addPaymentModal">
    <div class="modal">
        <div class="modal-title">Add Payment</div>
        <div class="modal-subtitle">Select an order first, then enter the payment details.</div>

        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf

            <div class="form-group">
                <label>Order <span style="color:#c0392b">*</span></label>
                <select name="order_id" id="addOrderSelect" required onchange="onAddOrderChange(this)">
                    <option value="" disabled selected>Select an order</option>
                    @foreach ($pendingOrders as $o)
                        @php
                            $oPaid      = $o->payments->sum('amount');
                            $oRemaining = $o->total - $oPaid;
                        @endphp
                        <option
                            value="{{ $o->id }}"
                            data-label="{{ $o->order_label }}"
                            data-customer="{{ $o->customer->full_name }}"
                            data-remaining="{{ $oRemaining }}"
                            data-status="{{ $o->payment_status }}">
                            {{ $o->order_label }} — {{ $o->customer->full_name }}
                            (₱{{ number_format($oRemaining, 2) }} remaining)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" id="addCustomerGroup" style="display:none;">
                <label>Customer</label>
                <input type="text" id="addCustomerName" readonly>
            </div>

            <div class="form-group" id="addStatusGroup" style="display:none;">
                <label>Current Status</label>
                <input type="text" id="addOrderStatus" readonly>
            </div>

            <div class="form-group">
                <label>Payment Method <span style="color:#c0392b">*</span></label>
                <select name="payment_method_id" required>
                    <option value="" disabled selected>Select method</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Amount (₱) <span style="color:#c0392b">*</span></label>
                {{--
                    Field starts DISABLED. onAddOrderChange() enables it and sets
                    the max once an order is selected. This prevents any amount
                    being entered before the remaining balance is known.
                --}}
                <input
                    type="number"
                    name="amount"
                    id="addPaymentAmount"
                    min="0.01"
                    step="0.01"
                    placeholder="Select an order first"
                    disabled
                    required>
                <span class="amount-locked-hint" id="addLockedHint">Select an order above to unlock this field.</span>
                <span class="remaining-hint" id="addRemainingHint" style="display:none;"></span>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="text" value="{{ now()->format('m/d/Y') }}" readonly>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeAddPayment()">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
    /* ── Record Payment modal (per-row) ── */
    function openPayment(orderId, orderLabel, customerName, remaining) {
        document.getElementById('paymentOrderId').value      = orderId;
        document.getElementById('paymentOrderLabel').value   = orderLabel;
        document.getElementById('paymentCustomerName').value = customerName;
        document.getElementById('paymentAmount').max         = remaining;
        document.getElementById('paymentAmount').value       = '';
        document.getElementById('remainingHint').textContent =
            'Remaining balance: ₱' + remaining.toFixed(2);
        document.getElementById('paymentSubtitle').textContent =
            'Recording payment for ' + orderLabel + ' — ' + customerName;
        document.getElementById('paymentModal').classList.add('open');
    }

    function closePayment() {
        document.getElementById('paymentModal').classList.remove('open');
    }

    document.getElementById('paymentModal').addEventListener('click', function (e) {
        if (e.target === this) closePayment();
    });

    /* ── Add Payment modal (header button) ── */
    function openAddPayment() {
        document.getElementById('addOrderSelect').value         = '';
        document.getElementById('addPaymentAmount').value       = '';
        document.getElementById('addPaymentAmount').max         = '';
        document.getElementById('addPaymentAmount').disabled    = true;
        document.getElementById('addPaymentAmount').placeholder = 'Select an order first';
        document.getElementById('addRemainingHint').style.display = 'none';
        document.getElementById('addRemainingHint').textContent    = '';
        document.getElementById('addLockedHint').style.display     = '';
        document.getElementById('addCustomerGroup').style.display  = 'none';
        document.getElementById('addStatusGroup').style.display    = 'none';
        document.getElementById('addPaymentModal').classList.add('open');
    }

    function closeAddPayment() {
        document.getElementById('addPaymentModal').classList.remove('open');
    }

    document.getElementById('addPaymentModal').addEventListener('click', function (e) {
        if (e.target === this) closeAddPayment();
    });

    function onAddOrderChange(select) {
        const option    = select.options[select.selectedIndex];
        const remaining = parseFloat(option.dataset.remaining);
        const status    = option.dataset.status;

        // Populate info fields
        document.getElementById('addCustomerName').value  = option.dataset.customer;
        document.getElementById('addOrderStatus').value   = status.charAt(0).toUpperCase() + status.slice(1);
        document.getElementById('addCustomerGroup').style.display = '';
        document.getElementById('addStatusGroup').style.display   = '';

        // Unlock and constrain the amount field now that we know the max
        const amountEl = document.getElementById('addPaymentAmount');
        amountEl.disabled    = false;
        amountEl.max         = remaining;
        amountEl.value       = '';
        amountEl.placeholder = '0.00';

        document.getElementById('addLockedHint').style.display     = 'none';
        document.getElementById('addRemainingHint').style.display  = '';
        document.getElementById('addRemainingHint').textContent     =
            'Remaining balance: ₱' + remaining.toFixed(2);
    }

    /* Re-open whichever modal had the server-side validation error */
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            @if (old('order_id'))
                openAddPayment();
                // Re-select the order to re-enable the amount field
                const sel = document.getElementById('addOrderSelect');
                sel.value = '{{ old('order_id') }}';
                if (sel.value) onAddOrderChange(sel);
            @else
                document.getElementById('paymentModal').classList.add('open');
            @endif
        });
    @endif
</script>

@endpush
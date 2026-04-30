@extends('admin.layout')

@section('title', 'Orders')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0;
        flex-shrink: 0;
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
    .btn-primary  { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }
    .btn-secondary { background: #f5edf1; color: var(--primary); }
    .btn-secondary:hover { background: #ecdbe3; }

    .flash {
        background: #f0faf0;
        border-left: 3px solid #4caf50;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 13px;
        color: #2e7d32;
        margin-bottom: 20px;
    }

    .flash-error { background: #fce8ef; border-left-color: var(--primary); color: var(--primary); }

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
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-partial  { background: #cfe2ff; color: #0a58ca; }
    .badge-paid     { background: #d1e7dd; color: #0f5132; }

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
        max-width: 720px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 8px 48px rgba(94,32,57,.18);
    }

    .modal-title { font-size: 22px; font-weight: 700; margin-bottom: 28px; letter-spacing: -0.02em; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }

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

    .form-group input[readonly],
    .form-group input[disabled] { background: #f9f3f6; color: var(--muted); cursor: default; }

    #delivery-fee-row { display: none; }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
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
    .btn-sm { padding: 8px 14px; font-size: 13px; border-radius: 8px; }

    /* ── Cart ── */
    .cart-item-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; }

    .cart-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fdf5f8;
        border: 1px solid #f0e6ec;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        gap: 10px;
    }

    .cart-item-name { flex: 1; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cart-item-meta { color: var(--muted); font-size: 12px; white-space: nowrap; flex-shrink: 0; }

    .cart-item-remove {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--muted);
        padding: 2px 4px;
        border-radius: 4px;
        line-height: 1;
        transition: color .15s, background .15s;
        flex-shrink: 0;
    }

    .cart-item-remove:hover { color: #c0392b; background: #fce8ef; }
    .cart-item-remove svg { width: 14px; height: 14px; pointer-events: none; }

    .cart-totals {
        font-size: 13px;
        color: var(--muted);
        padding: 8px 0 0;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .cart-totals .grand {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        margin-top: 4px;
        padding-top: 6px;
        border-top: 1px solid #f0e6ec;
    }

    .cart-empty {
        font-size: 13px;
        color: var(--muted);
        padding: 16px;
        text-align: center;
        background: #fdf8fa;
        border: 1.5px dashed #e8d5dd;
        border-radius: 10px;
    }
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
        <h1 class="page-title" style="margin-bottom:0">Orders</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.orders.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <button class="btn btn-primary" onclick="openModal()">
            + Add Order
        </button>

        <a href="{{ route('admin.orders.archives') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="21 8 21 21 3 21 3 8"/>
                <rect x="1" y="3" width="22" height="5"/>
                <line x1="10" y1="12" x2="14" y2="12"/>
            </svg>
            Archives
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
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                        <td>{{ $order->order_label }}</td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>{{ $order->deliveryMethod->label }}</td>
                        <td>₱{{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->order_date->format('m/d/Y') }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="badge {{ match($order->payment_status) {
                                    'pending'    => 'badge-pending',
                                    'partial'    => 'badge-partial',
                                    'fully_paid' => 'badge-paid',
                                    default      => ''
                                } }}">
                                    {{ $order->payment_status === 'fully_paid' ? 'Paid' : ucfirst($order->payment_status) }}
                                </span>
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   onclick="event.stopPropagation()"
                                   style="font-size:12px; color:var(--muted); text-decoration:none; white-space:nowrap;">
                                    View →
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">No orders found.</td>
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

@push('scripts')
<div class="modal-backdrop" id="orderModal">
    <div class="modal">
        <div class="modal-title">Order Form</div>

        <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px 28px;">

                {{-- Left column ── --}}
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" value="{{ $nextOrderId }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Customer <span style="color:#c0392b">*</span></label>
                        <select name="customer_id" required>
                            <option value="" disabled selected></option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Delivery Method <span style="color:#c0392b">*</span></label>
                        <select name="delivery_method_id" id="deliveryMethodSelect" required onchange="handleDeliveryChange(this)">
                            <option value="" disabled selected></option>
                            @foreach ($deliveryMethods as $method)
                                <option value="{{ $method->id }}" data-type="{{ $method->type }}" {{ old('delivery_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="delivery-fee-row">
                        <label>Delivery Fee <span style="color:#c0392b">*</span></label>
                        <input type="number" name="delivery_fee" id="deliveryFeeInput" min="0" step="0.01" value="{{ old('delivery_fee', 0) }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="text" value="{{ now()->format('m/d/Y') }}" readonly>
                        <input type="hidden" name="order_date" value="{{ now()->toDateString() }}">
                    </div>
                </div>

                {{-- Middle column ── --}}
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div class="form-group">
                        <label>Product</label>
                        <select id="productSelect">
                            <option value="" disabled selected></option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    data-name="{{ $product->name }}"
                                    data-qty="{{ $product->quantity }}">
                                    {{ $product->name }} ({{ $product->quantity }} left)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="qtyInput" min="1" value="1" placeholder="1">
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" onclick="addProduct()" style="align-self:flex-start; margin-top:4px;">
                        Add to Cart
                    </button>
                </div>

                {{-- Right column — cart ── --}}
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="font-size:13px; font-weight:600;">Cart</label>
                    <div id="cartItemList" class="cart-item-list"></div>
                    <div id="cartEmpty" class="cart-empty">No products added yet.</div>
                    <div id="cartTotals" class="cart-totals" style="display:none;"></div>
                </div>

            </div>

            <div id="hiddenCartFields"></div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    let cart = [];

    function openModal() { document.getElementById('orderModal').classList.add('open'); }
    function closeModal() { document.getElementById('orderModal').classList.remove('open'); }

    document.getElementById('orderModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    function handleDeliveryChange(select) {
        const type     = select.options[select.selectedIndex].dataset.type;
        const feeRow   = document.getElementById('delivery-fee-row');
        const feeInput = document.getElementById('deliveryFeeInput');
        if (type === 'pickup') {
            feeRow.style.display = 'none';
            feeInput.value = '0';
            feeInput.removeAttribute('required');
        } else {
            feeRow.style.display = 'flex';
            feeInput.setAttribute('required', 'required');
        }
        refreshCartDisplay();
    }

    function addProduct() {
        const sel = document.getElementById('productSelect');
        const qty = parseInt(document.getElementById('qtyInput').value);
        if (!sel.value) { alert('Please select a product.'); return; }
        if (!qty || qty < 1) { alert('Please enter a valid quantity.'); return; }

        const opt   = sel.options[sel.selectedIndex];
        const id    = sel.value;
        const name  = opt.dataset.name;
        const price = parseFloat(opt.dataset.price);
        const stock = parseInt(opt.dataset.qty);

        const existing = cart.find(item => item.product_id === id);
        const newQty   = existing ? existing.quantity + qty : qty;

        if (newQty > stock) { alert(`Only ${stock} units available for "${name}".`); return; }

        if (existing) {
            existing.quantity   = newQty;
            existing.line_total = existing.unit_price * newQty;
        } else {
            cart.push({ product_id: id, name, unit_price: price, quantity: qty, line_total: price * qty });
        }

        refreshCartDisplay();
        sel.selectedIndex = 0;
        document.getElementById('qtyInput').value = 1;
    }

    function removeProduct(productId) {
        cart = cart.filter(item => item.product_id !== productId);
        refreshCartDisplay();
    }

    function refreshCartDisplay() {
        const listEl   = document.getElementById('cartItemList');
        const emptyEl  = document.getElementById('cartEmpty');
        const totalsEl = document.getElementById('cartTotals');
        const hidden   = document.getElementById('hiddenCartFields');

        if (cart.length === 0) {
            listEl.innerHTML       = '';
            emptyEl.style.display  = '';
            totalsEl.style.display = 'none';
            hidden.innerHTML       = '';
            return;
        }

        emptyEl.style.display  = 'none';
        totalsEl.style.display = '';

        listEl.innerHTML = cart.map(item => `
            <div class="cart-item">
                <span class="cart-item-name" title="${item.name}">${item.name}</span>
                <span class="cart-item-meta">×${item.quantity} @ ₱${item.unit_price.toFixed(2)} = ₱${item.line_total.toFixed(2)}</span>
                <button type="button" class="cart-item-remove" onclick="removeProduct('${item.product_id}')" title="Remove">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        `).join('');

        const subtotal = cart.reduce((sum, item) => sum + item.line_total, 0);
        const fee      = parseFloat(document.getElementById('deliveryFeeInput').value) || 0;
        const total    = subtotal + fee;

        totalsEl.innerHTML = `
            <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>₱${subtotal.toFixed(2)}</span></div>
            ${fee > 0 ? `<div style="display:flex;justify-content:space-between;"><span>Delivery</span><span>₱${fee.toFixed(2)}</span></div>` : ''}
            <div class="grand" style="display:flex;justify-content:space-between;"><span>Total</span><span>₱${total.toFixed(2)}</span></div>
        `;

        hidden.innerHTML = cart.map((item, i) => `
            <input type="hidden" name="products[${i}][product_id]" value="${item.product_id}">
            <input type="hidden" name="products[${i}][quantity]"   value="${item.quantity}">
            <input type="hidden" name="products[${i}][unit_price]" value="${item.unit_price}">
        `).join('');
    }

    document.getElementById('deliveryFeeInput').addEventListener('input', refreshCartDisplay);

    @if ($errors->any() || !empty($openModal))
        document.addEventListener('DOMContentLoaded', () => openModal());
    @endif
</script>
@endpush
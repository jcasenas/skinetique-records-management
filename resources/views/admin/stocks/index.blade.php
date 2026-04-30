@extends('admin.layout')

@section('title', 'Stocks')

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
        width: 16px; height: 16px;
        stroke: var(--muted);
        pointer-events: none;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
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
    .btn-primary   { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }
    .btn-secondary { background: #f5edf1; color: var(--primary); }
    .btn-secondary:hover { background: #ecdbe3; }
    .btn-warning   { background: #fff8e1; color: #856404; border: 1.5px solid #fde5a0; }
    .btn-warning:hover { background: #fde8a0; }

    .flash       { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 20px; }
    .flash-error { background: #fce8ef; border-left-color: var(--primary); color: var(--primary); }

    /* ── Tabs ── */
    .tab-bar {
        display: flex;
        margin-bottom: 24px;
        border-bottom: 2px solid #f0e6ec;
    }

    .tab-btn {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        color: var(--muted);
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        font-family: inherit;
        transition: color .15s, border-color .15s;
    }

    .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-btn:hover  { color: var(--text); }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Summary strip ── */
    .stock-summary {
        display: flex;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .stock-stat {
        background: #fff;
        border-radius: 12px;
        padding: 18px 22px;
        box-shadow: 0 2px 10px rgba(94,32,57,.06);
        border-left: 3px solid var(--primary);
        min-width: 160px;
        flex: 1;
    }

    .stock-stat.warn { border-left-color: #c47a1e; }
    .stock-stat.warn .stock-stat-value { color: #c47a1e; }

    .stock-stat-label { font-size: 12px; color: var(--muted); font-weight: 500; margin-bottom: 4px; }
    .stock-stat-value { font-size: 22px; font-weight: 700; color: var(--primary); }

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

    .qty-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f0faf0;
        color: #2e7d32;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .qty-pill-neg {
        background: #fff8e1;
        color: #856404;
    }

    .reason-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        background: #f5edf1;
        color: var(--primary);
    }

    .reason-badge.damaged    { background: #fce8ef; color: #c0392b; }
    .reason-badge.lost       { background: #fff8e1; color: #856404; }
    .reason-badge.expired    { background: #f0e6ff; color: #6a3aad; }
    .reason-badge.correction { background: #e3f2fd; color: #1565c0; }

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

    /* ── Modals ── */
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
        max-width: 500px;
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

    .stock-preview {
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        margin-bottom: 16px;
        display: none;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .stock-preview.visible   { display: flex; }
    .stock-preview strong    { font-weight: 700; }
    .preview-add { background: #f5e6ed; color: var(--primary); }
    .preview-sub { background: #fff8e1; color: #856404; }

    .arrow-right { font-size: 16px; color: var(--muted); }

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

    {{-- ── Header ── --}}
    <div class="page-header">
        <h1 class="page-title" style="margin-bottom:0">Stocks</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.stocks.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search by product or supplier..." value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <button class="btn btn-primary" onclick="openModal('stockModal')">
            + Stock In
        </button>

        <button class="btn btn-warning" onclick="openModal('adjustModal')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Adjust Stock
        </button>

        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            ← Products
        </a>
    </div>

    {{-- ── Summary strip ── --}}
    <div class="stock-summary">
        <div class="stock-stat">
            <div class="stock-stat-label">Total Stock-In Records</div>
            <div class="stock-stat-value">{{ number_format($stockIns->total()) }}</div>
        </div>
        <div class="stock-stat">
            <div class="stock-stat-label">Available Products</div>
            <div class="stock-stat-value">{{ $products->where('status', 'available')->count() }}</div>
        </div>
        <div class="stock-stat">
            <div class="stock-stat-label">Out of Stock</div>
            <div class="stock-stat-value">{{ $products->where('status', 'unavailable')->count() }}</div>
        </div>
        <div class="stock-stat">
            <div class="stock-stat-label">Total Units in Stock</div>
            <div class="stock-stat-value">{{ number_format($products->sum('quantity')) }}</div>
        </div>
        <div class="stock-stat warn">
            <div class="stock-stat-label">Total Adjustments</div>
            <div class="stock-stat-value">{{ number_format($adjustments->total()) }}</div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="tab-bar">
        <button class="tab-btn active" id="btn-stockins"    onclick="switchTab('stockins')">Stock-In History</button>
        <button class="tab-btn"        id="btn-adjustments" onclick="switchTab('adjustments')">Adjustments</button>
    </div>

    {{-- ══ TAB: Stock-Ins ══ --}}
    <div class="tab-panel active" id="tab-stockins">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Supplier</th>
                        <th>Qty Added</th>
                        <th>Current Stock</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stockIns as $record)
                        <tr>
                            <td>{{ $record->stock_in_date->format('m/d/Y') }}</td>
                            <td><span style="font-weight:600;">{{ $record->product->name }}</span></td>
                            <td>{{ $record->supplier->name }}</td>
                            <td><span class="qty-pill">+{{ number_format($record->quantity) }}</span></td>
                            <td>{{ number_format($record->product->quantity) }} units</td>
                            <td style="color:var(--muted); font-size:13px;">{{ $record->employee->full_name }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6">No stock-in records yet. Click <strong>+ Stock In</strong> to record your first one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            @if ($stockIns->onFirstPage())
                <span class="disabled">← Previous</span>
            @else
                <a href="{{ $stockIns->previousPageUrl() }}">← Previous</a>
            @endif
            <span>Page {{ $stockIns->currentPage() }} of {{ $stockIns->lastPage() }}</span>
            @if ($stockIns->hasMorePages())
                <a href="{{ $stockIns->nextPageUrl() }}">Next →</a>
            @else
                <span class="disabled">Next →</span>
            @endif
        </div>
    </div>

    {{-- ══ TAB: Adjustments ══ --}}
    <div class="tab-panel" id="tab-adjustments">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Qty Removed</th>
                        <th>Reason</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $adj)
                        <tr>
                            <td>{{ $adj->adjustment_date->format('m/d/Y') }}</td>
                            <td><span style="font-weight:600;">{{ $adj->product->name }}</span></td>
                            <td>
                                <span class="qty-pill qty-pill-neg">{{ number_format(abs($adj->quantity)) }} units</span>
                            </td>
                            <td>
                                <span class="reason-badge {{ $adj->reason }}">{{ ucfirst($adj->reason) }}</span>
                            </td>
                            <td style="color:var(--muted); font-size:13px;">{{ $adj->notes ?? '—' }}</td>
                            <td style="color:var(--muted); font-size:13px;">{{ $adj->employee->full_name }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6">No adjustments recorded yet. Click <strong>Adjust Stock</strong> to log a damaged, lost, or expired item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            @if ($adjustments->onFirstPage())
                <span class="disabled">← Previous</span>
            @else
                <a href="{{ $adjustments->previousPageUrl() }}">← Previous</a>
            @endif
            <span>Page {{ $adjustments->currentPage() }} of {{ $adjustments->lastPage() }}</span>
            @if ($adjustments->hasMorePages())
                <a href="{{ $adjustments->nextPageUrl() }}">Next →</a>
            @else
                <span class="disabled">Next →</span>
            @endif
        </div>
    </div>

@endsection

@push('scripts')

{{-- ══════════════════════════════
     STOCK IN MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="stockModal">
    <div class="modal">
        <div class="modal-title">Stock In</div>
        <div class="modal-sub">Record a new stock delivery. The product's quantity and status will update immediately.</div>

        <form method="POST" action="{{ route('admin.stocks.store') }}">
            @csrf

            <div class="form-group">
                <label>Product <span style="color:#c0392b">*</span></label>
                <select name="product_id" id="stockProductSelect" required onchange="refreshPreview('add')">
                    <option value="" disabled selected>Select a product</option>
                    @foreach ($products as $product)
                        <option
                            value="{{ $product->id }}"
                            data-qty="{{ $product->quantity }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->quantity }} in stock)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Supplier <span style="color:#c0392b">*</span></label>
                <select name="supplier_id" required>
                    <option value="" disabled selected>Select a supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Quantity to Add <span style="color:#c0392b">*</span></label>
                <input
                    type="number"
                    name="quantity"
                    id="stockQtyInput"
                    min="1"
                    placeholder="e.g. 50"
                    value="{{ old('quantity') }}"
                    required
                    oninput="refreshPreview('add')">
            </div>

            <div class="stock-preview preview-add" id="previewAdd">
                <div>Current: <strong id="previewAddCurrent">—</strong></div>
                <span class="arrow-right">→</span>
                <div>After stock-in: <strong id="previewAddAfter" style="color:#2e7d32;">—</strong></div>
            </div>

            <div class="form-group">
                <label>Stock-In Date <span style="color:#c0392b">*</span></label>
                <input
                    type="date"
                    name="stock_in_date"
                    value="{{ old('stock_in_date', now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('stockModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Stock</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════
     STOCK ADJUSTMENT MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="adjustModal">
    <div class="modal">
        <div class="modal-title">Adjust Stock</div>
        <div class="modal-sub">Record a deduction for damaged, lost, or expired items. The product quantity will be reduced immediately.</div>

        <form method="POST" action="{{ route('admin.stocks.adjustments.store') }}">
            @csrf

            <div class="form-group">
                <label>Product <span style="color:#c0392b">*</span></label>
                <select name="product_id" id="adjProductSelect" required onchange="refreshPreview('sub')">
                    <option value="" disabled selected>Select a product</option>
                    @foreach ($products as $product)
                        <option
                            value="{{ $product->id }}"
                            data-qty="{{ $product->quantity }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->quantity }} in stock)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Reason <span style="color:#c0392b">*</span></label>
                <select name="reason" required>
                    <option value="" disabled selected>Select a reason</option>
                    <option value="damaged"    {{ old('reason') === 'damaged'    ? 'selected' : '' }}>Damaged</option>
                    <option value="lost"       {{ old('reason') === 'lost'       ? 'selected' : '' }}>Lost</option>
                    <option value="expired"    {{ old('reason') === 'expired'    ? 'selected' : '' }}>Expired</option>
                    <option value="correction" {{ old('reason') === 'correction' ? 'selected' : '' }}>Inventory Correction</option>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity to Deduct <span style="color:#c0392b">*</span></label>
                <input
                    type="number"
                    name="quantity"
                    id="adjQtyInput"
                    min="1"
                    placeholder="e.g. 2"
                    value="{{ old('quantity') }}"
                    required
                    oninput="refreshPreview('sub')">
            </div>

            <div class="stock-preview preview-sub" id="previewSub">
                <div>Current: <strong id="previewSubCurrent">—</strong></div>
                <span class="arrow-right">→</span>
                <div>After adjustment: <strong id="previewSubAfter" style="color:#856404;">—</strong></div>
            </div>

            <div class="form-group">
                <label>Notes <span style="font-weight:400; color:var(--muted);">(optional)</span></label>
                <textarea
                    name="notes"
                    rows="2"
                    placeholder="e.g. Soap cracked during storage"
                    maxlength="255">{{ old('notes') }}</textarea>
            </div>

            <div class="form-group">
                <label>Adjustment Date <span style="color:#c0392b">*</span></label>
                <input
                    type="date"
                    name="adjustment_date"
                    value="{{ old('adjustment_date', now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('adjustModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background:#856404;">Record Adjustment</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ── Tab switching ────────────────────────────────────────
    function switchTab(name) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        document.getElementById('btn-' + name).classList.add('active');
    }

    // ── Modal helpers ────────────────────────────────────────
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ── Live stock previews ──────────────────────────────────
    function refreshPreview(mode) {
        if (mode === 'add') {
            const sel     = document.getElementById('stockProductSelect');
            const qty     = parseInt(document.getElementById('stockQtyInput').value) || 0;
            const preview = document.getElementById('previewAdd');
            if (!sel.value) { preview.classList.remove('visible'); return; }
            const cur = parseInt(sel.options[sel.selectedIndex].dataset.qty) || 0;
            document.getElementById('previewAddCurrent').textContent = cur + ' units';
            document.getElementById('previewAddAfter').textContent   = (cur + qty) + ' units';
            preview.classList.add('visible');
        } else {
            const sel     = document.getElementById('adjProductSelect');
            const qty     = parseInt(document.getElementById('adjQtyInput').value) || 0;
            const preview = document.getElementById('previewSub');
            if (!sel.value) { preview.classList.remove('visible'); return; }
            const cur   = parseInt(sel.options[sel.selectedIndex].dataset.qty) || 0;
            const after = Math.max(0, cur - qty);
            document.getElementById('previewSubCurrent').textContent = cur + ' units';
            document.getElementById('previewSubAfter').textContent   = after + ' units';
            preview.classList.add('visible');
        }
    }

    // ── Re-open correct modal after validation error ─────────
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            @if (old('reason') !== null)
                {{-- 'reason' field belongs to the adjustment form --}}
                openModal('adjustModal');
                switchTab('adjustments');
            @else
                openModal('stockModal');
            @endif
        });
    @endif
</script>

@endpush
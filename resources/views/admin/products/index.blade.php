@extends('admin.layout')

@section('title', 'Products & Stocks')

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

    .btn-icon   { padding: 6px 10px; border-radius: 8px; line-height: 1; }
    .btn-edit   { background: #eef2ff; color: #3730a3; border: none; }
    .btn-edit:hover { background: #e0e7ff; }
    .btn-delete { background: #fce8ef; color: var(--primary); border: none; }
    .btn-delete:hover { background: #f8cfe0; }

    /* ── Flash ── */
    .flash       { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 20px; }
    .flash-error { background: #fce8ef; border-left-color: var(--primary); color: var(--primary); }

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

    .action-cell { display: flex; gap: 8px; align-items: center; }

    /* ── Status badge ── */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-available   { background: #d1f5e0; color: #1a7a42; }
    .badge-unavailable { background: #f5edf1; color: var(--muted); }

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
        max-width: 480px;
        box-shadow: 0 8px 48px rgba(94,32,57,.18);
    }

    .modal-sm { max-width: 380px; }

    .modal-title { font-size: 20px; font-weight: 700; margin-bottom: 24px; letter-spacing: -0.02em; }

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
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: var(--primary); }

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

    .confirm-text { font-size: 14px; color: var(--muted); line-height: 1.6; margin-bottom: 0; }
</style>
@endpush

@section('content')

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash flash-error">{{ $errors->first() }}</div>
    @endif

    {{-- ── Header ── --}}
    <div class="page-header">
        <h1 class="page-title" style="margin-bottom:0">Products & Stocks</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.products.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <button class="btn btn-primary" onclick="openModal('addModal')">
            + Add Product
        </button>

        <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary">
            Stocks
        </a>
    </div>

    {{-- ── Products table ── --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Supplier</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td style="font-weight:600;">{{ $product->name }}</td>
                        <td>{{ $product->supplier->name }}</td>
                        <td style="color:var(--muted); font-size:13px;">{{ $product->description ?? '—' }}</td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td>{{ number_format($product->quantity) }}</td>
                        <td>
                            <span class="badge {{ $product->status === 'available' ? 'badge-available' : 'badge-unavailable' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-cell">
                                <button
                                    class="btn btn-icon btn-edit"
                                    title="Edit"
                                    onclick="openEdit(
                                        {{ $product->id }},
                                        {{ json_encode($product->supplier_id) }},
                                        {{ json_encode($product->name) }},
                                        {{ json_encode($product->description) }},
                                        {{ $product->price }}
                                    )">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>

                                <button
                                    class="btn btn-icon btn-delete"
                                    title="Delete"
                                    onclick="openDelete({{ $product->id }}, {{ json_encode($product->name) }})">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="7">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    <div class="pagination">
        @if ($products->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $products->previousPageUrl() }}">← Previous</a>
        @endif

        <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

        @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>

@endsection

@push('scripts')

{{-- ══════════════════════════════
     ADD PRODUCT MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal">
        <div class="modal-title">Add Product</div>

        <form method="POST" action="{{ route('admin.products.store') }}">
            @csrf

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
                <label>Product Name <span style="color:#c0392b">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Signature Kit" maxlength="100" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="Short description (optional)" maxlength="255">
            </div>

            <div class="form-group">
                <label>Price (₱) <span style="color:#c0392b">*</span></label>
                <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" placeholder="0.00" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════
     EDIT PRODUCT MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-title">Edit Product</div>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Supplier <span style="color:#c0392b">*</span></label>
                <select name="supplier_id" id="edit_supplier_id" required>
                    <option value="" disabled>Select a supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Product Name <span style="color:#c0392b">*</span></label>
                <input type="text" name="name" id="edit_name" maxlength="100" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" id="edit_description" maxlength="255">
            </div>

            <div class="form-group">
                <label>Price (₱) <span style="color:#c0392b">*</span></label>
                <input type="number" name="price" id="edit_price" min="0" step="0.01" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal modal-sm">
        <div class="modal-title">Delete Product</div>
        <p class="confirm-text" id="deleteConfirmText"></p>

        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background:#c0392b;">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    function openEdit(id, supplierId, name, description, price) {
        document.getElementById('editForm').action         = `/admin/products/${id}`;
        document.getElementById('edit_supplier_id').value  = supplierId;
        document.getElementById('edit_name').value         = name;
        document.getElementById('edit_description').value  = description ?? '';
        document.getElementById('edit_price').value        = price;
        openModal('editModal');
    }

    function openDelete(id, name) {
        document.getElementById('deleteForm').action = `/admin/products/${id}`;
        document.getElementById('deleteConfirmText').textContent =
            `Are you sure you want to delete "${name}"? This cannot be undone.`;
        openModal('deleteModal');
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => openModal('addModal'));
    @endif
</script>

@endpush
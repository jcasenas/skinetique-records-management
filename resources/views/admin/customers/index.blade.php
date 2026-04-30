@extends('admin.layout')

@section('title', 'Customers')

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
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }
    .btn-icon   { padding: 6px 10px; border-radius: 8px; line-height: 1; }
    .btn-edit   { background: #eef2ff; color: #3730a3; border: none; }
    .btn-edit:hover { background: #e0e7ff; }
    .btn-delete { background: #fce8ef; color: var(--primary); border: none; }
    .btn-delete:hover { background: #f8cfe0; }

    .flash       { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 20px; }
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
        max-width: 520px;
        box-shadow: 0 8px 48px rgba(94,32,57,.18);
    }

    .modal-sm { max-width: 380px; }

    .modal-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 24px;
        letter-spacing: -0.02em;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
    }

    .form-group input {
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

    .form-group input:focus { border-color: var(--primary); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

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

    .confirm-text { font-size: 14px; color: var(--muted); line-height: 1.6; }
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
        <h1 class="page-title" style="margin-bottom:0">Customers</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search customers..." value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <button class="btn btn-primary" onclick="openModal('addModal')">
            + Add Customer
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->address }}</td>
                        <td>{{ $customer->contact_num }}</td>
                        <td>
                            <div class="action-cell">
                                <button
                                    class="btn btn-icon btn-edit"
                                    title="Edit"
                                    onclick="openEdit(
                                        {{ $customer->id }},
                                        {{ json_encode($customer->first_name) }},
                                        {{ json_encode($customer->last_name) }},
                                        {{ json_encode($customer->address) }},
                                        {{ json_encode($customer->contact_num) }}
                                    )">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>

                                <button
                                    class="btn btn-icon btn-delete"
                                    title="Delete"
                                    onclick="openDelete({{ $customer->id }}, {{ json_encode($customer->full_name) }})">
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
                        <td colspan="4">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    <div class="pagination">
        @if ($customers->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $customers->previousPageUrl() }}">← Previous</a>
        @endif

        <span>Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}</span>

        @if ($customers->hasMorePages())
            <a href="{{ $customers->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>

@endsection


@push('scripts')

{{-- ══════════════════════════════
     ADD CUSTOMER MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal">
        <div class="modal-title">Add Customer</div>
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label>First Name <span style="color:#c0392b">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Maria" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label>Last Name <span style="color:#c0392b">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Santos" maxlength="50" required>
                </div>
            </div>

            <div class="form-group">
                <label>Address <span style="color:#c0392b">*</span></label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. Davao City" maxlength="100" required>
            </div>

            <div class="form-group">
                <label>Contact Number <span style="color:#c0392b">*</span></label>
                <input type="text" name="contact_num" value="{{ old('contact_num') }}" placeholder="e.g. 09123456789" maxlength="13" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════
     EDIT CUSTOMER MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-title">Edit Customer</div>
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label>First Name <span style="color:#c0392b">*</span></label>
                    <input type="text" name="first_name" id="edit_first_name" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label>Last Name <span style="color:#c0392b">*</span></label>
                    <input type="text" name="last_name" id="edit_last_name" maxlength="50" required>
                </div>
            </div>

            <div class="form-group">
                <label>Address <span style="color:#c0392b">*</span></label>
                <input type="text" name="address" id="edit_address" maxlength="100" required>
            </div>

            <div class="form-group">
                <label>Contact Number <span style="color:#c0392b">*</span></label>
                <input type="text" name="contact_num" id="edit_contact_num" maxlength="13" required>
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
        <div class="modal-title">Delete Customer</div>
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

    function openEdit(id, firstName, lastName, address, contactNum) {
        document.getElementById('editForm').action = `/admin/customers/${id}`;
        document.getElementById('edit_first_name').value  = firstName;
        document.getElementById('edit_last_name').value   = lastName;
        document.getElementById('edit_address').value     = address;
        document.getElementById('edit_contact_num').value = contactNum;
        openModal('editModal');
    }

    function openDelete(id, name) {
        document.getElementById('deleteForm').action = `/admin/customers/${id}`;
        document.getElementById('deleteConfirmText').textContent =
            `Are you sure you want to delete "${name}"? This cannot be undone.`;
        openModal('deleteModal');
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => openModal('addModal'));
    @endif
</script>
@endpush
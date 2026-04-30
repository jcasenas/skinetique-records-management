@extends('admin.layout')

@section('title', 'Employees')

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

    .btn:active    { transform: scale(.97); }
    .btn-primary   { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }
    .btn-icon      { padding: 6px 10px; border-radius: 8px; line-height: 1; }
    .btn-edit      { background: #eef2ff; color: #3730a3; border: none; }
    .btn-edit:hover   { background: #e0e7ff; }
    .btn-delete    { background: #fce8ef; color: var(--primary); border: none; }
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

    /* ── Role badge ── */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-owner { background: #f8e1eb; color: var(--primary); border: 1px solid #e8b4c8; }
    .badge-staff { background: #e9ecef; color: #495057; }

    /* You badge (current logged-in user) */
    .you-tag {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        color: var(--muted);
        background: #f5edf1;
        border-radius: 6px;
        padding: 2px 6px;
        margin-left: 6px;
        vertical-align: middle;
    }

    .action-cell { display: flex; gap: 8px; align-items: center; }

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
       MODALS
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
        max-width: 540px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 8px 48px rgba(94,32,57,.18);
    }

    .modal-sm { max-width: 380px; }

    .modal-title { font-size: 20px; font-weight: 700; margin-bottom: 24px; letter-spacing: -0.02em; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

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

    .password-hint {
        font-size: 11px;
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

    .confirm-text { font-size: 14px; color: var(--muted); line-height: 1.6; margin-bottom: 0; }

    /* Section divider inside modal */
    .modal-section {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        color: var(--muted);
        text-transform: uppercase;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f0e6ec;
    }
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
        <h1 class="page-title" style="margin-bottom:0">Employees</h1>

        <div class="search-wrap">
            <form method="GET" action="{{ route('admin.employees.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search employees..." value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <button class="btn btn-primary" onclick="openModal('addModal')">
            + Add Employee
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Position</th>
                    <th>Contact Number</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    @php $isSelf = $employee->id === Auth::guard('employee')->id(); @endphp
                    <tr>
                        <td>
                            {{ $employee->full_name }}
                            @if ($isSelf)
                                <span class="you-tag">You</span>
                            @endif
                        </td>
                        <td style="color:var(--muted); font-size:13px;">{{ $employee->username }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->contact_num }}</td>
                        <td>
                            <span class="badge {{ $employee->role === 'owner' ? 'badge-owner' : 'badge-staff' }}">
                                {{ ucfirst($employee->role) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-cell">
                                <button
                                    class="btn btn-icon btn-edit"
                                    title="Edit"
                                    onclick="openEdit(
                                        {{ $employee->id }},
                                        {{ json_encode($employee->first_name) }},
                                        {{ json_encode($employee->last_name) }},
                                        {{ json_encode($employee->position) }},
                                        {{ json_encode($employee->contact_num) }},
                                        {{ json_encode($employee->username) }},
                                        {{ json_encode($employee->role) }}
                                    )">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>

                                {{-- Cannot delete yourself --}}
                                @if (! $isSelf)
                                    <button
                                        class="btn btn-icon btn-delete"
                                        title="Delete"
                                        onclick="openDelete({{ $employee->id }}, {{ json_encode($employee->full_name) }})">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    <div class="pagination">
        @if ($employees->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $employees->previousPageUrl() }}">← Previous</a>
        @endif

        <span>Page {{ $employees->currentPage() }} of {{ $employees->lastPage() }}</span>

        @if ($employees->hasMorePages())
            <a href="{{ $employees->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>

@endsection


@push('scripts')

{{-- ══════════════════════════════
     ADD EMPLOYEE MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal">
        <div class="modal-title">Add Employee</div>

        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf

            <div class="modal-section">Personal Information</div>

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

            <div class="form-row">
                <div class="form-group">
                    <label>Position <span style="color:#c0392b">*</span></label>
                    <input type="text" name="position" value="{{ old('position') }}" placeholder="e.g. Sales Staff" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label>Contact Number <span style="color:#c0392b">*</span></label>
                    <input type="text" name="contact_num" value="{{ old('contact_num') }}" placeholder="e.g. 09123456789" maxlength="13" required>
                </div>
            </div>

            <div class="modal-section" style="margin-top:8px;">Account Credentials</div>

            <div class="form-row">
                <div class="form-group">
                    <label>Username <span style="color:#c0392b">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="e.g. maria_santos" maxlength="50" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label>Role <span style="color:#c0392b">*</span></label>
                    <select name="role" required>
                        <option value="staff"  {{ old('role', 'staff') === 'staff'  ? 'selected' : '' }}>Staff</option>
                        <option value="owner"  {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password <span style="color:#c0392b">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 characters" autocomplete="new-password" required>
                    <span class="password-hint">Must be at least 8 characters.</span>
                </div>
                <div class="form-group">
                    <label>Confirm Password <span style="color:#c0392b">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" autocomplete="new-password" required>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════
     EDIT EMPLOYEE MODAL
══════════════════════════════ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-title">Edit Employee</div>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="modal-section">Personal Information</div>

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

            <div class="form-row">
                <div class="form-group">
                    <label>Position <span style="color:#c0392b">*</span></label>
                    <input type="text" name="position" id="edit_position" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label>Contact Number <span style="color:#c0392b">*</span></label>
                    <input type="text" name="contact_num" id="edit_contact_num" maxlength="13" required>
                </div>
            </div>

            <div class="modal-section" style="margin-top:8px;">Account Credentials</div>

            <div class="form-row">
                <div class="form-group">
                    <label>Username <span style="color:#c0392b">*</span></label>
                    <input type="text" name="username" id="edit_username" maxlength="50" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label>Role <span style="color:#c0392b">*</span></label>
                    <select name="role" id="edit_role" required>
                        <option value="staff">Staff</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
            </div>

            <div class="modal-section" style="margin-top:8px;">Change Password <span style="font-weight:400; text-transform:none; letter-spacing:0;">(leave blank to keep current)</span></div>

            <div class="form-row">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Min. 8 characters" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" autocomplete="new-password">
                </div>
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
        <div class="modal-title">Delete Employee</div>
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

    function openEdit(id, firstName, lastName, position, contactNum, username, role) {
        document.getElementById('editForm').action    = `/admin/employees/${id}`;
        document.getElementById('edit_first_name').value  = firstName;
        document.getElementById('edit_last_name').value   = lastName;
        document.getElementById('edit_position').value    = position;
        document.getElementById('edit_contact_num').value = contactNum;
        document.getElementById('edit_username').value    = username;
        document.getElementById('edit_role').value        = role;
        // Clear password fields
        document.querySelectorAll('#editModal input[type="password"]').forEach(el => el.value = '');
        openModal('editModal');
    }

    function openDelete(id, name) {
        document.getElementById('deleteForm').action = `/admin/employees/${id}`;
        document.getElementById('deleteConfirmText').textContent =
            `Are you sure you want to delete the account for "${name}"? This cannot be undone.`;
        openModal('deleteModal');
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => openModal('addModal'));
    @endif
</script>
@endpush
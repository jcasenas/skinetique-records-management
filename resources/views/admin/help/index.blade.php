@extends('admin.layout')

@section('title', 'Help — User Manual')

@push('styles')
<style>
    /* ── Page wrapper ── */
    .help-wrap {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 28px;
        align-items: start;
    }

    /* ── Search bar ── */
    .help-search-wrap {
        position: relative;
        margin-bottom: 28px;
    }

    .help-search-wrap svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 17px; height: 17px;
        stroke: var(--muted);
        pointer-events: none;
    }

    .help-search {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 1.5px solid #e8d5dd;
        border-radius: 10px;
        background: #fff;
        font-size: 14px;
        font-family: inherit;
        color: var(--text);
        outline: none;
        box-shadow: 0 2px 8px rgba(94,32,57,.05);
        transition: border-color .2s, box-shadow .2s;
    }

    .help-search:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(123,45,78,.09);
    }

    .help-search::placeholder { color: var(--muted); }

    .search-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--muted);
        font-size: 18px;
        line-height: 1;
        display: none;
        padding: 2px 6px;
        border-radius: 4px;
        transition: color .15s;
    }

    .search-clear:hover { color: var(--primary); }
    .search-clear.visible { display: block; }

    .search-count {
        font-size: 12px;
        color: var(--muted);
        margin-top: 8px;
        margin-bottom: -16px;
        min-height: 18px;
    }

    /* ── Left TOC ── */
    .help-toc {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
        position: sticky;
        top: 24px;
    }

    .toc-header {
        padding: 16px 20px 12px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        border-bottom: 1.5px solid #f0e6ec;
    }

    .toc-list { padding: 8px 0; }

    .toc-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 20px;
        font-size: 13.5px;
        color: var(--text);
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: background .12s, border-color .12s, color .12s;
        cursor: pointer;
    }

    .toc-item:hover {
        background: #fdf5f8;
        color: var(--primary);
        border-left-color: #f2d6e0;
    }

    .toc-item.active {
        background: #f5e6ed;
        color: var(--primary);
        border-left-color: var(--primary);
        font-weight: 600;
    }

    .toc-item.hidden-section { display: none; }

    .toc-icon {
        width: 18px; height: 18px;
        stroke: currentColor;
        flex-shrink: 0;
        opacity: .7;
    }

    /* ── Right: content ── */
    .help-content { display: flex; flex-direction: column; gap: 20px; }

    .help-section {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
        overflow: hidden;
        transition: box-shadow .2s;
    }

    .help-section.no-match { display: none; }

    .section-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 24px;
        border-bottom: 1.5px solid #f0e6ec;
        cursor: pointer;
        user-select: none;
        transition: background .12s;
    }

    .section-head:hover { background: #fdf5f8; }

    .section-head-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: #f5e6ed;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .section-head-icon svg {
        width: 20px; height: 20px;
        stroke: var(--primary);
        stroke-width: 1.8;
    }

    .section-head-text { flex: 1; }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 2px;
    }

    .section-subtitle {
        font-size: 12px;
        color: var(--muted);
    }

    .section-chevron {
        width: 18px; height: 18px;
        stroke: var(--muted);
        transition: transform .2s;
        flex-shrink: 0;
    }

    .help-section.collapsed .section-chevron { transform: rotate(-90deg); }

    /* Section body — improved spacing */
    .section-body {
        padding: 28px 28px 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .help-section.collapsed .section-body { display: none; }

    /* Topic block — improved spacing */
    .topic {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .topic-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    .topic-body {
        font-size: 14px;
        color: var(--text);
        line-height: 1.8;
    }

    .topic-body p {
        margin-bottom: 10px;
    }

    .topic-body p:last-child { margin-bottom: 0; }

    /* Steps list — improved spacing */
    .steps {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 4px;
    }

    .steps li {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        font-size: 14px;
        color: var(--text);
        line-height: 1.7;
    }

    .step-num {
        width: 24px; height: 24px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* Callouts */
    .callout {
        display: flex;
        gap: 12px;
        background: #f5e6ed;
        border-radius: 10px;
        padding: 14px 16px;
        font-size: 13.5px;
        color: var(--primary);
        line-height: 1.7;
        margin-top: 4px;
    }

    .callout-warn {
        background: #fff8e1;
        color: #7a4d00;
    }

    .callout svg {
        width: 18px; height: 18px;
        stroke: currentColor;
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* Divider */
    .topic-divider {
        height: 1px;
        background: #f0e6ec;
        border: none;
        margin: 0;
    }

    /* No results */
    .no-results {
        display: none;
        text-align: center;
        padding: 48px 20px;
        color: var(--muted);
        font-size: 14px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(94,32,57,.06);
    }

    .no-results svg {
        width: 40px; height: 40px;
        stroke: #e8d5dd;
        margin-bottom: 12px;
    }

    mark {
        background: #fde8a8;
        color: var(--text);
        border-radius: 3px;
        padding: 0 2px;
    }

    .owner-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f8e1eb;
        color: var(--primary);
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
        vertical-align: middle;
    }

    /* New badge for recently added features */
    .new-badge {
        display: inline-flex;
        align-items: center;
        background: #d1f5e0;
        color: #1a7a42;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

    <h1 class="page-title">User Manual</h1>

    {{-- ── Search ── --}}
    <div class="help-search-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input
            type="text"
            class="help-search"
            id="helpSearch"
            placeholder="Search the manual… e.g. &quot;how to record a return&quot;"
            autocomplete="off"
        >
        <button class="search-clear" id="searchClear" onclick="clearSearch()">×</button>
    </div>
    <div class="search-count" id="searchCount"></div>

    <div class="help-wrap">

        {{-- ════════ TABLE OF CONTENTS ════════ --}}
        <div class="help-toc">
            <div class="toc-header">Sections</div>
            <div class="toc-list" id="tocList">

                <a class="toc-item active" data-section="getting-started" onclick="scrollTo('getting-started')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                    Getting Started
                </a>

                <a class="toc-item" data-section="dashboard" onclick="scrollTo('dashboard')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Dashboard
                </a>

                <a class="toc-item" data-section="customers" onclick="scrollTo('customers')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Customers
                </a>

                <a class="toc-item" data-section="products" onclick="scrollTo('products')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    Products & Stocks
                </a>

                <a class="toc-item" data-section="orders" onclick="scrollTo('orders')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Orders
                </a>

                <a class="toc-item" data-section="payments" onclick="scrollTo('payments')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>
                    Payment
                </a>

                <a class="toc-item" data-section="returns" onclick="scrollTo('returns')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg>
                    Returns
                    <span class="new-badge">New</span>
                </a>

                <a class="toc-item" data-section="stock-adjustments" onclick="scrollTo('stock-adjustments')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Stock Adjustments
                    <span class="new-badge">New</span>
                </a>

                <a class="toc-item" data-section="reports" onclick="scrollTo('reports')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Business Reports
                </a>

                <a class="toc-item" data-section="settings" onclick="scrollTo('settings')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Settings
                </a>

                @if (Auth::guard('employee')->user()?->isOwner())
                <a class="toc-item" data-section="employees" onclick="scrollTo('employees')">
                    <svg class="toc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    Employees
                    <span class="owner-badge">Owner</span>
                </a>
                @endif

            </div>
        </div>

        {{-- ════════ MANUAL CONTENT ════════ --}}
        <div class="help-content" id="helpContent">

            <div class="no-results" id="noResults">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <div>No results found for your search.</div>
                <div style="font-size:12px; margin-top:4px;">Try different keywords or <button onclick="clearSearch()" style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:12px;text-decoration:underline;">clear the search</button>.</div>
            </div>

            {{-- ══ SECTION: Getting Started ══ --}}
            <div class="help-section" id="section-getting-started" data-section="getting-started">
                <div class="section-head" onclick="toggleSection('getting-started')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Getting Started</div>
                        <div class="section-subtitle">Logging in, navigating the system, and understanding your role</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Logging In</div>
                        <div class="topic-body">
                            <p>Open the SKINETIQUE system in your browser. You will be greeted by the login page. Enter the username and password provided to you by the owner, then click <strong>Sign In</strong>.</p>
                            <p>Use the eye icon beside the password field to show or hide your password as you type.</p>
                        </div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Enter your <strong>Username</strong> in the first field.</div></li>
                            <li><span class="step-num">2</span><div>Enter your <strong>Password</strong> in the second field.</div></li>
                            <li><span class="step-num">3</span><div>Click <strong>Sign In</strong>. You will be taken to the Dashboard.</div></li>
                        </ol>
                        <div class="callout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            If your credentials are incorrect, an error message will appear above the form. Contact the owner to reset your password.
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Navigating the System</div>
                        <div class="topic-body">
                            <p>The dark sidebar on the left contains all navigation links. Click any item to go to that page. The currently active page is highlighted in the sidebar.</p>
                            <p>At the bottom of the sidebar you will find <strong>Help</strong> (this page) and <strong>Logout</strong>.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">User Roles</div>
                        <div class="topic-body">
                            <p>The system has two roles. Your role was set by the owner when your account was created.</p>
                        </div>
                        <ol class="steps">
                            <li><span class="step-num" style="background:#7b2d4e;">O</span><div><strong>Owner</strong> — full access to all pages including the Employees section for managing staff accounts.</div></li>
                            <li><span class="step-num" style="background:#6c757d;">S</span><div><strong>Staff</strong> — access to all operational pages: Dashboard, Customers, Products & Stocks, Orders, Payment, Reports, and Settings.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Logging Out</div>
                        <div class="topic-body">
                            <p>Click <strong>Logout</strong> at the bottom of the sidebar. You will be redirected to the login page. Always log out when leaving the system unattended.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Dashboard ══ --}}
            <div class="help-section collapsed" id="section-dashboard" data-section="dashboard">
                <div class="section-head" onclick="toggleSection('dashboard')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Dashboard</div>
                        <div class="section-subtitle">KPIs, quick actions, and the activity feed</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">KPI Cards</div>
                        <div class="topic-body">
                            <p>The two cards at the top left show <strong>Orders Today</strong> — the number of orders placed today — and <strong>Earned Today</strong> — the combined total value of those orders. These update automatically as new orders are created.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Quick Actions</div>
                        <div class="topic-body">
                            <p>Three shortcut buttons are displayed below the KPIs for the most common tasks:</p>
                        </div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div><strong>Create New Order</strong> — navigates to the Orders page and immediately opens the Order Form modal.</div></li>
                            <li><span class="step-num">2</span><div><strong>Manage Stocks</strong> — takes you to the Stocks page to record incoming product deliveries.</div></li>
                            <li><span class="step-num">3</span><div><strong>Pending Payments</strong> — takes you to the Payment page showing only orders with unpaid or partially paid balances.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Recent Activity Feed</div>
                        <div class="topic-body">
                            <p>The panel on the right shows the 10 most recent events across the system, sorted from newest to oldest. Each entry shows the event type, a short description, the amount where applicable, and how long ago it happened.</p>
                            <p>The feed now tracks five types of events: new orders, recorded payments, stock-ins, stock adjustments, and returns. Each type has its own color-coded icon so you can identify events at a glance.</p>
                            <p>If there are orders with pending or partial payments, an amber alert banner appears above the dashboard with a direct link to the Payment page.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Customers ══ --}}
            <div class="help-section collapsed" id="section-customers" data-section="customers">
                <div class="section-head" onclick="toggleSection('customers')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Customers</div>
                        <div class="section-subtitle">Adding, editing, and searching customer records</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Adding a Customer</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Click <strong>+ Add Customer</strong> in the top right of the Customers page.</div></li>
                            <li><span class="step-num">2</span><div>Fill in the customer's First Name, Last Name, Address, and Contact Number.</div></li>
                            <li><span class="step-num">3</span><div>Click <strong>Add Customer</strong> to save. The customer will appear in the list immediately.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Editing a Customer</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Find the customer using the search bar or by scrolling the list.</div></li>
                            <li><span class="step-num">2</span><div>Click the <strong>blue pencil icon</strong> in the Action column.</div></li>
                            <li><span class="step-num">3</span><div>Update the fields in the Edit modal and click <strong>Save Changes</strong>.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Deleting a Customer</div>
                        <div class="topic-body">
                            <p>Click the <strong>red trash icon</strong> next to a customer. A confirmation dialog will appear. Click <strong>Delete</strong> to confirm.</p>
                        </div>
                        <div class="callout callout-warn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            Customers with existing orders cannot be deleted. You must remove all their orders first, or keep the customer record.
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Searching Customers</div>
                        <div class="topic-body">
                            <p>Use the search bar at the top of the page. You can search by first name, last name, address, or contact number. Results update as you type and paginate across multiple pages.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Products & Stocks ══ --}}
            <div class="help-section collapsed" id="section-products" data-section="products">
                <div class="section-head" onclick="toggleSection('products')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Products & Stocks</div>
                        <div class="section-subtitle">Managing the product catalogue and recording stock deliveries</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Adding a Product</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Click <strong>+ Add Product</strong> on the Products & Stocks page.</div></li>
                            <li><span class="step-num">2</span><div>Select the <strong>Supplier</strong>, enter the <strong>Product Name</strong>, an optional <strong>Description</strong>, and the <strong>Price</strong>.</div></li>
                            <li><span class="step-num">3</span><div>Click <strong>Add Product</strong>. The product is created with a quantity of 0 and a status of <em>Unavailable</em>.</div></li>
                        </ol>
                        <div class="callout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Stock quantity is never set manually when creating a product. It is managed through Stock In records only.
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Product Status</div>
                        <div class="topic-body">
                            <p>Each product shows either <strong>Available</strong> (green) or <strong>Unavailable</strong> (grey). The system sets this automatically — a product becomes Available as soon as its stock quantity is greater than zero, and reverts to Unavailable when stock reaches zero.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Recording a Stock Delivery (Stock In)</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Click the <strong>Stocks</strong> button (beside Add Product) to go to the Stocks page.</div></li>
                            <li><span class="step-num">2</span><div>Click <strong>+ Stock In</strong>.</div></li>
                            <li><span class="step-num">3</span><div>Select the <strong>Product</strong> and the <strong>Supplier</strong> the delivery came from.</div></li>
                            <li><span class="step-num">4</span><div>Enter the <strong>Quantity</strong> received. A live preview shows the current stock and what it will become after recording.</div></li>
                            <li><span class="step-num">5</span><div>Set the <strong>Stock-In Date</strong> (defaults to today; cannot be a future date).</div></li>
                            <li><span class="step-num">6</span><div>Click <strong>Record Stock</strong>. The product quantity updates immediately.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Editing or Deleting a Product</div>
                        <div class="topic-body">
                            <p>Use the <strong>pencil icon</strong> to edit a product's name, supplier, description, or price. Use the <strong>trash icon</strong> to delete it. Products that have existing order lines cannot be deleted.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Orders ══ --}}
            <div class="help-section collapsed" id="section-orders" data-section="orders">
                <div class="section-head" onclick="toggleSection('orders')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Orders</div>
                        <div class="section-subtitle">Creating orders, managing the cart, and archiving</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Creating an Order</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Click <strong>+ Add Order</strong> on the Orders page (or use the Create New Order shortcut on the Dashboard).</div></li>
                            <li><span class="step-num">2</span><div>Select a <strong>Customer</strong> and a <strong>Delivery Method</strong>. For Rider or Shipping deliveries, enter the <strong>Delivery Fee</strong>. For Pickup, the fee is automatically set to zero.</div></li>
                            <li><span class="step-num">3</span><div>In the middle column, select a <strong>Product</strong> and enter a <strong>Quantity</strong>, then click <strong>Add Product</strong>. Repeat for each product.</div></li>
                            <li><span class="step-num">4</span><div>The <strong>Cart</strong> column on the right shows a live summary with line totals, subtotal, delivery fee, and grand total.</div></li>
                            <li><span class="step-num">5</span><div>To remove a product, click <strong>Remove Product</strong> — this removes the last item added.</div></li>
                            <li><span class="step-num">6</span><div>Click <strong>Submit</strong> to save the order. The Order ID and Date are filled automatically.</div></li>
                        </ol>
                        <div class="callout callout-warn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            You can only add products marked as Available. The system also checks that your requested quantity does not exceed current stock.
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Payment Status Badges</div>
                        <div class="topic-body">
                            <p>Every order in the list shows one of three badges: <strong>Pending</strong> (no payment recorded yet), <strong>Partial</strong> (some payment recorded but a balance remains), or <strong>Paid</strong> (fully settled). These update automatically whenever a payment is recorded.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Order Detail Page</div>
                        <div class="topic-body">
                            <p>Click any order row or the <strong>View →</strong> link to open the order detail page. It shows the full order information, all products ordered, a payment progress bar, payment history with receipt download links, and the returns history for that order.</p>
                            <p>If a product has been partially or fully returned, a small <strong>"X returned"</strong> tag appears beside its name in the products table.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Order Archives</div>
                        <div class="topic-body">
                            <p>Click <strong>Archives</strong> on the Orders page to view fulfilled orders that have been archived. An order can only be archived once it is fully paid. Archiving is irreversible — it marks the order as delivered and deducts the sold quantities from stock. Any units already returned before archiving are not double-deducted.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Payment ══ --}}
            <div class="help-section collapsed" id="section-payments" data-section="payments">
                <div class="section-head" onclick="toggleSection('payments')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Payment</div>
                        <div class="section-subtitle">Recording payments against pending and partial orders</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Recording a Payment</div>
                        <div class="topic-body">
                            <p>The Payment page shows only orders with outstanding balances — Pending or Partial. Each row displays the order total, how much has been paid so far, the remaining balance, and a visual progress bar.</p>
                        </div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Find the order and click <strong>Record Payment</strong>.</div></li>
                            <li><span class="step-num">2</span><div>Select the <strong>Payment Method</strong> (Cash, GCash, or Bank Transfer).</div></li>
                            <li><span class="step-num">3</span><div>Enter the <strong>Amount</strong>. The remaining balance is shown as a hint below the field. You cannot enter more than the remaining balance.</div></li>
                            <li><span class="step-num">4</span><div>Click <strong>Record Payment</strong>. The order's payment status updates automatically and a receipt is generated.</div></li>
                        </ol>
                        <div class="callout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Payments support partial instalments. An order can receive multiple payments over time until it is fully settled, at which point it moves to Payment History.
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Returns (NEW) ══ --}}
            <div class="help-section collapsed" id="section-returns" data-section="returns">
                <div class="section-head" onclick="toggleSection('returns')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Returns <span class="new-badge">New</span></div>
                        <div class="section-subtitle">Recording customer returns and restoring stock</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">What Is a Return?</div>
                        <div class="topic-body">
                            <p>A return is recorded when a customer sends a product back. When a return is processed, the product's stock quantity is automatically restored — the units go back into inventory and the product is marked Available again if it was previously out of stock.</p>
                            <p>Returns are recorded per order. You can only return a product that was part of the original order, and you cannot return more units than were originally ordered.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Recording a Return</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Open the order detail page by clicking <strong>View →</strong> on the Orders list.</div></li>
                            <li><span class="step-num">2</span><div>Scroll to the <strong>Returns</strong> card in the right column and click <strong>Record Return</strong>.</div></li>
                            <li><span class="step-num">3</span><div>Select the <strong>Product</strong> from the dropdown — only products from this order are shown, along with how many units are still returnable.</div></li>
                            <li><span class="step-num">4</span><div>Enter the <strong>Quantity</strong> being returned. The system will suggest a refund amount based on the unit price, which you can adjust.</div></li>
                            <li><span class="step-num">5</span><div>Add an optional <strong>Reason</strong> (e.g. "Customer received damaged item").</div></li>
                            <li><span class="step-num">6</span><div>Enter the <strong>Refund Amount</strong>. Set to 0 if no refund is being issued.</div></li>
                            <li><span class="step-num">7</span><div>Click <strong>Record Return</strong>. Stock is restored immediately.</div></li>
                        </ol>
                        <div class="callout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            The refund amount is recorded for reference only. It does not automatically reverse any payment entries in the system, as refunds are typically processed externally via GCash or bank transfer.
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Returns and Archiving</div>
                        <div class="topic-body">
                            <p>If a return is recorded before an order is archived, the system is smart enough to account for it. When archiving, only the <em>net</em> quantity (ordered minus returned) is deducted from stock — so returned units are never double-counted.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Stock Adjustments (NEW) ══ --}}
            <div class="help-section collapsed" id="section-stock-adjustments" data-section="stock-adjustments">
                <div class="section-head" onclick="toggleSection('stock-adjustments')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Stock Adjustments <span class="new-badge">New</span></div>
                        <div class="section-subtitle">Recording inventory losses for damaged, lost, or expired items</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">What Is a Stock Adjustment?</div>
                        <div class="topic-body">
                            <p>A stock adjustment records a deduction from inventory that is not caused by a sale. This covers situations like a soap that cracked during storage, items that went missing during a stock count, products that expired before being sold, or a correction to fix a previous counting error.</p>
                            <p>When an adjustment is recorded, the product's quantity is reduced immediately. If stock reaches zero, the product is automatically marked Unavailable.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Recording a Stock Adjustment</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Go to <strong>Products & Stocks</strong> in the sidebar, then click the <strong>Stocks</strong> button.</div></li>
                            <li><span class="step-num">2</span><div>Click <strong>Adjust Stock</strong> (the amber button beside + Stock In).</div></li>
                            <li><span class="step-num">3</span><div>Select the <strong>Product</strong> to adjust. A live preview will show current stock and the quantity after the deduction.</div></li>
                            <li><span class="step-num">4</span><div>Select the <strong>Reason</strong>: Damaged, Lost, Expired, or Inventory Correction.</div></li>
                            <li><span class="step-num">5</span><div>Enter the <strong>Quantity</strong> to deduct.</div></li>
                            <li><span class="step-num">6</span><div>Add optional <strong>Notes</strong> to describe what happened (e.g. "Soap cracked during storage").</div></li>
                            <li><span class="step-num">7</span><div>Set the <strong>Adjustment Date</strong> and click <strong>Record Adjustment</strong>.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Viewing Adjustment History</div>
                        <div class="topic-body">
                            <p>All recorded adjustments are visible in the <strong>Adjustments tab</strong> on the Stocks page, alongside the Stock-In History tab. Each record shows the date, product, quantity removed, reason (color-coded by type), any notes, and the employee who recorded it.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Adjustment Reasons Explained</div>
                        <ol class="steps">
                            <li><span class="step-num" style="background:#c0392b;">D</span><div><strong>Damaged</strong> — items that are broken or otherwise unusable, e.g. a bottle that cracked.</div></li>
                            <li><span class="step-num" style="background:#856404;">L</span><div><strong>Lost</strong> — items that could not be located during a physical stock count.</div></li>
                            <li><span class="step-num" style="background:#6a3aad;">E</span><div><strong>Expired</strong> — items that have passed their usable date and need to be disposed of.</div></li>
                            <li><span class="step-num" style="background:#1565c0;">C</span><div><strong>Inventory Correction</strong> — a manual fix for a discrepancy found during auditing, e.g. a stock-in was previously recorded with the wrong quantity.</div></li>
                        </ol>
                        <div class="callout callout-warn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            Stock adjustments cannot be undone. If you record an incorrect adjustment, use an Inventory Correction entry to add the units back.
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Business Reports ══ --}}
            <div class="help-section collapsed" id="section-reports" data-section="reports">
                <div class="section-head" onclick="toggleSection('reports')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Business Reports</div>
                        <div class="section-subtitle">Generating PDF reports and viewing inventory health</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Summary Cards</div>
                        <div class="topic-body">
                            <p>At the top of the Reports page, six summary cards give a quick overview of the current year. The first three cover revenue, fulfilled orders, and total customers. The two new cards show <strong>Units Returned</strong> (with total refund amount) and <strong>Units Adjusted Out</strong> (damaged, lost, or expired). A sixth card shows the combined inventory loss.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Available PDF Reports</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div><strong>Monthly Sales Report</strong> — a detailed breakdown of every fulfilled order in a selected month.</div></li>
                            <li><span class="step-num">2</span><div><strong>Bestselling Products</strong> — products ranked by units sold for a selected year.</div></li>
                            <li><span class="step-num">3</span><div><strong>Frequent Customers</strong> — customers ranked by order count for a selected year.</div></li>
                            <li><span class="step-num">4</span><div><strong>Annual Summary</strong> — full-year overview combining monthly revenue, top products, and top customers.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Inventory Health Section</div>
                        <div class="topic-body">
                            <p>Below the monthly table, the <strong>Inventory Health</strong> section shows two live panels — a breakdown of stock adjustments by reason (Damaged, Lost, Expired, Correction), and a table of the 5 most recent returns for the year. Each return links directly to its order detail page.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Exporting a Report</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Go to <strong>Business Reports</strong> in the sidebar.</div></li>
                            <li><span class="step-num">2</span><div>Find the report card you want. Use the year and month dropdowns to select the period.</div></li>
                            <li><span class="step-num">3</span><div>Click <strong>Export PDF</strong>. The file will download automatically.</div></li>
                        </ol>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Settings ══ --}}
            <div class="help-section collapsed" id="section-settings" data-section="settings">
                <div class="section-head" onclick="toggleSection('settings')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Settings</div>
                        <div class="section-subtitle">System preferences and accessibility options</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">System Panel</div>
                        <div class="topic-body">
                            <p>Contains three groups of settings. After making changes, click <strong>Save Changes</strong> at the bottom of the panel.</p>
                        </div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div><strong>Time and Language</strong> — set the system Timezone and display Language.</div></li>
                            <li><span class="step-num">2</span><div><strong>Sound and Display</strong> — toggle Alert Notifications and Confirmation Messages on or off.</div></li>
                            <li><span class="step-num">3</span><div><strong>Data and Storage</strong> — shows the connected database name and lets you toggle Auto Backup.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Accessibility Panel</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div><strong>Dark Mode</strong> — toggles the entire interface to a dark theme. The change is previewed live before saving.</div></li>
                            <li><span class="step-num">2</span><div><strong>Color Correction</strong> — enable this toggle to reveal a dropdown for choosing a color filter (Protanopia, Deuteranopia, or Tritanopia).</div></li>
                            <li><span class="step-num">3</span><div><strong>System Sound</strong> — toggles UI sound effects on or off.</div></li>
                        </ol>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION: Employees (Owner only) ══ --}}
            @if (Auth::guard('employee')->user()?->isOwner())
            <div class="help-section collapsed" id="section-employees" data-section="employees">
                <div class="section-head" onclick="toggleSection('employees')">
                    <div class="section-head-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    </div>
                    <div class="section-head-text">
                        <div class="section-title">Employees <span class="owner-badge">Owner Only</span></div>
                        <div class="section-subtitle">Creating and managing staff accounts</div>
                    </div>
                    <svg class="section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="section-body">

                    <div class="topic">
                        <div class="topic-title">Creating a Staff Account</div>
                        <ol class="steps">
                            <li><span class="step-num">1</span><div>Go to <strong>Employees</strong> in the sidebar (visible to owners only).</div></li>
                            <li><span class="step-num">2</span><div>Click <strong>+ Add Employee</strong>.</div></li>
                            <li><span class="step-num">3</span><div>Fill in the employee's personal information: First Name, Last Name, Position, and Contact Number.</div></li>
                            <li><span class="step-num">4</span><div>Set their <strong>Username</strong> (must be unique) and <strong>Role</strong> — Staff for regular employees, Owner for full-access accounts.</div></li>
                            <li><span class="step-num">5</span><div>Enter and confirm a <strong>Password</strong> (minimum 8 characters).</div></li>
                            <li><span class="step-num">6</span><div>Click <strong>Create Account</strong>. The employee can now log in with those credentials.</div></li>
                        </ol>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Editing an Employee</div>
                        <div class="topic-body">
                            <p>Click the <strong>pencil icon</strong> next to any employee. You can update their name, position, contact number, username, and role. Leave the password fields blank to keep their current password, or enter a new password to change it.</p>
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Deleting an Employee</div>
                        <div class="topic-body">
                            <p>Click the <strong>trash icon</strong> to delete an account. A confirmation dialog will appear. You cannot delete your own account — the trash icon is hidden on your own row.</p>
                        </div>
                        <div class="callout callout-warn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            Deleting an employee account is permanent. Ensure all their recorded activities are accounted for before removing an account.
                        </div>
                    </div>

                    <hr class="topic-divider">

                    <div class="topic">
                        <div class="topic-title">Your Own Row</div>
                        <div class="topic-body">
                            <p>Your own account is marked with a <strong>"You"</strong> tag in the Name column. You can edit your own details but cannot delete your own account.</p>
                        </div>
                    </div>

                </div>
            </div>
            @endif

        </div>{{-- /help-content --}}
    </div>{{-- /help-wrap --}}

@endsection

@push('scripts')
<script>
(function () {

    function toggleSection(id) {
        const el = document.getElementById('section-' + id);
        if (!el) return;
        el.classList.toggle('collapsed');
        updateTocActive(id);
    }

    window.toggleSection = toggleSection;

    window.scrollTo = function (id) {
        const el = document.getElementById('section-' + id);
        if (!el) return;
        el.classList.remove('collapsed');
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        updateTocActive(id);
    };

    function updateTocActive(id) {
        document.querySelectorAll('.toc-item').forEach(item => {
            item.classList.toggle('active', item.dataset.section === id);
        });
    }

    const searchInput = document.getElementById('helpSearch');
    const searchClear = document.getElementById('searchClear');
    const searchCount = document.getElementById('searchCount');
    const noResults   = document.getElementById('noResults');
    const sections    = document.querySelectorAll('.help-section');
    const tocItems    = document.querySelectorAll('.toc-item');

    const originals = {};
    sections.forEach(sec => {
        originals[sec.id] = sec.querySelector('.section-body').innerHTML;
    });

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        searchClear.classList.toggle('visible', query.length > 0);
        performSearch(query);
    });

    function performSearch(query) {
        sections.forEach(sec => {
            sec.querySelector('.section-body').innerHTML = originals[sec.id];
        });

        if (!query) {
            sections.forEach(sec => sec.classList.remove('no-match'));
            tocItems.forEach(t => t.classList.remove('hidden-section'));
            noResults.style.display = 'none';
            searchCount.textContent = '';
            return;
        }

        const lower = query.toLowerCase();
        let matchCount = 0;

        sections.forEach(sec => {
            const body      = sec.querySelector('.section-body');
            const text      = body.innerText.toLowerCase();
            const titleEl   = sec.querySelector('.section-title');
            const titleText = titleEl ? titleEl.innerText.toLowerCase() : '';

            if (text.includes(lower) || titleText.includes(lower)) {
                sec.classList.remove('no-match');
                sec.classList.remove('collapsed');
                highlightText(body, query);
                matchCount++;
                const sectionId = sec.dataset.section;
                tocItems.forEach(t => {
                    if (t.dataset.section === sectionId) t.classList.remove('hidden-section');
                });
            } else {
                sec.classList.add('no-match');
                const sectionId = sec.dataset.section;
                tocItems.forEach(t => {
                    if (t.dataset.section === sectionId) t.classList.add('hidden-section');
                });
            }
        });

        noResults.style.display = matchCount === 0 ? 'block' : 'none';
        searchCount.textContent = matchCount > 0
            ? matchCount + ' section' + (matchCount > 1 ? 's' : '') + ' matched'
            : '';
    }

    function highlightText(container, query) {
        const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                const parent = node.parentNode;
                if (!parent) return NodeFilter.FILTER_REJECT;
                const tag = parent.tagName;
                if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'MARK') return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });

        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);

        const lower = query.toLowerCase();

        nodes.forEach(node => {
            const val = node.nodeValue;
            const idx = val.toLowerCase().indexOf(lower);
            if (idx === -1) return;
            const before = document.createTextNode(val.substring(0, idx));
            const mark   = document.createElement('mark');
            mark.textContent = val.substring(idx, idx + query.length);
            const after  = document.createTextNode(val.substring(idx + query.length));
            const parent = node.parentNode;
            parent.insertBefore(before, node);
            parent.insertBefore(mark, node);
            parent.insertBefore(after, node);
            parent.removeChild(node);
        });
    }

    window.clearSearch = function () {
        searchInput.value = '';
        searchClear.classList.remove('visible');
        performSearch('');
        searchInput.focus();
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.dataset.section;
                tocItems.forEach(t => {
                    t.classList.toggle('active', t.dataset.section === id);
                });
            }
        });
    }, { threshold: 0.3 });

    sections.forEach(sec => observer.observe(sec));

})();
</script>
@endpush
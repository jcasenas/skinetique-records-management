<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKINETIQUE — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Varela+Round&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-bg:    #5e2039;
            --sidebar-hover: #7b2d4e;
            --sidebar-w:     200px;
            --content-bg:    #faf5f7;
            --primary:       #7b2d4e;
            --text:          #3a1a28;
            --muted:         #9e7286;
            --white:         #ffffff;
            --radius:        14px;
            --shadow:        0 4px 20px rgba(94,32,57,.10);

            --card-bg:      #ffffff;
            --card-border:  #f0e6ec;
            --input-bg:     #ffffff;
            --input-border: #e8d5dd;
            --table-hover:  #fdf5f8;
            --table-stripe: #f9f3f6;
        }

        /* ══════════════════════════════════════════════════════════
           GLOBAL DARK MODE
           <body class="dark-mode"> is set server-side from the cache.
           The cache is busted by SettingsController on every save so
           the change takes effect on the very next page load.
        ══════════════════════════════════════════════════════════ */
        body.dark-mode {
            --content-bg:   #12080e;
            --text:         #e8d5dd;
            --muted:        #9e7286;
            --card-bg:      #1e0f18;
            --card-border:  #3d1f2c;
            --input-bg:     #2a1520;
            --input-border: #3d1f2c;
            --table-hover:  #2a1520;
            --table-stripe: #1e0f18;
            --shadow:       0 4px 20px rgba(0,0,0,.35);
        }

        body.dark-mode { background: var(--content-bg); color: var(--text); }
        body.dark-mode .page-title { color: var(--text); }

        body.dark-mode .table-wrap,
        body.dark-mode .detail-card,
        body.dark-mode .activity-card,
        body.dark-mode .preview-card,
        body.dark-mode .monthly-table-wrap,
        body.dark-mode .report-card,
        body.dark-mode .summary-card,
        body.dark-mode .stock-stat,
        body.dark-mode .help-toc,
        body.dark-mode .help-section,
        body.dark-mode .shortcut-card,
        body.dark-mode .modal { background: var(--card-bg); }

        body.dark-mode thead th                { color: var(--muted); border-bottom-color: var(--card-border); }
        body.dark-mode tbody td                { border-bottom-color: var(--card-border); color: var(--text); }
        body.dark-mode tbody tr:hover          { background: var(--table-hover); }
        body.dark-mode tbody tr:nth-child(even){ background: var(--table-stripe); }

        body.dark-mode input,
        body.dark-mode select,
        body.dark-mode textarea,
        body.dark-mode .form-group input,
        body.dark-mode .form-group select,
        body.dark-mode .cart-box { background: var(--input-bg); border-color: var(--input-border); color: var(--text); }

        body.dark-mode input::placeholder,
        body.dark-mode textarea::placeholder   { color: var(--muted); }
        body.dark-mode input[readonly],
        body.dark-mode input:disabled          { background: #12080e; color: var(--muted); }
        body.dark-mode .search-wrap input      { background: var(--input-bg); border-color: var(--input-border); color: var(--text); }

        body.dark-mode .flash       { background: #0f2a14; border-left-color: #4caf50; color: #81c784; }
        body.dark-mode .flash-error { background: #2a0f18; border-left-color: var(--primary); color: #e8a0b4; }

        body.dark-mode .badge-pending     { background: #3a2e00; color: #ffd54f; }
        body.dark-mode .badge-partial     { background: #0a1e3a; color: #90caf9; }
        body.dark-mode .badge-paid        { background: #0a2a18; color: #81c784; }
        body.dark-mode .badge-available   { background: #0a2a18; color: #81c784; }
        body.dark-mode .badge-unavailable { background: #2a1520; color: var(--muted); }
        body.dark-mode .badge-archived    { background: #1e1e1e; color: #aaa; }

        body.dark-mode .kpi-card,
        body.dark-mode .stat-card,
        body.dark-mode .summary-card       { background: #2a1020; }
        body.dark-mode .kpi-label,
        body.dark-mode .stat-label,
        body.dark-mode .summary-card-label { color: var(--muted); }

        body.dark-mode .card-header,
        body.dark-mode .activity-header,
        body.dark-mode .preview-card-header,
        body.dark-mode .monthly-table-header { border-bottom-color: var(--card-border); }
        body.dark-mode .card-title,
        body.dark-mode .activity-title,
        body.dark-mode .preview-card-header,
        body.dark-mode .monthly-table-title  { color: var(--text); }

        body.dark-mode .info-row              { border-bottom-color: var(--card-border); }
        body.dark-mode .info-label            { color: var(--muted); }
        body.dark-mode .info-value            { color: var(--text); }
        body.dark-mode .total-row             { color: var(--muted); }
        body.dark-mode .total-row.grand       { color: var(--text); border-top-color: var(--card-border); }
        body.dark-mode .totals-block          { border-top-color: var(--card-border); }

        body.dark-mode .balance-bar-bg        { background: #3d1f2c; }
        body.dark-mode .balance-bar-fill      { background: var(--primary); }
        body.dark-mode .balance-text          { color: var(--muted); }

        body.dark-mode .activity-item         { border-bottom-color: var(--card-border); }
        body.dark-mode .activity-item:hover   { background: var(--table-hover); }
        body.dark-mode .activity-label        { color: var(--text); }
        body.dark-mode .activity-detail       { color: var(--muted); }
        body.dark-mode .activity-time         { color: var(--muted); }

        body.dark-mode .shortcut-card         { background: var(--card-bg); border-color: var(--card-border); color: var(--text); }
        body.dark-mode .shortcut-icon         { background: #2a1520; }
        body.dark-mode .shortcut-label        { color: var(--text); }

        body.dark-mode .btn-secondary         { background: #2a1520; color: #e8a0b4; }
        body.dark-mode .btn-secondary:hover   { background: #3d1f2c; }
        body.dark-mode .btn-ghost             { color: var(--muted); }
        body.dark-mode .btn-ghost:hover       { background: #2a1520; color: var(--text); }

        body.dark-mode .tab-bar               { border-bottom-color: var(--card-border); }
        body.dark-mode .tab-link              { color: var(--muted); }
        body.dark-mode .tab-link.active       { color: #e8a0b4; border-bottom-color: #e8a0b4; }
        body.dark-mode .tab-link:hover        { color: var(--text); }

        body.dark-mode .pagination a          { color: var(--text); }
        body.dark-mode .pagination a:hover    { color: #e8a0b4; }
        body.dark-mode .pagination .disabled  { color: #3d1f2c; }

        body.dark-mode .modal-backdrop        { background: rgba(0,0,0,.65); }
        body.dark-mode .modal                 { background: var(--card-bg); box-shadow: 0 8px 48px rgba(0,0,0,.5); }
        body.dark-mode .modal-actions         { border-top-color: var(--card-border); }
        body.dark-mode .modal-title           { color: var(--text); }
        body.dark-mode .modal-subtitle        { color: var(--muted); }

        body.dark-mode .cart-item             { background: #2a1520; border-color: var(--card-border); }
        body.dark-mode .cart-item-name        { color: var(--text); }
        body.dark-mode .cart-item-meta        { color: var(--muted); }
        body.dark-mode .cart-empty            { background: #1e0f18; border-color: var(--card-border); color: var(--muted); }
        body.dark-mode .cart-totals           { color: var(--muted); }
        body.dark-mode .cart-totals .grand    { color: #e8a0b4; border-top-color: var(--card-border); }

        body.dark-mode .payment-item          { border-bottom-color: var(--card-border); }
        body.dark-mode .payment-method        { color: var(--text); }
        body.dark-mode .payment-date          { color: var(--muted); }
        body.dark-mode .method-tag            { background: #2a1520; color: var(--muted); }

        body.dark-mode .btn-receipt           { background: #2a1520; color: #e8a0b4; }
        body.dark-mode .btn-receipt:hover     { background: #3d1f2c; }
        body.dark-mode .btn-receipt svg       { stroke: #e8a0b4; }

        body.dark-mode .section-heading::after { background: var(--card-border); }

        body.dark-mode .report-card           { background: var(--card-bg); }
        body.dark-mode .report-icon           { background: #2a1520; }
        body.dark-mode .report-card-title     { color: var(--text); }
        body.dark-mode .report-card-desc      { color: var(--muted); }
        body.dark-mode .filter-select         { background: var(--input-bg); border-color: var(--input-border); color: var(--text); }

        body.dark-mode .toc-header            { border-bottom-color: var(--card-border); color: var(--muted); }
        body.dark-mode .toc-item              { color: var(--text); border-left-color: transparent; }
        body.dark-mode .toc-item:hover        { background: var(--table-hover); color: #e8a0b4; }
        body.dark-mode .toc-item.active       { background: #2a1520; color: #e8a0b4; border-left-color: #e8a0b4; }
        body.dark-mode .section-head          { border-bottom-color: var(--card-border); }
        body.dark-mode .section-head:hover    { background: var(--table-hover); }
        body.dark-mode .section-title         { color: var(--text); }
        body.dark-mode .section-subtitle      { color: var(--muted); }
        body.dark-mode .topic-title           { color: #e8a0b4; }
        body.dark-mode .topic-body            { color: var(--text); }
        body.dark-mode .topic-divider         { background: var(--card-border); }
        body.dark-mode .callout               { background: #2a1520; color: #e8a0b4; }
        body.dark-mode .callout-warn          { background: #2a1e00; color: #ffd54f; }
        body.dark-mode .section-chevron       { stroke: var(--muted); }

        body.dark-mode .archived-banner         { background: #1e1e1e; border-color: #333; color: #aaa; }
        body.dark-mode .archived-banner-icon    { background: #2a2a2a; }
        body.dark-mode .archived-banner strong  { color: #ccc; }

        body.dark-mode .pending-banner          { background: #2a1e00; border-color: #5a3a00; }
        body.dark-mode .pending-banner-text     { color: #ffd54f; }
        body.dark-mode .pending-banner-sub      { color: #c8a000; }
        body.dark-mode .pending-banner-icon     { background: #5a3a00; }

        body.dark-mode .stock-preview           { background: #2a1520; color: #e8a0b4; }

        body.dark-mode .empty-row td,
        body.dark-mode .empty-state,
        body.dark-mode .activity-empty         { color: var(--muted); }

        body.dark-mode .qty-pill               { background: #0a2a18; color: #81c784; }

        body.dark-mode .stock-stat             { background: var(--card-bg); border-left-color: var(--primary); }
        body.dark-mode .stock-stat-label       { color: var(--muted); }
        body.dark-mode .stock-stat-value       { color: #e8a0b4; }

        body.dark-mode .btn-edit               { background: #0a1e3a; color: #90caf9; }
        body.dark-mode .btn-delete             { background: #2a0f18; color: #e8a0b4; }

        body.dark-mode .notif-badge            { background: #2a1520; color: #e8a0b4; }
        body.dark-mode .notif-dot              { background: #e8a0b4; }

        body.dark-mode ::-webkit-scrollbar       { width: 6px; height: 6px; }
        body.dark-mode ::-webkit-scrollbar-track { background: #1e0f18; }
        body.dark-mode ::-webkit-scrollbar-thumb { background: #3d1f2c; border-radius: 99px; }

        /* ══════════════════════════════════════════════════════════
           BASE STYLES
        ══════════════════════════════════════════════════════════ */
        html, body { height: 100%; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--content-bg);
            color: var(--text);
            line-height: 1.6;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            font-family: 'Varela Round', sans-serif;
            font-size: 19.5px;
            font-weight: 400;
            letter-spacing: 0.10em;
            color: var(--white);
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255,255,255,.12);
        }

        .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            color: rgba(255,255,255,.85);
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 500;
            transition: all .15s ease;
        }

        .nav-item:hover  { background: var(--sidebar-hover); color: var(--white); }
        .nav-item.active { background: rgba(255,255,255,.16); color: var(--white); font-weight: 600; }
        .nav-icon        { width: 22px; height: 22px; flex-shrink: 0; }

        .sidebar-footer  { border-top: 1px solid rgba(255,255,255,.12); padding: 16px 0 20px; }
        .sidebar-avatar  { display: flex; align-items: center; gap: 12px; padding: 10px 20px 4px; }

        .avatar-circle {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,.18);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .avatar-circle svg { width: 20px; height: 20px; color: var(--white); }

        .avatar-actions { display: flex; flex-direction: column; gap: 2px; }

        .avatar-actions a,
        .avatar-actions button {
            background: none; border: none;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px; color: rgba(255,255,255,.78);
            cursor: pointer; text-align: left;
            padding: 4px 8px; border-radius: 6px;
            text-decoration: none; transition: all .15s;
        }

        .avatar-actions a:hover,
        .avatar-actions button:hover { color: var(--white); background: rgba(255,255,255,.12); }

        .main       { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; padding: 40px; }
        .page-title { font-size: 28px; font-weight: 700; color: var(--text); margin-bottom: 32px; letter-spacing: -0.02em; }

        .stat-grid  { display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 52px; }

        .stat-card {
            background: #f8e1eb; border-radius: var(--radius);
            padding: 32px 28px; min-width: 260px; flex: 1;
            box-shadow: var(--shadow); transition: transform 0.2s ease;
        }

        .stat-card:hover { transform: translateY(-4px); }
        .stat-value      { font-size: 42px; font-weight: 700; color: #7b2d4e; line-height: 1.05; margin-bottom: 8px; }
        .stat-label      { font-size: 15px; color: #9e7286; font-weight: 500; }

        .section-title { font-size: 26px; font-weight: 700; margin-bottom: 28px; }
        .shortcut-grid { display: flex; gap: 32px; flex-wrap: wrap; }

        .shortcut-card {
            display: flex; flex-direction: column; align-items: center;
            gap: 16px; text-decoration: none; color: var(--text);
            min-width: 160px; padding: 12px; transition: transform 0.25s ease;
        }

        .shortcut-card:hover { transform: translateY(-6px); }

        .shortcut-icon {
            width: 92px; height: 92px;
            display: flex; align-items: center; justify-content: center;
            background: white; border-radius: 50%; box-shadow: var(--shadow);
        }

        .shortcut-icon svg { width: 42px; height: 42px; stroke: var(--primary); stroke-width: 1.8; }
        .shortcut-label    { font-size: 15.5px; font-weight: 600; text-align: center; line-height: 1.3; }

        @yield('extra-styles')
    </style>
    @stack('styles')
</head>
{{--
    dark_mode is cached for up to 1 hour.
    SettingsController calls Cache::forget('setting.dark_mode') on every
    accessibility save, so toggling dark mode takes effect immediately on the
    next page load — with zero DB cost on every subsequent load.
--}}
@php
    $darkMode = \Illuminate\Support\Facades\Cache::remember('setting.dark_mode', 3600, function () {
        return \App\Models\SystemSetting::where('key', 'dark_mode')->value('value');
    }) === '1';
@endphp
<body class="{{ $darkMode ? 'dark-mode' : '' }}">

<aside class="sidebar">
    <div class="sidebar-brand">SKINETIQUE</div>

    <nav class="sidebar-nav">

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Home
        </a>

        <a href="{{ route('admin.customers.index') }}"
           class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Customers
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                <polyline points="2 17 12 22 22 17"/>
                <polyline points="2 12 12 17 22 12"/>
            </svg>
            Products & Stocks
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            Orders
        </a>

        <a href="{{ route('admin.payments.index') }}"
           class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="6" width="20" height="12" rx="2"/>
                <circle cx="12" cy="12" r="3"/>
                <path d="M6 12h.01M18 12h.01"/>
            </svg>
            Payment
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6"  y1="20" x2="6"  y2="14"/>
            </svg>
            Business Reports
        </a>

        <a href="{{ route('admin.settings.index') }}"
           class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Settings
        </a>

        @if (Auth::guard('employee')->user()?->isOwner())
        <a href="{{ route('admin.employees.index') }}"
           class="nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
            Employees
        </a>
        @endif

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-avatar">
            <div class="avatar-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="avatar-actions">
                <a href="{{ route('admin.help.index') }}">Help</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</aside>

<main class="main">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
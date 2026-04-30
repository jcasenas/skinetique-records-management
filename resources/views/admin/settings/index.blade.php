@extends('admin.layout')

@section('title', 'Settings')

@push('styles')
<style>
    /* ── Dark mode class applied to <body> ── */
    body.dark-mode {
        --content-bg: #1a0f14;
        --text:        #e8d5dd;
        --muted:       #9e7286;
        --white:       #ffffff;
        background: var(--content-bg);
        color: var(--text);
    }

    body.dark-mode .settings-panel-list { background: #2a1520; border-color: #3d1f2c; }
    body.dark-mode .panel-item          { border-color: #3d1f2c; }
    body.dark-mode .panel-item:hover    { background: #3a1f2c; }
    body.dark-mode .panel-item.active   { background: var(--primary); }
    body.dark-mode .settings-content    { background: #1a0f14; border-left-color: #3d1f2c; }
    body.dark-mode .setting-row         { border-color: #3d1f2c; }
    body.dark-mode .setting-section-title { color: #e8d5dd; }
    body.dark-mode .setting-label       { color: #c4a0b0; }
    body.dark-mode select.setting-select { background: #2a1520; color: #e8d5dd; border-color: #3d1f2c; }

    /* ── Layout ── */
    .settings-wrap {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 0;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(94,32,57,.08);
        overflow: hidden;
        min-height: 480px;
    }

    body.dark-mode .settings-wrap { background: #2a1520; }

    /* ── Left: panel list ── */
    .settings-panel-list {
        background: #fff;
        border-right: 1.5px solid #f0e6ec;
        padding: 12px 0;
    }

    .panel-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 22px;
        cursor: pointer;
        border-bottom: 1px solid #f5edf1;
        transition: background .15s;
        text-decoration: none;
    }

    .panel-item:last-child { border-bottom: none; }
    .panel-item:hover { background: #fdf5f8; }

    .panel-item.active { background: var(--primary); }

    .panel-item.active .panel-item-title,
    .panel-item.active .panel-item-sub,
    .panel-item.active svg { color: #fff !important; stroke: #fff !important; }

    .panel-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .panel-item.active .panel-icon { background: rgba(255,255,255,.18); }
    .panel-item:not(.active) .panel-icon { background: #f5e6ed; }

    .panel-icon svg { width: 20px; height: 20px; stroke: var(--primary); }

    .panel-item-text { display: flex; flex-direction: column; gap: 2px; }
    .panel-item-title { font-size: 14.5px; font-weight: 600; color: var(--text); line-height: 1.2; }
    .panel-item-sub   { font-size: 12px; color: var(--muted); }

    /* ── Right: content ── */
    .settings-content {
        padding: 32px 36px;
        background: #faf5f7;
        border-left: 1.5px solid #f0e6ec;
    }

    body.dark-mode .settings-content { background: #1a0f14; }

    .setting-section { margin-bottom: 36px; }
    .setting-section:last-child { margin-bottom: 0; }

    .setting-section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0e6ec;
    }

    body.dark-mode .setting-section-title { border-color: #3d1f2c; }

    .setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f5edf1;
        gap: 20px;
    }

    .setting-row:last-child { border-bottom: none; }

    .setting-label { font-size: 14px; color: var(--text); font-weight: 500; }

    /* ── Select dropdown ── */
    select.setting-select {
        padding: 7px 28px 7px 12px;
        border: 1.5px solid #e8d5dd;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        color: var(--text);
        background: #fff;
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237b2d4e' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        min-width: 140px;
        transition: border-color .2s;
    }

    select.setting-select:focus { border-color: var(--primary); }

    /* ── Toggle switch ── */
    .toggle-wrap { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

    .toggle {
        position: relative;
        width: 44px; height: 24px;
        cursor: pointer;
        flex-shrink: 0;
    }

    .toggle input { opacity: 0; width: 0; height: 0; position: absolute; }

    .toggle-track {
        position: absolute;
        inset: 0;
        border-radius: 99px;
        background: #e0ccd6;
        transition: background .2s;
    }

    .toggle input:checked ~ .toggle-track { background: var(--primary); }

    .toggle-thumb {
        position: absolute;
        top: 3px; left: 3px;
        width: 18px; height: 18px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.18);
        transition: left .2s;
    }

    .toggle input:checked ~ .toggle-thumb { left: 23px; }

    /* Flash */
    .flash       { background: #f0faf0; border-left: 3px solid #4caf50; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #2e7d32; margin-bottom: 20px; }
    .flash-error { background: #fce8ef; border-left-color: var(--primary); color: var(--primary); }

    /* Save button row */
    .save-row { display: flex; justify-content: flex-end; margin-top: 28px; }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        border: none;
        transition: opacity .15s, transform .1s;
    }

    .btn:active { transform: scale(.97); }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { opacity: .88; }

    /* Color correction select shown beside toggle */
    .color-row-right { display: flex; align-items: center; gap: 10px; }

    /* Owner-only badge on panel item */
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
        margin-left: 4px;
    }

    .panel-item.active .owner-badge {
        background: rgba(255,255,255,.2);
        color: #fff;
    }

    /* Staff notice inside content area */
    .staff-notice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #fff8e1;
        border: 1.5px solid #fde5c3;
        border-radius: 12px;
        padding: 16px 20px;
        font-size: 13px;
        color: #7a4d1e;
        line-height: 1.6;
    }

    .staff-notice svg { width: 18px; height: 18px; stroke: #c47a1e; flex-shrink: 0; margin-top: 1px; }
</style>
@endpush

@section('content')

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <h1 class="page-title">Settings</h1>

    @php
        $activePanel = request('panel', 'system');
        $isOwner     = Auth::guard('employee')->user()?->isOwner();

        // Staff trying to access system panel — redirect them to accessibility
        if (! $isOwner && $activePanel === 'system') {
            $activePanel = 'accessibility';
        }
    @endphp

    <div class="settings-wrap">

        {{-- ── Left: Panel list ── --}}
        <div class="settings-panel-list">

            {{-- System panel tab: only shown to owners --}}
            @if ($isOwner)
                <a href="{{ route('admin.settings.index', ['panel' => 'system']) }}"
                   class="panel-item {{ $activePanel === 'system' ? 'active' : '' }}">
                    <div class="panel-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2"  y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </div>
                    <div class="panel-item-text">
                        <span class="panel-item-title">
                            System
                            <span class="owner-badge">Owner</span>
                        </span>
                        <span class="panel-item-sub">Change system settings</span>
                    </div>
                </a>
            @endif

            <a href="{{ route('admin.settings.index', ['panel' => 'accessibility']) }}"
               class="panel-item {{ $activePanel === 'accessibility' ? 'active' : '' }}">
                <div class="panel-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="5" r="1.5"/>
                        <path d="M12 7v8"/>
                        <path d="M8 21l4-6 4 6"/>
                        <path d="M7 10h10"/>
                    </svg>
                </div>
                <div class="panel-item-text">
                    <span class="panel-item-title">Accessibility</span>
                    <span class="panel-item-sub">Change accessibility settings</span>
                </div>
            </a>

        </div>

        {{-- ── Right: Content ── --}}
        <div class="settings-content">

            {{-- ════════ SYSTEM PANEL ════════ --}}
            @if ($activePanel === 'system' && $isOwner)

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="panel" value="system">

                    <div class="setting-section">
                        <div class="setting-section-title">Time and Language</div>

                        <div class="setting-row">
                            <span class="setting-label">Timezone</span>
                            <select name="timezone" class="setting-select">
                                @foreach ([
                                    'Asia/Manila'     => 'UTC+08:00 — Manila',
                                    'UTC'             => 'UTC+00:00 — UTC',
                                    'Asia/Singapore'  => 'UTC+08:00 — Singapore',
                                    'Asia/Tokyo'      => 'UTC+09:00 — Tokyo',
                                    'America/New_York' => 'UTC-05:00 — New York',
                                ] as $tz => $label)
                                    <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'Asia/Manila') === $tz ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="setting-row">
                            <span class="setting-label">Language</span>
                            <select name="language" class="setting-select">
                                <option value="en"  {{ ($settings['language'] ?? 'en') === 'en'  ? 'selected' : '' }}>English</option>
                                <option value="fil" {{ ($settings['language'] ?? 'en') === 'fil' ? 'selected' : '' }}>Filipino</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-section">
                        <div class="setting-section-title">Sound and Display</div>

                        <div class="setting-row">
                            <span class="setting-label">Alert Notification</span>
                            <label class="toggle">
                                <input type="checkbox" name="alert_notification" value="1"
                                    {{ ($settings['alert_notification'] ?? '1') === '1' ? 'checked' : '' }}>
                                <span class="toggle-track"></span>
                                <span class="toggle-thumb"></span>
                            </label>
                        </div>

                        <div class="setting-row">
                            <span class="setting-label">Confirmation Messages</span>
                            <label class="toggle">
                                <input type="checkbox" name="confirmation_messages" value="1"
                                    {{ ($settings['confirmation_messages'] ?? '1') === '1' ? 'checked' : '' }}>
                                <span class="toggle-track"></span>
                                <span class="toggle-thumb"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-section">
                        <div class="setting-section-title">Data and Storage</div>

                        <div class="setting-row">
                            <span class="setting-label">Database</span>
                            <select class="setting-select" disabled>
                                <option>{{ config('database.connections.' . config('database.default') . '.database', 'skinetique') }}</option>
                            </select>
                        </div>

                        <div class="setting-row">
                            <span class="setting-label">Auto Backup</span>
                            <label class="toggle">
                                <input type="checkbox" name="auto_backup" value="1"
                                    {{ ($settings['auto_backup'] ?? '0') === '1' ? 'checked' : '' }}>
                                <span class="toggle-track"></span>
                                <span class="toggle-thumb"></span>
                            </label>
                        </div>
                    </div>

                    <div class="save-row">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>

            @endif

            {{-- ════════ ACCESSIBILITY PANEL ════════ --}}
            @if ($activePanel === 'accessibility')

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="panel" value="accessibility">

                    <div class="setting-section">
                        <div class="setting-section-title">Visual</div>

                        <div class="setting-row">
                            <span class="setting-label">Dark Mode</span>
                            <label class="toggle" id="darkModeToggle">
                                <input type="checkbox" name="dark_mode" value="1" id="darkModeInput"
                                    {{ ($settings['dark_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                <span class="toggle-track"></span>
                                <span class="toggle-thumb"></span>
                            </label>
                        </div>

                        <div class="setting-row">
                            <span class="setting-label">Color Correction</span>
                            <div class="color-row-right">
                                <label class="toggle">
                                    <input type="checkbox" id="colorCorrectionToggle"
                                        {{ ($settings['color_correction'] ?? 'none') !== 'none' ? 'checked' : '' }}
                                        onchange="toggleColorSelect(this)">
                                    <span class="toggle-track"></span>
                                    <span class="toggle-thumb"></span>
                                </label>
                                <select name="color_correction" class="setting-select" id="colorCorrectionSelect"
                                    style="{{ ($settings['color_correction'] ?? 'none') === 'none' ? 'display:none' : '' }}">
                                    <option value="protanopia"   {{ ($settings['color_correction'] ?? '') === 'protanopia'   ? 'selected' : '' }}>Protanopia</option>
                                    <option value="deuteranopia" {{ ($settings['color_correction'] ?? '') === 'deuteranopia' ? 'selected' : '' }}>Deuteranopia</option>
                                    <option value="tritanopia"   {{ ($settings['color_correction'] ?? '') === 'tritanopia'   ? 'selected' : '' }}>Tritanopia</option>
                                </select>
                                <input type="hidden" name="color_correction" value="none" id="colorCorrectionNone"
                                    {{ ($settings['color_correction'] ?? 'none') !== 'none' ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="setting-section">
                        <div class="setting-section-title">Sound</div>

                        <div class="setting-row">
                            <span class="setting-label">System Sound</span>
                            <label class="toggle">
                                <input type="checkbox" name="system_sound" value="1"
                                    {{ ($settings['system_sound'] ?? '1') === '1' ? 'checked' : '' }}>
                                <span class="toggle-track"></span>
                                <span class="toggle-thumb"></span>
                            </label>
                        </div>
                    </div>

                    <div class="save-row">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>

            @endif

        </div>{{-- /settings-content --}}
    </div>{{-- /settings-wrap --}}

@endsection

@push('scripts')
<script>
    @if (($settings['dark_mode'] ?? '0') === '1')
        document.body.classList.add('dark-mode');
    @endif

    const darkInput = document.getElementById('darkModeInput');
    if (darkInput) {
        darkInput.addEventListener('change', function () {
            document.body.classList.toggle('dark-mode', this.checked);
        });
    }

    function toggleColorSelect(checkbox) {
        const sel  = document.getElementById('colorCorrectionSelect');
        const none = document.getElementById('colorCorrectionNone');
        if (checkbox.checked) {
            sel.style.display = '';
            none.disabled = true;
        } else {
            sel.style.display = 'none';
            none.disabled = false;
        }
    }
</script>
@endpush
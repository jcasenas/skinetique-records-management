<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKINETIQUE — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:    #7b2d4e;
            --primary-dk: #5e2039;
            --input-bd:   #e8d5dd;
            --text:       #3a1a28;
            --muted:      #9e7286;
            --radius:     12px;
        }

        html, body { height: 100%; }

        body {
            display: flex;
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 55%;
            min-height: 100vh;
            background: var(--primary-dk);
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(168,77,112,.55) 0%, transparent 60%),
                radial-gradient(ellipse 60% 70% at 80% 10%, rgba(123,45,78,.70) 0%, transparent 55%),
                radial-gradient(ellipse 100% 80% at 50% 50%, #5e2039 0%, #3a0f20 100%);
            z-index: 0;
        }

        .left-blob { position: absolute; z-index: 1; pointer-events: none; }

        .left-logo {
            position: relative;
            z-index: 2;
            padding: 36px 40px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-img-wrap {
            width: 52px; height: 52px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,.25);
            flex-shrink: 0;
            background: linear-gradient(145deg, #f2d6e0, #d4849e);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img-wrap img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        .logo-wordmark {
            font-family: 'Cormorant Garamond', serif;
            font-size: 19px;
            font-weight: 600;
            letter-spacing: .22em;
            color: rgba(255,255,255,.92);
            text-transform: uppercase;
        }

        .left-center {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 56px 80px;
        }

        .left-tagline {
            font-family: 'Cormorant Garamond', serif;
            font-size: 46px;
            font-weight: 500;
            color: #fff;
            line-height: 1.18;
            margin-bottom: 22px;
            letter-spacing: -.01em;
        }

        .left-tagline em { font-style: italic; color: #f2d6e0; }

        .left-sub {
            font-size: 14px;
            color: rgba(255,255,255,.52);
            line-height: 1.75;
            max-width: 340px;
            font-weight: 300;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 45%;
            min-height: 100vh;
            background: #faf5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(123,45,78,.04) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .form-card {
            position: relative;
            width: 100%;
            max-width: 380px;
        }

        .form-heading { margin-bottom: 36px; }

        .form-heading-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -.01em;
            margin-bottom: 6px;
        }

        .form-heading-sub { font-size: 13px; color: var(--muted); font-weight: 300; }

        /* ── Alert ── */
        .alert {
            background: #fce8ef;
            border-left: 3px solid var(--primary);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 22px;
            font-size: 13px;
            color: var(--primary);
        }

        /* ── Fields ── */
        .field { position: relative; margin-bottom: 16px; }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 7px;
            display: block;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            bottom: 13px;
            width: 17px; height: 17px;
            color: var(--muted);
            pointer-events: none;
        }

        .field input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 1.5px solid var(--input-bd);
            border-radius: var(--radius);
            background: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field input::placeholder { color: #c9a8b6; }
        .field input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(123,45,78,.09); }
        .field input.is-invalid { border-color: #c0392b; }

        /* ── Eye toggle ── */
        .field-eye {
            position: absolute;
            right: 14px;
            bottom: 13px;
            width: 17px; height: 17px;
            color: var(--muted);
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color .15s;
        }

        .field-eye:hover { color: var(--primary); }
        .field-eye svg { width: 17px; height: 17px; pointer-events: none; }

        .field-has-eye input { padding-right: 42px; }

        /* ── Submit ── */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 14.5px;
            font-weight: 500;
            letter-spacing: .04em;
            cursor: pointer;
            transition: background .2s, transform .1s, box-shadow .2s;
            box-shadow: 0 4px 16px rgba(94,32,57,.22);
        }

        .btn-login:hover { background: var(--primary-dk); box-shadow: 0 6px 20px rgba(94,32,57,.30); }
        .btn-login:active { transform: scale(.985); }

        /* ── Footer note ── */
        .form-footer { margin-top: 28px; text-align: center; font-size: 11.5px; color: var(--muted); }
    </style>
</head>
<body>

<!-- ════════ LEFT PANEL ════════ -->
<div class="left-panel">

    <svg class="left-blob" style="top:-80px;right:-60px;width:420px;opacity:.12;" viewBox="0 0 400 400" fill="none">
        <circle cx="200" cy="200" r="200" fill="#f2d6e0"/>
    </svg>
    <svg class="left-blob" style="bottom:-100px;left:-80px;width:380px;opacity:.10;" viewBox="0 0 400 400" fill="none">
        <ellipse cx="200" cy="200" rx="200" ry="160" fill="#f2d6e0"/>
    </svg>
    <svg class="left-blob" style="top:38%;left:30%;width:260px;opacity:.07;" viewBox="0 0 300 300" fill="none">
        <path d="M150 20C220 20 280 80 280 150C280 220 220 280 150 280C80 280 20 220 20 150C20 80 80 20 150 20Z" fill="#fff"/>
    </svg>
    <svg class="left-blob" style="bottom:120px;right:30px;width:180px;opacity:.18;" viewBox="0 0 200 200" fill="none">
        <path d="M100 10Q160 50 190 100Q160 150 100 190Q40 150 10 100Q40 50 100 10Z" stroke="#f2d6e0" stroke-width="1.5" fill="none"/>
        <path d="M100 30Q148 62 172 100Q148 138 100 170Q52 138 28 100Q52 62 100 30Z" stroke="#f2d6e0" stroke-width="1" fill="none"/>
        <path d="M100 50Q136 74 154 100Q136 126 100 150Q64 126 46 100Q64 74 100 50Z" stroke="#f2d6e0" stroke-width=".7" fill="none"/>
    </svg>
    <svg class="left-blob" style="top:150px;left:16px;width:110px;opacity:.14;" viewBox="0 0 200 200" fill="none">
        <path d="M100 10Q160 50 190 100Q160 150 100 190Q40 150 10 100Q40 50 100 10Z" stroke="#f2d6e0" stroke-width="1.5" fill="none"/>
        <path d="M100 35Q145 65 168 100Q145 135 100 165Q55 135 32 100Q55 65 100 35Z" stroke="#f2d6e0" stroke-width="1" fill="none"/>
    </svg>

    <div class="left-logo">
        <div class="logo-img-wrap">
            <img src="{{ asset('images/skinetique_logo_new.png') }}" alt="SKINETIQUE">
        </div>
        <span class="logo-wordmark">Skinetique</span>
    </div>

    <div class="left-center">
        <div class="left-tagline">
            Manage your<br>
            business<br>
            <em>beautifully.</em>
        </div>
        <p class="left-sub">
            A complete records management system for SKINETIQUE —
            orders, payments, inventory, and reports all in one place.
        </p>
    </div>

</div>

<!-- ════════ RIGHT PANEL ════════ -->
<div class="right-panel">
    <div class="form-card">

        <div class="form-heading">
            <div class="form-heading-title">Welcome back</div>
            <div class="form-heading-sub">Sign in to your account to continue</div>
        </div>

        @if ($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="field">
                <label class="field-label">Username</label>
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <input
                    type="text"
                    name="username"
                    placeholder="Enter your username"
                    value="{{ old('username') }}"
                    autocomplete="username"
                    class="{{ $errors->has('username') ? 'is-invalid' : '' }}"
                    required
                >
            </div>

            <div class="field field-has-eye" style="margin-bottom: 28px;">
                <label class="field-label">Password</label>
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                    type="password"
                    id="passwordInput"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
                <button type="button" class="field-eye" id="eyeBtn" onclick="togglePassword()" aria-label="Show password">
                    <svg id="eyeShow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="eyeHide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                </button>
            </div>

            {{-- Forgot Password removed — no password reset flow exists in this system.
                 Contact the owner to have your password changed via the Employees page. --}}

            <button type="submit" class="btn-login">Sign In</button>

        </form>

        <div class="form-footer">
            SKINETIQUE Records Management System &copy; {{ date('Y') }}
        </div>

    </div>
</div>

<script>
    function togglePassword() {
        const input   = document.getElementById('passwordInput');
        const eyeShow = document.getElementById('eyeShow');
        const eyeHide = document.getElementById('eyeHide');
        const btn     = document.getElementById('eyeBtn');

        if (input.type === 'password') {
            input.type = 'text';
            eyeShow.style.display = 'none';
            eyeHide.style.display = '';
            btn.setAttribute('aria-label', 'Hide password');
        } else {
            input.type = 'password';
            eyeShow.style.display = '';
            eyeHide.style.display = 'none';
            btn.setAttribute('aria-label', 'Show password');
        }
    }
</script>
</body>
</html>
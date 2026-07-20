<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login - Sistem Informasi Manajemen Bengkel CV Masman Sejahtera">
    <title>Masuk · CV Masman Sejahtera</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:       #2563EB;
            --primary-dark:  #1D4ED8;
            --primary-light: #EFF6FF;
            --success:       #16A34A;
            --warning:       #D97706;
            --danger:        #DC2626;
            --danger-light:  #FEF2F2;
            --text:          #1E293B;
            --text-muted:    #64748B;
            --surface:       #F8FAFC;
            --border:        #E2E8F0;
            --white:         #FFFFFF;
            --radius:        12px;
            --shadow-sm:     0 1px 3px rgba(0,0,0,.08);
            --shadow-md:     0 4px 24px rgba(0,0,0,.10);
            --shadow-lg:     0 20px 60px rgba(37,99,235,.15);
            --transition:    .2s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .25;
            pointer-events: none;
        }
        body::before {
            width: 500px; height: 500px;
            background: #2563EB;
            top: -150px; left: -150px;
            animation: float1 8s ease-in-out infinite;
        }
        body::after {
            width: 400px; height: 400px;
            background: #7C3AED;
            bottom: -100px; right: -100px;
            animation: float2 10s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(40px, 30px) scale(1.1); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-30px, -40px) scale(1.05); }
        }

        /* ─── LOGIN CARD ────────────────────────────────────── */
        .login-card {
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255,255,255,.3);
            animation: slideUp .4s ease;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── HEADER ─────────────────────────────────────────── */
        .login-header { text-align: center; margin-bottom: 2rem; }

        .login-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px; height: 72px;
            background: var(--primary-light);
            border-radius: 18px;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(37,99,235,.25);
        }

        .login-title {
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: .25rem;
        }

        .login-subtitle {
            font-size: .875rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* ─── ALERTS ─────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .75rem 1rem;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            animation: fadeIn .2s ease;
        }
        .alert-danger {
            background: var(--danger-light);
            color: var(--danger);
            border: 1px solid #FECACA;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── FORM ───────────────────────────────────────────── */
        .login-form { display: flex; flex-direction: column; gap: 1.25rem; }

        .form-group { display: flex; flex-direction: column; gap: .375rem; }

        .form-label {
            font-size: .8125rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: .01em;
        }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute;
            left: .875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: .75rem 1rem .75rem 2.75rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: .9375rem;
            color: var(--text);
            background: var(--white);
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
        }
        .form-input::placeholder { color: #94A3B8; }
        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .form-input.input-error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(220,38,38,.08);
        }

        .toggle-password {
            position: absolute;
            right: .875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            padding: .25rem;
            border-radius: 4px;
            transition: color var(--transition);
        }
        .toggle-password:hover { color: var(--primary); }

        .error-text {
            font-size: .75rem;
            color: var(--danger);
            font-weight: 500;
        }

        /* ─── REMEMBER ME ────────────────────────────────────── */
        .form-check { display: flex; align-items: center; }

        .check-label {
            display: flex;
            align-items: center;
            gap: .625rem;
            cursor: pointer;
            font-size: .875rem;
            color: var(--text-muted);
            user-select: none;
        }

        .check-input { display: none; }

        .check-custom {
            width: 18px; height: 18px;
            border: 2px solid var(--border);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition);
            flex-shrink: 0;
        }
        .check-input:checked + .check-custom {
            background: var(--primary);
            border-color: var(--primary);
        }
        .check-input:checked + .check-custom::after {
            content: '';
            width: 5px; height: 9px;
            border: 2px solid white;
            border-top: none;
            border-left: none;
            transform: rotate(45deg) translate(-1px, -1px);
            display: block;
        }

        /* ─── BUTTON ─────────────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: .875rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: 0 4px 12px rgba(37,99,235,.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: .25rem;
            min-height: 52px;
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37,99,235,.45);
        }
        .btn-login:active:not(:disabled) { transform: translateY(0); }
        .btn-login:disabled, .btn-login.btn-loading {
            opacity: .75;
            cursor: not-allowed;
            transform: none;
        }

        .loading-text {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .spin { animation: spin 1s linear infinite; }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ─── FOOTER ─────────────────────────────────────────── */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .login-footer p {
            font-size: .75rem;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    {{ $slot }}

    @livewireScripts
</body>
</html>

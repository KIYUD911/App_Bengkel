<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Manajemen Bengkel CV Masman Sejahtera">
    <title>@yield('title', 'Dashboard') · CV Masman Sejahtera</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        /* ═══════════════════════════════════════════════════════
           DESIGN SYSTEM — CSS VARIABLES
        ═══════════════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Brand Colors */
            --primary:          #2563EB;
            --primary-dark:     #1D4ED8;
            --primary-light:    #EFF6FF;
            --primary-muted:    rgba(37,99,235,.1);

            /* Semantic Colors */
            --success:          #16A34A;
            --success-light:    #F0FDF4;
            --warning:          #D97706;
            --warning-light:    #FFFBEB;
            --danger:           #DC2626;
            --danger-light:     #FEF2F2;
            --info:             #0891B2;
            --info-light:       #ECFEFF;

            /* Text */
            --text:             #1E293B;
            --text-secondary:   #475569;
            --text-muted:       #94A3B8;
            --text-inverse:     #FFFFFF;

            /* Surface */
            --surface:          #F8FAFC;
            --surface-raised:   #FFFFFF;
            --surface-overlay:  rgba(0,0,0,.5);

            /* Border */
            --border:           #E2E8F0;
            --border-focus:     #2563EB;

            /* Sidebar */
            --sidebar-bg:       #0F172A;
            --sidebar-width:    260px;
            --sidebar-hover:    rgba(255,255,255,.07);
            --sidebar-active:   rgba(37,99,235,.25);
            --sidebar-text:     #CBD5E1;
            --sidebar-text-active: #FFFFFF;

            /* Spacing & Shape */
            --radius-sm:        6px;
            --radius:           10px;
            --radius-lg:        14px;
            --radius-xl:        20px;

            /* Shadow */
            --shadow-sm:        0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md:        0 4px 16px rgba(0,0,0,.08);
            --shadow-lg:        0 12px 40px rgba(0,0,0,.12);

            /* Typography */
            --font:             'Inter', -apple-system, sans-serif;
            --text-xs:          .75rem;
            --text-sm:          .8125rem;
            --text-base:        .9375rem;
            --text-lg:          1.0625rem;
            --text-xl:          1.25rem;
            --text-2xl:         1.5rem;

            /* Transitions */
            --transition:       .2s ease;
            --transition-slow:  .35s ease;

            /* Header */
            --header-height:    64px;
        }

        /* ─── BASE ──────────────────────────────────────────── */
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font);
            font-size: var(--text-base);
            color: var(--text);
            background: var(--surface);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
        }

        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }

        img { max-width: 100%; }

        /* ─── LAYOUT ─────────────────────────────────────────── */
        .app-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform var(--transition-slow);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .app-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ─── SIDEBAR HEADER (LOGO) ──────────────────────────── */
        .sidebar-header {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(37,99,235,.4);
        }

        .sidebar-brand-text { flex: 1; min-width: 0; }
        .sidebar-brand-name {
            display: block;
            font-size: var(--text-sm);
            font-weight: 700;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .sidebar-brand-sub {
            display: block;
            font-size: .6875rem;
            color: var(--sidebar-text);
            margin-top: 1px;
        }

        /* ─── SIDEBAR NAV ────────────────────────────────────── */
        .sidebar-nav { flex: 1; padding: .75rem 0; }

        .nav-section { margin-bottom: .25rem; }

        .nav-section-title {
            font-size: .625rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #475569;
            padding: .875rem 1.25rem .375rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .625rem 1.25rem;
            margin: 1px .5rem;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            font-size: var(--text-sm);
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition);
            cursor: pointer;
            border: none;
            background: none;
            width: calc(100% - 1rem);
            text-align: left;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-active);
            text-decoration: none;
        }

        .nav-item.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
            font-weight: 600;
        }

        .nav-item.active .nav-icon { color: var(--primary); filter: brightness(1.4); }

        .nav-icon {
            width: 18px; height: 18px;
            flex-shrink: 0;
            opacity: .8;
        }

        .nav-item:hover .nav-icon, .nav-item.active .nav-icon { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: .625rem;
            font-weight: 700;
            padding: .125rem .375rem;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }

        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,.06);
            margin: .5rem 1.25rem;
        }

        /* ─── SIDEBAR FOOTER ─────────────────────────────────── */
        .sidebar-footer {
            padding: .75rem .5rem;
            border-top: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }

        /* ─── TOP HEADER ─────────────────────────────────────── */
        .app-header {
            height: var(--header-height);
            background: var(--surface-raised);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .header-page-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--text);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        /* Role Badge */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .3rem .75rem;
            border-radius: 999px;
            font-size: var(--text-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .role-badge.owner   { background: #FDF4FF; color: #7E22CE; border: 1px solid #E9D5FF; }
        .role-badge.kasir   { background: var(--primary-light); color: var(--primary-dark); border: 1px solid #BFDBFE; }
        .role-badge.gudang  { background: var(--success-light); color: #15803D; border: 1px solid #BBF7D0; }

        /* User Avatar */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #7C3AED 100%);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: var(--text-sm);
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: var(--text-sm); font-weight: 600; color: var(--text); line-height: 1.2; }
        .user-role { font-size: var(--text-xs); color: var(--text-muted); }

        /* Logout Button */
        .btn-logout {
            display: flex; align-items: center; gap: .4rem;
            padding: .5rem .875rem;
            background: var(--danger-light);
            color: var(--danger);
            border: 1px solid #FECACA;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: var(--text-sm);
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition);
        }
        .btn-logout:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        /* Mobile menu toggle */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            cursor: pointer;
            padding: .375rem;
            border-radius: var(--radius-sm);
        }

        /* ─── CONTENT ────────────────────────────────────────── */
        .app-content {
            flex: 1;
            padding: 1.5rem;
            max-width: 100%;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .page-title { font-size: var(--text-2xl); font-weight: 700; color: var(--text); }
        .page-subtitle { font-size: var(--text-sm); color: var(--text-muted); margin-top: .25rem; }

        /* ─── COMPONENTS ─────────────────────────────────────── */

        /* Cards */
        .card {
            background: var(--surface-raised);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem; padding-bottom: .875rem;
            border-bottom: 1px solid var(--border);
        }
        .card-title { font-size: var(--text-base); font-weight: 600; color: var(--text); }

        /* Stat Cards */
        .stat-card {
            background: var(--surface-raised);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: all var(--transition);
        }
        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: var(--primary-light); color: var(--primary); }
        .stat-icon.green  { background: var(--success-light); color: var(--success); }
        .stat-icon.amber  { background: var(--warning-light); color: var(--warning); }
        .stat-icon.red    { background: var(--danger-light);  color: var(--danger); }
        .stat-icon.purple { background: #F5F3FF; color: #7C3AED; }

        .stat-content { flex: 1; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text); line-height: 1.2; }
        .stat-label { font-size: var(--text-xs); color: var(--text-muted); margin-top: .25rem; font-weight: 500; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem 1rem; border-radius: var(--radius-sm); font-family: var(--font); font-size: var(--text-sm); font-weight: 500; cursor: pointer; transition: all var(--transition); border: 1px solid transparent; white-space: nowrap; text-decoration: none; }
        .btn:hover { text-decoration: none; }
        .btn-primary   { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 2px 8px rgba(37,99,235,.25); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-secondary { background: white; color: var(--text); border-color: var(--border); }
        .btn-secondary:hover { background: var(--surface); }
        .btn-success   { background: var(--success); color: white; border-color: var(--success); }
        .btn-danger    { background: var(--danger); color: white; border-color: var(--danger); }
        .btn-warning   { background: var(--warning); color: white; border-color: var(--warning); }
        .btn-sm { padding: .35rem .75rem; font-size: var(--text-xs); }
        .btn-lg { padding: .75rem 1.5rem; font-size: var(--text-base); }
        .btn:disabled { opacity: .6; cursor: not-allowed; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: .2rem .6rem; border-radius: 999px; font-size: var(--text-xs); font-weight: 600; }
        .badge-primary  { background: var(--primary-light); color: var(--primary); }
        .badge-success  { background: var(--success-light); color: var(--success); }
        .badge-warning  { background: var(--warning-light); color: var(--warning); }
        .badge-danger   { background: var(--danger-light);  color: var(--danger); }
        .badge-info     { background: var(--info-light);    color: var(--info); }
        .badge-gray     { background: #F1F5F9; color: var(--text-secondary); }

        /* Status Badges (WO) */
        .status-pending     { background: var(--warning-light); color: var(--warning); }
        .status-in_progress { background: var(--info-light);    color: var(--info); }
        .status-completed   { background: var(--success-light); color: var(--success); }
        .status-cancelled   { background: #F1F5F9; color: var(--text-muted); }

        /* Tables */
        .table-wrap { overflow-x: auto; border-radius: var(--radius); border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: var(--surface); padding: .75rem 1rem; font-size: var(--text-xs); font-weight: 600; color: var(--text-secondary); text-align: left; border-bottom: 1px solid var(--border); white-space: nowrap; text-transform: uppercase; letter-spacing: .04em; }
        tbody td { padding: .875rem 1rem; font-size: var(--text-sm); border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--surface); }

        /* Forms */
        .form-group { display: flex; flex-direction: column; gap: .375rem; margin-bottom: 1rem; }
        .form-label { font-size: var(--text-sm); font-weight: 600; color: var(--text); }
        .form-label .required { color: var(--danger); margin-left: .2rem; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: .625rem .875rem;
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            font-family: var(--font); font-size: var(--text-base); color: var(--text);
            background: white; outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
        }
        .form-input::placeholder, .form-textarea::placeholder { color: var(--text-muted); }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }
        .form-input.is-invalid { border-color: var(--danger); }
        .form-textarea { resize: vertical; min-height: 100px; }
        .form-hint { font-size: var(--text-xs); color: var(--text-muted); }
        .form-error { font-size: var(--text-xs); color: var(--danger); font-weight: 500; }

        /* Alerts */
        .alert { display: flex; align-items: flex-start; gap: .75rem; padding: .875rem 1rem; border-radius: var(--radius); margin-bottom: 1rem; font-size: var(--text-sm); }
        .alert-success { background: var(--success-light); color: var(--success); border: 1px solid #BBF7D0; }
        .alert-danger  { background: var(--danger-light);  color: var(--danger);  border: 1px solid #FECACA; }
        .alert-warning { background: var(--warning-light); color: var(--warning); border: 1px solid #FDE68A; }
        .alert-info    { background: var(--info-light);    color: var(--info);    border: 1px solid #A5F3FC; }

        /* Grid helpers */
        .grid { display: grid; gap: 1.25rem; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        /* Flex helpers */
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: .25rem; }
        .gap-2 { gap: .5rem; }
        .gap-3 { gap: .75rem; }
        .gap-4 { gap: 1rem; }
        .flex-1 { flex: 1; }

        /* Pagination */
        .pagination { display: flex; align-items: center; justify-content: center; gap: .25rem; margin-top: 1.25rem; }
        .page-btn { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: var(--text-sm); color: var(--text-secondary); background: white; cursor: pointer; transition: all var(--transition); }
        .page-btn:hover { border-color: var(--primary); color: var(--primary); }
        .page-btn.active { background: var(--primary); border-color: var(--primary); color: white; font-weight: 600; }

        /* Empty State */
        .empty-state { text-align: center; padding: 3rem 1.5rem; }
        .empty-state-icon { font-size: 3rem; margin-bottom: .75rem; opacity: .4; }
        .empty-state-title { font-size: var(--text-lg); font-weight: 600; color: var(--text); margin-bottom: .375rem; }
        .empty-state-text { font-size: var(--text-sm); color: var(--text-muted); }

        /* Loading */
        .loading-overlay { position: absolute; inset: 0; background: rgba(255,255,255,.8); display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: inherit; }
        .spinner { width: 24px; height: 24px; border: 3px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Toast / Flash messages */
        .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: .5rem; max-width: 360px; pointer-events: none; }
        .toast { background: var(--surface-raised); border: 1px solid var(--border); border-radius: var(--radius); padding: .875rem 1rem; box-shadow: var(--shadow-lg); pointer-events: auto; display: flex; align-items: flex-start; gap: .75rem; animation: toastIn .3s ease; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }

        /* Separator */
        hr { border: none; border-top: 1px solid var(--border); margin: 1.25rem 0; }

        /* Responsive */
        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.open { transform: translateX(0); }
            .app-main { margin-left: 0; }
            .sidebar-toggle { display: flex; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .app-content { padding: 1rem; }
            .page-header { flex-direction: column; }
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: var(--surface-overlay);
            z-index: 99;
        }
        @media (max-width: 768px) {
            .sidebar-overlay.show { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- MOBILE SIDEBAR OVERLAY --}}
<div class="sidebar-overlay" id="sidebarOverlay" x-data @click="$dispatch('close-sidebar')"></div>

{{-- ─── SIDEBAR ─────────────────────────────────────────── --}}
<aside class="app-sidebar" id="appSidebar" x-data="{ open: false }"
    @open-sidebar.window="open = true; document.getElementById('sidebarOverlay').classList.add('show')"
    @close-sidebar.window="open = false; document.getElementById('sidebarOverlay').classList.remove('show')"
    :class="{ 'open': open }">

    {{-- Brand --}}
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">CV Masman Sejahtera</span>
                <span class="sidebar-brand-sub">Sistem Manajemen Bengkel</span>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- UMUM — semua role --}}
        <div class="nav-section">
            <div class="nav-section-title">Umum</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                Dashboard
            </a>
        </div>

        {{-- OPERASIONAL — kasir & owner --}}
        @if(auth()->user()->isKasir() || auth()->user()->isOwner())
        <div class="nav-section">
            <div class="nav-section-title">Operasional</div>
            <a href="{{ route('work-orders.index') }}" class="nav-item {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Work Order
            </a>
            <a href="{{ route('direct-sales.index') }}" class="nav-item {{ request()->routeIs('direct-sales.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                Penjualan Langsung
            </a>
            <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Pelanggan (CRM)
            </a>
        </div>
        @endif

        {{-- INVENTORI — staf gudang & owner --}}
        @if(auth()->user()->isStafGudang() || auth()->user()->isOwner())
        <div class="nav-section">
            <div class="nav-section-title">Inventori</div>
            <a href="{{ route('spare-parts.index') }}" class="nav-item {{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                Sparepart
            </a>
            <a href="{{ route('stock-movements.index') }}" class="nav-item {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                Mutasi Stok
            </a>
        </div>
        @endif

        {{-- LAPORAN & ADMIN — owner only --}}
        @if(auth()->user()->isOwner())
        <div class="nav-divider"></div>
        <div class="nav-section">
            <div class="nav-section-title">Laporan & Admin</div>
            <a href="{{ route('reports.revenue') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Laporan
            </a>
            <a href="{{ route('audit-logs.index') }}" class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Audit Log
            </a>
            <a href="{{ route('recycle-bin') }}" class="nav-item {{ request()->routeIs('recycle-bin') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Recycle Bin
            </a>
        </div>
        @endif

    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item" style="color:#F87171;">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ─── MAIN CONTENT ────────────────────────────────────── --}}
<div class="app-main">

    {{-- Header --}}
    <header class="app-header">
        <div class="header-left">
            {{-- Mobile toggle --}}
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" @click.window="$dispatch('open-sidebar')">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <h1 class="header-page-title">@yield('page-title', 'Dashboard')</h1>
        </div>

        <div class="header-right">
            {{-- Role Badge --}}
            @php
                $roleLabels = ['owner' => 'Owner', 'kasir' => 'Kasir', 'staf_gudang' => 'Staf Gudang'];
                $roleClass  = ['owner' => 'owner', 'kasir' => 'kasir', 'staf_gudang' => 'gudang'];
                $role = auth()->user()->role;
            @endphp

            <span class="role-badge {{ $roleClass[$role] ?? '' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="6"/></svg>
                {{ $roleLabels[$role] ?? $role }}
            </span>

            {{-- User Info --}}
            <div class="flex items-center gap-2">
                <div class="user-avatar" title="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="user-info" style="display: none;">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ $roleLabels[$role] ?? $role }}</span>
                </div>
            </div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin: 1rem 1.5rem 0; border-radius: var(--radius);" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin: 1rem 1.5rem 0; border-radius: var(--radius);" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Content --}}
    <main class="app-content">
        {{ $slot }}
    </main>

</div>

@livewireScripts

@stack('scripts')
</body>
</html>

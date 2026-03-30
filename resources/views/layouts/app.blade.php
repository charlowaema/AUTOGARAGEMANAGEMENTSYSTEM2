<!DOCTYPE html>
<html lang="en" class="h-full" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AGMS') — Auto Garage Management</title>
    {{-- Local fonts (offline-safe) --}}
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    {{-- Tailwind: Blade checks if local file exists, uses CDN otherwise --}}
    @if(file_exists(public_path('js/tailwind.js')))
        <script src="{{ asset('js/tailwind.js') }}"></script>
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    {{-- Apply theme BEFORE render to prevent flash --}}
    <script>
        (function() {
            const theme = localStorage.getItem('agms-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Barlow', 'sans-serif'],
                        condensed: ['Barlow Condensed', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style>
        /* ── CSS Variables: Dark (default) ── */
        :root, [data-theme="dark"] {
            --bg-base:        #020617;
            --bg-surface:     #0f172a;
            --bg-sidebar:     #0a0f1e;
            --bg-elevated:    #1e293b;
            --bg-hover:       #263248;
            --bg-input:       #1e293b;
            --bg-topbar:      rgba(2, 6, 23, 0.88);

            --border:         #1e293b;
            --border-input:   #334155;

            --text-primary:   #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted:     #475569;
            --text-input:     #f1f5f9;

            --brand:          #f97316;
            --brand-hover:    #ea580c;
            --brand-shadow:   rgba(249, 115, 22, 0.25);

            --table-stripe:   rgba(30, 41, 59, 0.5);
            --shadow-card:    0 1px 3px rgba(0,0,0,0.5);

            --nav-label:      rgba(255,255,255,0.22);
            --nav-link:       rgba(255,255,255,0.5);
            --nav-link-hover-bg: rgba(255,255,255,0.07);
            --sidebar-footer-border: rgba(255,255,255,0.07);
            --sidebar-user-name: #fff;
            --sidebar-user-role: rgba(255,255,255,0.32);
            --sidebar-btn:    rgba(255,255,255,0.32);
            --sidebar-btn-hover-bg: rgba(255,255,255,0.08);
        }

        /* ── CSS Variables: Light ── */
        [data-theme="light"] {
            --bg-base:        #f1f5f9;
            --bg-surface:     #ffffff;
            --bg-sidebar:     #1e293b;
            --bg-elevated:    #f8fafc;
            --bg-hover:       #f1f5f9;
            --bg-input:       #ffffff;
            --bg-topbar:      rgba(255, 255, 255, 0.92);

            --border:         #e2e8f0;
            --border-input:   #cbd5e1;

            --text-primary:   #0f172a;
            --text-secondary: #475569;
            --text-muted:     #94a3b8;
            --text-input:     #0f172a;

            --brand:          #f97316;
            --brand-hover:    #ea580c;
            --brand-shadow:   rgba(249, 115, 22, 0.18);

            --table-stripe:   rgba(248, 250, 252, 0.9);
            --shadow-card:    0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.05);

            /* sidebar stays dark in light mode for contrast */
            --nav-label:      rgba(255,255,255,0.22);
            --nav-link:       rgba(255,255,255,0.5);
            --nav-link-hover-bg: rgba(255,255,255,0.07);
            --sidebar-footer-border: rgba(255,255,255,0.07);
            --sidebar-user-name: #fff;
            --sidebar-user-role: rgba(255,255,255,0.32);
            --sidebar-btn:    rgba(255,255,255,0.32);
            --sidebar-btn-hover-bg: rgba(255,255,255,0.08);
        }

        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; padding: 0; }
        body {
            font-family: 'Barlow', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            transition: background 0.2s ease, color 0.2s ease;
        }

        /* ── Shell ── */
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* ── Sidebar (always dark) ── */
        .sidebar {
            width: 16rem;
            flex-shrink: 0;
            background: var(--bg-sidebar);
            border-right: 1px solid rgba(255,255,255,0.06);
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .logo-icon {
            width: 2.25rem; height: 2.25rem;
            background: var(--brand);
            border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px var(--brand-shadow);
        }
        .logo-text-main { color: #fff; font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 1.125rem; line-height: 1; }
        .logo-text-sub  { color: rgba(255,255,255,0.35); font-size: 0.6875rem; margin-top: 0.125rem; }

        .sidebar-nav { flex: 1; padding: 0.875rem 0.625rem; overflow-y: auto; }
        .nav-section-label {
            font-size: 0.625rem; font-weight: 700;
            color: var(--nav-label);
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 0 0.875rem;
            margin-bottom: 0.375rem; margin-top: 1.25rem;
        }
        .nav-section-label:first-child { margin-top: 0; }

        .nav-link {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.5rem 0.875rem;
            border-radius: 0.5rem;
            color: var(--nav-link);
            font-size: 0.8125rem; font-weight: 500;
            text-decoration: none;
            transition: all 0.12s;
            margin-bottom: 0.0625rem;
        }
        .nav-link:hover { color: #fff; background: var(--nav-link-hover-bg); }
        .nav-link.active { color: #fff; background: var(--brand); box-shadow: 0 4px 12px var(--brand-shadow); }

        .sidebar-footer {
            padding: 0.875rem 1rem;
            border-top: 1px solid var(--sidebar-footer-border);
        }
        .sidebar-user { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.625rem; }
        .sidebar-avatar {
            width: 1.875rem; height: 1.875rem;
            border-radius: 9999px;
            background: rgba(249,115,22,0.18);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-avatar span { font-size: 0.625rem; font-weight: 700; color: #f97316; }
        .sidebar-user-name { font-size: 0.75rem; font-weight: 600; color: var(--sidebar-user-name); }
        .sidebar-user-role { font-size: 0.625rem; color: var(--sidebar-user-role); }
        .sidebar-actions { display: flex; gap: 0.375rem; }
        .sidebar-btn {
            flex: 1; text-align: center; font-size: 0.6875rem;
            color: var(--sidebar-btn);
            padding: 0.375rem 0.5rem;
            border-radius: 0.375rem;
            background: none; border: none; cursor: pointer;
            text-decoration: none; transition: all 0.12s;
            display: block;
        }
        .sidebar-btn:hover { color: #fff; background: var(--sidebar-btn-hover-bg); }
        .sidebar-btn.danger:hover { color: #f87171; background: rgba(239,68,68,0.12); }

        /* ── Main content ── */
        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }

        /* ── Topbar ── */
        .topbar {
            position: sticky; top: 0; z-index: 10;
            background: var(--bg-topbar);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 0.875rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            transition: background 0.2s, border-color 0.2s;
        }
        .page-title { font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-primary); }
        .page-breadcrumb { font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.125rem; }
        .topbar-actions { display: flex; align-items: center; gap: 0.625rem; }

        /* ── Theme toggle ── */
        .theme-toggle {
            width: 2.125rem; height: 2.125rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-input);
            background: var(--bg-elevated);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s; flex-shrink: 0;
        }
        .theme-toggle:hover { background: var(--brand); color: #fff; border-color: var(--brand); }
        .icon-sun  { display: none; }
        .icon-moon { display: block; }
        [data-theme="light"] .icon-sun  { display: block; }
        [data-theme="light"] .icon-moon { display: none; }

        /* ── Main body ── */
        .main-body { padding: 1.5rem 2rem; flex: 1; }

        /* ── Card ── */
        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-card);
            transition: background 0.2s, border-color 0.2s;
        }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: var(--brand); color: #fff; font-weight: 600;
            padding: 0.5625rem 1rem; border-radius: 0.5rem; font-size: 0.875rem;
            border: none; cursor: pointer; text-decoration: none;
            transition: background 0.15s; box-shadow: 0 4px 10px var(--brand-shadow);
        }
        .btn-primary:hover { background: var(--brand-hover); }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: var(--bg-elevated); color: var(--text-primary); font-weight: 500;
            padding: 0.5625rem 1rem; border-radius: 0.5rem; font-size: 0.875rem;
            border: 1px solid var(--border-input); cursor: pointer; text-decoration: none;
            transition: all 0.15s;
        }
        .btn-secondary:hover { background: var(--bg-hover); }

        .btn-danger {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #dc2626; color: #fff; font-weight: 600;
            padding: 0.5625rem 1rem; border-radius: 0.5rem; font-size: 0.875rem;
            border: none; cursor: pointer; text-decoration: none; transition: background 0.15s;
        }
        .btn-danger:hover { background: #b91c1c; }

        /* ── Form inputs ── */
        .form-input {
            width: 100%;
            background-color: var(--bg-input) !important;
            border: 1px solid var(--border-input);
            color: var(--text-input) !important;
            -webkit-text-fill-color: var(--text-input) !important;
            border-radius: 0.5rem;
            padding: 0.5625rem 0.75rem;
            font-size: 0.875rem;
            font-family: 'Barlow', sans-serif;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.2s, color 0.2s;
        }
        .form-input::placeholder { color: var(--text-muted); opacity: 1; }
        .form-input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-shadow); }
        .form-input option { background: var(--bg-input); color: var(--text-input); }
        select.form-input { cursor: pointer; }
        textarea.form-input { resize: vertical; }

        .form-label {
            display: block; font-size: 0.6875rem; font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.375rem;
        }

        /* ── Tables ── */
        .table-row { border-bottom: 1px solid var(--border); transition: background 0.1s; }
        .table-row:hover { background: var(--table-stripe); }

        /* ── Badges ── */
        .badge-open     { display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.75rem;font-weight:600; background:rgba(59,130,246,0.1); color:#60a5fa; border:1px solid rgba(59,130,246,0.2); }
        .badge-progress { display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.75rem;font-weight:600; background:rgba(249,115,22,0.1); color:#f97316; border:1px solid rgba(249,115,22,0.2); }
        .badge-closed   { display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.75rem;font-weight:600; background:rgba(34,197,94,0.1);  color:#4ade80; border:1px solid rgba(34,197,94,0.2); }
        .badge-regular  { display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.75rem;font-weight:600; background:rgba(14,165,233,0.1); color:#38bdf8; border:1px solid rgba(14,165,233,0.2); }
        .badge-full     { display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.75rem;font-weight:600; background:rgba(168,85,247,0.1);  color:#c084fc; border:1px solid rgba(168,85,247,0.2); }

        [data-theme="light"] .badge-open     { background:rgba(37,99,235,0.08);  color:#1d4ed8; border-color:rgba(37,99,235,0.2); }
        [data-theme="light"] .badge-progress { background:rgba(234,88,12,0.08);  color:#c2410c; border-color:rgba(234,88,12,0.2); }
        [data-theme="light"] .badge-closed   { background:rgba(22,163,74,0.08);  color:#15803d; border-color:rgba(22,163,74,0.2); }
        [data-theme="light"] .badge-regular  { background:rgba(2,132,199,0.08);  color:#0369a1; border-color:rgba(2,132,199,0.2); }
        [data-theme="light"] .badge-full     { background:rgba(124,58,237,0.08); color:#6d28d9; border-color:rgba(124,58,237,0.2); }

        /* ── Flash messages ── */
        .flash-success {
            display: flex; align-items: center; gap: 0.75rem;
            background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);
            color: #16a34a; padding: 0.75rem 1rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 500; margin-bottom: 1.5rem;
        }
        .flash-error {
            display: flex; align-items: flex-start; gap: 0.75rem; flex-direction: column;
            background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);
            color: #dc2626; padding: 0.75rem 1rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 500; margin-bottom: 1.5rem;
        }
        [data-theme="dark"] .flash-success { color: #4ade80; }
        [data-theme="dark"] .flash-error   { color: #f87171; }

        /* ── Tailwind dark-mode text overrides for light mode ── */
        [data-theme="light"] .text-white     { color: var(--text-primary) !important; }
        [data-theme="light"] .text-slate-300 { color: #334155 !important; }
        [data-theme="light"] .text-slate-400 { color: #475569 !important; }
        [data-theme="light"] .text-slate-500 { color: #64748b !important; }
        [data-theme="light"] .text-slate-600 { color: #94a3b8 !important; }
        [data-theme="light"] .text-slate-700 { color: #cbd5e1 !important; }
        [data-theme="light"] .border-slate-800 { border-color: #e2e8f0 !important; }
        [data-theme="light"] .divide-slate-800 > * + * { border-color: #e2e8f0 !important; }
        [data-theme="light"] .bg-slate-950   { background: var(--bg-base) !important; }
        [data-theme="light"] .bg-slate-900   { background: var(--bg-surface) !important; }
        [data-theme="light"] .bg-slate-800   { background: var(--bg-elevated) !important; }
        [data-theme="light"] .bg-slate-700   { background: #e2e8f0 !important; }
        [data-theme="light"] .hover\:bg-slate-800:hover { background: var(--bg-hover) !important; }
        [data-theme="light"] .hover\:bg-slate-700:hover { background: #e2e8f0 !important; }

        /* ── Print ── */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .card { border: 1px solid #e2e8f0 !important; box-shadow: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-shell">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar no-print">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1"/>
                </svg>
            </div>
            <div>
                <p class="logo-text-main">AGMS</p>
                <p class="logo-text-sub">Garage Management</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-section-label">Main</p>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                Dashboard
            </a>

            <p class="nav-section-label">Operations</p>
            <a href="{{ route('services.create') }}" class="nav-link {{ request()->routeIs('services.create') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Service
            </a>
            <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.index') || request()->routeIs('services.bay') || request()->routeIs('services.show') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Service Records
            </a>

            <p class="nav-section-label">Records</p>
            <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1"/></svg>
                Vehicles
            </a>
            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Customers
            </a>
            <a href="{{ route('parts.index') }}" class="nav-link {{ request()->routeIs('parts.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                Parts Inventory
            </a>
            <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Payments
            </a>

            @if(auth()->user()->isAdmin())
            <p class="nav-section-label">Admin</p>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') || request()->routeIs('register') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                User Management
            </a>
            <a href="{{ route('admin.makes.index') }}" class="nav-link {{ request()->routeIs('admin.makes.*') || request()->routeIs('admin.models.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Makes & Models
            </a>
            <a href="{{ route('admin.checklist.index') }}" class="nav-link {{ request()->routeIs('admin.checklist.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Checklist Templates
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    <span>{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div style="flex:1;min-width:0;">
                    <p class="sidebar-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                    <p class="sidebar-user-role">{{ auth()->user()->role_label }}</p>
                </div>
            </div>
            <div class="sidebar-actions">
                <a href="{{ route('password.change') }}" class="sidebar-btn">Password</a>
                <form method="POST" action="{{ route('logout') }}" style="flex:1;display:contents;">
                    @csrf
                    <button type="submit" class="sidebar-btn danger">Sign Out</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <main class="main-content">
        <div class="topbar no-print">
            <div>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <p class="page-breadcrumb">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="topbar-actions">
                @yield('page-actions')

                {{-- ☀/🌙 Theme Toggle --}}
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle light / dark mode">
                    <svg class="icon-moon" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="icon-sun" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="main-body">
            @if(session('success'))
                <div class="flash-success">
                    <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash-error">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="flash-error">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    function toggleTheme() {
        const html    = document.documentElement;
        const current = html.getAttribute('data-theme') || 'dark';
        const next    = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('agms-theme', next);
    }
</script>

@stack('scripts')
</body>
</html>

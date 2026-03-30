<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <script>
        (function() {
            const theme = localStorage.getItem('agms-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — AGMS</title>
    {{-- Local fonts (offline-safe) --}}
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    {{-- Tailwind: Blade checks if local file exists, uses CDN otherwise --}}
    @if(file_exists(public_path('js/tailwind.js')))
        <script src="{{ asset('js/tailwind.js') }}"></script>
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        :root, [data-theme="dark"] {
            --login-bg: #020617; --login-right-bg: #0a0f1e;
            --login-input-bg: #0f172a; --login-input-border: #1e293b;
            --login-input-text: #f1f5f9; --login-label: #94a3b8;
            --login-hint: #334155; --login-error-bg: rgba(239,68,68,0.1);
            --login-error-border: rgba(239,68,68,0.2); --login-error-text: #f87171;
        }
        [data-theme="light"] {
            --login-bg: #f1f5f9; --login-right-bg: #ffffff;
            --login-input-bg: #f8fafc; --login-input-border: #cbd5e1;
            --login-input-text: #0f172a; --login-label: #64748b;
            --login-hint: #94a3b8; --login-error-bg: rgba(239,68,68,0.08);
            --login-error-border: rgba(239,68,68,0.2); --login-error-text: #dc2626;
        }
        body { font-family: 'Barlow', sans-serif; background: var(--login-bg); transition: background 0.2s; }
        .form-input {
            width: 100%;
            background-color: var(--login-input-bg) !important;
            border: 1px solid var(--login-input-border);
            color: var(--login-input-text) !important;
            -webkit-text-fill-color: var(--login-input-text) !important;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.2s;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgb(249 115 22 / 0.15); }
        .gear { animation: spin 12s linear infinite; transform-origin: center; }
        .gear-reverse { animation: spin-reverse 8s linear infinite; transform-origin: center; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes spin-reverse { from { transform: rotate(0deg); } to { transform: rotate(-360deg); } }
    </style>
</head>
<body class="h-full flex">

    {{-- Left Panel - Branding --}}
    <div class="hidden lg:flex lg:w-1/2 bg-slate-950 border-r border-slate-800 flex-col items-center justify-center p-16 relative overflow-hidden">
        {{-- Animated background gears --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-5">
            <svg viewBox="0 0 400 400" class="w-full h-full">
                <g class="gear" style="transform-origin:130px 130px">
                    <path d="M130,80 L140,60 L150,80 L170,75 L165,95 L185,100 L175,118 L190,130 L175,142 L185,160 L165,165 L170,185 L150,180 L140,200 L130,180 L110,185 L115,165 L95,160 L105,142 L90,130 L105,118 L95,100 L115,95 L110,75 Z" fill="#f97316"/>
                    <circle cx="130" cy="130" r="35" fill="#020617"/>
                </g>
                <g class="gear-reverse" style="transform-origin:270px 200px">
                    <path d="M270,155 L278,140 L286,155 L302,151 L298,167 L314,172 L306,186 L318,196 L306,206 L314,220 L298,225 L302,241 L286,237 L278,252 L270,237 L254,241 L258,225 L242,220 L250,206 L238,196 L250,186 L242,172 L258,167 L254,151 Z" fill="#64748b"/>
                    <circle cx="270" cy="196" r="28" fill="#020617"/>
                </g>
                <g class="gear" style="transform-origin:200px 310px">
                    <path d="M200,275 L210,258 L220,275 L238,270 L233,288 L251,294 L242,310 L256,322 L242,334 L251,350 L233,356 L238,374 L220,369 L210,386 L200,369 L180,374 L185,356 L167,350 L176,334 L162,322 L176,310 L167,294 L185,288 L180,270 Z" fill="#334155"/>
                    <circle cx="200" cy="322" r="30" fill="#020617"/>
                </g>
            </svg>
        </div>

        <div class="relative z-10 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-500 rounded-2xl mb-8 shadow-2xl shadow-orange-500/30" style="background:#f97316">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1"/>
                </svg>
            </div>
            <h1 style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:3rem;color:white;line-height:1;letter-spacing:-0.02em;">AUTO GARAGE<br><span style="color:#f97316">MANAGEMENT</span><br>SYSTEM</h1>
            <p class="mt-6 text-slate-500 text-base max-w-xs mx-auto leading-relaxed">Complete vehicle service tracking, parts inventory & customer management for modern garages.</p>

            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div>
                    <p style="font-family:'Barlow Condensed',sans-serif;font-size:1.75rem;font-weight:700;color:#f97316;">5,000+</p>
                    <p class="text-xs text-slate-600 mt-1">Services Tracked</p>
                </div>
                <div>
                    <p style="font-family:'Barlow Condensed',sans-serif;font-size:1.75rem;font-weight:700;color:#f97316;">100%</p>
                    <p class="text-xs text-slate-600 mt-1">Digital Records</p>
                </div>
                <div>
                    <p style="font-family:'Barlow Condensed',sans-serif;font-size:1.75rem;font-weight:700;color:#f97316;">24/7</p>
                    <p class="text-xs text-slate-600 mt-1">Access Anywhere</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel - Login Form --}}
    <div class="flex-1 flex items-center justify-center p-8" style="background:var(--login-right-bg);transition:background 0.2s;">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#f97316">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1"/>
                    </svg>
                </div>
                <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:1.25rem;color:white;">AGMS</span>
            </div>

            <h2 style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:2rem;color:white;margin-bottom:0.5rem;">Welcome back</h2>
            <p style="color:#64748b;font-size:0.9375rem;margin-bottom:2.5rem;">Sign in to your AGMS account to continue.</p>

            {{-- Error messages --}}
            @if($errors->any())
            <div style="background:rgb(239 68 68 / 0.1);border:1px solid rgb(239 68 68 / 0.2);border-radius:0.5rem;padding:0.875rem 1rem;margin-bottom:1.5rem;">
                @foreach($errors->all() as $error)
                <p style="color:#f87171;font-size:0.875rem;">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
                @csrf

                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="you@garage.com" required autofocus>
                </div>

                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:#f97316;width:1rem;height:1rem;">
                        <span style="font-size:0.875rem;color:#64748b;">Remember me</span>
                    </label>
                </div>

                <button type="submit" style="width:100%;background:#f97316;color:white;font-weight:700;font-size:1rem;padding:0.875rem;border-radius:0.5rem;border:none;cursor:pointer;transition:background 0.15s;margin-top:0.5rem;letter-spacing:0.025em;" onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                    SIGN IN →
                </button>
            </form>

            <p style="margin-top:2rem;text-align:center;font-size:0.8125rem;color:#334155;">
                No self-registration. Contact your administrator for access.
            </p>

            <div style="margin-top:3rem;padding-top:1.5rem;border-top:1px solid #1e293b;text-align:center;">
                <p style="font-size:0.75rem;color:#1e293b;">AGMS v1.0 &mdash; Auto Garage Management System</p>
            </div>
        </div>
    </div>

    <button onclick="toggleLoginTheme()" style="position:fixed;bottom:1.5rem;right:1.5rem;width:2.5rem;height:2.5rem;border-radius:0.5rem;border:1px solid var(--login-input-border);background:var(--login-input-bg);color:var(--login-label);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;z-index:50;" title="Toggle theme">
        <svg id="toggle-moon" style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg id="toggle-sun" style="width:1.125rem;height:1.125rem;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
    </button>
    <script>
        function toggleLoginTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('agms-theme', next);
            document.getElementById('toggle-moon').style.display = next === 'dark' ? 'block' : 'none';
            document.getElementById('toggle-sun').style.display  = next === 'light' ? 'block' : 'none';
        }
        // Set initial icon
        const t = document.documentElement.getAttribute('data-theme') || 'dark';
        document.getElementById('toggle-moon').style.display = t === 'dark' ? 'block' : 'none';
        document.getElementById('toggle-sun').style.display  = t === 'light' ? 'block' : 'none';
    </script>
</body>
</html>

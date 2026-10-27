<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — BizJourney</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .bg-navy-dark { background-color: #121824; }
        .bg-navy-card { background-color: #1a2333; }
        .border-navy { border-color: #273449; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col md:flex-row min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-full md:w-64 bg-[#121824] text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800 z-30">
        <!-- Brand Header -->
        <div class="p-5 flex items-center gap-3 border-b border-slate-800/80">
            <div class="h-10 w-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold shadow-md">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
            <div>
                <h1 class="text-lg font-black tracking-tight text-white leading-tight">BizJourney</h1>
                <p class="text-[11px] text-amber-400 font-medium italic">Purnama Corporation</p>
            </div>
        </div>

        <!-- User Role Profile Badge -->
        <div class="p-4 mx-3 my-3 rounded-xl bg-[#1a2333] border border-slate-800">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center text-sm shadow-md">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/20">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-2">
                <span>Dept: {{ auth()->user()->department->name ?? 'Semua' }}</span>
                <span>{{ auth()->user()->position->name ?? 'Staff' }}</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto text-xs font-semibold">
            <!-- Common: Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-sm' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            <!-- SECTION: PERJALANAN DINAS -->
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Perjalanan Dinas</div>

            @if(auth()->user()->isEmployee())
                <a href="{{ route('travel.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('travel.create') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Ajukan Perjalanan</span>
                </a>
            @endif

            <a href="{{ route('travel.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('travel.index') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>{{ auth()->user()->isEmployee() ? 'Riwayat Pengajuan' : 'Daftar Perjalanan' }}</span>
            </a>

            <!-- SECTION: APPROVALS (Manager, HRD, Admin, Super Admin) -->
            @if(auth()->user()->canApproveTravel())
                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Persetujuan</div>
                <a href="{{ route('approvals.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('approvals.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ auth()->user()->isManager() ? 'Persetujuan Tim' : 'Persetujuan Dinas' }}</span>
                    </div>
                    @php
                        $pendingCount = \App\Models\Approval::where('status', 'PENDING')
                            ->when(auth()->user()->isManager(), fn($q) => $q->whereHas('travelRequest', fn($tq) => $tq->where('department_id', auth()->user()->department_id)))
                            ->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-500 text-white animate-pulse">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endif

            <!-- SECTION: REIMBURSEMENT -->
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Reimbursement</div>

            @if(auth()->user()->isEmployee())
                <a href="{{ route('reimbursements.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('reimbursements.create') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Ajukan Reimbursement</span>
                </a>
            @endif

            <a href="{{ route('reimbursements.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('reimbursements.index') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>{{ auth()->user()->isEmployee() ? 'Klaim Saya' : 'Semua Reimbursement' }}</span>
            </a>

            <!-- SECTION: FINANCE (Finance, Admin, Super Admin) -->
            @if(auth()->user()->isFinance() || auth()->user()->isAdmin())
                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Manajemen Keuangan</div>
                <a href="{{ route('finance.verifications') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('finance.verifications') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Verifikasi Bukti Klaim</span>
                    </div>
                    @php
                        $verifyCount = \App\Models\Reimbursement::whereIn('status', ['SUBMITTED', 'PENDING_VERIFICATION'])->count();
                    @endphp
                    @if($verifyCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-slate-950">{{ $verifyCount }}</span>
                    @endif
                </a>

                <a href="{{ route('finance.payments') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('finance.payments') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pembayaran (Payment)</span>
                    </div>
                    @php
                        $payCount = \App\Models\Reimbursement::where('status', 'APPROVED')->count();
                    @endphp
                    @if($payCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500 text-slate-950">{{ $payCount }}</span>
                    @endif
                </a>

                <a href="{{ route('finance.budgets') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('finance.budgets') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>Monitoring Anggaran</span>
                </a>

                <a href="{{ route('finance.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('finance.reports') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Keuangan</span>
                </a>
            @endif

            <!-- SECTION: ADMIN / SUPER ADMIN -->
            @if(auth()->user()->isAdmin())
                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Administrasi</div>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Manajemen Pengguna</span>
                </a>

                <a href="{{ route('admin.master') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.master') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Master Data & Kebijakan</span>
                </a>

                <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.logs') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#1a2333] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Audit / Activity Logs</span>
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer: Logout Form -->
        <div class="p-3 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-red-400 hover:bg-red-950/40 hover:text-red-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar Sistem (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN BODY -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc] overflow-y-auto">
        <!-- Topbar -->
        <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-slate-900">@yield('page-title', 'BizJourney Corporate')</h2>
                <span class="hidden sm:inline-block text-xs text-slate-400">|</span>
                <span class="hidden sm:inline-block text-xs font-medium text-slate-500">@yield('page-subtitle', 'Sistem Manajemen Perjalanan Dinas & Reimbursement')</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    Role: <span class="text-amber-700 font-extrabold">{{ auth()->user()->role }}</span>
                </span>
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
                    Home
                </a>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 mb-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 p-4 mb-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium shadow-xs">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">{{ session('error') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-4 mb-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm">
                    <div class="font-bold mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Mohon periksa kesalahan input berikut:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Page Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk ke Akun — BizJourney</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="min-h-screen grid lg:grid-cols-12 bg-[#f6f8fa] text-slate-800">

    <!-- Left Side Hero (Navy BizJourney Theme) -->
    <div class="hidden lg:flex lg:col-span-6 flex-col justify-between p-12 bg-[#121824] text-white relative overflow-hidden">
        <div class="absolute -top-32 -right-32 h-[500px] w-[500px] rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 h-[500px] w-[500px] rounded-full bg-amber-600/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <!-- Brand Logo Header -->
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center font-bold text-amber-400 shadow-xl backdrop-blur-md">
                    <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-white">BizJourney</h1>
                    <p class="text-xs text-amber-400/90 font-medium italic tracking-wide">Corporate Travel & Expense Management</p>
                </div>
            </div>

            <!-- Main Headline -->
            <div class="mt-16 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Purnama Corporation
                </div>
                <h2 class="text-4xl font-black leading-tight text-white tracking-tight">
                    Executive Control for<br>Travel, Approval & Claims
                </h2>
                <p class="text-slate-300 max-w-md text-sm leading-relaxed">
                    Sistem terpadu untuk pengajuan perjalanan dinas, approval multi-level (Manager & HRD), verifikasi bukti pengeluaran oleh Finance, dan pencatatan pembayaran real-time.
                </p>
            </div>

            <!-- Feature Badges -->
            <div class="mt-8 grid grid-cols-2 gap-4 max-w-md">
                <div class="p-4 rounded-2xl bg-[#1a2333] border border-slate-700/60">
                    <div class="text-amber-400 font-bold text-sm mb-1">⚡ Persetujuan Berjenjang</div>
                    <p class="text-xs text-slate-400">Persetujuan terpusat mulai dari Manager Departemen hingga HRD.</p>
                </div>
                <div class="p-4 rounded-2xl bg-[#1a2333] border border-slate-700/60">
                    <div class="text-amber-400 font-bold text-sm mb-1">💰 Verifikasi Reimbursement</div>
                    <p class="text-xs text-slate-400">Pemeriksaan bukti kuitansi & jurnal pencairan oleh Finance.</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 pt-8 border-t border-slate-800/80">
            <div class="text-xs text-slate-400">© 2026 BizJourney · Hak Cipta Dilindungi · Uji Kompetensi Keahlian (UKK)</div>
        </div>
    </div>

    <!-- Right Side Login Form & Demo Selector -->
    <div class="lg:col-span-6 flex items-center justify-center p-6 sm:p-10 bg-[#f6f8fa] overflow-y-auto">
        <div class="w-full max-w-lg space-y-6">

            <!-- Mobile Brand -->
            <div class="lg:hidden text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#121824] text-amber-400 font-bold text-xl mb-2 shadow-lg">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <h1 class="text-2xl font-black text-slate-900">BizJourney</h1>
                <p class="text-xs text-amber-600 font-medium italic">Corporate Travel Management</p>
            </div>

            <!-- Flash Error / Success -->
            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Login Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8 sm:p-10">
                <div class="mb-6">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-xs text-slate-500 mt-1">Masuk dengan kredensial perusahaan Anda (Session Web Login).</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">Email Perusahaan</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            <input name="email" value="{{ old('email') }}" type="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none font-medium text-slate-800 transition" placeholder="nama@purnama.co.id">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">Password</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input name="password" type="password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none font-medium text-slate-800 transition" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs font-medium pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                            <input name="remember" type="checkbox" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-sm bg-amber-500 text-slate-950 hover:bg-amber-400 active:scale-[0.99] transition shadow-lg shadow-amber-500/20 cursor-pointer">
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>

            <!-- QUICK LOGIN / TESTER PANEL -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Akses Cepat Akun Demo (1-Click Test)
                    </h3>
                    <span class="text-[10px] text-slate-400 font-medium">Password: password123</span>
                </div>
                <p class="text-[11px] text-slate-500 mb-4">
                    Klik salah satu tombol akun di bawah ini untuk langsung masuk dan menguji fitur sesuai role:
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @forelse($demoUsers as $du)
                        <a href="{{ route('quick.login', $du->id) }}" class="p-2.5 rounded-xl border border-slate-200 hover:border-amber-500 hover:bg-amber-50/50 transition text-left group">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded
                                    @if($du->role === 'SUPER_ADMIN') bg-purple-100 text-purple-700
                                    @elseif($du->role === 'ADMIN') bg-blue-100 text-blue-700
                                    @elseif($du->role === 'MANAGER') bg-indigo-100 text-indigo-700
                                    @elseif($du->role === 'HRD') bg-rose-100 text-rose-700
                                    @elseif($du->role === 'FINANCE') bg-emerald-100 text-emerald-700
                                    @else bg-amber-100 text-amber-800
                                    @endif">
                                    {{ $du->role }}
                                </span>
                            </div>
                            <div class="text-xs font-bold text-slate-800 mt-1 truncate group-hover:text-amber-700">{{ $du->name }}</div>
                            <div class="text-[10px] text-slate-400 truncate">{{ $du->department->name ?? 'Dept' }}</div>
                        </a>
                    @empty
                        <div class="text-xs text-slate-500 col-span-3">Tidak ada akun demo.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</body>
</html>

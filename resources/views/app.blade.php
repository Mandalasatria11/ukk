<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BizJourney — Corporate Travel Management</title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f8fa; }
  [x-cloak] { display: none !important; }
  .glass { background: rgba(255,255,255,.85); backdrop-filter: blur(14px); }
  .card { bg-white rounded-2xl shadow-sm border border-slate-200/80 bg-white; }
  .btn { inline-flex items-center gap-2 px-4 py-2 rounded-xl font-medium text-sm transition cursor-pointer; }
  .btn-primary { bg-amber-600 text-white hover:bg-amber-700 shadow-sm; }
  .btn-gold { background-color: #a17c08; color: #ffffff; }
  .btn-gold:hover { background-color: #886805; }
  .btn-ghost { bg-slate-100 text-slate-700 hover:bg-slate-200; }
  .input { w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none bg-white; }
  .badge { px-2.5 py-0.5 rounded-full text-xs font-semibold; }
</style>
</head>
<body class="bg-[#f6f8fa] text-slate-800 min-h-screen" x-data="app()" x-init="init()" x-cloak>

<!-- LOGIN -->
<div x-show="!token" class="min-h-screen grid lg:grid-cols-12 bg-[#f6f8fa]">
  <!-- Left Side Hero (Navy BizJourney Theme) -->
  <div class="hidden lg:flex lg:col-span-6 flex-col justify-between p-12 bg-[#18202F] text-white relative overflow-hidden">
    <!-- Ambient Gold Glows -->
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
          <p class="text-xs text-amber-400/90 font-medium italic tracking-wide">Manage Every Business Journey</p>
        </div>
      </div>

      <!-- Main Headline -->
      <div class="mt-16 space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold tracking-wide uppercase">
          <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Corporate Travel Platform
        </div>
        <h2 class="text-4xl font-black leading-tight text-white tracking-tight">
          Executive Control for<br>Corporate Travel & Expenses
        </h2>
        <p class="text-slate-300 max-w-md text-sm leading-relaxed">
          Platform terpadu untuk pengajuan perjalanan dinas, approval multi-level, booking itinerary, dan kelola reimbursement secara real-time.
        </p>
      </div>

      <!-- Feature Badges -->
      <div class="mt-10 grid grid-cols-2 gap-4 max-w-md">
        <div class="p-4 rounded-2xl bg-[#232D3F]/80 border border-slate-700/60 backdrop-blur-xs">
          <div class="text-amber-400 font-bold text-sm mb-1">⚡ Multi-Level Approval</div>
          <p class="text-xs text-slate-400">Persetujuan terpusat untuk Manager, HRD & Dept Head.</p>
        </div>
        <div class="p-4 rounded-2xl bg-[#232D3F]/80 border border-slate-700/60 backdrop-blur-xs">
          <div class="text-amber-400 font-bold text-sm mb-1">📊 Budget Analytics</div>
          <p class="text-xs text-slate-400">Monitoring konsumsi anggaran departemen real-time.</p>
        </div>
      </div>
    </div>

    <!-- Testimonial Quote Footer -->
    <div class="relative z-10 pt-8 border-t border-slate-800/80">
      <blockquote class="text-xs text-slate-400 italic">
        "Mengotomatisasi alur perjalanan dinas perusahaan dengan akurasi penganggaran dan kepatuhan kebijakan 100%."
      </blockquote>
      <div class="mt-2 text-xs font-semibold text-slate-300">© 2026 BizJourney · Purnama Corporation</div>
    </div>
  </div>

  <!-- Right Side Login Form -->
  <div class="lg:col-span-6 flex items-center justify-center p-6 sm:p-12 bg-[#f6f8fa]">
    <div class="w-full max-w-md">
      <!-- Mobile Logo Header -->
      <div class="lg:hidden text-center mb-8">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#18202F] text-amber-400 font-bold text-xl mb-2 shadow-lg">
          <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-[#18202F]">BizJourney</h1>
        <p class="text-xs text-amber-600 font-medium italic">Manage Every Business Journey</p>
      </div>

      <!-- Login Card -->
      <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8 sm:p-10">
        <div class="mb-6">
          <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang</h2>
          <p class="text-xs text-slate-500 mt-1">Masuk dengan akun kredensial perusahaan Anda.</p>
        </div>

        <form @submit.prevent="login" class="space-y-4">
          <div>
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">Email Perusahaan</label>
            <div class="relative">
              <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
              <input x-model="loginForm.email" type="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none font-medium text-slate-800 transition" placeholder="nama@andrea.co.id">
            </div>
          </div>

          <div>
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">Password</label>
            <div class="relative">
              <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input x-model="loginForm.password" type="password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none font-medium text-slate-800 transition" placeholder="••••••••">
            </div>
          </div>

          <div class="flex items-center justify-between text-xs font-medium pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600">
              <input type="checkbox" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
              <span>Ingat saya</span>
            </label>
            <a href="#" class="text-amber-700 font-bold hover:underline">Lupa password?</a>
          </div>

          <p x-show="loginError" x-text="loginError" class="text-xs text-red-600 font-bold bg-red-50 p-2.5 rounded-xl border border-red-200"></p>

          <button type="submit" class="w-full py-3.5 px-4 bg-[#856c12] hover:bg-[#9a7d15] active:scale-[0.99] text-white rounded-xl font-extrabold text-sm shadow-lg shadow-amber-900/10 transition flex items-center justify-center gap-2 cursor-pointer">
            Masuk ke Dashboard →
          </button>
        </form>

        <!-- Quick Demo Account Chips -->
        <div class="mt-8 pt-6 border-t border-slate-100">
          <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center justify-between">
            <span>Pilih Akun Demo (Click to fill)</span>
            <span class="text-slate-400 font-mono text-[10px]">pass: password123</span>
          </div>
          <div class="flex flex-wrap gap-1.5">
            <button type="button" @click="loginForm.email='admin@andrea.co.id'; loginForm.password='password123'" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
              👑 Admin
            </button>
            <button type="button" @click="loginForm.email='superadmin@andrea.co.id'; loginForm.password='password123'" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
              ⭐ SuperAdmin
            </button>
            <button type="button" @click="loginForm.email='finance@andrea.co.id'; loginForm.password='password123'" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
              💰 Finance
            </button>
            <button type="button" @click="loginForm.email='manager@andrea.co.id'; loginForm.password='password123'" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
              👔 Manager
            </button>
            <button type="button" @click="loginForm.email='hrd@andrea.co.id'; loginForm.password='password123'" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
              🏢 HRD
            </button>
            <button type="button" @click="loginForm.email='azka@andrea.co.id'; loginForm.password='password123'" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
              👤 Employee
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- APP SHELL -->
<div x-show="token" class="min-h-screen flex bg-[#f6f8fa]">
  <!-- SIDEBAR (BizJourney Dark Navy Theme) -->
  <aside class="w-64 bg-[#18202F] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen">
    <div>
      <!-- Brand Logo -->
      <div class="p-6 pb-4">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center font-bold text-amber-400 shadow-md">
            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
          </div>
          <div>
            <div class="font-extrabold text-xl tracking-tight text-white">BizJourney</div>
            <div class="text-[11px] text-amber-400/90 font-medium italic">Manage Every Business Journey</div>
          </div>
        </div>
      </div>

      <!-- New Request Button -->
      <div class="px-4 my-2">
        <button x-show="isEmployee" @click="openTravelForm()" class="w-full py-2.5 px-4 bg-[#856c12] hover:bg-[#9a7d15] text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 shadow-md transition">
          <span class="text-lg leading-none">+</span> New Request
        </button>
      </div>

      <!-- Navigation Links (Role Based) -->
      <nav class="px-3 mt-4 space-y-1 text-sm font-medium">
        <template x-for="item in navItems" :key="item.key">
          <button x-show="canAccess(item)" @click="view=item.key"
            :class="view===item.key?'bg-[#232D3F] text-amber-400 border-l-4 border-amber-500 shadow-sm font-semibold':'text-slate-400 hover:bg-[#232D3F]/60 hover:text-white'"
            class="w-full text-left px-3.5 py-2.5 rounded-xl flex items-center gap-3 transition">
            <span class="text-lg" x-html="item.iconSvg"></span>
            <span x-text="navLabel(item)"></span>
            <span x-show="item.key==='approvals' && approvalsPending.length" class="ml-auto text-[10px] bg-amber-500 text-slate-900 font-bold px-1.5 py-0.5 rounded-full" x-text="approvalsPending.length"></span>
            <span x-show="item.key==='reimbursements' && isFinance && reimsWaitingCount" class="ml-auto text-[10px] bg-amber-500 text-slate-900 font-bold px-1.5 py-0.5 rounded-full" x-text="reimsWaitingCount"></span>
          </button>
        </template>
      </nav>

      <!-- Role Info Badge -->
      <div class="mx-3 mt-4 p-3 rounded-xl bg-[#232D3F]/80 border border-slate-700/60">
        <div class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">Role Aktif</div>
        <div class="text-xs text-amber-400 font-bold" x-text="roleLabel"></div>
        <div class="text-[10px] text-slate-400 mt-0.5 leading-snug" x-text="roleDescription"></div>
      </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 space-y-3">
      <div class="space-y-1 text-xs">
        <button class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-3 text-slate-400 hover:bg-[#232D3F] hover:text-white transition">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Support
        </button>
        <button @click="logout" class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-3 text-slate-400 hover:bg-red-950/40 hover:text-red-300 transition">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Log Out
        </button>
      </div>
      <div class="pt-3 border-t border-slate-800 flex items-center gap-3 px-1">
        <div class="h-9 w-9 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold flex items-center justify-center text-sm" x-text="user?.name ? user.name[0].toUpperCase() : 'U'"></div>
        <div class="min-w-0 flex-1">
          <div class="text-xs font-bold text-white truncate" x-text="user?.name"></div>
          <div class="text-[10px] text-amber-400 uppercase font-semibold tracking-wider truncate" x-text="user?.role"></div>
        </div>
      </div>
    </div>
  </aside>

  <!-- MAIN CONTENT AREA -->
  <main class="flex-1 flex flex-col min-w-0 bg-[#f6f8fa]">
    <!-- TOP HEADER BAR -->
    <header class="bg-white border-b border-slate-200/80 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-xs">
      <div>
        <h2 class="font-bold text-xl text-slate-900" x-text="viewTitle"></h2>
        <p class="text-xs text-slate-500" x-text="viewSubtitle"></p>
      </div>

      <div class="flex items-center gap-4">
        <!-- Search Bar -->
        <div class="relative hidden sm:block w-64">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" placeholder="Search itineraries..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-1.5 text-xs focus:ring-2 focus:ring-amber-500 outline-none text-slate-700">
        </div>

        <!-- Notification Bell -->
        <button @click="view='notifications'" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/></svg>
          <span x-show="unread>0" class="absolute top-1 right-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold px-1" x-text="unread"></span>
        </button>

        <!-- Settings Icon -->
        <button class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </button>

        <!-- User Avatar Profile -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
          <div class="h-9 w-9 rounded-full bg-slate-900 text-amber-400 font-bold flex items-center justify-center text-sm shadow-sm" x-text="user?.name ? user.name[0].toUpperCase() : 'U'"></div>
        </div>
      </div>
    </header>

    <div class="flex-1 p-8 overflow-auto space-y-6">

      <!-- DASHBOARD — ADMIN / SUPER_ADMIN (GLOBAL OVERVIEW DESIGN FROM FIGMA) -->
      <section x-show="view==='dashboard' && isAdmin" class="space-y-6">
        <!-- Top Title & Export Action -->
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-2xl text-slate-900 tracking-tight">Global Overview</h3>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring Q3 Corporate Travel Budget & Alerts</p>
          </div>
          <button @click="exportReport" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Report
          </button>
        </div>

        <!-- Layout Grid: 3 KPI Cards Top -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <!-- Card 1: TOTAL ANGGARAN DIGUNAKAN -->
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Anggaran Digunakan</span>
              <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
            </div>
            <div class="mt-2 font-black text-2xl text-slate-900" x-text="formatRp(totalExpenseCalculated)"></div>
            <div class="mt-2 text-xs text-indigo-600 font-semibold flex items-center gap-1">
              <span>↗ +12% dari bulan lalu</span>
            </div>
          </div>

          <!-- Card 2: PERJALANAN AKTIF -->
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Perjalanan Aktif</span>
              <div class="p-1.5 rounded-lg bg-slate-50 text-slate-700 border border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
              </div>
            </div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="activeTravelsCount"></div>
            <div class="mt-2 text-xs text-slate-500 font-medium">
              Menunggu persetujuan: <span class="font-bold text-slate-800" x-text="approvalsPending.length"></span>
            </div>
          </div>

          <!-- Card 3: RATA-RATA BIAYA / PERJALANAN -->
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rata-rata Biaya / Perjalanan</span>
              <div class="p-1.5 rounded-lg bg-slate-50 text-slate-700 border border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
              </div>
            </div>
            <div class="mt-2 font-black text-2xl text-slate-900" x-text="formatRp(avgExpensePerTravel)"></div>
            <div class="mt-2 text-xs text-red-600 font-semibold flex items-center gap-1">
              <span>↗ +5% dari target</span>
            </div>
          </div>
        </div>

        <!-- Middle Main Grid (2 Columns: Left 8 cols Chart, Right 4 cols Active Alerts) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
          <!-- Department Budget Usage Chart Box -->
          <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
              <h4 class="font-extrabold text-base text-slate-900">Konsumsi Anggaran Departemen</h4>
              <select class="bg-slate-50 border border-slate-200 text-xs text-slate-700 rounded-lg px-3 py-1.5 font-medium outline-none">
                <option>Tahun Ini</option>
                <option>Bulan Ini</option>
              </select>
            </div>

            <!-- Visual Bar Chart Representation -->
            <div class="space-y-4 py-2">
              <template x-for="d in (expenseByDept.length ? expenseByDept : sampleDepartmentData)" :key="d.department||d.name">
                <div>
                  <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-slate-700" x-text="d.department||d.name"></span>
                    <span class="text-slate-900" x-text="formatRp(d.totalExpense||d.amount)"></span>
                  </div>
                  <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden flex">
                    <div class="h-full bg-indigo-900 rounded-l-full" :style="'width: ' + Math.min(80, ((d.totalExpense||d.amount)/100000000)*60) + '%'"></div>
                    <div class="h-full bg-indigo-300" :style="'width: ' + Math.min(30, ((d.totalExpense||d.amount)/100000000)*25) + '%'"></div>
                  </div>
                </div>
              </template>
            </div>

            <!-- Legend at Bottom -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-center gap-6 text-[11px] text-slate-600 font-medium">
              <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-900 inline-block"></span> Digunakan (Aman)</div>
              <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-300 inline-block"></span> Mendekati Pagu</div>
              <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span> Melebihi Anggaran</div>
            </div>
          </div>

          <!-- Active Alerts Column -->
          <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
              <h4 class="font-extrabold text-base text-slate-900">Active Alerts</h4>
              <span class="h-5 min-w-5 px-1.5 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold">3</span>
            </div>

            <div class="space-y-3">
              <div class="p-3.5 bg-slate-50/80 rounded-xl border-l-4 border-red-500 text-xs">
                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                  <span class="text-red-500">⚠️</span> Budget Exceeded: APAC
                </div>
                <p class="text-slate-500 mt-1 leading-relaxed">Regional spend is 15% over allocated Q3 budget.</p>
              </div>

              <div class="p-3.5 bg-slate-50/80 rounded-xl border-l-4 border-amber-500 text-xs">
                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                  <span class="text-amber-500">🧳</span> Route Disruption: LHR
                </div>
                <p class="text-slate-500 mt-1 leading-relaxed">3 executive flights delayed due to weather.</p>
              </div>

              <div class="p-3.5 bg-slate-50/80 rounded-xl border-l-4 border-red-500 text-xs">
                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                  <span class="text-red-500">🛡️</span> Policy Violation
                </div>
                <p class="text-slate-500 mt-1 leading-relaxed">Unapproved First Class booking by E. Carter.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Table: Recent High-Value Itineraries -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-extrabold text-base text-slate-900">Recent High-Value Itineraries</h4>
            <button @click="view='travel'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline tracking-wide uppercase">View All</button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                  <th class="py-3 px-3">Traveler</th>
                  <th class="py-3 px-3">Destination</th>
                  <th class="py-3 px-3">Dates</th>
                  <th class="py-3 px-3">Class</th>
                  <th class="py-3 px-3">Cost</th>
                  <th class="py-3 px-3 text-right">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                <template x-for="t in travels.slice(0,5)" :key="t.id">
                  <tr class="hover:bg-slate-50/80 transition">
                    <td class="py-3 px-3 font-semibold text-slate-900 flex items-center gap-2.5">
                      <div class="h-8 w-8 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs" x-text="t.destination ? t.destination[0] : 'T'"></div>
                      <div>
                        <div x-text="'User #'+(t.user_id||t.id)"></div>
                        <div class="text-[10px] text-slate-400 font-normal" x-text="t.purpose||'Executive Travel'"></div>
                      </div>
                    </td>
                    <td class="py-3 px-3 font-semibold text-slate-800" x-text="t.destination"></td>
                    <td class="py-3 px-3 text-slate-500" x-text="t.start_date + ' → ' + t.end_date"></td>
                    <td class="py-3 px-3">
                      <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-[#1c2434] text-amber-400 tracking-wider">FIRST CLASS</span>
                    </td>
                    <td class="py-3 px-3 font-bold text-slate-900" x-text="formatRp(t.estimated_cost)"></td>
                    <td class="py-3 px-3 text-right">
                      <span class="badge" :class="statusClass(t.status)" x-text="t.status"></span>
                    </td>
                  </tr>
                </template>
                <template x-if="!travels.length">
                  <tr>
                    <td colspan="6" class="text-center py-6 text-slate-400 text-xs">Belum ada data perjalanan.</td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- DASHBOARD — EMPLOYEE (Travel Saya) -->
      <section x-show="view==='dashboard' && isEmployee" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-2xl text-slate-900 tracking-tight">Travel Saya</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pantau pengajuan perjalanan dinas & reimbursement Anda</p>
          </div>
          <div class="flex gap-2">
            <button @click="view='reimbursements'" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition">+ Klaim Reimbursement</button>
            <button @click="openTravelForm()" class="px-4 py-2 bg-[#856c12] hover:bg-[#9a7d15] text-white text-xs font-bold rounded-xl shadow-sm transition">+ Ajukan Perjalanan</button>
          </div>
        </div>

        <!-- KPI Milik Karyawan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pengajuan</span>
              <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60">📄</div>
            </div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travels.length"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Seluruh riwayat pengajuan Anda</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Draft</span>
              <div class="p-1.5 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">📝</div>
            </div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travelCountByStatus('DRAFT')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Belum disubmit ke approver</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Proses Approval</span>
              <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/60">⏳</div>
            </div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travelCountByStatus('SUBMITTED')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Menunggu keputusan Manager/HRD</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-start">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Disetujui</span>
              <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/60">✅</div>
            </div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travelCountByStatus('APPROVED') + travelCountByStatus('COMPLETED')"></div>
            <div class="mt-1 text-xs text-emerald-600 font-semibold">Siap dipesan & berangkat</div>
          </div>
        </div>

        <!-- Pengajuan Milik Saya -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-extrabold text-base text-slate-900">Pengajuan Perjalanan Saya</h4>
            <button @click="view='travel'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline uppercase">Lihat Semua</button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5 text-left">Destinasi</th>
                <th class="px-5 py-3.5 text-left">Tujuan</th>
                <th class="px-5 py-3.5 text-left">Tanggal</th>
                <th class="px-5 py-3.5 text-left">Estimasi</th>
                <th class="px-5 py-3.5 text-right">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <template x-for="t in travels.slice(0,6)" :key="t.id">
                <tr class="hover:bg-slate-50/80 transition cursor-pointer" @click="viewTravel(t.id)">
                  <td class="px-5 py-3.5 font-bold text-slate-900" x-text="t.destination"></td>
                  <td class="px-5 py-3.5 text-slate-600 truncate max-w-[200px]" x-text="t.purpose"></td>
                  <td class="px-5 py-3.5 text-slate-500" x-text="t.start_date+' → '+t.end_date"></td>
                  <td class="px-5 py-3.5 font-semibold" x-text="formatRp(t.estimated_cost)"></td>
                  <td class="px-5 py-3.5 text-right"><span class="badge" :class="statusClass(t.status)" x-text="t.status"></span></td>
                </tr>
              </template>
              <template x-if="!travels.length">
                <tr><td colspan="5" class="text-center py-6 text-slate-400 text-xs">Belum ada pengajuan. Klik <b>+ Ajukan Perjalanan</b> untuk memulai.</td></tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Reimbursement Saya -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-extrabold text-base text-slate-900">Reimbursement Saya</h4>
            <button @click="view='reimbursements'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline uppercase">Kelola</button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5 text-left">ID</th>
                <th class="px-5 py-3.5 text-left">Travel</th>
                <th class="px-5 py-3.5 text-left">Estimasi</th>
                <th class="px-5 py-3.5 text-left">Disetujui</th>
                <th class="px-5 py-3.5 text-right">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <template x-for="r in reimbursements.slice(0,5)" :key="r.id">
                <tr class="hover:bg-slate-50/80 transition cursor-pointer" @click="viewReimbursement(r.id)">
                  <td class="px-5 py-3.5 font-bold text-slate-900" x-text="'#'+r.id"></td>
                  <td class="px-5 py-3.5" x-text="'Travel #'+r.travel_request_id"></td>
                  <td class="px-5 py-3.5 text-slate-600" x-text="formatRp(r.advance_amount)"></td>
                  <td class="px-5 py-3.5 font-semibold" x-text="formatRp(r.approved_amount)"></td>
                  <td class="px-5 py-3.5 text-right"><span class="badge" :class="statusClass(r.status)" x-text="r.status"></span></td>
                </tr>
              </template>
              <template x-if="!reimbursements.length">
                <tr><td colspan="5" class="text-center py-6 text-slate-400 text-xs">Belum ada klaim reimbursement.</td></tr>
              </template>
            </tbody>
          </table>
        </div>
      </section>

      <!-- DASHBOARD — MANAGER / DEPARTMENT_HEAD (Approval Command Center) -->
      <section x-show="view==='dashboard' && isApprover" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-2xl text-slate-900 tracking-tight">Approval Command Center</h3>
            <p class="text-xs text-slate-500 mt-0.5">Review dan putuskan perjalanan dinas tim Anda</p>
          </div>
          <button @click="view='approvals'" class="px-4 py-2 bg-[#856c12] hover:bg-[#9a7d15] text-white text-xs font-bold rounded-xl shadow-sm transition">Buka Antrean Approval</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-amber-500 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Keputusan</div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="approvalsPending.length"></div>
            <div class="mt-1 text-xs text-red-500 font-semibold">Butuh tindakan Anda</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-emerald-500 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Perjalanan Tim Disetujui</div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travelCountByStatus('APPROVED') + travelCountByStatus('COMPLETED')"></div>
            <div class="mt-1 text-xs text-emerald-600 font-semibold">Berjalan sesuai rencana</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-blue-500 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Perjalanan</div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travels.length"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Cakupan departemen Anda</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-fuchsia-500 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Delegasi Wewenang</div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="delegations.length"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Sedang aktif terdaftar</div>
          </div>
        </div>

        <!-- Quick Approve Queue -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-extrabold text-base text-slate-900">Antrean Approval Cepat</h4>
            <button @click="view='approvals'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline uppercase">Lihat Detail</button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5 text-left">Request</th>
                <th class="px-5 py-3.5 text-left">Level</th>
                <th class="px-5 py-3.5 text-left">Tanggal Masuk</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <template x-for="a in approvalsPending.slice(0,5)" :key="a.id">
                <tr class="hover:bg-slate-50/80 transition">
                  <td class="px-5 py-3.5 font-bold text-slate-900" x-text="'Travel #'+a.travel_request_id"></td>
                  <td class="px-5 py-3.5 text-amber-600 font-semibold" x-text="'Level '+a.level"></td>
                  <td class="px-5 py-3.5 text-slate-500" x-text="a.created_at ? String(a.created_at).slice(0,10) : '-'"></td>
                  <td class="px-5 py-3.5 text-right space-x-2">
                    <button @click="decideApproval(a.id,'REJECTED')" class="px-3 py-1.5 border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-lg font-semibold">Tolak</button>
                    <button @click="decideApproval(a.id,'APPROVED')" class="px-3 py-1.5 bg-amber-600 text-white hover:bg-amber-700 rounded-lg font-semibold">Setujui</button>
                  </td>
                </tr>
              </template>
              <template x-if="!approvalsPending.length">
                <tr><td colspan="4" class="text-center py-6 text-slate-400 text-xs">🎉 Tidak ada approval menunggu.</td></tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Delegasi -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-extrabold text-base text-slate-900">Delegasi Wewenang</h4>
            <span class="text-xs text-slate-500">Saat Anda cuti/lugas, approval dialihkan</span>
          </div>
          <form @submit.prevent="createDelegation" class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-4">
            <input x-model="newDeleg.delegateId" type="number" placeholder="Delegate User ID" class="input text-sm">
            <input x-model="newDeleg.startDate" type="date" class="input text-sm">
            <input x-model="newDeleg.endDate" type="date" class="input text-sm">
            <input x-model="newDeleg.reason" placeholder="Alasan" class="input text-sm">
            <div class="md:col-span-4"><button type="submit" class="btn btn-primary text-sm">Buat Delegasi</button></div>
          </form>
          <ul class="text-sm divide-y divide-slate-100">
            <template x-for="d in delegations" :key="d.id">
              <li class="py-2.5 flex justify-between"><span>Delegasi #<span x-text="d.id"></span>: <span x-text="d.delegator_id+' → '+d.delegate_id"></span></span><span class="text-slate-500 text-xs" x-text="d.start_date+' → '+d.end_date+' · '+d.reason"></span></li>
            </template>
            <li x-show="!delegations.length" class="py-3 text-center text-slate-400 text-xs">Belum ada delegasi.</li>
          </ul>
        </div>
      </section>

      <!-- DASHBOARD — HRD (Ringkasan SDM & Kepatuhan) -->
      <section x-show="view==='dashboard' && isHrd" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-2xl text-slate-900 tracking-tight">HRD Control Center</h3>
            <p class="text-xs text-slate-500 mt-0.5">Kepatuhan kebijakan perjalanan & antrean persetujuan HRD</p>
          </div>
          <button @click="view='approvals'" class="px-4 py-2 bg-[#856c12] hover:bg-[#9a7d15] text-white text-xs font-bold rounded-xl shadow-sm transition">Review Approval</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Persetujuan HRD</div>
            <div class="mt-2 font-black text-3xl text-amber-600" x-text="approvalsPending.length"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Final level approval</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengajuan Aktif</div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="travelCountByStatus('SUBMITTED')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Sedang dalam proses</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai Perjalanan</div>
            <div class="mt-2 font-black text-3xl text-emerald-600" x-text="travelCountByStatus('COMPLETED')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Siap reimbursement</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ditolak / Dibatalkan</div>
            <div class="mt-2 font-black text-3xl text-red-500" x-text="travelCountByStatus('REJECTED') + travelCountByStatus('CANCELLED')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Perlu ditinjau kebijakan</div>
          </div>
        </div>

        <!-- Distribusi Status Perjalanan -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
          <h4 class="font-extrabold text-base text-slate-900 mb-4">Distribusi Status Perjalanan Dinas</h4>
          <ul class="space-y-3">
            <template x-for="s in dashboardByStatus.length ? dashboardByStatus : fallbackStatusCount" :key="s.status">
              <li class="flex items-center justify-between text-sm">
                <span class="text-slate-700 font-medium" x-text="s.status"></span>
                <div class="flex items-center gap-3">
                  <div class="h-2.5 w-48 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-amber-500 rounded-full" :style="'width:'+Math.min(100, (s.total / (travels.length||1)) * 100)+'%'"></div></div>
                  <span class="font-bold text-slate-900 w-8 text-right" x-text="s.total"></span>
                </div>
              </li>
            </template>
          </ul>
        </div>

        <!-- Antrean Approval HRD -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-extrabold text-base text-slate-900">Antrean Approval</h4>
            <button @click="view='approvals'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline uppercase">Buka Command Center</button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5 text-left">Request</th>
                <th class="px-5 py-3.5 text-left">Level</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <template x-for="a in approvalsPending.slice(0,5)" :key="a.id">
                <tr class="hover:bg-slate-50/80 transition">
                  <td class="px-5 py-3.5 font-bold text-slate-900" x-text="'Travel #'+a.travel_request_id"></td>
                  <td class="px-5 py-3.5 text-amber-600 font-semibold" x-text="'Level '+a.level"></td>
                  <td class="px-5 py-3.5 text-right space-x-2">
                    <button @click="decideApproval(a.id,'REJECTED')" class="px-3 py-1.5 border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-lg font-semibold">Tolak</button>
                    <button @click="decideApproval(a.id,'APPROVED')" class="px-3 py-1.5 bg-amber-600 text-white hover:bg-amber-700 rounded-lg font-semibold">Setujui</button>
                  </td>
                </tr>
              </template>
              <template x-if="!approvalsPending.length">
                <tr><td colspan="3" class="text-center py-6 text-slate-400 text-xs">🎉 Tidak ada approval menunggu.</td></tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Delegasi HRD -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-extrabold text-base text-slate-900">Delegasi Wewenang HRD</h4>
            <span class="text-xs text-slate-500">Alihkan hak approval ke HRD lain</span>
          </div>
          <form @submit.prevent="createDelegation" class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-4">
            <input x-model="newDeleg.delegateId" type="number" placeholder="Delegate User ID" class="input text-sm">
            <input x-model="newDeleg.startDate" type="date" class="input text-sm">
            <input x-model="newDeleg.endDate" type="date" class="input text-sm">
            <input x-model="newDeleg.reason" placeholder="Alasan" class="input text-sm">
            <div class="md:col-span-4"><button type="submit" class="btn btn-primary text-sm">Buat Delegasi</button></div>
          </form>
          <ul class="text-sm divide-y divide-slate-100">
            <template x-for="d in delegations" :key="d.id">
              <li class="py-2.5 flex justify-between"><span>Delegasi #<span x-text="d.id"></span>: <span x-text="d.delegator_id+' → '+d.delegate_id"></span></span><span class="text-slate-500 text-xs" x-text="d.start_date+' → '+d.end_date+' · '+d.reason"></span></li>
            </template>
            <li x-show="!delegations.length" class="py-3 text-center text-slate-400 text-xs">Belum ada delegasi.</li>
          </ul>
        </div>
      </section>

      <!-- DASHBOARD — FINANCE (Verifikasi Keuangan) -->
      <section x-show="view==='dashboard' && isFinance" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-2xl text-slate-900 tracking-tight">Finance Verification Hub</h3>
            <p class="text-xs text-slate-500 mt-0.5">Verifikasi, setujui, dan bayar reimbursement karyawan</p>
          </div>
          <button @click="view='reimbursements'" class="px-4 py-2 bg-[#856c12] hover:bg-[#9a7d15] text-white text-xs font-bold rounded-xl shadow-sm transition">Buka Antrean Verifikasi</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Verifikasi</div>
            <div class="mt-2 font-black text-3xl text-amber-600" x-text="reimsWaitingCount"></div>
            <div class="mt-1 text-xs text-amber-700 font-semibold">SUBMITTED — perlu tindakan</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Disetujui (APPROVED)</div>
            <div class="mt-2 font-black text-3xl text-emerald-600" x-text="reimsCountByStatus('APPROVED')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Siap dibayarkan</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sudah Dibayar (PAID)</div>
            <div class="mt-2 font-black text-3xl text-slate-900" x-text="reimsCountByStatus('PAID')"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Selesai periode ini</div>
          </div>
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Kewajiban Bayar</div>
            <div class="mt-2 font-black text-xl text-slate-900" x-text="formatRp(totalApprovedReimbursement)"></div>
            <div class="mt-1 text-xs text-slate-500 font-medium">Aproved + Paid</div>
          </div>
        </div>

        <!-- Antrean Verifikasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-extrabold text-base text-slate-900">Antrean Verifikasi Reimbursement</h4>
            <button @click="view='reimbursements'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline uppercase">Kelola Semua</button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5 text-left">ID</th>
                <th class="px-5 py-3.5 text-left">Travel</th>
                <th class="px-5 py-3.5 text-left">Advance</th>
                <th class="px-5 py-3.5 text-left">Status</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <template x-for="r in reimbursements.filter(x=>['SUBMITTED','APPROVED'].includes(x.status)).slice(0,6)" :key="r.id">
                <tr class="hover:bg-slate-50/80 transition">
                  <td class="px-5 py-3.5 font-bold text-slate-900" x-text="'#'+r.id"></td>
                  <td class="px-5 py-3.5" x-text="'Travel #'+r.travel_request_id"></td>
                  <td class="px-5 py-3.5" x-text="formatRp(r.advance_amount)"></td>
                  <td class="px-5 py-3.5"><span class="badge" :class="statusClass(r.status)" x-text="r.status"></span></td>
                  <td class="px-5 py-3.5 text-right">
                    <button @click="view='reimbursements'; viewReimbursement(r.id)" class="px-3 py-1.5 bg-amber-600 text-white hover:bg-amber-700 rounded-lg font-semibold">
                      <span x-text="r.status==='APPROVED' ? 'Bayar' : 'Verifikasi'"></span>
                    </button>
                  </td>
                </tr>
              </template>
              <template x-if="!reimbursements.filter(x=>['SUBMITTED','APPROVED'].includes(x.status)).length">
                <tr><td colspan="5" class="text-center py-6 text-slate-400 text-xs">🎉 Tidak ada reimbursement menunggu.</td></tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Laporan Singkat Keuangan -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-extrabold text-base text-slate-900">Expense per Departemen</h4>
            <button @click="view='reports'" class="text-xs font-bold text-slate-600 hover:text-amber-600 underline uppercase">Buka Laporan</button>
          </div>
          <ul class="text-sm divide-y divide-slate-100">
            <template x-for="d in expenseByDept" :key="d.id">
              <li class="flex justify-between py-2.5"><span class="text-slate-700 font-medium" x-text="d.department||'-'"></span><span class="font-bold text-slate-900" x-text="formatRp(d.totalExpense)"></span></li>
            </template>
            <li x-show="!expenseByDept.length" class="py-3 text-center text-slate-400 text-xs">Belum ada data pengeluaran.</li>
          </ul>
        </div>
      </section>

      <!-- TRAVEL -->
      <section x-show="view==='travel'" class="space-y-5">
        <div class="flex justify-between items-center">
          <h3 class="font-bold text-xl text-slate-900">Daftar Travel Request</h3>
          <button x-show="isEmployee" @click="openTravelForm()" class="btn btn-primary">+ Buat Pengajuan</button>
        </div>
        <div x-show="travelFormOpen" class="card p-6">
          <h4 class="font-bold mb-4">Pengajuan Baru</h4>
          <form @submit.prevent="createTravel" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input x-model="newTravel.destination" placeholder="Destinasi" required class="input">
            <input x-model="newTravel.purpose" placeholder="Tujuan / Purpose" required class="input">
            <input x-model="newTravel.startDate" type="date" required class="input">
            <input x-model="newTravel.endDate" type="date" required class="input">
            <input x-model="newTravel.estimatedCost" type="number" placeholder="Estimasi biaya" required class="input">
            <select x-model="newTravel.policyId" required class="input">
              <option value="">Pilih Policy</option>
              <template x-for="p in policies" :key="p.id"><option :value="p.id" x-text="p.name+' ('+p.destination_tier+')'"></option></template>
            </select>
            <div class="md:col-span-2 flex gap-2">
              <button type="submit" class="btn btn-primary">Simpan Draft</button>
              <button type="button" @click="travelFormOpen=false" class="btn btn-ghost">Batal</button>
            </div>
          </form>
        </div>
        <div class="card overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide border-b border-slate-100"><tr>
              <th class="text-left px-5 py-3.5">ID</th><th class="text-left px-5 py-3.5">Destinasi</th><th class="text-left px-5 py-3.5">Tanggal</th><th class="text-left px-5 py-3.5">Est. Biaya</th><th class="text-left px-5 py-3.5">Status</th><th class="text-left px-5 py-3.5">Aksi</th>
            </tr></thead>
            <tbody>
              <template x-for="t in travels" :key="t.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50/60">
                  <td class="px-5 py-3.5 text-slate-500" x-text="t.id"></td>
                  <td class="px-5 py-3.5 font-semibold text-slate-900" x-text="t.destination"></td>
                  <td class="px-5 py-3.5 text-slate-600" x-text="t.start_date+' → '+t.end_date"></td>
                  <td class="px-5 py-3.5 font-semibold" x-text="formatRp(t.estimated_cost)"></td>
                  <td class="px-5 py-3.5"><span class="badge" :class="statusClass(t.status)" x-text="t.status"></span></td>
                  <td class="px-5 py-3.5 flex gap-1.5 flex-wrap">
                    <button @click="viewTravel(t.id)" class="btn btn-ghost text-xs px-2.5 py-1">Detail</button>
                    <button x-show="isEmployee && t.status==='DRAFT'" @click="submitTravel(t.id)" class="btn text-xs px-2.5 py-1 bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Submit</button>
                    <button x-show="isEmployee && t.status==='DRAFT'" @click="deleteTravel(t.id)" class="btn text-xs px-2.5 py-1 bg-red-100 text-red-700 hover:bg-red-200">Hapus</button>
                    <button x-show="isEmployee && !['COMPLETED','CANCELLED'].includes(t.status)" @click="cancelTravel(t.id)" class="btn text-xs px-2.5 py-1 bg-amber-100 text-amber-700 hover:bg-amber-200">Batal</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <div x-show="travelDetail" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-3xl p-7 w-full max-w-2xl max-h-[85vh] overflow-auto shadow-2xl">
            <div class="flex justify-between items-center mb-5">
              <h4 class="font-bold text-lg text-slate-900" x-text="'Detail Travel #'+travelDetail?.travel?.id"></h4>
              <button @click="travelDetail=null" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center">✕</button>
            </div>
            <template x-if="travelDetail">
              <div>
                <div class="grid grid-cols-2 gap-3 text-sm mb-5">
                  <div class="bg-slate-50 rounded-xl p-3"><span class="text-slate-500 block text-xs">Destinasi</span><span class="font-semibold" x-text="travelDetail.travel.destination"></span></div>
                  <div class="bg-slate-50 rounded-xl p-3"><span class="text-slate-500 block text-xs">Status</span><span class="badge mt-1" :class="statusClass(travelDetail.travel.status)" x-text="travelDetail.travel.status"></span></div>
                  <div class="bg-slate-50 rounded-xl p-3"><span class="text-slate-500 block text-xs">Tanggal</span><span class="font-semibold" x-text="travelDetail.travel.start_date+' → '+travelDetail.travel.end_date"></span></div>
                  <div class="bg-slate-50 rounded-xl p-3"><span class="text-slate-500 block text-xs">Est. Biaya</span><span class="font-semibold" x-text="formatRp(travelDetail.travel.estimated_cost)"></span></div>
                  <div class="col-span-2 bg-slate-50 rounded-xl p-3"><span class="text-slate-500 block text-xs">Tujuan</span><span class="font-semibold" x-text="travelDetail.travel.purpose"></span></div>
                </div>
                <h5 class="font-bold mb-2 text-slate-900">Timeline Approval</h5>
                <ul class="space-y-2 mb-5">
                  <template x-for="a in (travelDetail.approvals||[])" :key="a.id">
                    <li class="border border-slate-200 rounded-xl p-3 text-sm flex justify-between items-center">
                      <span>Level <span x-text="a.level"></span> · Approver <span x-text="a.approver_id"></span></span>
                      <span class="badge" :class="statusClass(a.status)" x-text="a.status"></span>
                    </li>
                  </template>
                </ul>
                <div class="flex gap-2 flex-wrap">
                  <button x-show="isEmployee" @click="uploadDoc(travelDetail.travel.id)" class="btn btn-ghost text-xs">Upload Dokumen</button>
                  <button @click="loadDocs(travelDetail.travel.id)" class="btn btn-ghost text-xs">Lihat Dokumen</button>
                </div>
                <ul x-show="docs.length" class="mt-3 text-sm space-y-1">
                  <template x-for="d in docs" :key="d.id"><li class="flex justify-between items-center py-1.5 border-b border-slate-100"><span x-text="d.original_name"></span><button @click="deleteDoc(d.id)" class="text-red-600 text-xs font-medium">hapus</button></li></template>
                </ul>
              </div>
            </template>
          </div>
        </div>
      </section>

      <!-- APPROVALS (APPROVAL COMMAND CENTER DESIGN FROM FIGMA) -->
      <section x-show="view==='approvals' && (isApprover || isHrd)" class="space-y-5">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="font-extrabold text-2xl text-slate-900 tracking-tight">Approval Command Center</h3>
            <p class="text-xs text-slate-500">Review and manage high-priority corporate travel requests.</p>
          </div>
        </div>

        <!-- Approval Metric Cards (Figma Image 3) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-amber-500 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase tracking-wider">
              <span>Pending Approvals</span>
              <span class="text-amber-500">⏳</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-2" x-text="approvalsPending.length"></div>
            <div class="text-xs text-red-500 font-medium mt-1">↗ +3 since yesterday</div>
          </div>

          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-amber-500 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase tracking-wider">
              <span>Approval Rate</span>
              <span class="text-emerald-500">✓</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-2">92%</div>
            <div class="text-xs text-slate-500 font-medium mt-1">→ Steady this month</div>
          </div>

          <div class="bg-white rounded-2xl p-5 border-t-4 border-t-amber-500 border border-slate-200/80 shadow-xs">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase tracking-wider">
              <span>Budget Utilized</span>
              <span class="text-amber-500">💳</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-2" x-text="formatRp(totalExpenseCalculated)"></div>
            <div class="text-xs text-slate-500 font-medium mt-1">65% of Q3 Allocation</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-extrabold text-base text-slate-900">Recent Requests</h4>
            <span class="text-xs text-amber-600 font-bold uppercase tracking-wider">VIEW ALL</span>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5 text-left">Request / Executive</th>
                <th class="px-5 py-3.5 text-left">Destination</th>
                <th class="px-5 py-3.5 text-left">Dates</th>
                <th class="px-5 py-3.5 text-left">Level</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <template x-for="a in approvalsPending" :key="a.id">
                <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-5 py-3.5 font-bold text-slate-900" x-text="'Travel Request #'+a.travel_request_id"></td>
                  <td class="px-5 py-3.5 font-medium text-slate-700" x-text="a.destination||'-'"></td>
                  <td class="px-5 py-3.5 text-slate-500" x-text="a.created_at"></td>
                  <td class="px-5 py-3.5 font-semibold text-amber-600" x-text="'Level '+a.level"></td>
                  <td class="px-5 py-3.5 text-right space-x-2">
                    <button @click="decideApproval(a.id,'REJECTED')" class="px-3 py-1.5 border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-lg text-xs font-semibold">Tolak</button>
                    <button @click="decideApproval(a.id,'APPROVED')" class="px-3 py-1.5 bg-amber-600 text-white hover:bg-amber-700 rounded-lg text-xs font-semibold shadow-xs">Setujui</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
          <div x-show="!approvalsPending.length" class="p-8 text-center text-slate-400 text-sm">Tidak ada approval pending.</div>
        </div>

        <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <h4 class="font-bold text-slate-900 mb-4">Delegasi Wewenang</h4>
          <form @submit.prevent="createDelegation" class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-4">
            <input x-model="newDeleg.delegateId" type="number" placeholder="Delegate User ID" class="input text-sm">
            <input x-model="newDeleg.startDate" type="date" class="input text-sm">
            <input x-model="newDeleg.endDate" type="date" class="input text-sm">
            <input x-model="newDeleg.reason" placeholder="Alasan" class="input text-sm">
            <div class="md:col-span-4"><button type="submit" class="btn btn-primary text-sm">Buat Delegasi</button></div>
          </form>
          <ul class="text-sm divide-y divide-slate-100">
            <template x-for="d in delegations" :key="d.id"><li class="py-2.5 flex justify-between"><span>Delegasi #<span x-text="d.id"></span>: <span x-text="d.delegator_id+' → '+d.delegate_id"></span></span><span class="text-slate-500 text-xs" x-text="d.start_date+' → '+d.end_date+' · '+d.reason"></span></li></template>
          </ul>
        </div>
      </section>

      <!-- REIMBURSEMENTS -->
      <section x-show="view==='reimbursements' && ['EMPLOYEE','FINANCE','ADMIN','SUPER_ADMIN'].includes(user?.role)" class="space-y-5">
        <h3 class="font-bold text-xl text-slate-900">Reimbursement</h3>
        <div x-show="isEmployee" class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <h4 class="font-bold mb-3 text-slate-900">Mulai Reimbursement (travel COMPLETED)</h4>
          <form @submit.prevent="createReimbursement" class="flex gap-2 flex-wrap">
            <input x-model="newReim.travelId" type="number" placeholder="Travel ID" class="input text-sm w-32">
            <input x-model="newReim.advanceAmount" type="number" placeholder="Advance amount" class="input text-sm w-40">
            <button type="submit" class="btn btn-primary text-sm">Buat</button>
          </form>
        </div>
        <div class="card overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide border-b border-slate-100"><tr><th class="px-5 py-3.5 text-left">ID</th><th class="px-5 py-3.5 text-left">Travel</th><th class="px-5 py-3.5 text-left">Status</th><th class="px-5 py-3.5 text-left">Approved</th><th class="px-5 py-3.5 text-left">Aksi</th></tr></thead>
            <tbody>
              <template x-for="r in reimbursements" :key="r.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50/60">
                  <td class="px-5 py-3.5" x-text="r.id"></td>
                  <td class="px-5 py-3.5 font-semibold text-slate-900" x-text="r.travel_request_id"></td>
                  <td class="px-5 py-3.5"><span class="badge" :class="statusClass(r.status)" x-text="r.status"></span></td>
                  <td class="px-5 py-3.5 font-semibold" x-text="formatRp(r.approved_amount)"></td>
                  <td class="px-5 py-3.5 flex gap-1.5 flex-wrap">
                    <button @click="viewReimbursement(r.id)" class="btn btn-ghost text-xs px-2.5 py-1">Detail</button>
                    <button x-show="isEmployee && r.status==='DRAFT'" @click="submitReimbursement(r.id)" class="btn text-xs px-2.5 py-1 bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Submit</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <div x-show="reimDetail" class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-slate-900" x-text="'Reimbursement #'+reimDetail?.reimbursement?.id"></h4>
            <button @click="reimDetail=null" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center">✕</button>
          </div>
          <div x-show="isEmployee && reimDetail?.reimbursement?.status==='DRAFT'" class="mb-4">
            <form @submit.prevent="addItem" class="grid grid-cols-1 md:grid-cols-5 gap-2">
              <select x-model="newItem.category" class="input text-sm"><option>TRANSPORT</option><option>HOTEL</option><option>MEAL</option><option>FLIGHT</option><option>OTHER</option></select>
              <input x-model="newItem.description" placeholder="Deskripsi" class="input text-sm">
              <input x-model="newItem.amount" type="number" placeholder="Jumlah" class="input text-sm">
              <input x-model="newItem.transactionDate" type="date" class="input text-sm">
              <button type="submit" class="btn btn-primary text-sm">Tambah Item</button>
            </form>
          </div>
          <table class="w-full text-sm">
            <thead class="text-slate-500 text-xs uppercase tracking-wide"><tr><th class="text-left py-2">Kategori</th><th class="text-left">Deskripsi</th><th class="text-left">Jumlah</th><th></th></tr></thead>
            <tbody>
              <template x-for="it in (reimDetail?.items||[])" :key="it.id">
                <tr class="border-t border-slate-100">
                  <td class="py-2.5"><span class="badge bg-slate-100 text-slate-700" x-text="it.category"></span></td>
                  <td x-text="it.description"></td>
                  <td x-text="formatRp(it.amount)"></td>
                  <td><button x-show="isEmployee" @click="deleteItem(it.id)" class="text-red-600 text-xs font-medium">hapus</button></td>
                </tr>
              </template>
            </tbody>
          </table>
          <div x-show="isFinance" class="mt-5 flex gap-2 flex-wrap items-end bg-slate-50 rounded-2xl p-4">
            <input x-model="verify.approvedAmount" type="number" placeholder="Approved amount" class="input text-sm w-40">
            <select x-model="verify.status" class="input text-sm w-32"><option>APPROVED</option><option>REJECTED</option></select>
            <input x-model="verify.note" placeholder="Catatan" class="input text-sm flex-1">
            <button @click="verifyReimbursement(reimDetail.reimbursement.id)" class="btn btn-primary text-sm">Verifikasi</button>
            <button x-show="reimDetail?.reimbursement?.status==='APPROVED'" @click="payReimbursement(reimDetail.reimbursement.id)" class="btn text-sm bg-amber-600 text-white hover:bg-amber-700">Bayar</button>
          </div>
        </div>
      </section>

      <!-- BOOKINGS -->
      <section x-show="view==='bookings' && isAdmin" class="space-y-5">
        <h3 class="font-bold text-xl text-slate-900">Pending Bookings (Travel APPROVED)</h3>
        <div class="card overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide border-b border-slate-100"><tr><th class="px-5 py-3.5 text-left">Travel ID</th><th class="px-5 py-3.5 text-left">Destinasi</th><th class="px-5 py-3.5 text-left">Aksi</th></tr></thead>
            <tbody>
              <template x-for="t in pendingBookings" :key="t.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50/60">
                  <td class="px-5 py-3.5" x-text="t.id"></td>
                  <td class="px-5 py-3.5 font-semibold text-slate-900" x-text="t.destination"></td>
                  <td class="px-5 py-3.5"><button @click="createBooking(t.id)" class="btn btn-primary text-xs px-3 py-1.5">Buat Booking</button></td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </section>

      <!-- REPORTS -->
      <section x-show="view==='reports' && ['FINANCE','ADMIN','SUPER_ADMIN'].includes(user?.role)" class="space-y-5">
        <h3 class="font-bold text-xl text-slate-900">Reports</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <h4 class="font-bold mb-3 text-slate-900">Travel per Status</h4>
            <ul class="text-sm divide-y divide-slate-100"><template x-for="s in dashboardByStatus" :key="s.status"><li class="flex justify-between py-2"><span x-text="s.status"></span><span class="font-bold text-amber-600" x-text="s.total"></span></li></template></ul>
          </div>
          <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <h4 class="font-bold mb-3 text-slate-900">Expense by Department</h4>
            <ul class="text-sm divide-y divide-slate-100"><template x-for="d in expenseByDept" :key="d.id"><li class="flex justify-between py-2"><span x-text="d.department||'-'"></span><span class="font-bold" x-text="formatRp(d.totalExpense)"></span></li></template></ul>
          </div>
          <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <h4 class="font-bold mb-3 text-slate-900">Expense by Employee</h4>
            <ul class="text-sm divide-y divide-slate-100"><template x-for="e in expenseByEmp" :key="e.id"><li class="flex justify-between py-2"><span x-text="e.employee"></span><span class="font-bold" x-text="formatRp(e.totalExpense)"></span></li></template></ul>
          </div>
          <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <h4 class="font-bold mb-3 text-slate-900">Expense by Project/Purpose</h4>
            <ul class="text-sm divide-y divide-slate-100"><template x-for="p in expenseByProj" :key="p.project"><li class="flex justify-between py-2"><span x-text="p.project"></span><span class="font-bold" x-text="formatRp(p.totalExpense)"></span></li></template></ul>
          </div>
        </div>
      </section>

      <!-- MASTER DATA -->
      <section x-show="view==='master' && isAdmin" class="space-y-5">
        <h3 class="font-bold text-xl text-slate-900">Master Data</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <h4 class="font-bold mb-3 text-slate-900">Departemen</h4>
            <form @submit.prevent="createDept" class="flex gap-2 mb-4">
              <input x-model="newDept" placeholder="Nama departemen" class="input text-sm flex-1">
              <button class="btn btn-primary text-sm">Tambah</button>
            </form>
            <ul class="text-sm divide-y divide-slate-100"><template x-for="d in departments" :key="d.id"><li class="flex justify-between py-2"><span x-text="d.name"></span><button @click="deleteDept(d.id)" class="text-red-600 text-xs font-medium">hapus</button></li></template></ul>
          </div>
          <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <h4 class="font-bold mb-3 text-slate-900">Jabatan</h4>
            <form @submit.prevent="createPos" class="flex gap-2 mb-4">
              <input x-model="newPos" placeholder="Nama jabatan" class="input text-sm flex-1">
              <button class="btn btn-primary text-sm">Tambah</button>
            </form>
            <ul class="text-sm divide-y divide-slate-100"><template x-for="p in positions" :key="p.id"><li class="flex justify-between py-2"><span x-text="p.name"></span><button @click="deletePos(p.id)" class="text-red-600 text-xs font-medium">hapus</button></li></template></ul>
          </div>
        </div>
        <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <h4 class="font-bold mb-3 text-slate-900">Users</h4>
          <table class="w-full text-sm">
            <thead class="text-slate-500 text-xs uppercase tracking-wide"><tr><th class="text-left py-2">ID</th><th class="text-left">Nama</th><th class="text-left">Email</th><th class="text-left">Role</th><th class="text-left">Aksi</th></tr></thead>
            <tbody>
              <template x-for="u in users" :key="u.id">
                <tr class="border-t border-slate-100">
                  <td class="py-2.5" x-text="u.id"></td>
                  <td class="font-medium text-slate-900" x-text="u.name"></td>
                  <td class="text-slate-600" x-text="u.email"></td>
                  <td><span class="badge bg-amber-100 text-amber-800" x-text="u.role"></span></td>
                  <td><button @click="changeRole(u.id)" class="btn btn-ghost text-xs px-2.5 py-1">Ubah Role</button></td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        <div class="card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <h4 class="font-bold mb-3 text-slate-900">Travel Policies</h4>
          <form @submit.prevent="createPolicy" class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-4">
            <input x-model="newPolicy.name" placeholder="Nama" class="input text-sm">
            <input x-model="newPolicy.positionId" type="number" placeholder="Position ID" class="input text-sm">
            <input x-model="newPolicy.destinationTier" placeholder="Tier" class="input text-sm">
            <input x-model="newPolicy.max_budget" type="number" placeholder="Max Budget" class="input text-sm">
            <div class="md:col-span-4"><button class="btn btn-primary text-sm">Tambah Policy</button></div>
          </form>
          <ul class="text-sm divide-y divide-slate-100"><template x-for="p in policies" :key="p.id"><li class="py-2 flex justify-between"><span x-text="p.name+' — '+p.destination_tier"></span><span class="font-semibold" x-text="formatRp(p.max_budget)"></span></li></template></ul>
        </div>
      </section>

      <!-- NOTIFICATIONS -->
      <section x-show="view==='notifications'" class="space-y-5">
        <div class="flex justify-between items-center">
          <h3 class="font-bold text-xl text-slate-900">Notifikasi</h3>
          <button @click="readAllNotif" class="btn btn-ghost text-sm">Tandai semua dibaca</button>
        </div>
        <div class="card divide-y divide-slate-100 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
          <template x-for="n in notifications" :key="n.id">
            <div class="p-5 flex justify-between items-start gap-4" :class="n.is_read?'opacity-60':''">
              <div>
                <div class="font-semibold text-sm text-slate-900" x-text="n.title"></div>
                <div class="text-sm text-slate-600 mt-0.5" x-text="n.message"></div>
              </div>
              <button x-show="!n.is_read" @click="readNotif(n.id)" class="btn btn-ghost text-xs px-2.5 py-1">Baca</button>
            </div>
          </template>
          <div x-show="!notifications.length" class="p-8 text-center text-slate-400 text-sm">Tidak ada notifikasi.</div>
        </div>
      </section>

    </div>
  </main>
</div>

<script>
function app(){
  return {
    token: localStorage.getItem('token')||'',
    user: JSON.parse(localStorage.getItem('user')||'null'),
    view: 'dashboard',
    navItems: [
      {
        key:'dashboard',
        label:'Dashboard',
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>`
      },
      {
        key:'travel',
        label:'Travel Request',
        roles:['EMPLOYEE','MANAGER','DEPARTMENT_HEAD','HRD','FINANCE','ADMIN','SUPER_ADMIN'],
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>`
      },
      {
        key:'approvals',
        label:'Approvals',
        roles:['MANAGER','DEPARTMENT_HEAD','HRD'],
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`
      },
      {
        key:'reimbursements',
        label:'Reimbursement',
        roles:['EMPLOYEE','FINANCE','ADMIN','SUPER_ADMIN'],
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`
      },
      {
        key:'bookings',
        label:'Bookings',
        roles:['ADMIN','SUPER_ADMIN'],
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 3 3 3h-8.5l-1-1H5a2 2 0 00-2 2zm9-15.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/></svg>`
      },
      {
        key:'reports',
        label:'Reports',
        roles:['FINANCE','ADMIN','SUPER_ADMIN'],
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`
      },
      {
        key:'master',
        label:'Master Data',
        roles:['SUPER_ADMIN','ADMIN'],
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`
      },
      {
        key:'notifications',
        label:'Notifikasi',
        roles:'ALL',
        iconSvg:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/></svg>`
      },
    ],
    loginForm: {email:'',password:''},
    loginError: '',
    travels: [], policies: [], approvalsPending: [], delegations: [],
    reimbursements: [], reimDetail: null, newItem: {category:'HOTEL',description:'',amount:'',transactionDate:''},
    newReim: {travelId:'',advanceAmount:0}, verify: {approvedAmount:'',status:'APPROVED',note:''},
    notifications: [], unread: 0,
    departments: [], positions: [], users: [],
    newTravel: {destination:'',purpose:'',startDate:'',endDate:'',estimatedCost:'',policyId:''},
    travelFormOpen: false, travelDetail: null, docs: [],
    pendingBookings: [], dashboardByStatus: [], expenseByDept: [], expenseByEmp: [], expenseByProj: [],
    sampleDepartmentData: [
      { name: 'IT', amount: 30000000 },
      { name: 'Sales', amount: 80000000 },
      { name: 'HR', amount: 45000000 },
      { name: 'Ops', amount: 25000000 },
      { name: 'Mktg', amount: 60000000 }
    ],
    newDept: '', newPos: '', newPolicy: {name:'',positionId:'',destinationTier:'',max_budget:''},
    newDeleg: {delegateId:'',startDate:'',endDate:'',reason:''},

    get isEmployee(){return this.user?.role==='EMPLOYEE';},
    get isAdmin(){return ['ADMIN','SUPER_ADMIN'].includes(this.user?.role);},
    get isSuperAdmin(){return this.user?.role==='SUPER_ADMIN';},
    get isFinance(){return this.user?.role==='FINANCE';},
    get isHrd(){return this.user?.role==='HRD';},
    get isApprover(){return ['MANAGER','DEPARTMENT_HEAD'].includes(this.user?.role);},

    canAccess(item){
      if(!item.roles || item.roles==='ALL') return true;
      return item.roles.includes(this.user?.role);
    },
    navLabel(item){
      if(item.key==='dashboard'){
        if(this.isAdmin) return 'Dashboard Admin';
        if(this.isFinance) return 'Finance Hub';
        if(this.isHrd) return 'HRD Center';
        if(this.isApprover) return 'Approval Center';
        return 'Travel Saya';
      }
      if(item.key==='travel') return this.isEmployee ? 'Pengajuan Saya' : 'Daftar Travel';
      if(item.key==='notifications') return 'Notifikasi';
      return item.label;
    },
    get roleLabel(){
      const m={EMPLOYEE:'Employee — Karyawan',MANAGER:'Manager — Approver Level 1',DEPARTMENT_HEAD:'Department Head — Approver',HRD:'HRD — Approver Final & Kepatuhan',FINANCE:'Finance — Verifikasi Keuangan',ADMIN:'Admin — Operasional Travel',SUPER_ADMIN:'Super Admin — Penuh'};
      return m[this.user?.role] || (this.user?.role||'');
    },
    get roleDescription(){
      const m={
        EMPLOYEE:'Buat & pantau pengajuan perjalanan, upload dokumen, ajukan reimbursement.',
        MANAGER:'Setujui/tolak pengajuan tim, kelola delegasi wewenang.',
        DEPARTMENT_HEAD:'Setujui/tolak pengajuan departemen, kelola delegasi.',
        HRD:'Approval final, monitoring kebijakan & distribusi perjalanan.',
        FINANCE:'Verifikasi & bayar reimbursement, akses laporan keuangan.',
        ADMIN:'Kelola booking, dashboard operasional, laporan.',
        SUPER_ADMIN:'Kelola master data (user, dept, position, policy) & semua data.'
      };
      return m[this.user?.role] || '';
    },

    // Counter helpers per role
    travelCountByStatus(s){ return this.travels.filter(t=>t.status===s).length; },
    reimsCountByStatus(s){ return this.reimbursements.filter(r=>r.status===s).length; },
    get reimsWaitingCount(){ return this.reimbursements.filter(r=>r.status==='SUBMITTED').length; },
    get totalApprovedReimbursement(){
      return this.reimbursements.filter(r=>['APPROVED','PAID'].includes(r.status))
        .reduce((a,c)=>a+Number(c.approved_amount||0),0);
    },
    get fallbackStatusCount(){
      const order=['DRAFT','SUBMITTED','APPROVED','REJECTED','COMPLETED','CANCELLED'];
      return order.map(s=>({status:s,total:this.travelCountByStatus(s)})).filter(x=>x.total>0);
    },

    get viewTitle(){
      if(this.view==='dashboard'){
        if(this.isAdmin) return 'Global Overview';
        if(this.isFinance) return 'Finance Verification Hub';
        if(this.isHrd) return 'HRD Control Center';
        if(this.isApprover) return 'Approval Command Center';
        if(this.isEmployee) return 'Travel Saya';
        return 'Dashboard';
      }
      const titles = {
        travel: this.isEmployee ? 'Pengajuan Perjalanan Saya' : 'Daftar Travel Request',
        approvals: 'Approval Command Center',
        reimbursements: this.isFinance ? 'Verifikasi & Pembayaran' : (this.isEmployee ? 'Reimbursement Saya' : 'Reimbursement'),
        bookings: 'Booking Management',
        reports: 'Corporate Expense Reports',
        master: 'System Master Data',
        notifications: 'Notifications Center'
      };
      return titles[this.view] || 'Dashboard';
    },

    get viewSubtitle(){
      if(this.view==='dashboard'){
        if(this.isAdmin) return 'Monitoring Q3 Corporate Travel Budget & Alerts';
        if(this.isFinance) return 'Verifikasi, setujui, dan bayar reimbursement karyawan';
        if(this.isHrd) return 'Kepatuhan kebijakan perjalanan & antrean persetujuan HRD';
        if(this.isApprover) return 'Review dan putuskan perjalanan dinas tim Anda';
        if(this.isEmployee) return 'Pantau pengajuan perjalanan dinas & reimbursement Anda';
        return 'Corporate Travel Management';
      }
      const subs = {
        travel: this.isEmployee ? 'Buat dan pantau pengajuan perjalanan dinas Anda.' : 'Seluruh data pengajuan perjalanan perusahaan.',
        approvals: 'Review and manage high-priority corporate travel requests.',
        reimbursements: this.isFinance ? 'Verifikasi item, setujui nominal, lalu bayar klaim karyawan.' : 'Manage receipts, expense details and justification.',
        bookings: 'Buat booking tiket/hotel untuk travel berstatus APPROVED.',
        reports: 'Departmental and employee travel spend analytics.',
        master: 'Manage departments, positions, users, and travel policies.',
        notifications: 'Unread activity and system status alerts.'
      };
      return subs[this.view] || 'Corporate Travel Management';
    },

    get totalExpenseCalculated(){
      if(this.expenseByDept && this.expenseByDept.length){
        return this.expenseByDept.reduce((acc, curr) => acc + Number(curr.totalExpense||0), 0);
      }
      return this.travels.reduce((acc, curr) => acc + Number(curr.estimated_cost||0), 0);
    },

    get activeTravelsCount(){
      return this.travels.filter(t => ['SUBMITTED','APPROVED'].includes(t.status)).length || this.travels.length;
    },

    get avgExpensePerTravel(){
      const count = this.travels.length || 1;
      return Math.round(this.totalExpenseCalculated / count);
    },

    async init(){ if(this.token){ await this.loadAll(); this.ensureAccess(); } },
    ensureAccess(){
      const item = this.navItems.find(n=>n.key===this.view);
      if(item && !this.canAccess(item)) this.view='dashboard';
    },
    async api(path, opts={}){
      const res = await fetch('/api'+path, { ...opts, headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'Authorization':'Bearer '+this.token, ...(opts.headers||{}) } });
      const json = await res.json().catch(()=>({}));
      if(!res.ok){ throw new Error(json.message||'Request gagal'); }
      return json.data;
    },
    async login(){
      this.loginError='';
      try{
        const data = await fetch('/api/auth/login',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(this.loginForm)}).then(r=>r.json());
        if(!data.success) throw new Error(data.message);
        this.token=data.data.token; this.user=data.data.user;
        localStorage.setItem('token',this.token); localStorage.setItem('user',JSON.stringify(this.user));
        this.view='dashboard';
        await this.loadAll();
      }catch(e){ this.loginError=e.message; }
    },
    logout(){ this.token=''; this.user=null; localStorage.removeItem('token'); localStorage.removeItem('user'); },
    async loadAll(){
      await Promise.allSettled([this.loadTravels(),this.loadPolicies(),this.loadApprovals(),this.loadReims(),this.loadNotifs(),this.loadMasters()]);
      if(['ADMIN','SUPER_ADMIN','FINANCE'].includes(this.user?.role)){ this.loadPendingBookings(); this.loadReports(); }
    },
    async loadTravels(){ try{ this.travels = await this.api('/travel'); }catch(e){} },
    async loadPolicies(){ try{ this.policies = await this.api('/travel/policies'); }catch(e){} },
    async loadApprovals(){ try{ this.approvalsPending = await this.api('/approvals/pending'); }catch(e){} try{ this.delegations = await this.api('/approvals/delegations'); }catch(e){} },
    async loadReims(){ try{ this.reimbursements = await this.api('/reimbursements'); }catch(e){} },
    async loadNotifs(){ try{ this.notifications = await this.api('/notifications'); this.unread = (await this.api('/notifications/unread-count')).count; }catch(e){} },
    async loadMasters(){
      try{ this.departments = await this.api('/users/departments'); }catch(e){}
      try{ this.positions = await this.api('/users/positions'); }catch(e){}
      try{ if(this.isAdmin||this.isSuperAdmin) this.users = await this.api('/users'); }catch(e){}
    },
    async loadPendingBookings(){ try{ this.pendingBookings = await this.api('/travel/bookings/pending'); }catch(e){} },
    async loadReports(){
      try{ const d = await this.api('/reports/dashboard'); this.dashboardByStatus = d.byStatus; }catch(e){}
      try{ if(['ADMIN','SUPER_ADMIN','FINANCE'].includes(this.user?.role)) this.expenseByDept = await this.api('/reports/expense-by-department'); }catch(e){}
      try{ this.expenseByEmp = await this.api('/reports/expense-by-employee'); }catch(e){}
      try{ this.expenseByProj = await this.api('/reports/expense-by-project'); }catch(e){}
    },
    exportReport(){
      alert('Laporan sedang diexport. Menghubungi /api/reports/export...');
    },
    openTravelForm(){ this.travelFormOpen=true; this.view='travel'; },
    async createTravel(){
      try{
        const d = await this.api('/travel',{method:'POST',body:JSON.stringify({
          destination:this.newTravel.destination,purpose:this.newTravel.purpose,
          startDate:this.newTravel.startDate,endDate:this.newTravel.endDate,
          estimatedCost:Number(this.newTravel.estimatedCost),policyId:Number(this.newTravel.policyId)
        })});
        this.travelFormOpen=false; this.newTravel={destination:'',purpose:'',startDate:'',endDate:'',estimatedCost:'',policyId:''};
        await this.loadTravels();
        if(d && d.id && confirm('Pengajuan berhasil dibuat sebagai DRAFT.\n\nSubmit sekarang ke Manager/HRD untuk disetujui?')){
          await this.submitTravel(d.id);
        }
      }catch(e){ alert(e.message); }
    },
    async submitTravel(id){ try{ await this.api('/travel/'+id+'/submit',{method:'POST'}); await this.loadTravels(); }catch(e){ alert(e.message); } },
    async deleteTravel(id){ if(!confirm('Hapus draft?')) return; try{ await this.api('/travel/'+id,{method:'DELETE'}); await this.loadTravels(); }catch(e){ alert(e.message); } },
    async cancelTravel(id){ try{ await this.api('/travel/'+id+'/cancel',{method:'POST'}); await this.loadTravels(); }catch(e){ alert(e.message); } },
    async viewTravel(id){ try{ const d = await this.api('/travel/'+id); this.travelDetail = d; this.docs=[]; }catch(e){ alert(e.message); } },
    async uploadDoc(travelId){
      const input = document.createElement('input'); input.type='file';
      input.onchange = async (e)=>{
        const f=e.target.files[0]; if(!f) return;
        const fd=new FormData(); fd.append('file',f);
        const res = await fetch('/api/travel/'+travelId+'/documents',{method:'POST',headers:{'Authorization':'Bearer '+this.token},body:fd});
        const j=await res.json(); if(!j.success) alert(j.message); else { alert('Uploaded'); this.loadDocs(travelId); }
      };
      input.click();
    },
    async loadDocs(travelId){ try{ this.docs = await this.api('/travel/'+travelId+'/documents'); }catch(e){ alert(e.message); } },
    async deleteDoc(docId){ try{ await this.api('/travel/documents/'+docId,{method:'DELETE'}); if(this.travelDetail) this.loadDocs(this.travelDetail.travel.id); }catch(e){ alert(e.message); } },
    async decideApproval(id,status){
      const note = prompt('Catatan (opsional):')||'';
      try{ await this.api('/approvals/'+id+'/decision',{method:'PATCH',body:JSON.stringify({status,note})}); await this.loadApprovals(); await this.loadTravels(); }catch(e){ alert(e.message); }
    },
    async createDelegation(){
      try{
        await this.api('/approvals/delegations',{method:'POST',body:JSON.stringify({
          delegateId:Number(this.newDeleg.delegateId),startDate:this.newDeleg.startDate,endDate:this.newDeleg.endDate,reason:this.newDeleg.reason
        })});
        this.newDeleg={delegateId:'',startDate:'',endDate:'',reason:''}; await this.loadApprovals();
      }catch(e){ alert(e.message); }
    },
    async createReimbursement(){
      try{
        await this.api('/reimbursements',{method:'POST',body:JSON.stringify({travelId:Number(this.newReim.travelId),advanceAmount:Number(this.newReim.advanceAmount)})});
        this.newReim={travelId:'',advanceAmount:0}; await this.loadReims();
      }catch(e){ alert(e.message); }
    },
    async viewReimbursement(id){ try{ this.reimDetail = await this.api('/reimbursements/'+id); }catch(e){ alert(e.message); } },
    async submitReimbursement(id){ try{ await this.api('/reimbursements/'+id+'/submit',{method:'POST'}); await this.loadReims(); }catch(e){ alert(e.message); } },
    async addItem(){
      try{
        await this.api('/reimbursements/'+this.reimDetail.reimbursement.id+'/items',{method:'POST',body:JSON.stringify({
          category:this.newItem.category,description:this.newItem.description,amount:Number(this.newItem.amount),transactionDate:this.newItem.transactionDate
        })});
        this.newItem={category:'HOTEL',description:'',amount:'',transactionDate:''};
        await this.viewReimbursement(this.reimDetail.reimbursement.id);
      }catch(e){ alert(e.message); }
    },
    async deleteItem(itemId){ try{ await this.api('/reimbursements/items/'+itemId,{method:'DELETE'}); await this.viewReimbursement(this.reimDetail.reimbursement.id); }catch(e){ alert(e.message); } },
    async verifyReimbursement(id){
      try{
        await this.api('/reimbursements/'+id+'/verify',{method:'PATCH',body:JSON.stringify({
          approvedAmount:Number(this.verify.approvedAmount),status:this.verify.status,note:this.verify.note
        })});
        await this.viewReimbursement(id); await this.loadReims();
      }catch(e){ alert(e.message); }
    },
    async payReimbursement(id){
      const ref = prompt('External journal ref:'); if(!ref) return;
      try{ await this.api('/reimbursements/'+id+'/pay',{method:'PATCH',body:JSON.stringify({externalJournalRef:ref})}); await this.viewReimbursement(id); await this.loadReims(); }catch(e){ alert(e.message); }
    },
    async createBooking(travelId){
      const provider = prompt('Provider:'); if(!provider) return;
      const type = prompt('Type (FLIGHT/HOTEL/TRAIN/OTHER):','FLIGHT');
      const cost = prompt('Cost:','0');
      try{
        await this.api('/travel/'+travelId+'/bookings',{method:'POST',body:JSON.stringify({provider,bookingType:type,cost:Number(cost)})});
        alert('Booking dibuat'); await this.loadPendingBookings();
      }catch(e){ alert(e.message); }
    },
    async readNotif(id){ try{ await this.api('/notifications/'+id+'/read',{method:'PATCH'}); await this.loadNotifs(); }catch(e){} },
    async readAllNotif(){ try{ await this.api('/notifications/read-all',{method:'PATCH'}); await this.loadNotifs(); }catch(e){} },
    async createDept(){ try{ await this.api('/users/departments',{method:'POST',body:JSON.stringify({name:this.newDept})}); this.newDept=''; await this.loadMasters(); }catch(e){ alert(e.message); } },
    async deleteDept(id){ try{ await this.api('/users/departments/'+id,{method:'DELETE'}); await this.loadMasters(); }catch(e){ alert(e.message); } },
    async createPos(){ try{ await this.api('/users/positions',{method:'POST',body:JSON.stringify({name:this.newPos})}); this.newPos=''; await this.loadMasters(); }catch(e){ alert(e.message); } },
    async deletePos(id){ try{ await this.api('/users/positions/'+id,{method:'DELETE'}); await this.loadMasters(); }catch(e){ alert(e.message); } },
    async changeRole(id){
      const role = prompt('Role baru (EMPLOYEE/MANAGER/DEPARTMENT_HEAD/HRD/FINANCE/ADMIN/SUPER_ADMIN):');
      if(!role) return;
      try{ await this.api('/users/'+id+'/role',{method:'PATCH',body:JSON.stringify({role})}); await this.loadMasters(); }catch(e){ alert(e.message); }
    },
    async createPolicy(){
      try{
        await this.api('/travel/policies',{method:'POST',body:JSON.stringify({
          name:this.newPolicy.name,positionId:Number(this.newPolicy.positionId),destinationTier:this.newPolicy.destinationTier,max_budget:Number(this.newPolicy.max_budget)
        })});
        this.newPolicy={name:'',positionId:'',destinationTier:'',max_budget:''}; await this.loadPolicies();
      }catch(e){ alert(e.message); }
    },
    formatRp(n){ if(n==null || isNaN(n)) return '-'; return 'Rp '+Number(n).toLocaleString('id-ID'); },
    statusClass(s){
      const m={
        DRAFT:'bg-slate-100 text-slate-700',
        SUBMITTED:'bg-blue-100 text-blue-700',
        APPROVED:'bg-emerald-100 text-emerald-700',
        REJECTED:'bg-red-100 text-red-700',
        CANCELLED:'bg-gray-100 text-gray-600',
        COMPLETED:'bg-teal-100 text-teal-700',
        PAID:'bg-green-100 text-green-700',
        PENDING:'bg-amber-100 text-amber-700',
        CONFIRMED:'bg-emerald-100 text-emerald-700'
      };
      return m[s]||'bg-slate-100 text-slate-700';
    }
  }
}
</script>
</body>
</html>

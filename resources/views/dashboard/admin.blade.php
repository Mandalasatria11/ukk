@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Pusat Kontrol Administrator')
@section('page-subtitle', 'Monitoring menyeluruh seluruh sistem, pengguna, dan audit aktivitas BizJourney')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-[#121824] to-[#1e293b] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                    Administrator Utama
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ $user->name }}!</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                    Akses kontrol penuh atas seluruh pengguna (Admin, Manager, HRD, Finance, Employee), konfigurasi master data departemen & kebijakan, serta audit jejak aktivitas sistem.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="{{ route('admin.logs') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition border border-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Audit Trail Log</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Karyawan Aktif</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total_employees'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Akun terdaftar dalam sistem</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Perjalanan Dinas</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total_travels'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Semua departemen</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/20">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Pending Approval</div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['pending_approvals'] }}</div>
            <div class="text-[11px] text-amber-700/80 mt-1">Menunggu Manager / HRD</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-indigo-200 shadow-xs bg-indigo-50/20">
            <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Total Reimbursement Disetujui</div>
            <div class="text-xl font-black text-indigo-600 mt-2">Rp {{ number_format($stats['total_reimbursement'], 0, ',', '.') }}</div>
            <div class="text-[11px] text-indigo-700/80 mt-1">Klaim verified & paid</div>
        </div>
    </div>

    <!-- 2 Column Layout: Recent Activities (Audit Log) & Recent Travels -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Activity Logs / Audit Trail -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Audit Trail / Jejak Aktivitas Terbaru
                    </h3>
                    <p class="text-xs text-slate-400">Pencatatan kepatuhan & transparansi seluruh tindakan administratif</p>
                </div>
                <a href="{{ route('admin.logs') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">Lihat Semua →</a>
            </div>

            <div class="divide-y divide-slate-100 max-h-[380px] overflow-y-auto">
                @forelse($recentActivities as $log)
                    <div class="p-4 hover:bg-slate-50 transition flex items-start gap-3">
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full flex-shrink-0 mt-0.5
                            @if(str_contains($log->action, 'APPROVE')) bg-emerald-100 text-emerald-800
                            @elseif(str_contains($log->action, 'REJECT') || str_contains($log->action, 'CANCEL')) bg-red-100 text-red-800
                            @elseif(str_contains($log->action, 'PAY')) bg-blue-100 text-blue-800
                            @elseif(str_contains($log->action, 'SUBMIT')) bg-amber-100 text-amber-800
                            @else bg-slate-100 text-slate-700
                            @endif">
                            {{ $log->action }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-slate-800 leading-snug">{{ $log->description }}</p>
                            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                <span>·</span>
                                <span>IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">Belum ada aktivitas tercatat.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Travels -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Perjalanan Dinas Terkini</h3>
                    <p class="text-xs text-slate-400">Daftar pengajuan terbaru seluruh staf</p>
                </div>
                <a href="{{ route('travel.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">Lihat Semua →</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentTravels as $rt)
                    <div class="p-4 hover:bg-slate-50 transition flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $rt->user->name }}</div>
                            <div class="text-[11px] text-slate-500">Tujuan: <strong>{{ $rt->destination }}</strong> ({{ $rt->department->name ?? 'Dept' }})</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $rt->start_date->format('d/m/Y') }} · Rp {{ number_format($rt->estimated_cost, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            @if($rt->status === 'APPROVED')
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Approved</span>
                            @elseif(in_array($rt->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800">Pending</span>
                            @elseif($rt->status === 'REJECTED')
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-100 text-red-800">Rejected</span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">{{ $rt->status }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">Belum ada data perjalanan.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

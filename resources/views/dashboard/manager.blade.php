@extends('layouts.app')

@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard Manager')
@section('page-subtitle', 'Pusat persetujuan perjalanan dinas dan pengawasan staf departemen Anda')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-[#121824] to-[#1e293b] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    Manager Departemen: {{ $user->department->name ?? 'Sales' }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ $user->name }}!</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                    Anda memegang otoritas Level 1 untuk menyetujui atau menolak permohonan perjalanan dinas dari seluruh anggota staf di Departemen <strong>{{ $user->department->name ?? 'Sales' }}</strong>.
                </p>
            </div>
            <div>
                <a href="{{ route('approvals.index') }}" class="px-5 py-3 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-xs hover:bg-amber-400 transition shadow-lg shadow-amber-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Buka Pusat Persetujuan</span>
                    @if($stats['pending_approvals'] > 0)
                        <span class="px-2 py-0.5 rounded-full bg-red-600 text-white text-[10px]">{{ $stats['pending_approvals'] }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Anggota Tim</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['team_members'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Staf Departemen {{ $user->department->name ?? 'Sales' }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/20">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Perlu Persetujuan Anda</div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['pending_approvals'] }}</div>
            <div class="text-[11px] text-amber-700/80 mt-1">Menunggu keputusan Anda</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui Tim</div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['dept_travels_approved'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Total perjalanan disetujui</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Perjalanan Tim</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['dept_travels_total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Semua status</div>
        </div>
    </div>

    <!-- PENDING APPROVALS QUEUE (EXPLICIT REQUESTER IDENTITY) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    Permintaan Persetujuan dari Staf Tim Anda
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar permohonan dinas yang diajukan oleh anggota tim Anda yang membutuhkan persetujuan Manager</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                {{ $pendingApprovals->count() }} Menunggu Tindakan
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($pendingApprovals as $a)
                @php $t = $a->travelRequest; @endphp
                <div class="p-5 hover:bg-slate-50/80 transition flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <!-- Requester Profile & Info -->
                    <div class="flex items-start gap-4">
                        <div class="h-11 w-11 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                            {{ strtoupper(substr($t->user->name ?? 'User', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-extrabold text-slate-900">{{ $t->user->name ?? 'Pemohon' }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $t->user->position->name ?? 'Staff' }}
                                </span>
                                <span class="text-[10px] text-slate-400">({{ $t->user->email }})</span>
                            </div>
                            <div class="text-xs font-bold text-slate-700 mt-1 flex items-center gap-2">
                                <span class="text-amber-600 font-extrabold">Tujuan: {{ $t->destination }}</span>
                                <span>·</span>
                                <span class="text-slate-500">{{ $t->start_date->format('d M') }} s/d {{ $t->end_date->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5 line-clamp-1 italic">
                                "{{ $t->purpose }}"
                            </div>
                        </div>
                    </div>

                    <!-- Cost & Actions -->
                    <div class="flex items-center gap-4 self-end md:self-center">
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Estimasi Biaya</div>
                            <div class="text-base font-black text-slate-900">Rp {{ number_format($t->estimated_cost, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-slate-400">Kebijakan: {{ $t->policy->name ?? 'Standar' }}</div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('travel.show', $t->id) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                Lihat Rincian
                            </a>
                            <a href="{{ route('approvals.index') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold transition shadow-sm">
                                Tinjau Keputusan →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold text-slate-600">Tidak ada permohonan dinas yang menunggu persetujuan Anda saat ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Semua permohonan dari staf Departemen {{ $user->department->name ?? 'Sales' }} sudah selesai diproses.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Team Travels -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
        <h3 class="text-sm font-bold text-slate-900 mb-3">Semua Aktivitas Perjalanan Dinas Departemen {{ $user->department->name ?? 'Sales' }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($recentDeptTravels as $dt)
                <div class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 transition bg-slate-50/40">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-slate-900">{{ $dt->user->name }}</span>
                        @if($dt->status === 'APPROVED')
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Approved</span>
                        @elseif(in_array($dt->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800">Pending</span>
                        @elseif($dt->status === 'REJECTED')
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-100 text-red-800">Rejected</span>
                        @else
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ $dt->status }}</span>
                        @endif
                    </div>
                    <div class="text-xs font-medium text-slate-600 mb-1">Ke: <span class="font-bold text-slate-800">{{ $dt->destination }}</span></div>
                    <div class="text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-200/60 pt-2 mt-2">
                        <span>{{ $dt->start_date->format('d/m/Y') }}</span>
                        <span class="font-bold text-slate-700">Rp {{ number_format($dt->estimated_cost, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="text-xs text-slate-400 col-span-3">Belum ada riwayat perjalanan tim.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard Karyawan')
@section('page-subtitle', 'Pantau status perjalanan dinas dan klaim reimbursement Anda')

@section('content')
<div class="space-y-6">

    <!-- Hero Greeting Card -->
    <div class="bg-gradient-to-r from-[#121824] to-[#1e293b] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Portal Karyawan
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Halo, {{ $user->name }}! 👋</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                    Departemen <strong>{{ $user->department->name ?? 'Sales' }}</strong> · Jabatan <strong>{{ $user->position->name ?? 'Staff' }}</strong>.
                    Kelola pengajuan perjalanan dinas resmi dan klaim biaya operasional Anda dengan mudah di sini.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('travel.create') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Ajukan Perjalanan Dinas</span>
                </a>
                <a href="{{ route('reimbursements.create') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition border border-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Ajukan Reimbursement</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengajuan</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total_travels'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Perjalanan dinas tercatat</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Approval</div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['pending_travels'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Sedang ditinjau Manager / HRD</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui (Approved)</div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['approved_travels'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Siap berangkat & klaim</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Reimbursement Cair</div>
            <div class="text-xl font-black text-indigo-600 mt-2">Rp {{ number_format($stats['reimbursements_paid'], 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Telah dibayar Finance</div>
        </div>
    </div>

    <!-- Travel List Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pengajuan Perjalanan Dinas Terakhir</h2>
                <p class="text-xs text-slate-400">Pantau proses persetujuan dan rincian perjalanan dinas Anda</p>
            </div>
            <a href="{{ route('travel.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3 px-4">Tujuan & Keperluan</th>
                        <th class="py-3 px-4">Jadwal</th>
                        <th class="py-3 px-4">Estimasi Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($myTravels as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $t->destination }}</div>
                                <div class="text-slate-500 text-xs line-clamp-1">{{ $t->purpose }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-700">{{ $t->start_date->format('d M Y') }} - {{ $t->end_date->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $t->start_date->diffInDays($t->end_date) + 1 }} Hari</div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Rp {{ number_format($t->estimated_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if(in_array($t->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Menunggu Approval</span>
                                @elseif($t->status === 'APPROVED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Disetujui (Approved)</span>
                                @elseif($t->status === 'REJECTED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">Ditolak</span>
                                @elseif($t->status === 'DRAFT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Draft</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $t->status }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('travel.show', $t->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 font-bold hover:bg-amber-50 hover:text-amber-700 transition">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                Belum ada pengajuan perjalanan dinas. Klik "Ajukan Perjalanan Dinas" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

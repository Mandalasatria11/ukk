@extends('layouts.app')

@section('title', 'Dashboard HRD')
@section('page-title', 'Dashboard HRD')
@section('page-subtitle', 'Pemeriksaan kepatuhan kebijakan perjalanan dinas seluruh perusahaan')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-[#121824] to-[#1e293b] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                    Human Resources Department (HRD)
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ $user->name }}!</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                    Tinjau kepatuhan kebijakan perjalanan dinas, persetujuan penugasan kerja, dan monitoring mobilitas seluruh karyawan perusahaan.
                </p>
            </div>
            <div>
                <a href="{{ route('approvals.index') }}" class="px-5 py-3 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-xs hover:bg-amber-400 transition shadow-lg shadow-amber-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pusat Persetujuan HRD</span>
                    @if($stats['pending_approvals'] > 0)
                        <span class="px-2 py-0.5 rounded-full bg-red-600 text-white text-[10px]">{{ $stats['pending_approvals'] }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Permohonan</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total_travels'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Seluruh departemen</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/20">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Approval</div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['pending_approvals'] }}</div>
            <div class="text-[11px] text-amber-700/80 mt-1">Perlu ditindaklanjuti</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui (Approved)</div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['approved_requests'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Telah disahkan</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-red-600 uppercase tracking-wider">Ditolak (Rejected)</div>
            <div class="text-2xl font-black text-red-600 mt-2">{{ $stats['rejected_requests'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Tidak memenuhi syarat</div>
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pengajuan Perjalanan Dinas Karyawan Terkini</h2>
                <p class="text-xs text-slate-400">Pantau seluruh pergerakan tugas dinas karyawan lintas departemen</p>
            </div>
            <a href="{{ route('travel.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3 px-4">Nama Karyawan</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Tujuan & Keperluan</th>
                        <th class="py-3 px-4">Tanggal Perjalanan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTravels as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900">{{ $t->user->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $t->user->email }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700">
                                {{ $t->department->name ?? 'Dept' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $t->destination }}</div>
                                <div class="text-slate-400 text-[11px] line-clamp-1">{{ $t->purpose }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $t->start_date->format('d/m/Y') }} - {{ $t->end_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if(in_array($t->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Menunggu Approval</span>
                                @elseif($t->status === 'APPROVED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Approved</span>
                                @elseif($t->status === 'REJECTED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Rejected</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ $t->status }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('travel.show', $t->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data perjalanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

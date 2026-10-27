@extends('layouts.app')

@section('title', 'Daftar Perjalanan Dinas')
@section('page-title', 'Perjalanan Dinas')
@section('page-subtitle', auth()->user()->isEmployee() ? 'Riwayat permohonan dinas pribadi Anda' : 'Monitoring perjalanan dinas seluruh staf')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('travel.index') }}" class="w-full md:w-auto flex-1 flex flex-wrap items-center gap-2">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kota tujuan, alasan, atau nama karyawan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs focus:ring-2 focus:ring-amber-500 outline-none text-slate-800">
            </div>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 outline-none font-medium text-slate-700">
                <option value="ALL" {{ request('status') === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Menunggu Approval</option>
                <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui (Approved)</option>
                <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draf</option>
                <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('travel.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
            @endif
        </form>

        @if(auth()->user()->isEmployee())
            <a href="{{ route('travel.create') }}" class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Ajukan Perjalanan Baru</span>
            </a>
        @endif
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">ID & Pemohon</th>
                        <th class="py-3.5 px-5">Tujuan & Alasan Dinas</th>
                        <th class="py-3.5 px-5">Jadwal Perjalanan</th>
                        <th class="py-3.5 px-5">Estimasi Biaya</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($travels as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-5">
                                <div class="font-bold text-amber-700 text-xs">#TRV-{{ $t->id }}</div>
                                <div class="font-extrabold text-slate-900 mt-0.5">{{ $t->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $t->department->name ?? 'Dept' }} · {{ $t->user->position->name ?? 'Staff' }}</div>
                            </td>
                            <td class="py-4 px-5 max-w-xs">
                                <div class="font-black text-slate-900 text-sm">{{ $t->destination }}</div>
                                <div class="text-slate-500 text-xs line-clamp-2 mt-0.5">{{ $t->purpose }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-semibold text-slate-800">{{ $t->start_date->format('d M Y') }}</div>
                                <div class="text-slate-400 text-[11px]">s/d {{ $t->end_date->format('d M Y') }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-600">
                                    {{ $t->start_date->diffInDays($t->end_date) + 1 }} Hari
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-black text-slate-900 text-sm">Rp {{ number_format($t->estimated_cost, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400">Plafon: {{ $t->policy->name ?? 'Standar' }}</div>
                            </td>
                            <td class="py-4 px-5">
                                @if(in_array($t->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu Approval
                                    </span>
                                @elseif($t->status === 'APPROVED')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Disetujui (Approved)
                                    </span>
                                @elseif($t->status === 'REJECTED')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        Ditolak (Rejected)
                                    </span>
                                @elseif($t->status === 'DRAFT')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Draf
                                    </span>
                                @elseif($t->status === 'CANCELLED')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                        {{ $t->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <a href="{{ route('travel.show', $t->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-50 hover:text-amber-800 font-bold text-xs transition">
                                    <span>Lihat Rincian</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Tidak ditemukan data perjalanan dinas sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($travels->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $travels->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

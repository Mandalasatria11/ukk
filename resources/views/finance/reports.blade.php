@extends('layouts.app')

@section('title', 'Laporan Keuangan Perjalanan Dinas')
@section('page-title', 'Laporan Keuangan & Pengeluaran')
@section('page-subtitle', 'Rekapitulasi biaya dinas, audit reimbursement, dan laporan pencairan dana')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs print:hidden">
        <form method="GET" action="{{ route('finance.reports') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Departemen</label>
                    <select name="department_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Semua Status</option>
                        <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                        <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>APPROVED (Pending Payment)</option>
                        <option value="PENDING_VERIFICATION" {{ request('status') === 'PENDING_VERIFICATION' ? 'selected' : '' }}>Pending Verification</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('finance.reports') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Klaim Terfilter</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalClaims }} Transaksi</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Total Nominal Disetujui</div>
            <div class="text-2xl font-black text-amber-600 mt-1">Rp {{ number_format($totalApproved, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Total Dana Terbayar (PAID)</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Report Table with Print Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Rekapitulasi Laporan Keuangan Perjalanan Dinas</h3>
                <p class="text-xs text-slate-400">Dicetak pada: {{ date('d F Y, H:i') }} · Purnama Corporation</p>
            </div>
            <button onclick="window.print()" class="print:hidden px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan / PDF</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3 px-3">No. Klaim</th>
                        <th class="py-3 px-3">Karyawan</th>
                        <th class="py-3 px-3">Departemen</th>
                        <th class="py-3 px-3">Tujuan Dinas</th>
                        <th class="py-3 px-3 text-right">Total Bukti</th>
                        <th class="py-3 px-3 text-right">Disetujui</th>
                        <th class="py-3 px-3">Ref. Jurnal</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reimbursements as $r)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-3 font-bold text-amber-700 font-mono">#RMB-{{ $r->id }}</td>
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $r->user->name }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $r->user->department->name ?? '-' }}</td>
                            <td class="py-3 px-3 text-slate-700">{{ $r->travelRequest->destination ?? 'Dinas' }}</td>
                            <td class="py-3 px-3 text-right font-semibold text-slate-700">Rp {{ number_format($r->total_items_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-black text-slate-900">
                                {{ $r->approved_amount ? 'Rp ' . number_format($r->approved_amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3 font-mono text-[11px] text-slate-600">{{ $r->external_journal_ref ?? '-' }}</td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($r->status === 'PAID') bg-emerald-100 text-emerald-800
                                    @elseif($r->status === 'APPROVED') bg-blue-100 text-blue-800
                                    @elseif($r->status === 'REJECTED') bg-red-100 text-red-800
                                    @else bg-amber-100 text-amber-800
                                    @endif">
                                    {{ $r->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada data laporan yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

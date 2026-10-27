@extends('layouts.app')

@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')
@section('page-subtitle', 'Pusat verifikasi reimbursement dan pencatatan pembayaran perjalanan dinas')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-[#121824] to-[#1e293b] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Finance & Accounting Suite
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ $user->name }}!</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                    Kelola anggaran operasional, lakukan verifikasi bukti kuitansi pengeluaran karyawan, dan selesaikan pencairan pembayaran reimbursement.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('finance.verifications') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Verifikasi Klaim</span>
                    @if($stats['pending_verification'] > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-red-600 text-white text-[10px]">{{ $stats['pending_verification'] }}</span>
                    @endif
                </a>
                <a href="{{ route('finance.payments') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-500 transition shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Proses Pembayaran</span>
                    @if($stats['pending_payment'] > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-400 text-slate-950 text-[10px]">{{ $stats['pending_payment'] }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Realisasi Pengeluaran</div>
            <div class="text-xl font-black text-slate-900 mt-2">Rp {{ number_format($stats['total_expenses'], 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Klaim disetujui & dibayar</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/20">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Perlu Verifikasi Bukti</div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['pending_verification'] }}</div>
            <div class="text-[11px] text-amber-700/80 mt-1">Menunggu pemeriksaan Finance</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs bg-emerald-50/20">
            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Menunggu Pencairan</div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['pending_payment'] }}</div>
            <div class="text-[11px] text-emerald-700/80 mt-1">Siap transfer / kas keluar</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Klaim Selesai Dibayar</div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['completed_payments'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Status PAID dengan Ref Jurnal</div>
        </div>
    </div>

    <!-- PENDING VERIFICATIONS QUEUE -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Antrean Klaim Reimbursement Menunggu Verifikasi
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Periksa bukti transaksi/struk kuitansi yang diunggah karyawan sebelum disetujui</p>
            </div>
            <a href="{{ route('finance.verifications') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">Lihat Semua →</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($pendingReimbursements as $r)
                <div class="p-5 hover:bg-slate-50/80 transition flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="h-11 w-11 rounded-2xl bg-emerald-500 text-slate-950 font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                            {{ strtoupper(substr($r->user->name ?? 'User', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-extrabold text-slate-900">{{ $r->user->name ?? 'Pemohon' }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                    {{ $r->user->department->name ?? 'Dept' }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                Perjalanan Dinas: <strong class="text-slate-800">{{ $r->travelRequest->destination ?? 'Dinas' }}</strong>
                                ({{ $r->items->count() }} item pengeluaran)
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Diajukan pada: {{ $r->updated_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 self-end md:self-center">
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Total Klaim</div>
                            <div class="text-base font-black text-slate-900">
                                Rp {{ number_format($r->total_items_amount, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-slate-400">Uang Muka: Rp {{ number_format($r->advance_amount, 0, ',', '.') }}</div>
                        </div>
                        <a href="{{ route('reimbursements.show', $r->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition shadow-sm">
                            Periksa Bukti & Verifikasi →
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold text-slate-600">Tidak ada pengajuan reimbursement yang menunggu verifikasi.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

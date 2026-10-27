@extends('layouts.app')

@section('title', 'Buat Pengajuan Reimbursement')
@section('page-title', 'Form Pengajuan Reimbursement')
@section('page-subtitle', 'Pilih perjalanan dinas yang telah disetujui untuk mengklaim biaya pengeluaran')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('reimbursements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Klaim
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-10 space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900">Mulai Pengajuan Klaim Baru</h2>
            <p class="text-xs text-slate-500 mt-1">Reimbursement hanya dapat dikaitkan dengan perjalanan dinas yang telah berstatus Disetujui (Approved).</p>
        </div>

        @if($approvedTravels->isEmpty())
            <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-3">
                <div class="font-extrabold text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Belum Ada Perjalanan Dinas yang Berstatus "Approved"
                </div>
                <p>
                    Anda hanya dapat mengajukan klaim reimbursement jika permohonan perjalanan dinas Anda telah disetujui oleh Manager/HRD.
                </p>
                <a href="{{ route('travel.create') }}" class="inline-block px-4 py-2 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition">
                    Ajukan Perjalanan Dinas Sekarang →
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('reimbursements.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">
                        Pilih Perjalanan Dinas (Approved) *
                    </label>
                    <select name="travel_request_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800">
                        <option value="">-- Pilih Perjalanan Dinas Terkait --</option>
                        @foreach($approvedTravels as $at)
                            <option value="{{ $at->id }}">
                                #TRV-{{ $at->id }} : Ke {{ $at->destination }} ({{ $at->start_date->format('d/m/Y') }} - {{ $at->end_date->format('d/m/Y') }}) · Estimasi: Rp {{ number_format($at->estimated_cost, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">
                        Uang Muka yang Pernah Diterima / Kasbon (Rp)
                    </label>
                    <input type="number" name="advance_amount" value="0" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800" placeholder="0 jika tidak ada uang muka">
                    <span class="text-[10px] text-slate-400 mt-1 block">Nominal uang muka yang sudah diberikan perusahaan sebelumnya (jika ada).</span>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('reimbursements.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition shadow-md shadow-amber-500/20">
                        Lanjutkan ke Pengisian Item & Bukti Kuitansi →
                    </button>
                </div>
            </form>
        @endif
    </div>

</div>
@endsection

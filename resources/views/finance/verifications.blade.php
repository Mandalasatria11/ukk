@extends('layouts.app')

@section('title', 'Verifikasi Reimbursement')
@section('page-title', 'Verifikasi Klaim Reimbursement')
@section('page-subtitle', 'Pemeriksaan bukti nota, kuitansi transaksi, dan persetujuan nominal pengeluaran')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Antrean Klaim Menunggu Verifikasi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar reimbursement yang telah diajukan karyawan dan siap diperiksa bukti transaksinya</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                {{ $pendingReimbursements->total() }} Menunggu Verifikasi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">ID & Karyawan</th>
                        <th class="py-3.5 px-5">Perjalanan Dinas</th>
                        <th class="py-3.5 px-5">Jumlah Item</th>
                        <th class="py-3.5 px-5">Uang Muka (Kasbon)</th>
                        <th class="py-3.5 px-5">Total Klaim</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingReimbursements as $r)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-5">
                                <div class="font-bold text-amber-700 text-xs">#RMB-{{ $r->id }}</div>
                                <div class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $r->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $r->user->department->name ?? 'Dept' }} · {{ $r->user->email }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-800 text-sm">{{ $r->travelRequest->destination ?? 'Dinas' }}</div>
                                <div class="text-[11px] text-slate-400">#TRV-{{ $r->travel_request_id }} ({{ $r->travelRequest->start_date->format('d/m/Y') }} - {{ $r->travelRequest->end_date->format('d/m/Y') }})</div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs">
                                    {{ $r->items->count() }} Item Nota
                                </span>
                            </td>
                            <td class="py-4 px-5 font-semibold text-slate-600">
                                Rp {{ number_format($r->advance_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 font-black text-slate-900 text-base">
                                Rp {{ number_format($r->total_items_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-right">
                                <a href="{{ route('reimbursements.show', $r->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition shadow-sm">
                                    <span>Periksa & Verifikasi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Tidak ada klaim reimbursement yang menunggu verifikasi saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingReimbursements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pendingReimbursements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Daftar Reimbursement')
@section('page-title', 'Klaim Reimbursement')
@section('page-subtitle', auth()->user()->isEmployee() ? 'Riwayat klaim biaya operasional perjalanan dinas Anda' : 'Monitoring dan verifikasi pengajuan klaim karyawan')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('reimbursements.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 outline-none font-medium text-slate-700">
                <option value="ALL" {{ request('status') === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draf</option>
                <option value="PENDING_VERIFICATION" {{ request('status') === 'PENDING_VERIFICATION' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui (Approved)</option>
                <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Selesai Dibayar (PAID)</option>
                <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak (Rejected)</option>
            </select>
        </form>

        @if(auth()->user()->isEmployee())
            <a href="{{ route('reimbursements.create') }}" class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Buat Pengajuan Klaim Baru</span>
            </a>
        @endif
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">ID & Karyawan</th>
                        <th class="py-3.5 px-5">Terkait Perjalanan Dinas</th>
                        <th class="py-3.5 px-5">Total Klaim</th>
                        <th class="py-3.5 px-5">Uang Muka (Advance)</th>
                        <th class="py-3.5 px-5">Disetujui Finance</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reimbursements as $r)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-5">
                                <div class="font-bold text-amber-700 text-xs">#RMB-{{ $r->id }}</div>
                                <div class="font-extrabold text-slate-900 mt-0.5">{{ $r->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $r->user->department->name ?? 'Dept' }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-800">{{ $r->travelRequest->destination ?? 'Dinas' }}</div>
                                <div class="text-[10px] text-slate-400">#TRV-{{ $r->travel_request_id }} · {{ $r->items->count() }} item pengeluaran</div>
                            </td>
                            <td class="py-4 px-5 font-black text-slate-900 text-sm">
                                Rp {{ number_format($r->total_items_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 font-semibold text-slate-600">
                                Rp {{ number_format($r->advance_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 font-bold text-slate-900">
                                {{ $r->approved_amount ? 'Rp ' . number_format($r->approved_amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 px-5">
                                @if($r->status === 'DRAFT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Draft</span>
                                @elseif(in_array($r->status, ['SUBMITTED', 'PENDING_VERIFICATION']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Menunggu Verifikasi</span>
                                @elseif($r->status === 'APPROVED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Disetujui (Menunggu Pembayaran)</span>
                                @elseif($r->status === 'PAID')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Selesai Dibayar (PAID)</span>
                                @elseif($r->status === 'REJECTED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">Ditolak</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $r->status }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <a href="{{ route('reimbursements.show', $r->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-50 hover:text-amber-800 font-bold text-xs transition">
                                    <span>Detail Bukti</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada data reimbursement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reimbursements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reimbursements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

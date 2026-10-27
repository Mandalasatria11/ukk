@extends('layouts.app')

@section('title', 'Manajemen Pembayaran (Payment)')
@section('page-title', 'Pencairan Dana & Pembayaran')
@section('page-subtitle', 'Eksekusi pembayaran klaim reimbursement yang telah disetujui Finance')

@section('content')
<div class="space-y-6">

    <!-- Section 1: Pending Payments Queue -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Klaim Disetujui Menunggu Pencairan (Pending Payment)
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Lakukan transfer dana ke rekening karyawan dan input nomor referensi bukti pembayaran</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                {{ $pendingPayments->total() }} Siap Dicairkan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">ID & Karyawan</th>
                        <th class="py-3.5 px-5">Perjalanan Dinas</th>
                        <th class="py-3.5 px-5">Nominal Disetujui</th>
                        <th class="py-3.5 px-5">Uang Muka</th>
                        <th class="py-3.5 px-5">Net Dibayarkan</th>
                        <th class="py-3.5 px-5 text-right">Aksi Eksekusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingPayments as $p)
                        <tr class="hover:bg-slate-50/60 transition" x-data="{ payModal: false }">
                            <td class="py-4 px-5">
                                <div class="font-bold text-amber-700 text-xs">#RMB-{{ $p->id }}</div>
                                <div class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $p->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $p->user->department->name ?? 'Dept' }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-800">{{ $p->travelRequest->destination ?? 'Dinas' }}</div>
                                <div class="text-[10px] text-slate-400">#TRV-{{ $p->travel_request_id }}</div>
                            </td>
                            <td class="py-4 px-5 font-bold text-slate-800">
                                Rp {{ number_format($p->approved_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-slate-500">
                                Rp {{ number_format($p->advance_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 font-black text-emerald-600 text-sm">
                                Rp {{ number_format($p->difference_amount ?? $p->approved_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-right">
                                <button type="button" @click="payModal = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
                                    Bayar / Cairkan →
                                </button>

                                <!-- Pay Modal -->
                                <div x-show="payModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs text-left">
                                    <div @click.away="payModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                            <h3 class="text-base font-extrabold text-slate-900">Catat Pembayaran Reimbursement</h3>
                                            <button type="button" @click="payModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                                        </div>

                                        <form method="POST" action="{{ route('finance.payments.action', $p->id) }}" class="space-y-4">
                                            @csrf
                                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-xs">
                                                <div>Karyawan: <strong class="text-slate-900">{{ $p->user->name }}</strong></div>
                                                <div class="mt-1">Nominal Transfer: <strong class="text-emerald-700 text-sm">Rp {{ number_format($p->difference_amount ?? $p->approved_amount, 0, ',', '.') }}</strong></div>
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Tanggal Pembayaran *</label>
                                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Nomor Referensi Transfer / Jurnal *</label>
                                                <input type="text" name="external_journal_ref" placeholder="Contoh: TRF-BCA-20261026-9021" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
                                            </div>

                                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                                <button type="button" @click="payModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                                                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-sm">
                                                    Konfirmasi Pembayaran Selesai (PAID)
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tidak ada klaim reimbursement yang menunggu pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingPayments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pendingPayments->links() }}
            </div>
        @endif
    </div>

    <!-- Section 2: Recent Paid History -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Pembayaran Selesai (PAID)</h3>
            <p class="text-xs text-slate-400">Klaim yang telah berhasil ditransfer dan tercatat nomor referensi jurnalnya</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3 px-5">ID & Karyawan</th>
                        <th class="py-3 px-5">Nomor Referensi Jurnal</th>
                        <th class="py-3 px-5">Nominal Dicairkan</th>
                        <th class="py-3 px-5">Waktu Pencairan</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($paidHistory as $ph)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-5">
                                <span class="font-bold text-slate-900">{{ $ph->user->name }}</span>
                                <span class="text-[10px] text-slate-400 block">#RMB-{{ $ph->id }} · {{ $ph->travelRequest->destination ?? 'Dinas' }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-mono font-bold text-emerald-800">
                                {{ $ph->external_journal_ref }}
                            </td>
                            <td class="py-3.5 px-5 font-black text-slate-900">
                                Rp {{ number_format($ph->approved_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-slate-500">
                                {{ $ph->updated_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                    PAID
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

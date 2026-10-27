@extends('layouts.app')

@section('title', 'Monitoring Anggaran Perjalanan')
@section('page-title', 'Monitoring Anggaran & Kebijakan')
@section('page-subtitle', 'Pengawasan konsumsi anggaran dinas per departemen dan kepatuhan plafon biaya')

@section('content')
<div class="space-y-6">

    <!-- Overview Banner -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Realisasi Pengeluaran Perusahaan</div>
            <div class="text-3xl font-black text-slate-900 mt-1">Rp {{ number_format($totalApprovedSpent, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-500 mt-1">Akumulasi klaim reimbursement yang telah disetujui & dibayarkan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.reports') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                Lihat Laporan Keuangan Lengkap →
            </a>
        </div>
    </div>

    <!-- Department Expenses Cards -->
    <div>
        <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 mb-3">Pengeluaran Berdasarkan Departemen</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($departments as $dept)
                @php
                    $spent = $deptExpenses->firstWhere('id', $dept->id)->total_spent ?? 0;
                @endphp
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">{{ $dept->name }}</span>
                        <span class="text-[10px] text-slate-400">{{ $dept->users_count }} Karyawan</span>
                    </div>
                    <div class="text-xl font-black text-slate-900 mt-2">Rp {{ number_format($spent, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Realisasi beban dinas</div>
                </div>
            @empty
                <div class="text-xs text-slate-400">Belum ada departemen terdaftar.</div>
            @endforelse
        </div>
    </div>

    <!-- Travel Policies Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Kebijakan Batas Plafon Biaya Perjalanan Dinas (Travel Policies)</h3>
                <p class="text-xs text-slate-400">Aturan batas maksimal biaya pengajuan perjalanan dinas sesuai tingkat jabatan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3 px-5">Nama Kebijakan</th>
                        <th class="py-3 px-5">Jabatan Berlaku</th>
                        <th class="py-3 px-5">Kategori Wilayah</th>
                        <th class="py-3 px-5">Batas Maksimal Plafon</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($policies as $pol)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-5 font-bold text-slate-900">
                                {{ $pol->name }}
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-700">
                                {{ $pol->position->name ?? 'Semua Jabatan' }}
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $pol->destination_tier }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-black text-slate-900">
                                Rp {{ number_format($pol->max_budget, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pol->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $pol->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada kebijakan perjalanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

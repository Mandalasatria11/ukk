@extends('layouts.app')

@section('title', 'Klaim Reimbursement #RMB-' . $reimbursement->id)
@section('page-title', 'Detail Klaim Reimbursement')
@section('page-subtitle', 'Pemeriksaan bukti pengeluaran, verifikasi nominal, dan eksekusi pembayaran')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Bar Navigation & Status -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="{{ route('reimbursements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Klaim
        </a>

        <div>
            @if($reimbursement->status === 'DRAFT')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                    Draft (Belum Diajukan)
                </span>
            @elseif(in_array($reimbursement->status, ['SUBMITTED', 'PENDING_VERIFICATION']))
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu Verifikasi Finance
                </span>
            @elseif($reimbursement->status === 'APPROVED')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Disetujui (Menunggu Pembayaran / Kas Keluar)
                </span>
            @elseif($reimbursement->status === 'PAID')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Selesai Dibayar (PAID)
                </span>
            @elseif($reimbursement->status === 'REJECTED')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-red-100 text-red-800 border border-red-300">
                    Ditolak (Rejected)
                </span>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT: Items & Receipt Proofs -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card: Data Pemohon & Info Perjalanan -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Identitas Pengaju Klaim</div>
                    <span class="text-xs font-bold text-amber-700">#RMB-{{ $reimbursement->id }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 font-black text-base flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($reimbursement->user->name ?? 'User', 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-black text-slate-900">{{ $reimbursement->user->name }}</div>
                        <div class="text-xs text-slate-400">{{ $reimbursement->user->email }} · Dept: {{ $reimbursement->user->department->name ?? 'Sales' }}</div>
                        <div class="text-xs font-bold text-slate-700 mt-1">
                            Perjalanan Dinas: <a href="{{ route('travel.show', $reimbursement->travel_request_id) }}" class="text-amber-600 underline">#TRV-{{ $reimbursement->travel_request_id }} ke {{ $reimbursement->travelRequest->destination }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Itemized Expenses Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rincian Item Pengeluaran & Kuitansi</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar nota, tiket, dan bukti pembayaran yang dilampirkan</p>
                    </div>
                    <span class="text-xs font-bold text-slate-600">{{ $reimbursement->items->count() }} Item</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                            <tr>
                                <th class="py-3 px-4">Kategori & Deskripsi</th>
                                <th class="py-3 px-4">Tanggal Transaksi</th>
                                <th class="py-3 px-4">Bukti Nota</th>
                                <th class="py-3 px-4 text-right">Nominal</th>
                                @if($reimbursement->status === 'DRAFT' && auth()->id() === $reimbursement->user_id)
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reimbursement->items as $item)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold mb-1
                                            @if($item->category === 'TRANSPORT') bg-blue-100 text-blue-700
                                            @elseif($item->category === 'HOTEL') bg-purple-100 text-purple-700
                                            @elseif($item->category === 'MEAL') bg-orange-100 text-orange-700
                                            @elseif($item->category === 'FLIGHT') bg-sky-100 text-sky-700
                                            @else bg-slate-100 text-slate-700
                                            @endif">
                                            {{ $item->category }}
                                        </span>
                                        <div class="font-bold text-slate-800 text-xs">{{ $item->description }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 font-medium">
                                        {{ $item->transaction_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($item->receipt_path)
                                            <a href="{{ asset('storage/' . $item->receipt_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 font-bold text-[11px] hover:bg-amber-100 transition">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat Kuitansi</span>
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">Tanpa Bukti</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </td>
                                    @if($reimbursement->status === 'DRAFT' && auth()->id() === $reimbursement->user_id)
                                        <td class="py-3.5 px-4 text-right">
                                            <form method="POST" action="{{ route('reimbursements.items.delete', $item->id) }}" onsubmit="return confirm('Hapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded-md text-red-500 hover:bg-red-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        Belum ada rincian item pengeluaran yang dimasukkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form: Tambah Item Pengeluaran (Hanya saat status DRAFT) -->
            @if($reimbursement->status === 'DRAFT' && auth()->id() === $reimbursement->user_id)
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Tambah Item Pengeluaran Baru
                    </h3>

                    <form method="POST" action="{{ route('reimbursements.items.add', $reimbursement->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Kategori Pengeluaran *</label>
                                <select name="category" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="TRANSPORT">TRANSPORT (Taksi, Bensin, Tol)</option>
                                    <option value="HOTEL">HOTEL (Akomodasi Penginapan)</option>
                                    <option value="MEAL">MEAL (Konsumsi Makan Dinas)</option>
                                    <option value="FLIGHT">FLIGHT (Tiket Pesawat)</option>
                                    <option value="OTHER">OTHER (Biaya Operasional Lainnya)</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Tanggal Transaksi *</label>
                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Deskripsi / Keterangan Transaksi *</label>
                                <input type="text" name="description" placeholder="Contoh: Tiket Grab dari Bandara Juanda ke Lokasi Klien" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Nominal Biaya (Rp) *</label>
                                <input type="number" name="amount" min="1" placeholder="Contoh: 185000" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Foto Struk / File Kuitansi</label>
                                <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800">
                            </div>
                        </div>

                        <div class="pt-2 text-right">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition shadow-sm">
                                + Tambahkan Item Pengeluaran
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        <!-- RIGHT: Financial Summary & Actions -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card: Financial Calculation Breakdown -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Rekapitulasi Keuangan</div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Total Bukti Pengeluaran:</span>
                        <span class="font-extrabold text-slate-900">Rp {{ number_format($reimbursement->total_items_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Uang Muka (Kasbon):</span>
                        <span class="font-bold text-slate-700">Rp {{ number_format($reimbursement->advance_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="border-t border-slate-100 pt-2 flex items-center justify-between">
                        <span class="font-bold text-slate-800">Nominal Disetujui:</span>
                        <span class="text-base font-black text-amber-700">
                            {{ $reimbursement->approved_amount ? 'Rp ' . number_format($reimbursement->approved_amount, 0, ',', '.') : 'Belum Diverifikasi' }}
                        </span>
                    </div>

                    @if($reimbursement->difference_amount !== null)
                        <div class="flex items-center justify-between text-xs font-bold pt-1 border-t border-slate-100">
                            <span>Selisih Pembayaran:</span>
                            <span class="{{ $reimbursement->difference_amount >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($reimbursement->difference_amount, 0, ',', '.') }}
                                <span class="text-[10px] block text-right font-normal text-slate-400">
                                    {{ $reimbursement->difference_amount >= 0 ? '(Dibayarkan ke Karyawan)' : '(Kelebihan Uang Muka)' }}
                                </span>
                            </span>
                        </div>
                    @endif
                </div>

                @if($reimbursement->external_journal_ref)
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs">
                        <div class="text-[10px] text-emerald-700 font-bold uppercase">Referensi Pembayaran</div>
                        <div class="font-black text-emerald-900 mt-0.5 text-sm">{{ $reimbursement->external_journal_ref }}</div>
                        <div class="text-[10px] text-emerald-600 mt-1">Dicatat pada: {{ $reimbursement->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                @endif

                @if($reimbursement->finance_note)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 italic">
                        <strong>Catatan Finance:</strong> "{{ $reimbursement->finance_note }}"
                    </div>
                @endif
            </div>

            <!-- Employee Action: Submit to Finance -->
            @if($reimbursement->status === 'DRAFT' && auth()->id() === $reimbursement->user_id)
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Aksi Karyawan</div>
                    <form method="POST" action="{{ route('reimbursements.submit', $reimbursement->id) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow-md shadow-amber-500/20">
                            Kirim Klaim ke Finance untuk Diverifikasi →
                        </button>
                    </form>
                </div>
            @endif

            <!-- Finance Verification Panel (When PENDING_VERIFICATION) -->
            @if(in_array($reimbursement->status, ['SUBMITTED', 'PENDING_VERIFICATION']) && (auth()->user()->isFinance() || auth()->user()->isAdmin()))
                <div class="bg-white rounded-3xl border border-amber-300 p-6 shadow-md shadow-amber-500/10 space-y-4" x-data="{ verStatus: 'APPROVED' }">
                    <div class="flex items-center gap-2 text-xs font-black text-slate-900 uppercase tracking-wider">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Verifikasi Keuangan (Finance)
                    </div>

                    <form method="POST" action="{{ route('finance.verifications.action', $reimbursement->id) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <label class="p-2.5 rounded-xl border text-center font-bold text-xs cursor-pointer transition" :class="verStatus === 'APPROVED' ? 'bg-emerald-50 border-emerald-500 text-emerald-800' : 'border-slate-200 text-slate-600'">
                                <input type="radio" name="status" value="APPROVED" x-model="verStatus" class="hidden">
                                ✓ Setujui Klaim
                            </label>
                            <label class="p-2.5 rounded-xl border text-center font-bold text-xs cursor-pointer transition" :class="verStatus === 'REJECTED' ? 'bg-red-50 border-red-500 text-red-800' : 'border-slate-200 text-slate-600'">
                                <input type="radio" name="status" value="REJECTED" x-model="verStatus" class="hidden">
                                ✕ Tolak Klaim
                            </label>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Nominal yang Disetujui (Rp) *</label>
                            <input type="number" name="approved_amount" value="{{ $reimbursement->total_items_amount }}" min="0" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-extrabold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">
                                Catatan Verifikasi <span x-show="verStatus === 'REJECTED'" class="text-red-600">* (Wajib jika ditolak)</span>
                            </label>
                            <textarea name="finance_note" rows="2" placeholder="Catatan hasil verifikasi bukti kuitansi..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl font-extrabold text-xs text-white transition shadow-sm" :class="verStatus === 'APPROVED' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-red-600 hover:bg-red-500'">
                            Simpan Verifikasi Keuangan
                        </button>
                    </form>
                </div>
            @endif

            <!-- Finance Payment Execution Panel (When APPROVED and waiting payout) -->
            @if($reimbursement->status === 'APPROVED' && (auth()->user()->isFinance() || auth()->user()->isAdmin()))
                <div class="bg-white rounded-3xl border border-emerald-300 p-6 shadow-md shadow-emerald-500/10 space-y-4">
                    <div class="flex items-center gap-2 text-xs font-black text-slate-900 uppercase tracking-wider">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Eksekusi Pencairan / Pembayaran
                    </div>

                    <p class="text-xs text-slate-500">
                        Klaim ini telah disetujui sebesar <strong>Rp {{ number_format($reimbursement->approved_amount, 0, ',', '.') }}</strong>. Masukkan nomor bukti transfer / jurnal untuk menandai lunas.
                    </p>

                    <form method="POST" action="{{ route('finance.payments.action', $reimbursement->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Tanggal Pembayaran *</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Nomor Referensi Jurnal / Bank *</label>
                            <input type="text" name="external_journal_ref" placeholder="Contoh: TRF-BCA-20261026-0041" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-md shadow-emerald-600/20">
                            Konfirmasi Pembayaran Selesai (PAID)
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

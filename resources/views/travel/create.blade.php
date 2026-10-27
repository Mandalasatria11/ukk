@extends('layouts.app')

@section('title', 'Ajukan Perjalanan Dinas')
@section('page-title', 'Form Pengajuan Perjalanan Dinas')
@section('page-subtitle', 'Isi data formulir penugasan kerja dinas resmi perusahaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('travel.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-10">
        <form method="POST" action="{{ route('travel.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Lokasi & Jadwal -->
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    1. Rincian Lokasi & Waktu Penugasan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Kota / Lokasi Tujuan *</label>
                        <input type="text" name="destination" value="{{ old('destination') }}" required placeholder="Contoh: Surabaya, Jawa Timur (Kantor Cabang / Klien)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Tanggal Keberangkatan *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Tanggal Kepulangan *</label>
                        <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+2 days'))) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800">
                    </div>
                </div>
            </div>

            <!-- Section 2: Keperluan & Alasan -->
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    2. Maksud & Tujuan Perjalanan Dinas
                </h3>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Uraian Tugas / Alasan Perjalanan *</label>
                    <textarea name="purpose" rows="3" required placeholder="Jelaskan agenda kerja, klien yang ditemui, target kunjungan, dan urgensi perjalanan dinas ini..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800 leading-relaxed">{{ old('purpose') }}</textarea>
                </div>
            </div>

            <!-- Section 3: Anggaran & Kebijakan -->
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    3. Anggaran & Kebijakan Perusahaan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Kebijakan / Kategori Wilayah *</label>
                        <select name="policy_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800">
                            <option value="">Pilih Kebijakan & Plafon Biaya</option>
                            @foreach($policies as $p)
                                <option value="{{ $p->id }}" {{ old('policy_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (Maks Plafon: Rp {{ number_format($p->max_budget, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Estimasi Total Biaya (Rp) *</label>
                        <input type="number" name="estimated_cost" value="{{ old('estimated_cost') }}" min="0" required placeholder="Contoh: 3500000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Estimasi mencakup tiket transportasi, hotel penginapan, dan uang harian.</span>
                    </div>
                </div>
            </div>

            <!-- Section 4: Dokumen Pendukung -->
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    4. Dokumen Pendukung (Opsional)
                </h3>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Lampiran Surat Tugas / Undangan / Memo</label>
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none text-slate-800 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, PNG, JPG, DOC (Maks. 10MB)</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                <button type="submit" name="action_type" value="draft" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer">
                    Simpan sebagai Draf
                </button>
                <button type="submit" name="action_type" value="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition shadow-md shadow-amber-500/20 cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Kirim Pengajuan ke Manager</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

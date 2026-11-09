@extends('layouts.app')

@section('title', 'Backup Data & Export/Import Excel')
@section('page-title', 'Pusat Backup Data, Hapus Data & Integrasi Excel')
@section('page-subtitle', 'Kelola pencadangan data (Backup), konversi/import ke Microsoft Excel, dan penghapusan data sistem')

@section('content')
<div class="space-y-6">

    <!-- Top Banner: Buat Backup & Export Cepat ke Excel -->
    <div class="bg-gradient-to-r from-[#121824] to-[#1e293b] rounded-3xl p-6 sm:p-8 text-white shadow-lg border border-slate-800">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider">
                    <span>Otoritas: {{ auth()->user()->role }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight">Manajemen Backup Data & Export/Import Excel</h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Buat cadangan (snapshot) seluruh data sistem kapan saja. Setiap data yang sudah di-backup dapat langsung dibuka atau di-import ke <strong>Microsoft Excel (.xls Multi-Sheet & .csv UTF-8)</strong> maupun dipulihkan kembali ke sistem.
                </p>
            </div>

            <!-- Form Buat Backup Sekarang -->
            <div class="bg-[#1a2333] p-4 rounded-2xl border border-slate-700/80 w-full lg:w-96 space-y-3">
                <form method="POST" action="{{ route('admin.backups.create') }}" class="space-y-2.5">
                    @csrf
                    <label class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">
                        Buat Cadangan (Backup) Baru
                    </label>
                    <input type="text" name="label" placeholder="Catatan Backup (misal: Backup Bulanan Q4)" class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Buat Backup Data Sekarang</span>
                    </button>
                </form>

                <div class="pt-2 border-t border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.backups.excel', 'live') }}" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] text-center transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Live ke Excel (.xls)</span>
                    </a>
                    <a href="{{ route('admin.backups.csv', ['source' => 'live', 'module' => 'travel_requests']) }}" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-[11px] transition border border-slate-700">
                        CSV (.csv)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Jumlah Data Aktif Saat Ini -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Pengguna</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $liveStats['users'] }}</div>
            <a href="{{ route('admin.backups.csv', ['source' => 'live', 'module' => 'users']) }}" class="text-[10px] font-bold text-emerald-600 hover:underline mt-1 inline-block">Unduh CSV Excel →</a>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Perjalanan Dinas</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $liveStats['travel_requests'] }}</div>
            <a href="{{ route('admin.backups.csv', ['source' => 'live', 'module' => 'travel_requests']) }}" class="text-[10px] font-bold text-emerald-600 hover:underline mt-1 inline-block">Unduh CSV Excel →</a>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Data Approval</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $liveStats['approvals'] }}</div>
            <a href="{{ route('admin.backups.csv', ['source' => 'live', 'module' => 'approvals']) }}" class="text-[10px] font-bold text-emerald-600 hover:underline mt-1 inline-block">Unduh CSV Excel →</a>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Reimbursement</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $liveStats['reimbursements'] }}</div>
            <a href="{{ route('admin.backups.csv', ['source' => 'live', 'module' => 'reimbursements']) }}" class="text-[10px] font-bold text-emerald-600 hover:underline mt-1 inline-block">Unduh CSV Excel →</a>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Master Kebijakan</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $liveStats['policies'] }}</div>
            <span class="text-[10px] text-slate-400 mt-1 block">Aturan Plafon</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Audit Logs</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $liveStats['logs'] }}</div>
            <span class="text-[10px] text-slate-400 mt-1 block">Jejak Aktivitas</span>
        </div>
    </div>

    <!-- Tabel Daftar Arsip Backup Data (Siap Di-Import / Dibuka di Excel) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Daftar Riwayat Backup Data Tersimpan</h3>
                <p class="text-xs text-slate-400">Setiap data backup di bawah ini dapat di-export/dibuka langsung di Microsoft Excel (.xls / .csv), dipulihkan (restore), atau dihapus</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                {{ count($backups) }} Arsip Backup
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">Nama File & Label Backup</th>
                        <th class="py-3.5 px-5">Waktu & Pembuat</th>
                        <th class="py-3.5 px-5">Ringkasan Data Tersimpan</th>
                        <th class="py-3.5 px-5">Buka / Import ke Excel</th>
                        <th class="py-3.5 px-5 text-right">Aksi (Restore / Hapus)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($backups as $b)
                        @php $counts = $b['meta']['counts'] ?? []; @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-5">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $b['meta']['label'] ?? 'System Backup' }}</div>
                                <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $b['filename'] }} ({{ $b['size_kb'] }} KB)</div>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $b['meta']['created_at'] ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500">Oleh: {{ $b['meta']['created_by'] ?? 'Admin' }} ({{ $b['meta']['created_by_role'] ?? 'ADMIN' }})</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        {{ $counts['travel_requests'] ?? 0 }} Perjalanan
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        {{ $counts['approvals'] ?? 0 }} Approval
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        {{ $counts['reimbursements'] ?? 0 }} Reimbursement
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        {{ $counts['users'] ?? 0 }} User
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <!-- Unduh Workbook Excel Multi-Sheet (.xls) -->
                                    <a href="{{ route('admin.backups.excel', $b['filename']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[11px] transition shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Excel (.xls)</span>
                                    </a>

                                    <!-- Unduh CSV untuk Import ke Excel -->
                                    <a href="{{ route('admin.backups.csv', ['source' => $b['filename'], 'module' => 'travel_requests']) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-[11px] transition" title="Unduh CSV Perjalanan Dinas untuk Import ke Excel">
                                        <span>CSV Dinas</span>
                                    </a>
                                    <a href="{{ route('admin.backups.csv', ['source' => $b['filename'], 'module' => 'reimbursements']) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-[11px] transition" title="Unduh CSV Reimbursement untuk Import ke Excel">
                                        <span>CSV Klaim</span>
                                    </a>
                                    <a href="{{ route('admin.backups.json', $b['filename']) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition" title="Unduh file mentah JSON">
                                        <span>JSON</span>
                                    </a>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-right whitespace-nowrap space-x-1">
                                <form method="POST" action="{{ route('admin.backups.restore', $b['filename']) }}" class="inline-block" onsubmit="return confirm('Pulihkan (Restore) data dari backup ini? Data perjalanan & reimbursement saat ini akan digantikan oleh data dari backup ini.')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold text-xs transition cursor-pointer">
                                        ↺ Pulihkan
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.backups.delete', $b['filename']) }}" class="inline-block" onsubmit="return confirm('Hapus file arsip backup ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold text-xs transition cursor-pointer">
                                        ✕ Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <p class="text-sm font-bold text-slate-600">Belum ada file arsip backup yang dibuat.</p>
                                <p class="text-xs text-slate-400 mt-1">Klik tombol "Buat Backup Data Sekarang" di atas untuk menyimpan cadangan data pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Grid: Import File Backup/CSV & Hapus Data Massal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Card 1: Import Data dari File Backup (.json) atau File Excel CSV (.csv) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Import / Pulihkan Data dari File (.csv / .json)</h3>
                    <p class="text-xs text-slate-400">Unggah file CSV yang telah diolah di Excel atau file JSON hasil backup</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-200">
                    Excel CSV & JSON
                </span>
            </div>

            <form method="POST" action="{{ route('admin.backups.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="p-4 rounded-2xl bg-slate-50 border border-dashed border-slate-300 space-y-2">
                    <label class="text-xs font-bold text-slate-700 block">Pilih File Backup (.csv atau .json) *</label>
                    <input type="file" name="backup_file" accept=".csv,.json" required class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        • Gunakan file <strong>.csv</strong> hasil export Backup Dinas yang telah dibuka/diedit di Microsoft Excel untuk menambahkan data perjalanan dinas.<br>
                        • Gunakan file <strong>.json</strong> untuk memulihkan penuh seluruh snapshot backup.
                    </p>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
                    Unggah & Import Data ke Sistem →
                </button>
            </form>
        </div>

        <!-- Card 2: Hapus / Bersihkan Data Sistem (Khusus Admin & Super Admin) -->
        <div class="bg-white rounded-3xl border border-red-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-red-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-red-700">Hapus & Bersihkan Data Sistem</h3>
                    <p class="text-xs text-slate-500">Hapus data perjalanan dinas, reimbursement, atau riwayat log dengan proteksi auto-backup</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-[10px] font-extrabold border border-red-200">
                    Admin & Super Admin
                </span>
            </div>

            <form method="POST" action="{{ route('admin.backups.purge') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pada kategori yang dipilih?')" class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Pilih Kategori Data yang Akan Dihapus *</label>
                    <select name="target" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-red-500">
                        <option value="rejected_cancelled_travels">Hapus Perjalanan Dinas Berstatus Ditolak / Dibatalkan / Draft</option>
                        <option value="all_reimbursements">Hapus Seluruh Data Klaim Reimbursement</option>
                        <option value="all_travels">Hapus Seluruh Data Perjalanan Dinas & Approval</option>
                        <option value="activity_logs">Bersihkan Seluruh Riwayat Audit / Activity Logs</option>
                    </select>
                </div>

                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 cursor-pointer">
                    <input type="checkbox" name="auto_backup" value="1" checked class="rounded text-amber-600 focus:ring-amber-500">
                    <span class="font-semibold">Otomatis buat Backup Data (.json) terlebih dahulu sebelum menghapus</span>
                </label>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
                    ✕ Eksekusi Hapus Data Terpilih
                </button>
            </form>
        </div>

    </div>

</div>
@endsection


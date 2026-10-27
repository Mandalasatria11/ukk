@extends('layouts.app')

@section('title', 'Pusat Persetujuan Perjalanan Dinas')
@section('page-title', auth()->user()->isManager() ? 'Persetujuan Dinas Tim' : 'Pusat Persetujuan Perjalanan Dinas')
@section('page-subtitle', auth()->user()->isManager() ? 'Tinjau permohonan staf di departemen Anda' : 'Pemeriksaan kepatuhan permohonan dinas seluruh karyawan')

@section('content')
<div class="space-y-6">

    <!-- Header Navigation Tabs -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('approvals.index', ['tab' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'pending' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Menunggu Keputusan</span>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'pending' ? 'bg-slate-950 text-white' : 'bg-red-500 text-white' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('approvals.index', ['tab' => 'history']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'history' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Riwayat Keputusan (Selesai)
            </a>
        </div>

        <span class="text-xs text-slate-500 font-medium hidden sm:inline-block">
            Login sebagai: <strong class="text-slate-800">{{ auth()->user()->name }} ({{ auth()->user()->role }})</strong>
        </span>
    </div>

    <!-- Approvals List -->
    <div class="space-y-4">
        @forelse($approvals as $a)
            @php $t = $a->travelRequest; @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:border-slate-300 transition" x-data="{ modalOpen: false, decision: 'APPROVED' }">

                <!-- Header: Requester Profile & Metadata -->
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <!-- Siapa yang meminta pengajuan (Requester Profile) -->
                    <div class="flex items-center gap-3.5">
                        <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-base shadow-sm">
                            {{ strtoupper(substr($t->user->name ?? 'User', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-slate-900">{{ $t->user->name }}</h3>
                                <span class="px-2.5 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 text-[11px] font-bold">
                                    {{ $t->user->position->name ?? 'Staff' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">
                                    Departemen: {{ $t->department->name ?? 'Dept' }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                                <span>{{ $t->user->email }}</span>
                                <span>·</span>
                                <span>Diajukan: {{ $a->created_at->format('d M Y, H:i') }}</span>
                                <span>·</span>
                                <span class="text-amber-700 font-bold">#TRV-{{ $t->id }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <div>
                        @if($a->status === 'PENDING')
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Keputusan Anda
                            </span>
                        @elseif($a->status === 'APPROVED')
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Disetujui (Approved)
                            </span>
                        @else
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-red-100 text-red-800 border border-red-300">
                                Ditolak (Rejected)
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Body: Travel Itinerary & Cost Details -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 py-4">
                    <!-- Left: Tujuan & Alasan -->
                    <div class="md:col-span-7 space-y-2">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Tujuan Perjalanan Dinas</div>
                            <div class="text-base font-black text-slate-900 mt-0.5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $t->destination }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Alasan & Uraian Tugas</div>
                            <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed mt-0.5">
                                {{ $t->purpose }}
                            </p>
                        </div>

                        <!-- Dokumen Pendukung -->
                        @if($t->documents->isNotEmpty())
                            <div class="flex items-center gap-2 pt-1">
                                <span class="text-[11px] font-bold text-slate-500">Dokumen Lampiran:</span>
                                @foreach($t->documents as $doc)
                                    <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-bold hover:bg-amber-100 transition border border-amber-200">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span>{{ Str::limit($doc->original_name, 20) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Right: Jadwal & Anggaran -->
                    <div class="md:col-span-5 bg-slate-50 p-4 rounded-2xl border border-slate-100 flex flex-col justify-between space-y-3">
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase block">Jadwal</span>
                                <span class="font-bold text-slate-800">{{ $t->start_date->format('d/m/Y') }}</span>
                                <span class="text-slate-400 text-[10px] block">s/d {{ $t->end_date->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase block">Durasi</span>
                                <span class="font-extrabold text-slate-900">{{ $t->start_date->diffInDays($t->end_date) + 1 }} Hari</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-200/80 pt-2">
                            <span class="text-slate-400 text-[10px] font-bold uppercase block">Estimasi Biaya Diajukan</span>
                            <span class="text-xl font-black text-slate-900 block">Rp {{ number_format($t->estimated_cost, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">
                                Plafon Kategori: {{ $t->policy->name ?? 'Standar' }} (Maks. Rp {{ number_format($t->policy->max_budget ?? 0, 0, ',', '.') }})
                            </span>
                        </div>

                        @if($a->note)
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 italic">
                                <strong>Catatan Review:</strong> "{{ $a->note }}"
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer: Decision Action Buttons (If PENDING) -->
                @if($a->status === 'PENDING')
                    <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ route('travel.show', $t->id) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                            Lihat Rincian Halaman Penuh →
                        </a>

                        <div class="flex items-center gap-2">
                            <!-- Button Trigger Decision Modal -->
                            <button type="button" @click="decision = 'REJECTED'; modalOpen = true" class="px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-extrabold text-xs transition border border-red-200 cursor-pointer">
                                ✕ Tolak Pengajuan
                            </button>
                            <button type="button" @click="decision = 'APPROVED'; modalOpen = true" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-md shadow-emerald-600/20 cursor-pointer">
                                ✓ Setujui Pengajuan
                            </button>
                        </div>
                    </div>

                    <!-- MODAL KEPUTUSAN (APPROVE / REJECT) -->
                    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
                        <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <h3 class="text-base font-extrabold text-slate-900" x-text="decision === 'APPROVED' ? 'Konfirmasi Persetujuan Dinas' : 'Alasan Penolakan Pengajuan'"></h3>
                                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                            </div>

                            <form method="POST" action="{{ route('approvals.decide', $a->id) }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="decision" :value="decision">

                                <div class="text-xs text-slate-600">
                                    Pengajuan perjalanan dinas ke <strong class="text-slate-900">{{ $t->destination }}</strong> oleh <strong class="text-slate-900">{{ $t->user->name }}</strong>.
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">
                                        Catatan / Alasan
                                        <span x-show="decision === 'REJECTED'" class="text-red-600 font-bold">* (Wajib diisi jika menolak)</span>
                                    </label>
                                    <textarea name="note" rows="3" :required="decision === 'REJECTED'" placeholder="Berikan catatan persetujuan atau alasan detail penolakan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white text-slate-800"></textarea>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-extrabold text-white transition shadow-sm" :class="decision === 'APPROVED' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-red-600 hover:bg-red-500'">
                                        <span x-text="decision === 'APPROVED' ? 'Ya, Setujui Perjalanan' : 'Ya, Tolak Pengajuan'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-bold text-slate-700">Tidak ada data persetujuan pada tab ini.</p>
                <p class="text-xs text-slate-400 mt-1">Semua permohonan dinas sudah tertinjau.</p>
            </div>
        @endforelse

        @if($approvals->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-slate-200">
                {{ $approvals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

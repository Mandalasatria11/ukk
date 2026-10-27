@extends('layouts.app')

@section('title', 'Detail Perjalanan #TRV-' . $travel->id)
@section('page-title', 'Detail Perjalanan Dinas')
@section('page-subtitle', 'Informasi lengkap pengajuan, rincian biaya, dan timeline persetujuan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Bar Navigation & Status -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="{{ route('travel.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center gap-2">
            @if(in_array($travel->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu Persetujuan
                </span>
            @elseif($travel->status === 'APPROVED')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Disetujui (Approved)
                </span>
            @elseif($travel->status === 'REJECTED')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-red-100 text-red-800 border border-red-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Ditolak (Rejected)
                </span>
            @elseif($travel->status === 'DRAFT')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                    Draft
                </span>
            @else
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-gray-100 text-gray-700">
                    {{ $travel->status }}
                </span>
            @endif
        </div>
    </div>

    <!-- Main Grid: Left Details, Right Approvals & Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: Travel Details & Requester Info -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card 1: Data Pemohon (Requester Identity) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Identitas Pemohon</div>
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-amber-500 text-slate-950 font-black text-lg flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($travel->user->name ?? 'User', 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-base font-black text-slate-900">{{ $travel->user->name }}</div>
                        <div class="text-xs font-semibold text-slate-500">{{ $travel->user->email }}</div>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">
                                Dept: {{ $travel->department->name ?? 'Dept' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[11px] font-bold">
                                Jabatan: {{ $travel->user->position->name ?? 'Staff' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Rincian Perjalanan & Keperluan -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Rincian Perjalanan Dinas</div>

                <div>
                    <div class="text-xs text-slate-400 font-semibold">Tujuan Perjalanan</div>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ $travel->destination }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-y border-slate-100 py-3">
                    <div>
                        <div class="text-xs text-slate-400 font-semibold">Keberangkatan</div>
                        <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $travel->start_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-semibold">Kepulangan</div>
                        <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $travel->end_date->format('d M Y') }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-400 font-semibold">Uraian Tugas / Alasan Perjalanan</div>
                    <p class="text-xs text-slate-700 mt-1 bg-slate-50 p-4 rounded-xl leading-relaxed whitespace-pre-line border border-slate-100">
                        {{ $travel->purpose }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Estimasi Biaya</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">Rp {{ number_format($travel->estimated_cost, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Plafon Kebijakan</div>
                        <div class="text-xs font-bold text-slate-700 mt-1">{{ $travel->policy->name ?? 'Standar' }}</div>
                        <div class="text-[10px] text-slate-500">Maks: Rp {{ number_format($travel->policy->max_budget ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Dokumen Pendukung -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Dokumen Pendukung</div>
                    <span class="text-xs font-bold text-slate-500">{{ $travel->documents->count() }} Lampiran</span>
                </div>

                <div class="space-y-2">
                    @forelse($travel->documents as $doc)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">{{ $doc->original_name }}</div>
                                    <div class="text-[10px] text-slate-400">Diunggah: {{ $doc->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                    Buka File
                                </a>
                                @if(auth()->id() === $travel->user_id && $travel->status === 'DRAFT')
                                    <form method="POST" action="{{ route('travel.documents.delete', $doc->id) }}" onsubmit="return confirm('Hapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg text-red-500 hover:bg-red-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-4 text-center bg-slate-50/50 rounded-xl">
                            Belum ada dokumen yang diunggah.
                        </div>
                    @endforelse
                </div>

                @if(auth()->id() === $travel->user_id && in_array($travel->status, ['DRAFT', 'PENDING_APPROVAL']))
                    <form method="POST" action="{{ route('travel.documents.upload', $travel->id) }}" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                        @csrf
                        <input type="file" name="document" required class="flex-1 text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
                            Unggah Dokumen
                        </button>
                    </form>
                @endif
            </div>

        </div>

        <!-- RIGHT COLUMN: Timeline Persetujuan & Actions -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card: Timeline Persetujuan -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center justify-between">
                    <span>Alur Persetujuan (Approval Flow)</span>
                    <span class="text-amber-600 font-bold">Multi-Level</span>
                </div>

                <div class="space-y-4">
                    <!-- Step 1: Pengajuan -->
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                            ✓
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900">1. Pengajuan Diajukan Karyawan</div>
                            <div class="text-[11px] text-slate-500">{{ $travel->user->name }} · {{ $travel->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>

                    <!-- Approvals List -->
                    @forelse($travel->approvals as $idx => $appr)
                        <div class="flex items-start gap-3 pt-2 border-t border-slate-100">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5
                                @if($appr->status === 'APPROVED') bg-emerald-500 text-white
                                @elseif($appr->status === 'REJECTED') bg-red-500 text-white
                                @else bg-amber-500 text-slate-950 animate-pulse
                                @endif">
                                {{ $appr->status === 'APPROVED' ? '✓' : ($appr->status === 'REJECTED' ? '✕' : ($idx + 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs font-extrabold text-slate-900">
                                        {{ $idx === 0 ? 'Persetujuan Level 1: Manager' : 'Persetujuan Level 2: HRD' }}
                                    </div>
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded
                                        @if($appr->status === 'APPROVED') bg-emerald-100 text-emerald-800
                                        @elseif($appr->status === 'REJECTED') bg-red-100 text-red-800
                                        @else bg-amber-100 text-amber-800
                                        @endif">
                                        {{ $appr->status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Peninjau: <strong>{{ $appr->approver->name ?? 'Manager Departemen' }}</strong>
                                </div>
                                @if($appr->note)
                                    <div class="mt-1.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 italic">
                                        "{{ $appr->note }}"
                                    </div>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-1">
                                    {{ $appr->updated_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-3 italic">
                            Pengajuan belum dikirimkan ke persetujuan.
                        </div>
                    @endforelse
                </div>

                <!-- APPROVER INLINE ACTION FORM (If current user is the approver/manager/HRD) -->
                @php
                    $pendingApprovalForMe = $travel->approvals->where('status', 'PENDING')->first();
                    $canIApprove = $pendingApprovalForMe && (
                        auth()->id() === $pendingApprovalForMe->approver_id ||
                        (auth()->user()->isManager() && auth()->user()->department_id === $travel->department_id) ||
                        auth()->user()->isHrd() ||
                        auth()->user()->isAdmin()
                    );
                @endphp

                @if($canIApprove)
                    <div class="mt-6 pt-5 border-t border-slate-200" x-data="{ decision: 'APPROVED' }">
                        <div class="text-xs font-extrabold text-slate-900 mb-2 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Tindakan Persetujuan Anda ({{ auth()->user()->role }})
                        </div>

                        <form method="POST" action="{{ route('approvals.decide', $pendingApprovalForMe->id) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-2">
                                <label class="p-3 rounded-xl border cursor-pointer text-center font-bold text-xs transition" :class="decision === 'APPROVED' ? 'bg-emerald-50 border-emerald-500 text-emerald-800' : 'border-slate-200 text-slate-600'">
                                    <input type="radio" name="decision" value="APPROVED" x-model="decision" class="hidden">
                                    ✓ Setujui (Approve)
                                </label>
                                <label class="p-3 rounded-xl border cursor-pointer text-center font-bold text-xs transition" :class="decision === 'REJECTED' ? 'bg-red-50 border-red-500 text-red-800' : 'border-slate-200 text-slate-600'">
                                    <input type="radio" name="decision" value="REJECTED" x-model="decision" class="hidden">
                                    ✕ Tolak (Reject)
                                </label>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-slate-700 block mb-1">
                                    Catatan Keputusan <span x-show="decision === 'REJECTED'" class="text-red-600">* (Wajib jika ditolak)</span>
                                </label>
                                <textarea name="note" rows="2" placeholder="Tuliskan catatan persetujuan atau alasan penolakan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl font-extrabold text-xs text-white transition shadow-sm" :class="decision === 'APPROVED' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-red-600 hover:bg-red-500'">
                                Simpan Keputusan Persetujuan
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Card: Next Action for Employee -->
            @if(auth()->id() === $travel->user_id)
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Aksi Karyawan</div>

                    @if($travel->status === 'DRAFT')
                        <form method="POST" action="{{ route('travel.submit', $travel->id) }}">
                            @csrf
                            <button type="submit" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow-md shadow-amber-500/20">
                                Kirim Pengajuan ke Manager Sekarang →
                            </button>
                        </form>

                        <form method="POST" action="{{ route('travel.destroy', $travel->id) }}" onsubmit="return confirm('Hapus draft ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 rounded-xl text-red-600 hover:bg-red-50 font-bold text-xs transition">
                                Hapus Draft
                            </button>
                        </form>
                    @elseif($travel->status === 'APPROVED')
                        <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
                            <div class="font-bold mb-1">Perjalanan Dinas Disetujui!</div>
                            Anda dapat melakukan klaim biaya pengeluaran perjalanan (reimbursement) setelah perjalanan selesai.
                        </div>

                        @if(!$travel->reimbursement)
                            <form method="POST" action="{{ route('reimbursements.store') }}">
                                @csrf
                                <input type="hidden" name="travel_request_id" value="{{ $travel->id }}">
                                <button type="submit" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow-md">
                                    Ajukan Reimbursement untuk Perjalanan Ini →
                                </button>
                            </form>
                        @else
                            <a href="{{ route('reimbursements.show', $travel->reimbursement->id) }}" class="block w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs text-center transition">
                                Buka Klaim Reimbursement Terkait →
                            </a>
                        @endif
                    @elseif(in_array($travel->status, ['PENDING_APPROVAL', 'SUBMITTED']))
                        <form method="POST" action="{{ route('travel.cancel', $travel->id) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 font-bold text-xs transition">
                                Batalkan Pengajuan
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

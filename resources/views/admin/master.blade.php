@extends('layouts.app')

@section('title', 'Master Data & Kebijakan')
@section('page-title', 'Master Data Perusahaan')
@section('page-subtitle', 'Kelola struktur organisasi departemen, posisi jabatan, dan plafon kebijakan dinas')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1: Departemen -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900">Departemen Perusahaan</h3>
                <span class="text-xs font-bold text-amber-700">{{ $departments->count() }} Data</span>
            </div>

            <!-- Add Form -->
            <form method="POST" action="{{ route('admin.departments.store') }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Nama departemen baru..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    + Tambah
                </button>
            </form>

            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @foreach($departments as $d)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-800">{{ $d->name }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $d->users_count }} Karyawan</span>
                        </div>
                        @if($d->users_count == 0)
                            <form method="POST" action="{{ route('admin.departments.delete', $d->id) }}" onsubmit="return confirm('Hapus departemen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold p-1">Hapus</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column 2: Jabatan / Posisi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tingkat Jabatan (Positions)</h3>
                <span class="text-xs font-bold text-amber-700">{{ $positions->count() }} Data</span>
            </div>

            <!-- Add Form -->
            <form method="POST" action="{{ route('admin.positions.store') }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Nama jabatan baru..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    + Tambah
                </button>
            </form>

            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @foreach($positions as $p)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-800">{{ $p->name }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $p->users_count }} Karyawan</span>
                        </div>
                        @if($p->users_count == 0)
                            <form method="POST" action="{{ route('admin.positions.delete', $p->id) }}" onsubmit="return confirm('Hapus jabatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold p-1">Hapus</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column 3: Kebijakan Perjalanan -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900">Kebijakan Plafon Biaya</h3>
                <span class="text-xs font-bold text-amber-700">{{ $policies->count() }} Aturan</span>
            </div>

            <!-- Add Form -->
            <form method="POST" action="{{ route('admin.policies.store') }}" class="space-y-2 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                @csrf
                <input type="text" name="name" required placeholder="Nama Kebijakan (e.g. Domestik - Staff)" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs outline-none">

                <div class="grid grid-cols-2 gap-2">
                    <select name="position_id" required class="bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none">
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>

                    <select name="destination_tier" required class="bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none">
                        <option value="LOCAL">LOCAL</option>
                        <option value="DOMESTIC">DOMESTIC</option>
                        <option value="INTERNATIONAL">INTERNATIONAL</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="number" name="max_budget" required min="0" placeholder="Maks Budget (Rp)" class="flex-1 bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs outline-none">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition">
                        Simpan
                    </button>
                </div>
            </form>

            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @foreach($policies as $pol)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900">{{ $pol->name }}</span>
                            <span class="text-[10px] text-slate-500 block">{{ $pol->position->name ?? '-' }} · {{ $pol->destination_tier }}</span>
                            <span class="text-xs font-extrabold text-amber-700">Rp {{ number_format($pol->max_budget, 0, ',', '.') }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.policies.delete', $pol->id) }}" onsubmit="return confirm('Hapus aturan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold p-1">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

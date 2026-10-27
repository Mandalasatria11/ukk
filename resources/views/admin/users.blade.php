@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna & Hak Akses')
@section('page-subtitle', 'Kelola akun karyawan, penetapan role (Admin, HRD, Finance, Manager, Employee), dan status akun')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, selectedUser: {} }">

    <!-- Top Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex-1 flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pengguna..." class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 text-slate-800 flex-1 min-w-[200px]">

            <select name="role" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none text-slate-700">
                <option value="">Semua Role</option>
                <option value="SUPER_ADMIN" {{ request('role') === 'SUPER_ADMIN' ? 'selected' : '' }}>SUPER_ADMIN</option>
                <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                <option value="MANAGER" {{ request('role') === 'MANAGER' ? 'selected' : '' }}>MANAGER</option>
                <option value="HRD" {{ request('role') === 'HRD' ? 'selected' : '' }}>HRD</option>
                <option value="FINANCE" {{ request('role') === 'FINANCE' ? 'selected' : '' }}>FINANCE</option>
                <option value="EMPLOYEE" {{ request('role') === 'EMPLOYEE' ? 'selected' : '' }}>EMPLOYEE</option>
            </select>

            <select name="department_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none text-slate-700">
                <option value="">Semua Departemen</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
        </form>

        <button type="button" @click="createModal = true" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>+ Buat Akun Pengguna</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">Nama Pengguna & Email</th>
                        <th class="py-3.5 px-5">Role Sistem</th>
                        <th class="py-3.5 px-5">Departemen</th>
                        <th class="py-3.5 px-5">Jabatan</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-5">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold
                                    @if($u->role === 'SUPER_ADMIN') bg-purple-100 text-purple-700 border border-purple-200
                                    @elseif($u->role === 'ADMIN') bg-blue-100 text-blue-700 border border-blue-200
                                    @elseif($u->role === 'MANAGER') bg-indigo-100 text-indigo-700 border border-indigo-200
                                    @elseif($u->role === 'HRD') bg-rose-100 text-rose-700 border border-rose-200
                                    @elseif($u->role === 'FINANCE') bg-emerald-100 text-emerald-700 border border-emerald-200
                                    @else bg-amber-100 text-amber-800 border border-amber-200
                                    @endif">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-700">
                                {{ $u->department->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-slate-600">
                                {{ $u->position->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1">
                                <button type="button" @click="selectedUser = {{ json_encode($u) }}; editModal = true" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                                    Edit
                                </button>
                                @if(auth()->id() !== $u->id)
                                    <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}" class="inline-block" onsubmit="return confirm('Ubah status aktif/nonaktif akun ini?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition {{ $u->is_active ? 'text-red-600 hover:bg-red-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                            {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: CREATE USER -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Buat Akun Pengguna Baru</h3>
                <button type="button" @click="createModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Email Perusahaan *</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-medium" placeholder="nama@purnama.co.id">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Password Awal *</label>
                    <input type="password" name="password" required minlength="6" value="password123" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Default: password123</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Role *</label>
                        <select name="role" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-semibold">
                            <option value="EMPLOYEE">EMPLOYEE</option>
                            <option value="MANAGER">MANAGER</option>
                            <option value="HRD">HRD</option>
                            <option value="FINANCE">FINANCE</option>
                            <option value="ADMIN">ADMIN</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Departemen</label>
                        <select name="department_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Jabatan</label>
                        <select name="position_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Pilih Jabatan</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs transition shadow-sm">
                        Simpan Akun Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Edit Akun Pengguna</h3>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form :action="'/admin/users/' + selectedUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" :value="selectedUser.name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Email Perusahaan *</label>
                    <input type="email" name="email" :value="selectedUser.email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Password Baru (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tidak diubah" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Role *</label>
                        <select name="role" x-model="selectedUser.role" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-semibold">
                            <option value="EMPLOYEE">EMPLOYEE</option>
                            <option value="MANAGER">MANAGER</option>
                            <option value="HRD">HRD</option>
                            <option value="FINANCE">FINANCE</option>
                            <option value="ADMIN">ADMIN</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Departemen</label>
                        <select name="department_id" x-model="selectedUser.department_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase block mb-1">Jabatan</label>
                        <select name="position_id" x-model="selectedUser.position_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Pilih Jabatan</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition shadow-sm">
                        Perbarui Data Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

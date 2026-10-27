@extends('layouts.app')

@section('title', 'Audit & Activity Logs')
@section('page-title', 'Audit Trail & Jejak Aktivitas Sistem')
@section('page-subtitle', 'Pencatatan kronologis seluruh tindakan approval, transaksi, dan perubahan data pengguna')

@section('content')
<div class="space-y-6">

    <!-- Filters -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.logs') }}" class="flex flex-wrap items-center gap-2">
            <select name="action" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none text-slate-700">
                <option value="">Semua Tindakan (Action)</option>
                <option value="LOGIN" {{ request('action') === 'LOGIN' ? 'selected' : '' }}>LOGIN</option>
                <option value="SUBMIT_TRAVEL" {{ request('action') === 'SUBMIT_TRAVEL' ? 'selected' : '' }}>SUBMIT_TRAVEL</option>
                <option value="MANAGER_APPROVE_TRAVEL" {{ request('action') === 'MANAGER_APPROVE_TRAVEL' ? 'selected' : '' }}>MANAGER_APPROVE_TRAVEL</option>
                <option value="FINAL_APPROVE_TRAVEL" {{ request('action') === 'FINAL_APPROVE_TRAVEL' ? 'selected' : '' }}>FINAL_APPROVE_TRAVEL</option>
                <option value="REJECT_TRAVEL" {{ request('action') === 'REJECT_TRAVEL' ? 'selected' : '' }}>REJECT_TRAVEL</option>
                <option value="SUBMIT_REIMBURSEMENT" {{ request('action') === 'SUBMIT_REIMBURSEMENT' ? 'selected' : '' }}>SUBMIT_REIMBURSEMENT</option>
                <option value="VERIFY_REIMBURSEMENT" {{ request('action') === 'VERIFY_REIMBURSEMENT' ? 'selected' : '' }}>VERIFY_REIMBURSEMENT</option>
                <option value="PAY_REIMBURSEMENT" {{ request('action') === 'PAY_REIMBURSEMENT' ? 'selected' : '' }}>PAY_REIMBURSEMENT</option>
            </select>

            <select name="user_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none text-slate-700">
                <option value="">Semua Pengguna</option>
                @foreach($users as $usr)
                    <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }} ({{ $usr->role }})</option>
                @endforeach
            </select>

            @if(request()->hasAny(['action', 'user_id']))
                <a href="{{ route('admin.logs') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Reset Filter
                </a>
            @endif
        </form>

        <span class="text-xs text-slate-500 font-medium">Total: {{ $logs->total() }} Catatan Aktivitas</span>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200/60">
                    <tr>
                        <th class="py-3.5 px-5">Waktu & Tanggal</th>
                        <th class="py-3.5 px-5">Tindakan (Action)</th>
                        <th class="py-3.5 px-5">Pengguna Pelaksana</th>
                        <th class="py-3.5 px-5">Deskripsi Jejak Audit</th>
                        <th class="py-3.5 px-5 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold whitespace-nowrap
                                    @if(str_contains($log->action, 'APPROVE')) bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif(str_contains($log->action, 'REJECT') || str_contains($log->action, 'CANCEL')) bg-red-100 text-red-800 border border-red-200
                                    @elseif(str_contains($log->action, 'PAY')) bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif(str_contains($log->action, 'SUBMIT')) bg-amber-100 text-amber-800 border border-amber-200
                                    @else bg-slate-100 text-slate-700 border border-slate-200
                                    @endif">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $log->user->name ?? 'Sistem' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->user->role ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-5">
                                <p class="text-xs text-slate-700 leading-relaxed font-medium">
                                    {{ $log->description }}
                                </p>
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">Belum ada jejak audit yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

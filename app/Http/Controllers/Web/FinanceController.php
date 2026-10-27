<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Reimbursement;
use App\Models\ReimbursementItem;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function budgets()
    {
        $user = Auth::user();

        $policies = TravelPolicy::with('position')->get();
        $departments = Department::withCount('users')->get();

        $deptExpenses = DB::table('reimbursements as r')
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->join('departments as d', 'u.department_id', '=', 'd.id')
            ->whereIn('r.status', ['APPROVED', 'PAID'])
            ->select('d.id', 'd.name as department_name', DB::raw('SUM(r.approved_amount) as total_spent'))
            ->groupBy('d.id', 'd.name')
            ->get();

        $totalApprovedSpent = Reimbursement::whereIn('status', ['APPROVED', 'PAID'])->sum('approved_amount');

        return view('finance.budgets', compact('policies', 'departments', 'deptExpenses', 'totalApprovedSpent', 'user'));
    }

    public function verifications(Request $request)
    {
        $user = Auth::user();

        $query = Reimbursement::with(['user.department', 'travelRequest', 'items'])
            ->whereIn('status', ['SUBMITTED', 'PENDING_VERIFICATION']);

        $pendingReimbursements = $query->latest()->paginate(10);

        return view('finance.verifications', compact('pendingReimbursements', 'user'));
    }

    public function verifyAction(Request $request, $id)
    {
        $user = Auth::user();
        $reimbursement = Reimbursement::with('user')->findOrFail($id);

        if (!in_array($reimbursement->status, ['SUBMITTED', 'PENDING_VERIFICATION'], true)) {
            return back()->with('error', 'Reimbursement ini sudah pernah diverifikasi.');
        }

        $validated = $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'approved_amount' => 'required|numeric|min:0',
            'finance_note' => $request->status === 'REJECTED' ? 'required|string|min:5' : 'nullable|string',
        ], [
            'finance_note.required' => 'Catatan verifikasi wajib diisi jika reimbursement ditolak.',
        ]);

        $approvedAmount = (float) $validated['approved_amount'];
        $advance = (float) $reimbursement->advance_amount;
        $diff = $approvedAmount - $advance;

        $reimbursement->update([
            'status' => $validated['status'],
            'approved_amount' => $approvedAmount,
            'difference_amount' => $diff,
            'finance_note' => $validated['finance_note'] ?? null,
        ]);

        ActivityLog::log(
            action: 'VERIFY_REIMBURSEMENT',
            description: "Finance {$user->name} memverifikasi klaim #RMB-{$reimbursement->id} ({$reimbursement->user->name}). Status: {$validated['status']}, Disetujui: Rp " . number_format($approvedAmount, 0, ',', '.') . ".",
            entityType: 'Reimbursement',
            entityId: $reimbursement->id
        );

        $msg = ($validated['status'] === 'APPROVED')
            ? "Reimbursement #RMB-{$reimbursement->id} berhasil diverifikasi dan disetujui untuk pembayaran."
            : "Reimbursement #RMB-{$reimbursement->id} ditolak dengan catatan yang diberikan.";

        return redirect()->route('reimbursements.show', $reimbursement->id)->with('success', $msg);
    }

    public function payments(Request $request)
    {
        $user = Auth::user();

        $query = Reimbursement::with(['user.department', 'travelRequest', 'items'])
            ->where('status', 'APPROVED');

        $pendingPayments = $query->latest()->paginate(10);
        $paidHistory = Reimbursement::with(['user.department', 'travelRequest'])
            ->where('status', 'PAID')
            ->latest()
            ->take(10)
            ->get();

        return view('finance.payments', compact('pendingPayments', 'paidHistory', 'user'));
    }

    public function payAction(Request $request, $id)
    {
        $user = Auth::user();
        $reimbursement = Reimbursement::with('user')->findOrFail($id);

        if ($reimbursement->status !== 'APPROVED') {
            return back()->with('error', 'Hanya reimbursement yang sudah disetujui yang dapat dibayarkan.');
        }

        $validated = $request->validate([
            'external_journal_ref' => 'required|string|max:100',
            'payment_date' => 'required|date',
            'payment_notes' => 'nullable|string',
        ]);

        $reimbursement->update([
            'status' => 'PAID',
            'external_journal_ref' => $validated['external_journal_ref'],
            'updated_at' => $validated['payment_date'],
        ]);

        ActivityLog::log(
            action: 'PAY_REIMBURSEMENT',
            description: "Finance {$user->name} mencatat pembayaran reimbursement #RMB-{$reimbursement->id} ({$reimbursement->user->name}) sebesar Rp " . number_format($reimbursement->approved_amount, 0, ',', '.') . " dengan Ref: {$validated['external_journal_ref']}.",
            entityType: 'Reimbursement',
            entityId: $reimbursement->id
        );

        return redirect()->route('finance.payments')
            ->with('success', "Pembayaran klaim #RMB-{$reimbursement->id} berhasil dikonfirmasi dan dicatat (Status: PAID).");
    }

    public function reports(Request $request)
    {
        $user = Auth::user();

        $query = Reimbursement::with(['user.department', 'travelRequest', 'items']);

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Employee filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Department filter
        if ($request->filled('department_id')) {
            $query->whereHas('user', fn($uq) => $uq->where('department_id', $request->department_id));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reimbursements = $query->latest()->get();

        $totalApproved = $reimbursements->whereIn('status', ['APPROVED', 'PAID'])->sum('approved_amount');
        $totalPaid = $reimbursements->where('status', 'PAID')->sum('approved_amount');
        $totalClaims = $reimbursements->count();

        $departments = Department::all();
        $employees = User::where('is_active', true)->get();

        return view('finance.reports', compact('reimbursements', 'totalApproved', 'totalPaid', 'totalClaims', 'departments', 'employees', 'user'));
    }
}

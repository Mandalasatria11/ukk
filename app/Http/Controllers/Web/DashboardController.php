<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\Reimbursement;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Employee Dashboard Data
        if ($user->isEmployee()) {
            $myTravels = TravelRequest::with(['policy', 'approvals'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $stats = [
                'total_travels' => TravelRequest::where('user_id', $user->id)->count(),
                'pending_travels' => TravelRequest::where('user_id', $user->id)->whereIn('status', ['DRAFT', 'PENDING_APPROVAL', 'SUBMITTED'])->count(),
                'approved_travels' => TravelRequest::where('user_id', $user->id)->where('status', 'APPROVED')->count(),
                'rejected_travels' => TravelRequest::where('user_id', $user->id)->where('status', 'REJECTED')->count(),
                'total_reimbursements' => Reimbursement::where('user_id', $user->id)->count(),
                'reimbursements_paid' => Reimbursement::where('user_id', $user->id)->where('status', 'PAID')->sum('approved_amount'),
            ];

            $recentReimbursements = Reimbursement::with('travelRequest')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.employee', compact('user', 'stats', 'myTravels', 'recentReimbursements'));
        }

        // 2. Manager Dashboard Data
        if ($user->isManager()) {
            $deptId = $user->department_id;

            $pendingApprovals = Approval::with(['travelRequest.user', 'travelRequest.department', 'travelRequest.policy'])
                ->where('status', 'PENDING')
                ->where(function ($q) use ($user, $deptId) {
                    $q->where('approver_id', $user->id)
                      ->orWhereHas('travelRequest', function ($tq) use ($deptId) {
                          $tq->where('department_id', $deptId)->whereIn('status', ['SUBMITTED', 'PENDING_APPROVAL']);
                      });
                })
                ->latest()
                ->get();

            $recentDeptTravels = TravelRequest::with(['user', 'policy'])
                ->where('department_id', $deptId)
                ->latest()
                ->take(6)
                ->get();

            $teamCount = User::where('department_id', $deptId)->where('id', '!=', $user->id)->count();

            $stats = [
                'team_members' => $teamCount,
                'pending_approvals' => $pendingApprovals->count(),
                'dept_travels_total' => TravelRequest::where('department_id', $deptId)->count(),
                'dept_travels_approved' => TravelRequest::where('department_id', $deptId)->where('status', 'APPROVED')->count(),
            ];

            return view('dashboard.manager', compact('user', 'stats', 'pendingApprovals', 'recentDeptTravels'));
        }

        // 3. HRD Dashboard Data
        if ($user->isHrd()) {
            $pendingApprovals = Approval::with(['travelRequest.user', 'travelRequest.department', 'travelRequest.policy'])
                ->where('status', 'PENDING')
                ->latest()
                ->get();

            $recentTravels = TravelRequest::with(['user', 'department', 'policy'])
                ->latest()
                ->take(8)
                ->get();

            $stats = [
                'total_travels' => TravelRequest::count(),
                'pending_approvals' => $pendingApprovals->count(),
                'approved_requests' => TravelRequest::where('status', 'APPROVED')->count(),
                'rejected_requests' => TravelRequest::where('status', 'REJECTED')->count(),
            ];

            return view('dashboard.hrd', compact('user', 'stats', 'pendingApprovals', 'recentTravels'));
        }

        // 4. Finance Dashboard Data
        if ($user->isFinance()) {
            $pendingReimbursements = Reimbursement::with(['user', 'travelRequest', 'items'])
                ->whereIn('status', ['SUBMITTED', 'PENDING_VERIFICATION'])
                ->latest()
                ->get();

            $pendingPayments = Reimbursement::with(['user', 'travelRequest'])
                ->where('status', 'APPROVED')
                ->whereNull('external_journal_ref')
                ->latest()
                ->get();

            $totalBudget = TravelPolicy::sum('max_budget');
            $totalExpensesApproved = Reimbursement::whereIn('status', ['APPROVED', 'PAID'])->sum('approved_amount');
            $completedPayments = Reimbursement::where('status', 'PAID')->count();

            $stats = [
                'total_budget' => $totalBudget,
                'total_expenses' => $totalExpensesApproved,
                'pending_verification' => $pendingReimbursements->count(),
                'pending_payment' => $pendingPayments->count(),
                'completed_payments' => $completedPayments,
            ];

            return view('dashboard.finance', compact('user', 'stats', 'pendingReimbursements', 'pendingPayments'));
        }

        // 5. Admin / Super Admin Dashboard Data
        $totalEmployees = User::where('is_active', true)->count();
        $totalTravels = TravelRequest::count();
        $pendingApprovalsCount = Approval::where('status', 'PENDING')->count();
        $approvedTravels = TravelRequest::where('status', 'APPROVED')->count();
        $rejectedTravels = TravelRequest::where('status', 'REJECTED')->count();
        $totalReimbursementAmount = Reimbursement::whereIn('status', ['APPROVED', 'PAID'])->sum('approved_amount');

        $stats = [
            'total_employees' => $totalEmployees,
            'total_travels' => $totalTravels,
            'pending_approvals' => $pendingApprovalsCount,
            'approved_requests' => $approvedTravels,
            'rejected_requests' => $rejectedTravels,
            'total_reimbursement' => $totalReimbursementAmount,
        ];

        $recentActivities = ActivityLog::with('user')->latest()->take(8)->get();
        $recentTravels = TravelRequest::with(['user', 'department'])->latest()->take(5)->get();

        return view('dashboard.admin', compact('user', 'stats', 'recentActivities', 'recentTravels'));
    }
}

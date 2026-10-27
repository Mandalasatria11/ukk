<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->canApproveTravel()) {
            abort(403, 'Anda tidak memiliki hak akses persetujuan.');
        }

        $tab = $request->get('tab', 'pending'); // pending, history

        $query = Approval::with([
            'travelRequest.user.department',
            'travelRequest.user.position',
            'travelRequest.department',
            'travelRequest.policy',
            'travelRequest.documents',
            'approver'
        ]);

        if ($user->isManager()) {
            // Manager specifically sees requests from their department
            $query->where(function ($q) use ($user) {
                $q->where('approver_id', $user->id)
                  ->orWhereHas('travelRequest', fn($tq) => $tq->where('department_id', $user->department_id));
            });
        }

        if ($tab === 'pending') {
            $approvals = $query->where('status', 'PENDING')->latest()->paginate(10);
        } else {
            $approvals = $query->whereIn('status', ['APPROVED', 'REJECTED'])->latest()->paginate(10);
        }

        $pendingCount = (clone $query)->where('status', 'PENDING')->count();

        return view('approvals.index', compact('approvals', 'user', 'tab', 'pendingCount'));
    }

    public function decide(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->canApproveTravel()) {
            abort(403, 'Akses ditolak.');
        }

        $approval = Approval::with(['travelRequest.user'])->findOrFail($id);

        if ($approval->status !== 'PENDING') {
            return back()->with('error', 'Persetujuan ini sudah diproses sebelumnya.');
        }

        // Authorization: Approver itself, Department Manager, HRD, or Admin
        if ($user->isManager() && $approval->travelRequest->department_id !== $user->department_id && $approval->approver_id !== $user->id) {
            abort(403, 'Anda hanya dapat memproses persetujuan untuk staf di departemen Anda.');
        }

        $validated = $request->validate([
            'decision' => 'required|in:APPROVED,REJECTED',
            'note' => $request->decision === 'REJECTED' ? 'required|string|min:5' : 'nullable|string',
        ], [
            'note.required' => 'Alasan penolakan wajib diisi untuk memberikan kejelasan kepada pemohon.',
            'note.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $approval->update([
                'status' => $validated['decision'],
                'note' => $validated['note'] ?? null,
                'approver_id' => $user->id,
            ]);

            $travel = $approval->travelRequest;

            if ($validated['decision'] === 'REJECTED') {
                $travel->update(['status' => 'REJECTED']);

                ActivityLog::log(
                    action: 'REJECT_TRAVEL',
                    description: "{$user->name} ({$user->role}) MENOLAK pengajuan perjalanan dinas #TRV-{$travel->id} milik {$travel->user->name}. Alasan: {$validated['note']}",
                    entityType: 'TravelRequest',
                    entityId: $travel->id
                );

                $msg = "Pengajuan dinas milik {$travel->user->name} berhasil DITOLAK.";
            } else {
                // If Manager approved, check if HRD level 2 is configured, or approve directly
                if ($user->isManager()) {
                    // Create level 2 approval for HRD
                    $hrdUser = User::where('role', 'HRD')->where('is_active', true)->first();

                    if ($hrdUser) {
                        Approval::create([
                            'travel_request_id' => $travel->id,
                            'approver_id' => $hrdUser->id,
                            'level' => 2,
                            'status' => 'PENDING',
                        ]);

                        ActivityLog::log(
                            action: 'MANAGER_APPROVE_TRAVEL',
                            description: "Manager {$user->name} MENYETUJUI pengajuan dinas #TRV-{$travel->id} ({$travel->user->name}). Diteruskan ke HRD.",
                            entityType: 'TravelRequest',
                            entityId: $travel->id
                        );

                        $msg = "Pengajuan dinas disetujui di tingkat Manager dan kini diteruskan ke HRD untuk finalisasi.";
                    } else {
                        $travel->update(['status' => 'APPROVED']);
                        $msg = "Pengajuan dinas milik {$travel->user->name} telah DISETUJUI.";
                    }
                } else {
                    // HRD or Admin final approval
                    $travel->update(['status' => 'APPROVED']);

                    ActivityLog::log(
                        action: 'FINAL_APPROVE_TRAVEL',
                        description: "{$user->name} ({$user->role}) MENYETUJUI penuh pengajuan dinas #TRV-{$travel->id} ({$travel->user->name}).",
                        entityType: 'TravelRequest',
                        entityId: $travel->id
                    );

                    $msg = "Pengajuan dinas milik {$travel->user->name} telah DISETUJUI.";
                }
            }

            DB::commit();
            return redirect()->route('approvals.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses keputusan: ' . $e->getMessage());
        }
    }
}

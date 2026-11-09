<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\Notification;
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
            'travelRequest.approvals.approver',
            'approver'
        ]);

        if ($user->isManager()) {
            // Tahap 1: Manager hanya melihat & memproses persetujuan Level 1 dari divisinya / yang ditugaskan kepadanya
            $query->where('level', 1)->where(function ($q) use ($user) {
                $q->where('approver_id', $user->id)
                  ->orWhereHas('travelRequest', fn($tq) => $tq->where('department_id', $user->department_id));
            });
        } elseif ($user->isHrd()) {
            // Tahap 2: HRD hanya melihat & memproses persetujuan Level 2 (setelah disetujui Manager)
            $query->where(function ($q) use ($user) {
                $q->where('level', 2)
                  ->orWhere('approver_id', $user->id);
            });
        }

        $pendingCount = (clone $query)->where('status', 'PENDING')->count();

        if ($tab === 'pending') {
            $approvals = $query->where('status', 'PENDING')->latest()->paginate(10);
        } else {
            $approvals = $query->whereIn('status', ['APPROVED', 'REJECTED'])->latest()->paginate(10);
        }

        return view('approvals.index', compact('approvals', 'user', 'tab', 'pendingCount'));
    }

    public function decide(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->canApproveTravel()) {
            abort(403, 'Akses ditolak.');
        }

        $approval = Approval::with(['travelRequest.user.department'])->findOrFail($id);

        if ($approval->status !== 'PENDING') {
            return back()->with('error', 'Persetujuan ini sudah diproses sebelumnya.');
        }

        // Validasi Otoritas Berdasarkan Level (Tahap 1 = Manager, Tahap 2 = HRD)
        if ((int) $approval->level === 1) {
            if ($user->isHrd() && $approval->approver_id !== $user->id) {
                return back()->with('error', 'Pengajuan ini masih berada di Tahap 1 dan harus disetujui oleh Manager terlebih dahulu.');
            }
            if ($user->isManager() && $approval->travelRequest->department_id !== $user->department_id && $approval->approver_id !== $user->id) {
                abort(403, 'Anda hanya dapat memproses persetujuan untuk staf di divisi Anda.');
            }
        } elseif ((int) $approval->level === 2) {
            if ($user->isManager()) {
                return back()->with('error', 'Pengajuan ini sudah berada di Tahap 2 dan menunggu keputusan dari pihak HRD.');
            }
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
            $stageName = ((int) $approval->level === 1) ? 'Manager' : 'HRD';

            if ($validated['decision'] === 'REJECTED') {
                // Jika Manager menolak ATAU HRD menolak -> langsung tampilkan DITOLAK (REJECTED)
                $travel->update(['status' => 'REJECTED']);

                // Batalkan sisa approval pending jika ada
                $travel->approvals()
                    ->where('id', '!=', $approval->id)
                    ->where('status', 'PENDING')
                    ->update(['status' => 'CANCELLED']);

                Notification::notify(
                    userId: $travel->user_id,
                    title: "Pengajuan Dinas #TRV-{$travel->id} Ditolak oleh {$stageName}",
                    message: "Pengajuan perjalanan dinas Anda ke {$travel->destination} DITOLAK oleh {$stageName} ({$user->name}). Alasan: {$validated['note']}"
                );

                ActivityLog::log(
                    action: 'REJECT_TRAVEL',
                    description: "{$user->name} ({$stageName}) MENOLAK pengajuan perjalanan dinas #TRV-{$travel->id} milik {$travel->user->name}. Alasan: {$validated['note']}",
                    entityType: 'TravelRequest',
                    entityId: $travel->id
                );

                $msg = "Pengajuan perjalanan dinas milik {$travel->user->name} telah DITOLAK pada tahap {$stageName}.";
            } else {
                // Jika DISETUJUI: cek apakah ini Tahap 1 (Manager) atau Tahap 2 (HRD)
                if ((int) $approval->level === 1) {
                    // Tahap 1 (Manager) setuju -> lanjut ke Tahap 2 (HRD)
                    $hrdUsers = User::where('role', 'HRD')->where('is_active', true)->get();
                    $primaryHrd = $hrdUsers->first();

                    if ($primaryHrd) {
                        Approval::firstOrCreate(
                            [
                                'travel_request_id' => $travel->id,
                                'level' => 2,
                            ],
                            [
                                'approver_id' => $primaryHrd->id,
                                'status' => 'PENDING',
                                'note' => null,
                            ]
                        );

                        // Kirim notifikasi ke seluruh user HRD aktif
                        $deptName = $travel->user->department->name ?? 'Divisi';
                        foreach ($hrdUsers as $hrd) {
                            Notification::notify(
                                userId: $hrd->id,
                                title: "Persetujuan Tahap 2 (HRD) #TRV-{$travel->id}",
                                message: "Pengajuan dinas {$travel->user->name} (Divisi {$deptName}) ke {$travel->destination} telah disetujui Manager ({$user->name}) dan kini menunggu persetujuan HRD."
                            );
                        }

                        // Beri tahu pemohon bahwa Manager sudah menyetujui dan diteruskan ke HRD
                        Notification::notify(
                            userId: $travel->user_id,
                            title: "Disetujui Manager · Diteruskan ke HRD (#TRV-{$travel->id})",
                            message: "Pengajuan perjalanan dinas Anda ke {$travel->destination} telah disetujui oleh Manager ({$user->name}) dan kini diteruskan ke tahap persetujuan HRD."
                        );

                        ActivityLog::log(
                            action: 'MANAGER_APPROVE_TRAVEL',
                            description: "Manager {$user->name} MENYETUJUI pengajuan dinas #TRV-{$travel->id} ({$travel->user->name}). Diteruskan ke HRD untuk persetujuan Tahap 2.",
                            entityType: 'TravelRequest',
                            entityId: $travel->id
                        );

                        $msg = "Pengajuan dinas berhasil disetujui pada Tahap 1 (Manager) dan notifikasi telah diteruskan ke HRD untuk persetujuan Tahap 2.";
                    } else {
                        // Fallback jika tidak ada akun HRD aktif di sistem
                        $travel->update(['status' => 'APPROVED']);
                        $msg = "Pengajuan dinas milik {$travel->user->name} telah DISETUJUI.";
                    }
                } else {
                    // Tahap 2 (HRD) setuju -> Perjalanan Dinas resmi DISETUJUI (APPROVED)
                    $travel->update(['status' => 'APPROVED']);

                    Notification::notify(
                        userId: $travel->user_id,
                        title: "Perjalanan Dinas Disetujui! (#TRV-{$travel->id})",
                        message: "Selamat! Pengajuan perjalanan dinas Anda ke {$travel->destination} telah disetujui oleh HRD ({$user->name}). Perjalanan dinas Anda resmi DISETUJUI."
                    );

                    ActivityLog::log(
                        action: 'HRD_APPROVE_TRAVEL',
                        description: "HRD {$user->name} MENYETUJUI penuh pengajuan dinas #TRV-{$travel->id} ({$travel->user->name}). Status resmi: APPROVED.",
                        entityType: 'TravelRequest',
                        entityId: $travel->id
                    );

                    $msg = "Pengajuan dinas milik {$travel->user->name} telah DISETUJUI oleh HRD. Perjalanan dinas resmi disahkan!";
                }
            }

            DB::commit();
            return redirect()->route('approvals.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses keputusan: ' . $e->getMessage());
        }
    }

    public function markNotificationRead($id)
    {
        $notif = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notif->update(['is_read' => true]);

        return back();
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}

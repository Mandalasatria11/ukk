<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\TravelDocument;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TravelRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TravelRequest::with(['user.position', 'department', 'policy', 'approvals.approver']);

        // Role-based visibility
        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isManager()) {
            $query->where('department_id', $user->department_id);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'ALL') {
            if ($request->status === 'PENDING') {
                $query->whereIn('status', ['PENDING_APPROVAL', 'SUBMITTED']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Search destination or requester
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('destination', 'like', "%{$s}%")
                  ->orWhere('purpose', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$s}%"));
            });
        }

        $travels = $query->latest()->paginate(10)->withQueryString();

        return view('travel.index', compact('travels', 'user'));
    }

    public function create()
    {
        $user = Auth::user();

        // Applicable policies for user's position
        $policies = TravelPolicy::where('is_active', true)
            ->when($user->position_id, fn($q) => $q->where('position_id', $user->position_id))
            ->get();

        if ($policies->isEmpty()) {
            $policies = TravelPolicy::where('is_active', true)->get();
        }

        return view('travel.create', compact('policies', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'estimated_cost' => 'required|numeric|min:0',
            'policy_id' => 'required|exists:travel_policies,id',
            'action_type' => 'required|in:draft,submit',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $status = ($validated['action_type'] === 'submit') ? 'PENDING_APPROVAL' : 'DRAFT';

        DB::beginTransaction();
        try {
            $travel = TravelRequest::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'destination' => $validated['destination'],
                'purpose' => $validated['purpose'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'estimated_cost' => $validated['estimated_cost'],
                'policy_id' => $validated['policy_id'],
                'status' => $status,
            ]);

            // Save document attachment if provided
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $path = $file->store('travel-documents', 'public');

                TravelDocument::create([
                    'travel_request_id' => $travel->id,
                    'uploaded_by' => $user->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            // If submitted directly, assign approval to Department Manager
            if ($status === 'PENDING_APPROVAL') {
                $manager = User::where('department_id', $user->department_id)
                    ->where('role', 'MANAGER')
                    ->where('is_active', true)
                    ->first();

                // If no department manager or user is manager, assign to HRD/Admin
                $approverId = $manager ? $manager->id : User::where('role', 'HRD')->value('id');

                Approval::create([
                    'travel_request_id' => $travel->id,
                    'approver_id' => $approverId,
                    'level' => 1,
                    'status' => 'PENDING',
                    'note' => null,
                ]);

                ActivityLog::log(
                    action: 'SUBMIT_TRAVEL',
                    description: "{$user->name} mengajukan perjalanan dinas ke {$travel->destination} (Status: Pending Approval).",
                    entityType: 'TravelRequest',
                    entityId: $travel->id
                );
            } else {
                ActivityLog::log(
                    action: 'CREATE_DRAFT_TRAVEL',
                    description: "{$user->name} membuat draft pengajuan perjalanan dinas ke {$travel->destination}.",
                    entityType: 'TravelRequest',
                    entityId: $travel->id
                );
            }

            DB::commit();

            $msg = ($status === 'PENDING_APPROVAL')
                ? 'Pengajuan perjalanan dinas berhasil diajukan dan menunggu persetujuan Manager!'
                : 'Draft pengajuan perjalanan dinas berhasil disimpan.';

            return redirect()->route('travel.show', $travel->id)->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $travel = TravelRequest::with([
            'user.department',
            'user.position',
            'department',
            'policy',
            'approvals.approver',
            'documents.uploader',
            'bookings',
            'reimbursement.items'
        ])->findOrFail($id);

        // Authorization check
        if ($user->isEmployee() && $travel->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pengajuan ini.');
        }

        if ($user->isManager() && $travel->department_id !== $user->department_id && $travel->user_id !== $user->id) {
            abort(403, 'Manager hanya dapat melihat pengajuan staf dari departemennya.');
        }

        return view('travel.show', compact('travel', 'user'));
    }

    public function submit($id)
    {
        $user = Auth::user();
        $travel = TravelRequest::findOrFail($id);

        if ($travel->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Hanya pemohon yang dapat mengajukan.');
        }

        if ($travel->status !== 'DRAFT') {
            return back()->with('error', 'Hanya pengajuan berstatus DRAFT yang dapat disubmit.');
        }

        DB::beginTransaction();
        try {
            $travel->update(['status' => 'PENDING_APPROVAL']);

            // Find Department Manager
            $manager = User::where('department_id', $travel->department_id)
                ->where('role', 'MANAGER')
                ->where('is_active', true)
                ->first();

            $approverId = $manager ? $manager->id : User::where('role', 'HRD')->value('id');

            Approval::create([
                'travel_request_id' => $travel->id,
                'approver_id' => $approverId,
                'level' => 1,
                'status' => 'PENDING',
            ]);

            ActivityLog::log(
                action: 'SUBMIT_TRAVEL',
                description: "{$user->name} mengirim pengajuan travel #TRV-{$travel->id} ke {$travel->destination} untuk approval.",
                entityType: 'TravelRequest',
                entityId: $travel->id
            );

            DB::commit();
            return back()->with('success', 'Pengajuan berhasil dikirimkan ke Manager untuk diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim pengajuan: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $travel = TravelRequest::findOrFail($id);

        if ($travel->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if (in_array($travel->status, ['COMPLETED', 'CANCELLED'], true)) {
            return back()->with('error', 'Pengajuan ini sudah tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($travel, $user) {
            $travel->update(['status' => 'CANCELLED']);
            $travel->approvals()->where('status', 'PENDING')->update(['status' => 'CANCELLED']);

            ActivityLog::log(
                action: 'CANCEL_TRAVEL',
                description: "{$user->name} membatalkan perjalanan dinas #TRV-{$travel->id}.",
                entityType: 'TravelRequest',
                entityId: $travel->id
            );
        });

        return back()->with('success', 'Perjalanan dinas berhasil dibatalkan.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $travel = TravelRequest::findOrFail($id);

        if ($travel->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if ($travel->status !== 'DRAFT') {
            return back()->with('error', 'Hanya draf yang dapat dihapus.');
        }

        $travel->delete();

        return redirect()->route('travel.index')->with('success', 'Draft perjalanan dinas berhasil dihapus.');
    }

    public function uploadDocument(Request $request, $id)
    {
        $user = Auth::user();
        $travel = TravelRequest::findOrFail($id);

        if ($travel->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('travel-documents', 'public');

        TravelDocument::create([
            'travel_request_id' => $travel->id,
            'uploaded_by' => $user->id,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        ActivityLog::log(
            action: 'UPLOAD_DOCUMENT',
            description: "{$user->name} mengunggah dokumen '{$file->getClientOriginalName()}' pada travel #TRV-{$travel->id}.",
            entityType: 'TravelRequest',
            entityId: $travel->id
        );

        return back()->with('success', 'Dokumen pendukung berhasil diunggah.');
    }

    public function deleteDocument($docId)
    {
        $user = Auth::user();
        $doc = TravelDocument::with('travelRequest')->findOrFail($docId);

        if ($doc->uploaded_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        Storage::disk('public')->delete($doc->path);
        $doc->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}

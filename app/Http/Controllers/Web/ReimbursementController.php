<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Reimbursement;
use App\Models\ReimbursementItem;
use App\Models\TravelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Reimbursement::with(['user.department', 'travelRequest', 'items']);

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        $reimbursements = $query->latest()->paginate(10)->withQueryString();

        return view('reimbursements.index', compact('reimbursements', 'user'));
    }

    public function create()
    {
        $user = Auth::user();

        // Must be linked to user's APPROVED travel request that doesn't already have an active reimbursement
        $approvedTravels = TravelRequest::where('user_id', $user->id)
            ->whereIn('status', ['APPROVED', 'COMPLETED'])
            ->whereDoesntHave('reimbursement', function ($q) {
                $q->whereNotIn('status', ['REJECTED']);
            })
            ->latest()
            ->get();

        return view('reimbursements.create', compact('approvedTravels', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'travel_request_id' => 'required|exists:travel_requests,id',
            'advance_amount' => 'nullable|numeric|min:0',
        ]);

        $travel = TravelRequest::where('id', $validated['travel_request_id'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['APPROVED', 'COMPLETED'])
            ->firstOrFail();

        $reimbursement = Reimbursement::create([
            'travel_request_id' => $travel->id,
            'user_id' => $user->id,
            'advance_amount' => $validated['advance_amount'] ?? 0,
            'status' => 'DRAFT',
        ]);

        ActivityLog::log(
            action: 'CREATE_REIMBURSEMENT',
            description: "{$user->name} membuat draf pengajuan reimbursement untuk travel #TRV-{$travel->id} ke {$travel->destination}.",
            entityType: 'Reimbursement',
            entityId: $reimbursement->id
        );

        return redirect()->route('reimbursements.show', $reimbursement->id)
            ->with('success', 'Draf klaim reimbursement berhasil dibuat. Silakan tambahkan rincian bukti pengeluaran Anda.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $reimbursement = Reimbursement::with([
            'user.department',
            'user.position',
            'travelRequest.policy',
            'items'
        ])->findOrFail($id);

        if ($user->isEmployee() && $reimbursement->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('reimbursements.show', compact('reimbursement', 'user'));
    }

    public function addItem(Request $request, $id)
    {
        $user = Auth::user();
        $reimbursement = Reimbursement::findOrFail($id);

        if ($reimbursement->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if ($reimbursement->status !== 'DRAFT') {
            return back()->with('error', 'Item pengeluaran hanya dapat ditambahkan saat status DRAFT.');
        }

        $validated = $request->validate([
            'category' => 'required|in:TRANSPORT,HOTEL,MEAL,FLIGHT,OTHER',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        ReimbursementItem::create([
            'reimbursement_id' => $reimbursement->id,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'receipt_path' => $receiptPath,
        ]);

        return back()->with('success', 'Item bukti pengeluaran berhasil ditambahkan.');
    }

    public function deleteItem($itemId)
    {
        $user = Auth::user();
        $item = ReimbursementItem::with('reimbursement')->findOrFail($itemId);

        if ($item->reimbursement->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if ($item->reimbursement->status !== 'DRAFT') {
            return back()->with('error', 'Item hanya dapat dihapus saat status DRAFT.');
        }

        if ($item->receipt_path) {
            Storage::disk('public')->delete($item->receipt_path);
        }

        $item->delete();

        return back()->with('success', 'Item bukti pengeluaran berhasil dihapus.');
    }

    public function submit($id)
    {
        $user = Auth::user();
        $reimbursement = Reimbursement::with('items')->findOrFail($id);

        if ($reimbursement->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if ($reimbursement->status !== 'DRAFT') {
            return back()->with('error', 'Reimbursement ini sudah pernah diajukan.');
        }

        if ($reimbursement->items->isEmpty()) {
            return back()->with('error', 'Tambahkan minimal 1 item pengeluaran sebelum mengajukan ke Finance.');
        }

        $reimbursement->update(['status' => 'PENDING_VERIFICATION']);

        ActivityLog::log(
            action: 'SUBMIT_REIMBURSEMENT',
            description: "{$user->name} mengajukan reimbursement #RMB-{$reimbursement->id} sebesar Rp " . number_format($reimbursement->total_items_amount, 0, ',', '.') . " ke Finance untuk verifikasi.",
            entityType: 'Reimbursement',
            entityId: $reimbursement->id
        );

        return back()->with('success', 'Reimbursement berhasil diajukan ke Tim Finance untuk diverifikasi.');
    }
}

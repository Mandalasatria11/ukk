<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Position;
use App\Models\Reimbursement;
use App\Models\ReimbursementItem;
use App\Models\TravelBooking;
use App\Models\TravelDocument;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $user = Auth::user();
        $query = User::with(['department', 'position']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $users = $query->orderBy('role')->paginate(12)->withQueryString();
        $departments = Department::all();
        $positions = Position::all();

        return view('admin.users', compact('users', 'departments', 'positions', 'user'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['EMPLOYEE', 'MANAGER', 'HRD', 'FINANCE', 'ADMIN', 'SUPER_ADMIN'])],
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'position_id' => $validated['position_id'] ?? null,
            'is_active' => true,
        ]);

        ActivityLog::log(
            action: 'CREATE_USER',
            description: "Admin " . Auth::user()->name . " membuat akun baru: {$newUser->name} ({$newUser->role}).",
            entityType: 'User',
            entityId: $newUser->id
        );

        return back()->with('success', "Akun pengguna {$newUser->name} berhasil dibuat.");
    }

    public function updateUser(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', Rule::unique('users')->ignore($targetUser->id)],
            'role' => ['required', Rule::in(['EMPLOYEE', 'MANAGER', 'HRD', 'FINANCE', 'ADMIN', 'SUPER_ADMIN'])],
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'position_id' => $validated['position_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $targetUser->update($updateData);

        ActivityLog::log(
            action: 'UPDATE_USER',
            description: "Admin " . Auth::user()->name . " memperbarui data akun {$targetUser->name}.",
            entityType: 'User',
            entityId: $targetUser->id
        );

        return back()->with('success', "Data pengguna {$targetUser->name} berhasil diperbarui.");
    }

    public function toggleUserActive($id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $targetUser->is_active = !$targetUser->is_active;
        $targetUser->save();

        $statusStr = $targetUser->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log(
            action: 'TOGGLE_USER_STATUS',
            description: "Admin " . Auth::user()->name . " mengubah status akun {$targetUser->name} menjadi {$statusStr}.",
            entityType: 'User',
            entityId: $targetUser->id
        );

        return back()->with('success', "Akun {$targetUser->name} berhasil {$statusStr}.");
    }

    public function deleteUser($id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        DB::transaction(function () use ($targetUser) {
            Notification::where('user_id', $targetUser->id)->delete();
            ActivityLog::where('user_id', $targetUser->id)->delete();
            Approval::where('approver_id', $targetUser->id)->update(['approver_id' => null]);

            $reimbursements = Reimbursement::with('items')->where('user_id', $targetUser->id)->get();
            foreach ($reimbursements as $reimb) {
                foreach ($reimb->items as $item) {
                    if ($item->receipt_path) {
                        Storage::disk('public')->delete($item->receipt_path);
                    }
                }
                $reimb->items()->delete();
                $reimb->delete();
            }

            $travels = TravelRequest::with(['documents', 'reimbursement.items'])->where('user_id', $targetUser->id)->get();
            foreach ($travels as $travel) {
                if ($travel->reimbursement) {
                    foreach ($travel->reimbursement->items as $item) {
                        if ($item->receipt_path) {
                            Storage::disk('public')->delete($item->receipt_path);
                        }
                    }
                    $travel->reimbursement->items()->delete();
                    $travel->reimbursement->delete();
                }
                foreach ($travel->documents as $doc) {
                    if ($doc->path) {
                        Storage::disk('public')->delete($doc->path);
                    }
                }
                $travel->documents()->delete();
                $travel->bookings()->delete();
                $travel->approvals()->delete();
                $travel->delete();
            }

            TravelDocument::where('uploaded_by', $targetUser->id)->delete();
            DB::table('delegations')->where('delegator_id', $targetUser->id)->orWhere('delegate_id', $targetUser->id)->delete();

            $name = $targetUser->name;
            $role = $targetUser->role;
            $targetId = $targetUser->id;
            $targetUser->delete();

            ActivityLog::log(
                action: 'DELETE_USER',
                description: "Admin " . Auth::user()->name . " menghapus permanen akun {$name} ({$role}).",
                entityType: 'User',
                entityId: $targetId
            );
        });

        return back()->with('success', "Akun pengguna berhasil dihapus secara permanen.");
    }

    public function activityLogs(Request $request)
    {
        $user = Auth::user();
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        $users = User::all();

        return view('admin.logs', compact('logs', 'users', 'user'));
    }

    public function deleteLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();

        return back()->with('success', 'Catatan jejak aktivitas berhasil dihapus.');
    }

    public function clearLogs()
    {
        ActivityLog::query()->delete();

        ActivityLog::log(
            action: 'CLEAR_LOGS',
            description: "Admin " . Auth::user()->name . " membersihkan seluruh riwayat log aktivitas.",
            entityType: 'ActivityLog'
        );

        return back()->with('success', 'Seluruh riwayat log aktivitas berhasil dibersihkan.');
    }

    public function masterData()
    {
        $user = Auth::user();
        $departments = Department::withCount('users')->get();
        $positions = Position::withCount('users')->get();
        $policies = TravelPolicy::with('position')->get();

        return view('admin.master', compact('departments', 'positions', 'policies', 'user'));
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|unique:departments,name|max:100']);
        Department::create($validated);
        return back()->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function deleteDepartment($id)
    {
        $dept = Department::withCount('users')->findOrFail($id);
        if ($dept->users_count > 0) {
            return back()->with('error', 'Departemen tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }
        $dept->delete();
        return back()->with('success', 'Departemen berhasil dihapus.');
    }

    public function storePosition(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|unique:positions,name|max:100']);
        Position::create($validated);
        return back()->with('success', 'Jabatan baru berhasil ditambahkan.');
    }

    public function deletePosition($id)
    {
        $pos = Position::withCount('users')->findOrFail($id);
        if ($pos->users_count > 0) {
            return back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }
        $pos->delete();
        return back()->with('success', 'Jabatan berhasil dihapus.');
    }

    public function storePolicy(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'position_id' => 'required|exists:positions,id',
            'destination_tier' => 'required|string|max:50',
            'max_budget' => 'required|numeric|min:0',
        ]);

        TravelPolicy::create($validated);
        return back()->with('success', 'Kebijakan perjalanan baru berhasil ditambahkan.');
    }

    public function deletePolicy($id)
    {
        TravelPolicy::findOrFail($id)->delete();
        return back()->with('success', 'Kebijakan perjalanan berhasil dihapus.');
    }

    // =========================================================================
    // PUSAT BACKUP DATA, HAPUS DATA MASSAL & EXPORT / IMPORT EXCEL
    // =========================================================================

    private function backupDirectory(): string
    {
        $dir = storage_path('app/backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    private function buildSnapshotPayload(?string $label = null): array
    {
        $users = User::with(['department', 'position'])->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->role,
            'department_id' => $u->department_id,
            'department_name' => $u->department->name ?? '-',
            'position_id' => $u->position_id,
            'position_name' => $u->position->name ?? '-',
            'is_active' => $u->is_active,
            'created_at' => optional($u->created_at)->format('Y-m-d H:i:s'),
        ])->toArray();

        $travels = TravelRequest::with(['user.position', 'department', 'policy', 'approvals.approver'])->get()->map(fn($t) => [
            'id' => $t->id,
            'code' => '#TRV-' . $t->id,
            'user_id' => $t->user_id,
            'user_name' => $t->user->name ?? '-',
            'user_email' => $t->user->email ?? '-',
            'department_id' => $t->department_id,
            'department_name' => $t->department->name ?? '-',
            'position_name' => $t->user->position->name ?? '-',
            'destination' => $t->destination,
            'purpose' => $t->purpose,
            'start_date' => optional($t->start_date)->format('Y-m-d'),
            'end_date' => optional($t->end_date)->format('Y-m-d'),
            'estimated_cost' => (float) $t->estimated_cost,
            'policy_id' => $t->policy_id,
            'policy_name' => $t->policy->name ?? '-',
            'status' => $t->status,
            'stage_label' => $t->stage_status_label,
            'created_at' => optional($t->created_at)->format('Y-m-d H:i:s'),
        ])->toArray();

        $approvals = Approval::with(['travelRequest.user', 'approver'])->get()->map(fn($a) => [
            'id' => $a->id,
            'travel_request_id' => $a->travel_request_id,
            'travel_code' => '#TRV-' . $a->travel_request_id,
            'requester_name' => $a->travelRequest->user->name ?? '-',
            'destination' => $a->travelRequest->destination ?? '-',
            'level' => $a->level,
            'stage_name' => (int) $a->level === 1 ? 'Tahap 1: Manager' : 'Tahap 2: HRD',
            'approver_id' => $a->approver_id,
            'approver_name' => $a->approver->name ?? '-',
            'status' => $a->status,
            'note' => $a->note ?? '-',
            'updated_at' => optional($a->updated_at)->format('Y-m-d H:i:s'),
        ])->toArray();

        $reimbursements = Reimbursement::with(['user.department', 'travelRequest', 'items'])->get()->map(fn($r) => [
            'id' => $r->id,
            'code' => '#RMB-' . $r->id,
            'travel_request_id' => $r->travel_request_id,
            'travel_code' => '#TRV-' . $r->travel_request_id,
            'user_id' => $r->user_id,
            'user_name' => $r->user->name ?? '-',
            'department_name' => $r->user->department->name ?? '-',
            'destination' => $r->travelRequest->destination ?? '-',
            'total_items_amount' => (float) $r->total_items_amount,
            'advance_amount' => (float) $r->advance_amount,
            'approved_amount' => $r->approved_amount !== null ? (float) $r->approved_amount : null,
            'difference_amount' => $r->difference_amount !== null ? (float) $r->difference_amount : null,
            'status' => $r->status,
            'external_journal_ref' => $r->external_journal_ref ?? '-',
            'finance_note' => $r->finance_note ?? '-',
            'created_at' => optional($r->created_at)->format('Y-m-d H:i:s'),
        ])->toArray();

        $reimbursementItems = ReimbursementItem::all()->toArray();
        $departments = Department::all()->toArray();
        $positions = Position::all()->toArray();
        $policies = TravelPolicy::with('position')->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'position_id' => $p->position_id,
            'position_name' => $p->position->name ?? '-',
            'destination_tier' => $p->destination_tier,
            'max_budget' => (float) $p->max_budget,
            'is_active' => $p->is_active,
        ])->toArray();

        return [
            'meta' => [
                'app' => 'BizJourney Corporate',
                'label' => $label ?: 'Full System Backup',
                'created_at' => now()->format('Y-m-d H:i:s'),
                'created_by' => Auth::user()->name ?? 'Admin',
                'created_by_role' => Auth::user()->role ?? 'ADMIN',
                'counts' => [
                    'users' => count($users),
                    'travel_requests' => count($travels),
                    'approvals' => count($approvals),
                    'reimbursements' => count($reimbursements),
                    'policies' => count($policies),
                ],
            ],
            'departments' => $departments,
            'positions' => $positions,
            'policies' => $policies,
            'users' => $users,
            'travel_requests' => $travels,
            'approvals' => $approvals,
            'reimbursements' => $reimbursements,
            'reimbursement_items' => $reimbursementItems,
        ];
    }

    public function backups()
    {
        $user = Auth::user();
        $dir = $this->backupDirectory();
        $files = File::glob($dir . '/*.json');

        $backups = [];
        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $content = json_decode(File::get($filePath), true);
            $backups[] = [
                'filename' => $filename,
                'size_kb' => round(File::size($filePath) / 1024, 2),
                'modified_at' => File::lastModified($filePath),
                'meta' => $content['meta'] ?? [
                    'label' => 'System Backup',
                    'created_at' => date('Y-m-d H:i:s', File::lastModified($filePath)),
                    'created_by' => 'Admin',
                    'counts' => [],
                ],
            ];
        }

        usort($backups, fn($a, $b) => $b['modified_at'] <=> $a['modified_at']);

        $liveStats = [
            'users' => User::count(),
            'travel_requests' => TravelRequest::count(),
            'approvals' => Approval::count(),
            'reimbursements' => Reimbursement::count(),
            'policies' => TravelPolicy::count(),
            'logs' => ActivityLog::count(),
        ];

        return view('admin.backups', compact('backups', 'liveStats', 'user'));
    }

    public function createBackup(Request $request)
    {
        $label = $request->input('label') ?: 'Backup Manual (' . Auth::user()->role . ')';
        $payload = $this->buildSnapshotPayload($label);

        $filename = 'backup-bizjourney-' . now()->format('Y-m-d_His') . '.json';
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $filename;

        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        ActivityLog::log(
            action: 'CREATE_BACKUP',
            description: Auth::user()->name . " (" . Auth::user()->role . ") membuat snapshot backup data sistem: {$filename}.",
            entityType: 'Backup'
        );

        return back()->with('success', "Backup data berhasil dibuat ({$filename}). Anda dapat mengunduhnya ke format Excel (.xls / .csv) kapan saja.");
    }

    private function resolveBackupData(string $source): array
    {
        if ($source === 'live') {
            return $this->buildSnapshotPayload('Live Database Export');
        }

        $safeName = basename($source);
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $safeName;
        if (!File::exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return json_decode(File::get($path), true) ?: [];
    }

    public function downloadBackupExcel(Request $request, string $source = 'live')
    {
        $data = $this->resolveBackupData($source);
        $timestamp = now()->format('Y-m-d_His');
        $downloadName = ($source === 'live')
            ? "BizJourney_Data_Excel_{$timestamp}.xls"
            : pathinfo($source, PATHINFO_FILENAME) . ".xls";

        $xml = $this->generateExcelSpreadsheetXml($data);

        ActivityLog::log(
            action: 'EXPORT_EXCEL',
            description: Auth::user()->name . " mengunduh data backup ({$source}) ke format Microsoft Excel ({$downloadName}).",
            entityType: 'Backup'
        );

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$downloadName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadBackupCsv(Request $request, string $source = 'live')
    {
        $module = $request->get('module', 'travel_requests');
        $data = $this->resolveBackupData($source);
        $timestamp = now()->format('Y-m-d_His');
        $prefix = ($source === 'live') ? 'live' : pathinfo($source, PATHINFO_FILENAME);
        $downloadName = "{$prefix}_{$module}_{$timestamp}.csv";

        $rows = [];
        if ($module === 'users') {
            $rows[] = ['ID', 'Nama Lengkap', 'Email', 'Role', 'Departemen', 'Jabatan', 'Status Aktif', 'Terdaftar Pada'];
            foreach ($data['users'] ?? [] as $u) {
                $rows[] = [
                    $u['id'] ?? '',
                    $u['name'] ?? '',
                    $u['email'] ?? '',
                    $u['role'] ?? '',
                    $u['department_name'] ?? '',
                    $u['position_name'] ?? '',
                    !empty($u['is_active']) ? 'Aktif' : 'Nonaktif',
                    $u['created_at'] ?? '',
                ];
            }
        } elseif ($module === 'approvals') {
            $rows[] = ['ID Approval', 'Kode Travel', 'Nama Pemohon', 'Tujuan Dinas', 'Level', 'Tahap Approval', 'Nama Approver', 'Status Keputusan', 'Catatan Review', 'Waktu Proses'];
            foreach ($data['approvals'] ?? [] as $a) {
                $rows[] = [
                    $a['id'] ?? '',
                    $a['travel_code'] ?? '',
                    $a['requester_name'] ?? '',
                    $a['destination'] ?? '',
                    $a['level'] ?? '',
                    $a['stage_name'] ?? '',
                    $a['approver_name'] ?? '',
                    $a['status'] ?? '',
                    $a['note'] ?? '',
                    $a['updated_at'] ?? '',
                ];
            }
        } elseif ($module === 'reimbursements') {
            $rows[] = ['ID Klaim', 'Kode Klaim', 'Kode Travel', 'Nama Pemohon', 'Departemen', 'Tujuan Dinas', 'Total Bukti Klaim (Rp)', 'Uang Muka (Rp)', 'Disetujui Finance (Rp)', 'Selisih (Rp)', 'Status', 'No Referensi Jurnal/Bank', 'Catatan Finance', 'Tanggal Pengajuan'];
            foreach ($data['reimbursements'] ?? [] as $r) {
                $rows[] = [
                    $r['id'] ?? '',
                    $r['code'] ?? '',
                    $r['travel_code'] ?? '',
                    $r['user_name'] ?? '',
                    $r['department_name'] ?? '',
                    $r['destination'] ?? '',
                    $r['total_items_amount'] ?? 0,
                    $r['advance_amount'] ?? 0,
                    $r['approved_amount'] ?? 0,
                    $r['difference_amount'] ?? 0,
                    $r['status'] ?? '',
                    $r['external_journal_ref'] ?? '',
                    $r['finance_note'] ?? '',
                    $r['created_at'] ?? '',
                ];
            }
        } else {
            // Default: travel_requests
            $rows[] = ['ID Travel', 'Kode Travel', 'Nama Pemohon', 'Email Pemohon', 'Departemen/Divisi', 'Jabatan', 'Tujuan Perjalanan', 'Keperluan/Uraian Tugas', 'Tanggal Berangkat', 'Tanggal Pulang', 'Estimasi Biaya (Rp)', 'Kebijakan Plafon', 'Status Sistem', 'Tahap Persetujuan', 'Tanggal Diajukan'];
            foreach ($data['travel_requests'] ?? [] as $t) {
                $rows[] = [
                    $t['id'] ?? '',
                    $t['code'] ?? '',
                    $t['user_name'] ?? '',
                    $t['user_email'] ?? '',
                    $t['department_name'] ?? '',
                    $t['position_name'] ?? '',
                    $t['destination'] ?? '',
                    $t['purpose'] ?? '',
                    $t['start_date'] ?? '',
                    $t['end_date'] ?? '',
                    $t['estimated_cost'] ?? 0,
                    $t['policy_name'] ?? '',
                    $t['status'] ?? '',
                    $t['stage_label'] ?? ($t['status'] ?? ''),
                    $t['created_at'] ?? '',
                ];
            }
        }

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM agar Microsoft Excel otomatis mengenali encoding & kolom
            fwrite($handle, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$downloadName}\"",
        ]);
    }

    public function downloadBackupJson(string $filename)
    {
        $safeName = basename($filename);
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $safeName;
        if (!File::exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($path, $safeName, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function deleteBackup(string $filename)
    {
        $safeName = basename($filename);
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($path)) {
            File::delete($path);

            ActivityLog::log(
                action: 'DELETE_BACKUP',
                description: Auth::user()->name . " menghapus file arsip backup: {$safeName}.",
                entityType: 'Backup'
            );

            return back()->with('success', "File backup {$safeName} berhasil dihapus.");
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    public function restoreBackup(string $filename)
    {
        $safeName = basename($filename);
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $safeName;
        if (!File::exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        $payload = json_decode(File::get($path), true);
        if (!is_array($payload) || !isset($payload['travel_requests'])) {
            return back()->with('error', 'Format file backup tidak valid.');
        }

        $this->restoreFromPayload($payload, $safeName);

        return back()->with('success', "Data dari backup {$safeName} berhasil dipulihkan (restore) ke dalam sistem!");
    }

    public function importBackupFile(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:20480',
        ]);

        $file = $request->file('backup_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'json') {
            $payload = json_decode(file_get_contents($file->getRealPath()), true);
            if (!is_array($payload) || !isset($payload['travel_requests'])) {
                return back()->with('error', 'File JSON backup tidak valid.');
            }

            // Simpan juga salinannya ke folder backup
            $savedName = 'imported-backup-' . now()->format('Y-m-d_His') . '.json';
            File::put($this->backupDirectory() . DIRECTORY_SEPARATOR . $savedName, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $this->restoreFromPayload($payload, $file->getClientOriginalName());

            return back()->with('success', 'File backup JSON berhasil di-import dan dipulihkan ke sistem!');
        }

        if ($ext === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                return back()->with('error', 'Gagal membaca file CSV.');
            }

            // Baca baris pertama (header) & bersihkan BOM jika ada
            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return back()->with('error', 'File CSV kosong.');
            }
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);

            $importedCount = 0;
            $defaultPolicyId = TravelPolicy::value('id');
            $defaultUser = Auth::user();

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) < 6 || empty( array_filter($row) )) {
                        continue;
                    }

                    // Deteksi apakah CSV Perjalanan Dinas (minimal punya kolom Tujuan & Tanggal)
                    $userEmail = trim($row[3] ?? '');
                    $targetUser = User::where('email', $userEmail)->first() ?? $defaultUser;
                    $destination = trim($row[6] ?? ($row[2] ?? ''));
                    $purpose = trim($row[7] ?? ($row[3] ?? 'Perjalanan Dinas (Import CSV)'));
                    $startDate = trim($row[8] ?? date('Y-m-d'));
                    $endDate = trim($row[9] ?? date('Y-m-d'));
                    $cost = (float) preg_replace('/[^0-9.]/', '', (string) ($row[10] ?? 0));
                    $status = trim($row[12] ?? 'APPROVED');

                    if (!empty($destination) && $defaultPolicyId) {
                        TravelRequest::create([
                            'user_id' => $targetUser->id,
                            'department_id' => $targetUser->department_id,
                            'destination' => $destination,
                            'purpose' => $purpose ?: 'Perjalanan Dinas',
                            'start_date' => strtotime($startDate) ? date('Y-m-d', strtotime($startDate)) : date('Y-m-d'),
                            'end_date' => strtotime($endDate) ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d'),
                            'estimated_cost' => $cost,
                            'policy_id' => $defaultPolicyId,
                            'status' => in_array($status, ['DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'REJECTED', 'CANCELLED', 'COMPLETED'], true) ? $status : 'APPROVED',
                        ]);
                        $importedCount++;
                    }
                }
                fclose($handle);

                ActivityLog::log(
                    action: 'IMPORT_CSV',
                    description: Auth::user()->name . " mengimpor {$importedCount} baris data dari file CSV ({$file->getClientOriginalName()}).",
                    entityType: 'Backup'
                );

                DB::commit();
                return back()->with('success', "Berhasil mengimpor {$importedCount} baris data dari file CSV Excel!");
            } catch (\Exception $e) {
                fclose($handle);
                DB::rollBack();
                return back()->with('error', 'Gagal mengimpor CSV: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Format file harus berupa .json atau .csv (Excel CSV).');
    }

    public function purgeData(Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:rejected_cancelled_travels,all_travels,all_reimbursements,activity_logs',
            'auto_backup' => 'nullable|boolean',
        ]);

        if ($request->boolean('auto_backup', true)) {
            $payload = $this->buildSnapshotPayload('Auto-Backup Sebelum Hapus Massal (' . $validated['target'] . ')');
            $filename = 'backup-pre-delete-' . now()->format('Y-m-d_His') . '.json';
            File::put($this->backupDirectory() . DIRECTORY_SEPARATOR . $filename, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        DB::beginTransaction();
        try {
            if ($validated['target'] === 'rejected_cancelled_travels') {
                $travels = TravelRequest::whereIn('status', ['REJECTED', 'CANCELLED', 'DRAFT'])->get();
                $count = $travels->count();
                foreach ($travels as $t) {
                    $t->approvals()->delete();
                    $t->documents()->delete();
                    $t->bookings()->delete();
                    if ($t->reimbursement) {
                        $t->reimbursement->items()->delete();
                        $t->reimbursement->delete();
                    }
                    $t->delete();
                }
                $msg = "{$count} data perjalanan dinas berstatus Ditolak/Dibatalkan/Draft berhasil dihapus.";
            } elseif ($validated['target'] === 'all_reimbursements') {
                $count = Reimbursement::count();
                ReimbursementItem::query()->delete();
                Reimbursement::query()->delete();
                $msg = "Seluruh data klaim reimbursement ({$count} data) berhasil dihapus.";
            } elseif ($validated['target'] === 'all_travels') {
                $count = TravelRequest::count();
                ReimbursementItem::query()->delete();
                Reimbursement::query()->delete();
                TravelDocument::query()->delete();
                TravelBooking::query()->delete();
                Approval::query()->delete();
                TravelRequest::query()->delete();
                $msg = "Seluruh data perjalanan dinas & persetujuan ({$count} data) berhasil dihapus.";
            } else {
                $count = ActivityLog::count();
                ActivityLog::query()->delete();
                $msg = "Seluruh riwayat log aktivitas ({$count} catatan) berhasil dihapus.";
            }

            ActivityLog::log(
                action: 'PURGE_DATA',
                description: Auth::user()->name . " (" . Auth::user()->role . ") melakukan penghapusan data kategori [{$validated['target']}].",
                entityType: 'System'
            );

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function restoreFromPayload(array $payload, string $sourceName): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            if (!empty($payload['travel_requests'])) {
                Approval::truncate();
                ReimbursementItem::truncate();
                Reimbursement::truncate();
                TravelRequest::truncate();

                foreach ($payload['travel_requests'] as $t) {
                    TravelRequest::create([
                        'id' => $t['id'] ?? null,
                        'user_id' => $t['user_id'],
                        'department_id' => $t['department_id'] ?? null,
                        'destination' => $t['destination'],
                        'purpose' => $t['purpose'],
                        'start_date' => $t['start_date'],
                        'end_date' => $t['end_date'],
                        'estimated_cost' => $t['estimated_cost'],
                        'policy_id' => $t['policy_id'],
                        'status' => $t['status'],
                    ]);
                }
            }

            if (!empty($payload['approvals'])) {
                foreach ($payload['approvals'] as $a) {
                    Approval::create([
                        'id' => $a['id'] ?? null,
                        'travel_request_id' => $a['travel_request_id'],
                        'approver_id' => $a['approver_id'] ?? null,
                        'level' => $a['level'] ?? 1,
                        'status' => $a['status'] ?? 'PENDING',
                        'note' => ($a['note'] ?? '-') === '-' ? null : $a['note'],
                    ]);
                }
            }

            if (!empty($payload['reimbursements'])) {
                foreach ($payload['reimbursements'] as $r) {
                    Reimbursement::create([
                        'id' => $r['id'] ?? null,
                        'travel_request_id' => $r['travel_request_id'],
                        'user_id' => $r['user_id'],
                        'advance_amount' => $r['advance_amount'] ?? 0,
                        'approved_amount' => $r['approved_amount'] ?? null,
                        'difference_amount' => $r['difference_amount'] ?? null,
                        'status' => $r['status'] ?? 'DRAFT',
                        'external_journal_ref' => ($r['external_journal_ref'] ?? '-') === '-' ? null : $r['external_journal_ref'],
                        'finance_note' => ($r['finance_note'] ?? '-') === '-' ? null : $r['finance_note'],
                    ]);
                }
            }

            if (!empty($payload['reimbursement_items'])) {
                foreach ($payload['reimbursement_items'] as $item) {
                    ReimbursementItem::create([
                        'id' => $item['id'] ?? null,
                        'reimbursement_id' => $item['reimbursement_id'],
                        'category' => $item['category'],
                        'description' => $item['description'],
                        'amount' => $item['amount'],
                        'transaction_date' => substr((string) $item['transaction_date'], 0, 10),
                        'receipt_path' => $item['receipt_path'] ?? null,
                    ]);
                }
            }

            ActivityLog::log(
                action: 'RESTORE_BACKUP',
                description: Auth::user()->name . " memulihkan (restore) data sistem dari file backup: {$sourceName}.",
                entityType: 'Backup'
            );
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    private function escXml($val): string
    {
        return htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function generateExcelSpreadsheetXml(array $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:x="urn:schemas-microsoft-com:office:excel"
            xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";

        $xml .= '<Styles>
            <Style ss:ID="Default" ss:Name="Normal">
                <Alignment ss:Vertical="Center"/>
                <Font ss:FontName="Calibri" ss:Size="11"/>
            </Style>
            <Style ss:ID="Header">
                <Alignment ss:Vertical="Center" ss:Horizontal="Left"/>
                <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
                <Interior ss:Color="#1E293B" ss:Pattern="Solid"/>
            </Style>
            <Style ss:ID="Currency">
                <NumberFormat ss:Format="#,##0"/>
            </Style>
        </Styles>' . "\n";

        // Sheet 1: Perjalanan Dinas
        $xml .= $this->buildWorksheetXml(
            'Perjalanan Dinas',
            ['Kode Travel', 'Nama Pemohon', 'Email', 'Divisi', 'Jabatan', 'Tujuan Dinas', 'Uraian Tugas', 'Tgl Berangkat', 'Tgl Pulang', 'Estimasi Biaya (Rp)', 'Kebijakan Plafon', 'Status Sistem', 'Tahap Persetujuan', 'Waktu Pengajuan'],
            array_map(fn($t) => [
                $t['code'] ?? '',
                $t['user_name'] ?? '',
                $t['user_email'] ?? '',
                $t['department_name'] ?? '',
                $t['position_name'] ?? '',
                $t['destination'] ?? '',
                $t['purpose'] ?? '',
                $t['start_date'] ?? '',
                $t['end_date'] ?? '',
                (float) ($t['estimated_cost'] ?? 0),
                $t['policy_name'] ?? '',
                $t['status'] ?? '',
                $t['stage_label'] ?? ($t['status'] ?? ''),
                $t['created_at'] ?? '',
            ], $data['travel_requests'] ?? []),
            [9]
        );

        // Sheet 2: Persetujuan Approval (Manager & HRD)
        $xml .= $this->buildWorksheetXml(
            'Persetujuan Approval',
            ['ID Approval', 'Kode Travel', 'Nama Pemohon', 'Tujuan Dinas', 'Level', 'Tahap Persetujuan', 'Peninjau (Approver)', 'Status Keputusan', 'Catatan Review', 'Waktu Keputusan'],
            array_map(fn($a) => [
                $a['id'] ?? '',
                $a['travel_code'] ?? '',
                $a['requester_name'] ?? '',
                $a['destination'] ?? '',
                $a['level'] ?? '',
                $a['stage_name'] ?? '',
                $a['approver_name'] ?? '',
                $a['status'] ?? '',
                $a['note'] ?? '',
                $a['updated_at'] ?? '',
            ], $data['approvals'] ?? [])
        );

        // Sheet 3: Klaim Reimbursement
        $xml .= $this->buildWorksheetXml(
            'Klaim Reimbursement',
            ['Kode Klaim', 'Kode Travel', 'Nama Pemohon', 'Divisi', 'Tujuan Dinas', 'Total Bukti Klaim (Rp)', 'Uang Muka (Rp)', 'Disetujui Finance (Rp)', 'Selisih (Rp)', 'Status Klaim', 'No Referensi Bank', 'Catatan Finance', 'Tanggal Klaim'],
            array_map(fn($r) => [
                $r['code'] ?? '',
                $r['travel_code'] ?? '',
                $r['user_name'] ?? '',
                $r['department_name'] ?? '',
                $r['destination'] ?? '',
                (float) ($r['total_items_amount'] ?? 0),
                (float) ($r['advance_amount'] ?? 0),
                (float) ($r['approved_amount'] ?? 0),
                (float) ($r['difference_amount'] ?? 0),
                $r['status'] ?? '',
                $r['external_journal_ref'] ?? '',
                $r['finance_note'] ?? '',
                $r['created_at'] ?? '',
            ], $data['reimbursements'] ?? []),
            [5, 6, 7, 8]
        );

        // Sheet 4: Data Pengguna
        $xml .= $this->buildWorksheetXml(
            'Data Pengguna',
            ['ID User', 'Nama Lengkap', 'Email', 'Role Sistem', 'Divisi / Departemen', 'Jabatan', 'Status Akun', 'Tanggal Dibuat'],
            array_map(fn($u) => [
                $u['id'] ?? '',
                $u['name'] ?? '',
                $u['email'] ?? '',
                $u['role'] ?? '',
                $u['department_name'] ?? '',
                $u['position_name'] ?? '',
                !empty($u['is_active']) ? 'Aktif' : 'Nonaktif',
                $u['created_at'] ?? '',
            ], $data['users'] ?? [])
        );

        // Sheet 5: Master Kebijakan
        $xml .= $this->buildWorksheetXml(
            'Master Kebijakan',
            ['ID Kebijakan', 'Nama Kebijakan', 'Jabatan', 'Kategori Tujuan', 'Maksimal Plafon (Rp)', 'Status'],
            array_map(fn($p) => [
                $p['id'] ?? '',
                $p['name'] ?? '',
                $p['position_name'] ?? '',
                $p['destination_tier'] ?? '',
                (float) ($p['max_budget'] ?? 0),
                !empty($p['is_active']) ? 'Aktif' : 'Nonaktif',
            ], $data['policies'] ?? []),
            [4]
        );

        $xml .= '</Workbook>';
        return $xml;
    }

    private function buildWorksheetXml(string $sheetName, array $headers, array $rows, array $numericCols = []): string
    {
        $out = '<Worksheet ss:Name="' . $this->escXml($sheetName) . '"><Table>' . "\n";

        // Header Row
        $out .= '<Row ss:Height="24">';
        foreach ($headers as $h) {
            $out .= '<Cell ss:StyleID="Header"><Data ss:Type="String">' . $this->escXml($h) . '</Data></Cell>';
        }
        $out .= '</Row>' . "\n";

        // Data Rows
        foreach ($rows as $row) {
            $out .= '<Row ss:Height="20">';
            foreach (array_values($row) as $colIdx => $val) {
                if (in_array($colIdx, $numericCols, true) && is_numeric($val)) {
                    $out .= '<Cell ss:StyleID="Currency"><Data ss:Type="Number">' . $val . '</Data></Cell>';
                } else {
                    $out .= '<Cell><Data ss:Type="String">' . $this->escXml($val) . '</Data></Cell>';
                }
            }
            $out .= '</Row>' . "\n";
        }

        $out .= '</Table></Worksheet>' . "\n";
        return $out;
    }
}

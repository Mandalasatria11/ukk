<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Position;
use App\Models\TravelPolicy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
}

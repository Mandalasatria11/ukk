<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Demo users for quick login
        $demoUsers = User::with(['department', 'position'])
            ->where('is_active', true)
            ->get();

        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error', 'Akun Anda dinonaktifkan oleh Administrator.');
            }

            $request->session()->regenerate();

            ActivityLog::log(
                action: 'LOGIN',
                description: "User {$user->name} ({$user->role}) berhasil login.",
                entityType: 'User',
                entityId: $user->id,
                userId: $user->id
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withInput($request->only('email', 'remember'))
            ->with('error', 'Email atau password yang Anda masukkan salah.');
    }

    public function quickLogin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_active) {
            return back()->with('error', 'Akun ini sedang dinonaktifkan.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::log(
            action: 'QUICK_LOGIN',
            description: "Quick Login sebagai {$user->name} ({$user->role}).",
            entityType: 'User',
            entityId: $user->id,
            userId: $user->id
        );

        return redirect()->route('dashboard')
            ->with('success', "Login cepat berhasil sebagai {$user->name} ({$user->role})");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log(
                action: 'LOGOUT',
                description: "User {$user->name} logout.",
                entityType: 'User',
                entityId: $user->id,
                userId: $user->id
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}

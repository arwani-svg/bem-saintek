<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            ActivityLog::record($request->user(), ActivityLog::LOGIN, 'Masuk ke dashboard.');

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'nim' => 'NIM atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        ActivityLog::record(Auth::user(), ActivityLog::LOGOUT, 'Keluar dari dashboard.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

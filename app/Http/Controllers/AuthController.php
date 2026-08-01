<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUserBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('=== LOGIN DICOBA ===');
        Log::info($credentials);

        if (Auth::attempt($credentials)) {

            Log::info('AUTH BERHASIL');
            Log::info([
                'id' => Auth::id(),
                'user' => Auth::user(),
                'session_id' => session()->getId(),
            ]);

            $request->session()->regenerate();

            return $this->redirectUserBasedOnRole(Auth::user());
        }

        Log::info('AUTH GAGAL');

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }

    private function redirectUserBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'driver') {
            return redirect()->route('driver.dashboard');
        } elseif ($user->role === 'mitra') {
            return redirect()->route('mitra.dashboard');
        }
        return redirect()->route('home');
    }
}

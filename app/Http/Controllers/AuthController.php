<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah',
            ])->withInput($request->only('username'));
        }

        Auth::login($user);
        session(['last_activity' => time()]);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Anda telah keluar dari sistem.');
    }

    /**
     * Redirect the user based on their role.
     */
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'pemilik':
                return redirect('/dashboard');
            case 'kasir':
                return redirect('/transaksi');
            case 'gudang':
                return redirect('/peta-rak');
            default:
                return redirect('/login');
        }
    }
}

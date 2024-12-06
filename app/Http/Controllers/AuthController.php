<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek admin dengan email
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            // Gunakan guard 'admin' untuk login
            Auth::guard('admin')->login($admin);

            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->with('error', 'Email atau password salah');
    }

    // Logout admin
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('login');
    }
}

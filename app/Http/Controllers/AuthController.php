<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('index'); // dashboard admin
            }

            if (Auth::user()->role === 'user') {
                return redirect()->route('index.user'); // dashboard user
            }
        }

        return back()->with('error', 'Email atau password salah!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|min:5",
            "phone" => "nullable"
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
            "role" => "user" // default
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat!');
    }

    public function logout(Request $request)
    {
        Auth::logout();                     // Hapus autentikasi user
        $request->session()->invalidate();  // Hapus sesi lama
        $request->session()->regenerateToken(); // Buat CSRF token baru

        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }

}
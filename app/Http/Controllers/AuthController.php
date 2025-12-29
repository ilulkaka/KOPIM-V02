<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'success' => false,
            ], 401);
        }

        if (Auth::attempt($credentials)) {

            // HAPUS token lama (opsional tapi disarankan)
            $user->tokens()->delete();

            $request->session()->regenerate();

            $token = $request->user()->createToken('kopim')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'dashboard_url' => url('/dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login failed',
        ], 401);
    }

    public function logout(Request $request)
    {
        // Hapus token API saat ini (jika ada)
        if ($request->user()) {
            $request->user()->currentAccessToken()?->delete();
        }

        // Logout session web
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /*
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Buat token sederhana (bisa ganti random atau hash)
            $token = bin2hex(random_bytes(32));

            // Simpan di session (opsional, kalau mau server validasi)
            $request->session()->put('kopim_token', $token);

                        return redirect()->intended('/dashboard');

            // Kirim token ke frontend
            // return response()->json([
            //     'success' => true,
            //     'token' => $token
            // ]);
        }

        return response()->json(['success' => false, 'message' => 'Login failed'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
        */
}

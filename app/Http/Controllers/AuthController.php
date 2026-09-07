<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\AccountRequest;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($input['nip']);
        $password = $input['password'];

        // Determine user by NIP, Email, or Shortcut
        $user = null;
        if ($loginInput === 'admin') {
            $user = User::where('role', 'admin')->first();
        } elseif ($loginInput === 'superadmin') {
            $user = User::where('role', 'superadmin')->first();
        } elseif (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            $user = User::where('nip', $loginInput)->first();
        }

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'nip' => 'NIP/Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('nip');
    }

    public function showRegister()
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('auth.register', compact('jabatans'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:users,nip', 'unique:account_requests,nip'],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'golongan' => ['nullable', 'string', 'max:10'],
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'pesan' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Instead of creating a direct user, we create an AccountRequest and a User with status 'pending'
        $user = User::create([
            'nip' => $validated['nip'],
            'nama' => $validated['nama'],
            'email' => $validated['email'] ?? null,
            'golongan' => $validated['golongan'] ?? null,
            'jabatan_id' => $validated['jabatan_id'],
            'password' => Hash::make($validated['password']),
            'status_akun' => 'pending',
            'role' => 'peserta',
        ]);

        AccountRequest::create([
            'nip' => $validated['nip'],
            'nama' => $validated['nama'],
            'email' => $validated['email'] ?? null,
            'golongan' => $validated['golongan'] ?? null,
            'jabatan_id' => $validated['jabatan_id'],
            'pesan' => $validated['pesan'] ?? null,
        ]);

        // Notify all admins about new pending account
        Notification::kirimKeAdmin(
            'Permintaan Akun Baru',
            "Peserta baru {$validated['nama']} (NIP: {$validated['nip']}) mendaftar dan menunggu persetujuan.",
            'warning',
            'user-plus',
            route('admin.akun.index')
        );

        // Auto login so they see the waiting approval page
        Auth::login($user);

        return redirect()->route('auth.waiting');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showWaitingApproval()
    {
        if (Auth::check() && Auth::user()->isApproved()) {
            return redirect()->route('dashboard');
        }
        return view('auth.waiting-approval');
    }

    public function showChangePassword()
    {
        if (!Auth::user()->force_change_password) {
            return redirect()->route('dashboard');
        }
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->force_change_password = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccountRequest;
use App\Models\Notification;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ManajemenAkunController extends Controller
{
    public function index(Request $request)
    {
        $pendingRequests = AccountRequest::where('status', 'menunggu')->orderBy('created_at', 'desc')->get();
        
        $query = User::with(['jabatan', 'unitKerja']);

        // Non-superadmin cannot see/manage superadmin
        if (!auth()->user()->isSuperadmin()) {
            $query->where('role', '!=', 'superadmin');
        }

        // Search filter (Nama, NIP, Email)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role') && in_array($request->role, ['peserta', 'admin', 'superadmin'])) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status') && in_array($request->status, ['approved', 'pending', 'rejected'])) {
            $query->where('status_akun', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $jabatans = Jabatan::orderBy('nama_jabatan', 'asc')->get();
        $unitKerjas = UnitKerja::orderBy('nama_unit', 'asc')->get();

        return view('admin.akun.index', compact('pendingRequests', 'users', 'jabatans', 'unitKerjas'));
    }

    public function store(Request $request)
    {
        $allowedRoles = auth()->user()->isSuperadmin() ? ['peserta', 'admin', 'superadmin'] : ['peserta', 'admin'];

        $validated = $request->validate([
            'nip' => 'required|string|max:30|unique:users,nip',
            'nama' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:users,email',
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => 'required|string|min:6',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'golongan' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
            'status_akun' => 'required|in:approved,pending,rejected',
        ], [
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return back()->with('success', "User {$user->nama} ({$user->nip}) berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isSuperadmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Tidak diizinkan mengubah akun Super Administrator.');
        }

        $allowedRoles = auth()->user()->isSuperadmin() ? ['peserta', 'admin', 'superadmin'] : ['peserta', 'admin'];

        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($user->id)],
            'nama' => 'required|string|max:150',
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => 'nullable|string|min:6',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'golongan' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
            'status_akun' => 'required|in:approved,pending,rejected',
        ], [
            'nip.unique' => 'NIP sudah terdaftar di akun lain.',
            'email.unique' => 'Email sudah terdaftar di akun lain.',
            'password.min' => 'Password minimal 6 karakter jika diisi.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', "Data akun {$user->nama} ({$user->nip}) berhasil diperbarui.");
    }

    public function approve(Request $request, $id)
    {
        $accountRequest = AccountRequest::findOrFail($id);
        
        if ($accountRequest->status !== 'menunggu') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        // Update Request
        $accountRequest->update([
            'status' => 'disetujui',
            'diproses_oleh' => auth()->id(),
            'tanggal_proses' => now(),
        ]);

        // Update User
        $user = User::where('nip', $accountRequest->nip)->first();
        if ($user) {
            $user->update(['status_akun' => 'approved']);

            // Notify the peserta
            Notification::kirim(
                $user->id,
                'Akun Anda Telah Disetujui',
                'Selamat! Akun Anda sudah aktif. Silakan login dan mulai belajar.',
                'success',
                'check-circle',
                route('dashboard')
            );
            
            // Send Email (silently fail if mail config is broken)
            try {
                Mail::to($user->email)->send(new AccountApprovedMail($user));
            } catch (\Exception $e) {
                // Log or ignore
            }
        }

        return back()->with('success', "Akun dengan NIP {$accountRequest->nip} berhasil disetujui.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['alasan_tolak' => 'required|string|max:500']);
        
        $accountRequest = AccountRequest::findOrFail($id);
        
        if ($accountRequest->status !== 'menunggu') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        // Update Request
        $accountRequest->update([
            'status' => 'ditolak',
            'diproses_oleh' => auth()->id(),
            'tanggal_proses' => now(),
            'alasan_tolak' => $request->alasan_tolak,
        ]);

        // Update User
        $user = User::where('nip', $accountRequest->nip)->first();
        if ($user) {
            $user->update(['status_akun' => 'rejected']);

            // Notify the peserta
            Notification::kirim(
                $user->id,
                'Pendaftaran Akun Ditolak',
                'Maaf, pendaftaran akun Anda ditolak. Silakan hubungi administrator untuk informasi lebih lanjut.',
                'danger',
                'x-circle',
                ''
            );
            
            // Send Email
            try {
                Mail::to($user->email)->send(new AccountRejectedMail($user, $request->alasan_tolak));
            } catch (\Exception $e) {
                // Log or ignore
            }
        }

        return back()->with('success', "Akun dengan NIP {$accountRequest->nip} telah ditolak.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->isSuperadmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Tidak dapat mereset password superadmin.');
        }

        // Generate temporary password
        $tempPassword = Str::random(10);
        
        $user->update([
            'password' => Hash::make($tempPassword),
            'force_change_password' => true,
        ]);

        // Send Email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $tempPassword));
        } catch (\Exception $e) {
            // Log or ignore
        }

        // For this project, we'll flash it to the session to show the admin so they can tell the user.
        return back()->with('success_reset', [
            'nama' => $user->nama,
            'nip' => $user->nip,
            'password_baru' => $tempPassword
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->isSuperadmin()) {
            abort(403, 'Tidak dapat menghapus superadmin.');
        }
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        
        return back()->with('success', 'Akun berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccountRequest;
use App\Models\Notification;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Mail;

class ManajemenAkunController extends Controller
{
    public function index()
    {
        $pendingRequests = AccountRequest::where('status', 'menunggu')->orderBy('created_at', 'desc')->get();
        
        $users = User::where('role', '!=', 'superadmin')
            ->where('status_akun', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.akun.index', compact('pendingRequests', 'users'));
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

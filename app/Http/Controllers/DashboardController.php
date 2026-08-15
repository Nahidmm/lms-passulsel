<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Materi;
use App\Models\SesiEvaluasi;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->dashboardAdmin();
        }

        return $this->dashboardPeserta($user);
    }

    private function dashboardAdmin()
    {
        $totalPengguna = User::where('role', 'peserta')->where('status_akun', 'approved')->count();
        $penggunaAktif = SesiEvaluasi::where('status', 'berlangsung')->count();
        $pendingRequests = AccountRequest::where('status', 'menunggu')->count();
        
        $highestScore = SesiEvaluasi::where('status', 'selesai')->max('skor') ?? 0;
        $lowestScore = SesiEvaluasi::where('status', 'selesai')->min('skor') ?? 0;

        return view('admin.dashboard', compact(
            'totalPengguna', 'penggunaAktif', 'pendingRequests',
            'highestScore', 'lowestScore'
        ));
    }

    private function dashboardPeserta(User $user)
    {
        $materiSelesai = $user->getMateriSelesaiCount();
        $totalMateri = \App\Models\Materi::where('is_active', true)->count();
        
        $rataNilai = $user->getRataRataSkor();
        $progres = $user->getProgresKeseluruhan();

        return view('peserta.dashboard', compact(
            'materiSelesai', 'totalMateri', 'rataNilai', 'progres'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Materi;
use App\Models\SesiEvaluasi;
use App\Models\AccountRequest;
use Carbon\Carbon;
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

        // Chart Data: Registrations per day (last 7 days)
        $chartDates = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::now()->subDays($i)->format('d M');
            $chartData[] = User::where('role', 'peserta')
                ->whereDate('created_at', $date)
                ->count();
        }

        // Recent Activities: Last 5 completed evaluations
        $recentActivities = SesiEvaluasi::with(['user', 'materi'])
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengguna', 'penggunaAktif', 'pendingRequests',
            'highestScore', 'lowestScore', 'chartDates', 'chartData', 'recentActivities'
        ));
    }

    private function dashboardPeserta(User $user)
    {
        $materiSelesai = $user->getMateriSelesaiCount();
        $totalMateri = \App\Models\Materi::where('is_active', true)->count();
        
        $rataNilai = $user->getRataRataSkor();
        $progres = $user->getProgresKeseluruhan();

        // Leaderboard Calculation
        // Calculate total_poin for all participants
        $allPeserta = User::where('role', 'peserta')
            ->select('users.*')
            ->selectRaw('
                (
                    (SELECT COUNT(*) FROM progres_materis WHERE progres_materis.user_id = users.id AND progres_materis.status = "selesai") * 50
                ) + 
                COALESCE(
                    (SELECT SUM(skor) FROM sesi_evaluasis WHERE sesi_evaluasis.user_id = users.id AND sesi_evaluasis.status = "selesai"), 0
                ) as total_poin
            ')
            ->orderByDesc('total_poin')
            ->get();

        $leaderboard = $allPeserta->take(5);

        // Find current user's rank
        $userRank = $allPeserta->search(function ($item) use ($user) {
            return $item->id === $user->id;
        });
        
        // search() returns 0-based index, so add 1 for rank. If not found (e.g. not a peserta), return '-'.
        $userRank = $userRank !== false ? $userRank + 1 : '-';

        return view('peserta.dashboard', compact(
            'materiSelesai', 'totalMateri', 'rataNilai', 'progres',
            'leaderboard', 'userRank'
        ));
    }
}

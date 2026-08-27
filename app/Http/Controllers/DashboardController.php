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

        // Leaderboard: hitung total_poin per user menggunakan getTotalPoin()
        // yang sudah benar dan konsisten (menghindari MySQL correlated subquery di derived table)
        $allPeserta = User::where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->get()
            ->map(function ($u) {
                $u->total_poin = $u->getTotalPoin();
                return $u;
            })
            ->sortByDesc('total_poin')
            ->values();

        $leaderboard = $allPeserta->take(5);

        // Find current user's rank
        $userRank = $allPeserta->search(fn($item) => $item->id === $user->id);
        $userRank = $userRank !== false ? $userRank + 1 : '-';

        // Pretest Results & Recommendations
        $pretestResults = \App\Models\HasilPretestTopik::with('topik.pelatihan')
            ->where('user_id', $user->id)
            ->get();
            
        $rekomendasi = collect();
        foreach ($pretestResults as $hasil) {
            if ($hasil->topik && $hasil->skor < $hasil->topik->batas_nilai && $hasil->topik->pelatihan_id) {
                if (!$rekomendasi->contains('id', $hasil->topik->pelatihan_id)) {
                    $rekomendasi->push((object)[
                        'pelatihan' => $hasil->topik->pelatihan,
                        'alasan' => 'Skor ' . $hasil->topik->nama_topik . ' Anda (' . $hasil->skor . ') masih di bawah standar (' . $hasil->topik->batas_nilai . ').'
                    ]);
                }
            }
        }

        return view('peserta.dashboard', compact(
            'materiSelesai', 'totalMateri', 'rataNilai', 'progres',
            'leaderboard', 'userRank', 'pretestResults', 'rekomendasi'
        ));
    }
}

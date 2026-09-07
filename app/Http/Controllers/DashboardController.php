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

        // Rata-rata Komponen Pembelajaran STRAPSUSPAS
        $rataPretest = round(\App\Models\RekapNilai::whereNotNull('nilai_pretest')->avg('nilai_pretest') ?? 0, 1);
        $rataQuiz = round(\App\Models\RekapNilai::whereNotNull('nilai_quiz_rata')->avg('nilai_quiz_rata') ?? 0, 1);
        $rataTugas = round(\App\Models\RekapNilai::whereNotNull('nilai_tugas')->avg('nilai_tugas') ?? 0, 1);
        $rataPosttest = round(\App\Models\RekapNilai::whereNotNull('nilai_posttest')->avg('nilai_posttest') ?? 0, 1);

        // Tingkat Kelulusan & Predikat
        $totalRekap = \App\Models\RekapNilai::count();
        $totalLulus = \App\Models\RekapNilai::where('status_kelulusan', 'lulus')->count();
        $lulusRate = $totalRekap > 0 ? round(($totalLulus / $totalRekap) * 100, 1) : 0;

        $predikatCounts = [
            'A' => \App\Models\RekapNilai::where('predikat', 'A')->count(),
            'B' => \App\Models\RekapNilai::where('predikat', 'B')->count(),
            'C' => \App\Models\RekapNilai::where('predikat', 'C')->count(),
            'D' => \App\Models\RekapNilai::where('predikat', 'D')->count(),
        ];

        // Tugas yang Menunggu Penilaian (Pending Review)
        $pendingSubmissions = \App\Models\TugasSubmission::with(['user', 'tugas.pelatihan'])
            ->where('status', 'submitted')
            ->latest()
            ->take(6)
            ->get();
        $countPendingTugas = \App\Models\TugasSubmission::where('status', 'submitted')->count();

        // Analisis Kelemahan Pretest (Topik dengan nilai terendah)
        $topikKelemahan = \App\Models\HasilPretestTopik::with('topik')
            ->selectRaw('topik_pelatihan_id, AVG(skor) as avg_skor, COUNT(*) as total_test')
            ->groupBy('topik_pelatihan_id')
            ->orderBy('avg_skor', 'asc')
            ->take(5)
            ->get();

        // Kursus / Pelatihan Aktif
        $pelatihans = \App\Models\Pelatihan::withCount(['materis', 'tugas'])
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        // Evaluasi Terkini
        $recentActivities = SesiEvaluasi::with(['user', 'materi'])
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengguna', 'penggunaAktif', 'pendingRequests',
            'rataPretest', 'rataQuiz', 'rataTugas', 'rataPosttest',
            'lulusRate', 'totalLulus', 'totalRekap', 'predikatCounts',
            'pendingSubmissions', 'countPendingTugas',
            'topikKelemahan', 'pelatihans', 'recentActivities'
        ));
    }

    private function dashboardPeserta(User $user)
    {
        $materiSelesai = $user->getMateriSelesaiCount();
        $totalMateri = \App\Models\Materi::where('is_active', true)->count();
        
        $rataNilai = $user->getRataRataSkor();
        $progres = $user->getProgresKeseluruhan();

        // Pretest Results
        $pretestResults = \App\Models\HasilPretestTopik::with('topik.pelatihan')
            ->where('user_id', $user->id)
            ->get();
            
        $rekomendasi = collect();
        // Active Pelatihan & Enrolled Courses
        $activeProgres = $user->getActivePelatihan();
        $activePelatihan = $activeProgres ? $activeProgres->pelatihan : null;
        if (!$activePelatihan) {
            $latestProgres = $user->progresPelatihans()->with('pelatihan')->latest('updated_at')->first();
            if ($latestProgres && $latestProgres->pelatihan) {
                $activePelatihan = $latestProgres->pelatihan;
                $activeProgres = $latestProgres;
            } else {
                $completedMateri = $user->progresMateri()->with('materi.pelatihan')->first();
                if ($completedMateri && $completedMateri->materi && $completedMateri->materi->pelatihan) {
                    $activePelatihan = $completedMateri->materi->pelatihan;
                } else {
                    $activePelatihan = \App\Models\Pelatihan::where('is_published', true)->first();
                }
            }
        }

        $activePelatihanMateriCount = $activePelatihan ? $activePelatihan->materis()->where('is_active', true)->count() : 0;
        $activePelatihanMateriSelesai = 0;
        $activePelatihanPersen = 0;
        if ($activePelatihan && $activePelatihanMateriCount > 0) {
            $materiIds = $activePelatihan->materis()->where('is_active', true)->pluck('id');
            $activePelatihanMateriSelesai = $user->progresMateri()
                ->whereIn('materi_id', $materiIds)
                ->where('status', 'selesai')
                ->count();
            $activePelatihanPersen = min(100, (int) round(($activePelatihanMateriSelesai / $activePelatihanMateriCount) * 100));
        }

        // Pending Tasks for User
        $pendingTugasPeserta = \App\Models\Tugas::where('is_active', true)
            ->whereDoesntHave('submissions', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('pelatihan')
            ->orderBy('deadline', 'asc')
            ->take(4)
            ->get();

        // Rekap Nilai Peserta
        $rekapNilais = \App\Models\RekapNilai::with('pelatihan')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('peserta.dashboard', compact(
            'materiSelesai', 'totalMateri', 'rataNilai', 'progres',
            'pretestResults', 'rekomendasi',
            'activeProgres', 'activePelatihan', 'activePelatihanMateriCount',
            'activePelatihanMateriSelesai', 'activePelatihanPersen',
            'pendingTugasPeserta', 'rekapNilais'
        ));
    }
}

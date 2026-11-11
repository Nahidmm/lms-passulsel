<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BobotNilai;
use App\Models\Pelatihan;
use App\Models\RekapNilai;
use App\Models\SesiEvaluasi;
use App\Models\TugasSubmission;
use App\Models\User;
use App\Services\NilaiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    /**
     * Akses Global Gradebook dari Sidebar Admin — otomatis memilih kursus aktif atau pilihan admin.
     */
    public function globalIndex(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');

        if ($pelatihanId === 'all') {
            return $this->allCoursesIndex($request);
        }

        $pelatihan = null;
        if ($pelatihanId) {
            $pelatihan = Pelatihan::find($pelatihanId);
        }

        if (!$pelatihan) {
            $pelatihan = Pelatihan::where('is_active', true)->first() ?? Pelatihan::first();
        }

        if (!$pelatihan) {
            return redirect()->route('admin.pelatihan.index')
                ->with('warning', 'Belum ada data pelatihan/kursus. Silakan buat pelatihan terlebih dahulu.');
        }

        return $this->index($pelatihan, $request);
    }

    /**
     * Tampilan Buku Nilai untuk Seluruh Pelatihan / Kursus (Master Rekap).
     */
    public function allCoursesIndex(Request $request)
    {
        $allPelatihans = Pelatihan::orderBy('judul')->get();
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit')->get();

        $query = User::where('role', 'peserta')
            ->where('status_akun', 'approved');

        if ($request->filled('unit_kerja_id')) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }

        $users = $query->with(['unitKerja', 'progresPelatihans.pelatihan', 'sertifikats.pelatihan'])->orderBy('nama')->get();

        $allCoursesData = [];
        foreach ($users as $user) {
            $rekaps = RekapNilai::where('user_id', $user->id)->with('pelatihan')->get();
            $progresList = $user->progresPelatihans;

            $totalEnrolled = $progresList->count();
            // Kelulusan / Selesai Pelatihan murni dari RekapNilai (status_kelulusan == 'lulus')
            $totalCompleted = $rekaps->where('status_kelulusan', 'lulus')->count();

            // Sertifikat hanya dihitung untuk pelatihan yang sertif_aktif == true
            $totalCerts = $user->sertifikats->filter(function ($cert) {
                return $cert->pelatihan && (bool) $cert->pelatihan->sertif_aktif;
            })->count();

            $enrolledIds = $progresList->pluck('pelatihan_id');
            $enrolledRekaps = $rekaps->whereIn('pelatihan_id', $enrolledIds)->filter(fn($r) => $r->nilai_akhir !== null);
            $avgScore = $enrolledRekaps->isNotEmpty() ? $enrolledRekaps->avg('nilai_akhir') : 0;

            $courseScores = [];
            foreach ($allPelatihans as $p) {
                $r = $rekaps->firstWhere('pelatihan_id', $p->id);
                $pp = $progresList->firstWhere('pelatihan_id', $p->id);
                $courseScores[$p->id] = [
                    'pelatihan' => $p,
                    'enrolled' => $pp !== null,
                    'completed' => $r && $r->status_kelulusan === 'lulus',
                    'nilai_akhir' => $r ? $r->nilai_akhir : null,
                    'status_kelulusan' => $r ? $r->status_kelulusan : null,
                ];
            }

            $allCoursesData[] = [
                'user' => $user,
                'totalEnrolled' => $totalEnrolled,
                'totalCompleted' => $totalCompleted,
                'totalCerts' => $totalCerts,
                'avgScore' => $avgScore,
                'courseScores' => $courseScores,
                'rekaps' => $rekaps,
            ];
        }

        return view('admin.gradebook.all_courses', compact(
            'allPelatihans', 'unitKerjas', 'allCoursesData', 'users'
        ));
    }

    /**
     * Moodle-style Grader Report — matriks lengkap nilai peserta.
     */
    public function index(Pelatihan $pelatihan, Request $request)
    {
        $allPelatihans = Pelatihan::orderBy('judul')->get();
        $bobot = BobotNilai::getOrCreateDefault($pelatihan->id);

        // Get users with their rekap_nilais
        $query = User::where('role', 'peserta')
            ->where('status_akun', 'approved');

        if ($request->boolean('enrolled_only')) {
            $query->whereHas('progresPelatihans', function ($q) use ($pelatihan) {
                $q->where('pelatihan_id', $pelatihan->id);
            });
        }

        // Filter by unit kerja
        if ($request->filled('unit_kerja_id')) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }

        $users = $query->with(['unitKerja'])->orderBy('nama')->get();

        // Load course structure
        $materis = $pelatihan->materis()->where('is_active', true)->orderBy('urutan')->get();
        $tugasList = $pelatihan->tugas()->where('is_active', true)->orderBy('urutan')->get();

        // Identify posttest
        $posttestMateri = $materis->first(function ($m) {
            if ($m->is_posttest) return true;
            $judul = strtolower($m->judul ?? '');
            return str_contains($judul, 'posttest')
                || str_contains($judul, 'post-test')
                || str_contains($judul, 'ujian akhir')
                || str_contains($judul, 'evaluasi akhir');
        });

        // Regular quizzes (excluding pretest and posttest)
        $regularQuizzes = $materis->filter(function ($m) use ($posttestMateri) {
            if ($m->jenis !== 'quiz') return false;
            if ($m->is_pretest) return false;
            if ($posttestMateri && $m->id === $posttestMateri->id) return false;
            return true;
        });

        // Cek apakah pelatihan ini punya quiz formatif
        $hasQuiz = $regularQuizzes->isNotEmpty();

        // Build gradebook data - recalculate to guarantee 100% fresh scores
        $gradebookData = [];
        foreach ($users as $user) {
            $rekap = NilaiService::recalculate($user->id, $pelatihan->id);

            // Fetch individual quiz scores for tooltips/breakdown
            $quizDetails = [];
            foreach ($materis->where('jenis', 'quiz')->where('is_pretest', false) as $qm) {
                $score = SesiEvaluasi::where('user_id', $user->id)
                    ->where('materi_id', $qm->id)
                    ->where('status', 'selesai')
                    ->max('skor');
                $quizDetails[] = [
                    'judul' => $qm->judul,
                    'skor' => $score !== null ? number_format($score, 1) : null,
                    'is_posttest' => (bool) $qm->is_posttest,
                ];
            }

            // Fetch individual assignment scores
            $tugasDetails = [];
            foreach ($tugasList as $tg) {
                $sub = TugasSubmission::where('user_id', $user->id)
                    ->where('tugas_id', $tg->id)
                    ->first();
                $tugasDetails[] = [
                    'judul' => $tg->judul,
                    'tipe' => $tg->tipe,
                    'nilai' => $sub && $sub->status === 'graded' ? number_format($sub->nilai, 1) : null,
                    'status' => $sub?->status ?? 'belum_kumpul',
                ];
            }

            // Satu nilai final dari masing-masing Tim Pembinaan.
            $timAverages = NilaiService::getTeamScores($user->id);

            $gradebookData[] = [
                'user' => $user,
                'rekap' => $rekap,
                'quizDetails' => $quizDetails,
                'tugasDetails' => $tugasDetails,
                'timAverages' => $timAverages,
            ];
        }

        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit')->get();

        return view('admin.gradebook.index', compact(
            'pelatihan', 'allPelatihans', 'bobot', 'gradebookData', 'materis', 'tugasList', 'unitKerjas', 'hasQuiz'
        ));
    }

    /**
     * Update bobot nilai konfigurasi.
     */
    public function updateBobot(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'bobot_quiz' => 'required|numeric|min:0|max:100',
            'bobot_tugas' => 'required|numeric|min:0|max:100',
            'bobot_mental' => 'required|numeric|min:0|max:100',
            'bobot_fisik' => 'required|numeric|min:0|max:100',
            'bobot_akhlak' => 'required|numeric|min:0|max:100',
            'bobot_kompetensi' => 'required|numeric|min:0|max:100',
            'passing_grade' => 'required|numeric|min:0|max:100',
        ]);

        $validated['bobot_pretest'] = 0;
        $validated['bobot_posttest'] = 0;

        // Validate total equals 100
        $total = $validated['bobot_quiz'] + $validated['bobot_tugas']
            + $validated['bobot_mental'] + $validated['bobot_fisik']
            + $validated['bobot_akhlak'] + $validated['bobot_kompetensi'];

        if (abs($total - 100) > 0.01) {
            return back()->with('error', 'Total bobot harus tepat 100%. Saat ini: ' . $total . '%.');
        }

        BobotNilai::updateOrCreate(
            ['pelatihan_id' => $pelatihan->id],
            $validated
        );

        // Recalculate for all users so the new weights are instantly reflected
        $allPeserta = User::where('role', 'peserta')->where('status_akun', 'approved')->pluck('id');
        foreach ($allPeserta as $uId) {
            NilaiService::recalculate($uId, $pelatihan->id);
        }

        return back()->with('success', 'Konfigurasi bobot nilai berhasil disimpan dan seluruh nilai akhir telah diperbarui.');
    }

    /**
     * Export gradebook to Excel.
     */
    public function export(Pelatihan $pelatihan)
    {
        $users = User::where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->with(['unitKerja'])
            ->orderBy('nama')
            ->get();

        $posttestMateri = $pelatihan->materis()->where('is_active', true)->where(function ($q) {
            $q->where('is_posttest', true)
              ->orWhere('judul', 'LIKE', '%posttest%')
              ->orWhere('judul', 'LIKE', '%post-test%')
              ->orWhere('judul', 'LIKE', '%ujian akhir%')
              ->orWhere('judul', 'LIKE', '%evaluasi akhir%');
        })->first();

        $quizQuery = $pelatihan->materis()
            ->where('jenis', 'quiz')
            ->where('is_pretest', false)
            ->where('is_active', true);
        if ($posttestMateri) {
            $quizQuery->where('id', '!=', $posttestMateri->id);
        }
        $hasQuiz = $quizQuery->exists();

        $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $pelatihan->judul) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users, $pelatihan, $hasQuiz) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headerRow = ['No', 'NIP', 'Nama', 'Unit Kerja', 'Pretest'];
            if ($hasQuiz) {
                $headerRow[] = 'Quiz (Rata-rata)';
            }
            $headerRow = array_merge($headerRow, [
                'Tugas', 'Post-Test', 'Mental', 'Fisik', 'Akhlak', 'Kompetensi',
                'Nilai Akhir', 'Predikat', 'Status'
            ]);

            fputcsv($file, $headerRow);

            $no = 1;
            foreach ($users as $user) {
                $rekap = RekapNilai::where('user_id', $user->id)
                    ->where('pelatihan_id', $pelatihan->id)
                    ->first();

                if (!$rekap) {
                    $rekap = NilaiService::recalculate($user->id, $pelatihan->id);
                }

                $row = [
                    $no++,
                    $user->nip,
                    $user->nama,
                    $user->unitKerja?->nama ?? '-',
                    $rekap->nilai_pretest ?? 0,
                ];
                if ($hasQuiz) {
                    $row[] = $rekap->nilai_quiz_rata ?? 0;
                }
                $teamScores = NilaiService::getTeamScores($user->id);
                $row = array_merge($row, [
                    $rekap->nilai_tugas ?? 0,
                    $rekap->nilai_posttest ?? 0,
                    $teamScores['mental'] ?? '',
                    $teamScores['fisik'] ?? '',
                    $teamScores['akhlak'] ?? '',
                    $teamScores['kompetensi'] ?? '',
                    $rekap->nilai_akhir ?? 0,
                    $rekap->predikat ?? '-',
                    $rekap->status_kelulusan === 'lulus' ? 'LULUS' : 'BELUM LULUS',
                ]);

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Rekap hasil pretest seluruh peserta.
     */
    public function pretest()
    {
        return view('admin.gradebook.pretest', $this->pretestData());
    }

    /**
     * Export rekap hasil pretest ke PDF.
     */
    public function exportPretestPdf()
    {
        $data = $this->pretestData();
        $pdf = Pdf::loadView('pdf.pretest', $data)->setPaper('a4', 'portrait');

        return $pdf->download('Rekap-Hasil-Pretest-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Data peserta + nilai pretest (skor terbaik dari sesi pretest global).
     */
    private function pretestData(): array
    {
        $pretest = \App\Models\Materi::where('is_pretest', true)->first();

        $pesertas = User::where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->with('unitKerja')
            ->orderBy('nama')
            ->get();

        $sesiMap = [];
        if ($pretest) {
            $allSessions = SesiEvaluasi::where('materi_id', $pretest->id)
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('user_id');

            foreach ($allSessions as $uId => $sessions) {
                $active = $sessions->firstWhere('status', 'berlangsung');
                $completed = $sessions->where('status', 'selesai')->sortByDesc('skor')->first();
                $sesiMap[$uId] = [
                    'active' => $active,
                    'completed' => $completed,
                ];
            }
        }

        $stats = [
            'total' => $pesertas->count(),
            'selesai' => 0,
            'mengerjakan' => 0,
            'dijeda' => 0,
            'belum' => 0,
        ];

        $rows = [];
        foreach ($pesertas as $peserta) {
            $sesiInfo = $sesiMap[$peserta->id] ?? null;
            $activeSesi = $sesiInfo['active'] ?? null;
            $completedSesi = $sesiInfo['completed'] ?? null;

            $statusUjian = 'belum_mulai';
            $nilaiPretest = $completedSesi ? (float) $completedSesi->skor : null;

            if ($activeSesi) {
                if ($activeSesi->is_paused) {
                    $statusUjian = 'dijeda';
                    $stats['dijeda']++;
                } else {
                    $statusUjian = 'sedang_mengerjakan';
                    $stats['mengerjakan']++;
                }
            } elseif ($completedSesi || $peserta->has_taken_pretest) {
                $statusUjian = 'selesai';
                $stats['selesai']++;
            } else {
                $stats['belum']++;
            }

            $rows[] = [
                'user' => $peserta,
                'nilai_pretest' => $nilaiPretest,
                'status_ujian' => $statusUjian,
                'active_sesi' => $activeSesi,
                'completed_sesi' => $completedSesi,
            ];
        }

        return [
            'pretest' => $pretest,
            'rows' => $rows,
            'stats' => $stats,
        ];
    }

    /**
     * Detail hasil pretest seorang peserta (mirip koreksi jawaban).
     */
    public function pretestDetail(User $user)
    {
        return view('admin.gradebook.pretest-detail', $this->pretestDetailData($user));
    }

    /**
     * Export detail hasil pretest seorang peserta ke PDF.
     */
    public function exportPretestDetailPdf(User $user)
    {
        $data = $this->pretestDetailData($user);
        $pdf = Pdf::loadView('pdf.pretest-detail', $data)->setPaper('a4', 'portrait');

        return $pdf->download('Hasil-Pretest-' . $user->nip . '.pdf');
    }

    /**
     * Cetak Lengkap Laporan Gradebook (Buku Nilai) — Mode Kursus Tunggal atau Seluruh Kursus.
     */
    public function cetak(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id', 'all');
        $unitKerjaId = $request->get('unit_kerja_id');
        $setting = \App\Models\SertifikatSetting::first();

        $unitKerjaFilter = null;
        if ($unitKerjaId) {
            $unitKerjaFilter = \App\Models\UnitKerja::find($unitKerjaId);
        }

        $allPelatihans = Pelatihan::orderBy('judul')->get();

        if ($pelatihanId === 'all') {
            $query = User::where('role', 'peserta')
                ->where('status_akun', 'approved');

            if ($unitKerjaId) {
                $query->where('unit_kerja_id', $unitKerjaId);
            }

            $users = $query->with(['unitKerja', 'progresPelatihans.pelatihan', 'sertifikats.pelatihan'])->orderBy('nama')->get();

            $reportData = [];
            foreach ($users as $user) {
                $rekaps = RekapNilai::where('user_id', $user->id)->with('pelatihan')->get();
                $progresList = $user->progresPelatihans;

                $enrolledIds = $progresList->pluck('pelatihan_id');
            $enrolledRekaps = $rekaps->whereIn('pelatihan_id', $enrolledIds)->filter(fn($r) => $r->nilai_akhir !== null);
            $avgScore = $enrolledRekaps->isNotEmpty() ? $enrolledRekaps->avg('nilai_akhir') : 0;
                $totalEnrolled = $progresList->count();
                // Kelulusan murni dari RekapNilai status_kelulusan == 'lulus'
                $totalCompleted = $rekaps->where('status_kelulusan', 'lulus')->count();
                // Sertifikat hanya dihitung untuk pelatihan yang sertif_aktif == true
                $totalCerts = $user->sertifikats->filter(function ($cert) {
                    return $cert->pelatihan && (bool) $cert->pelatihan->sertif_aktif;
                })->count();

                $reportData[] = [
                    'user' => $user,
                    'totalEnrolled' => $totalEnrolled,
                    'totalCompleted' => $totalCompleted,
                    'totalCerts' => $totalCerts,
                    'avgScore' => $avgScore,
                    'rekaps' => $rekaps,
                ];
            }

            return view('admin.gradebook.cetak', [
                'isAll' => true,
                'pelatihan' => null,
                'allPelatihans' => $allPelatihans,
                'reportData' => $reportData,
                'unitKerjaFilter' => $unitKerjaFilter,
                'setting' => $setting,
            ]);
        }

        // Single Pelatihan
        $pelatihan = Pelatihan::findOrFail($pelatihanId);
        $bobot = BobotNilai::getOrCreateDefault($pelatihan->id);

        $query = User::where('role', 'peserta')
            ->where('status_akun', 'approved');

        if ($unitKerjaId) {
            $query->where('unit_kerja_id', $unitKerjaId);
        }

        $users = $query->with(['unitKerja'])->orderBy('nama')->get();

        $gradebookData = [];
        foreach ($users as $user) {
            $rekap = NilaiService::recalculate($user->id, $pelatihan->id);
            $gradebookData[] = [
                'user' => $user,
                'rekap' => $rekap,
                'timAverages' => NilaiService::getTeamScores($user->id),
            ];
        }

        return view('admin.gradebook.cetak', [
            'isAll' => false,
            'pelatihan' => $pelatihan,
            'bobot' => $bobot,
            'gradebookData' => $gradebookData,
            'unitKerjaFilter' => $unitKerjaFilter,
            'setting' => $setting,
        ]);
    }

    /**
     * Data detail pretest peserta: sesi terbaik + rincian jawaban per soal.
     */
    private function pretestDetailData(User $user): array
    {
        $pretest = \App\Models\Materi::where('is_pretest', true)->first();
        $sesi = null;
        $hasilLatihans = collect();

        if ($pretest) {
            $sesi = SesiEvaluasi::where('user_id', $user->id)
                ->where('materi_id', $pretest->id)
                ->where('status', 'selesai')
                ->orderByDesc('skor')
                ->first();

            if ($sesi) {
                $hasilLatihans = $sesi->hasilLatihan()->with(['soal.pilihanJawaban', 'pilihan'])->get();
            }
        }

        return compact('user', 'pretest', 'sesi', 'hasilLatihans');
    }
}

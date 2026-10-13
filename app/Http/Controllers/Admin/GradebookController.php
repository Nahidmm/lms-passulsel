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

            // Nilai per Tim Pembinaan (rata-rata)
            $timAverages = [];
            foreach (array_keys(\App\Models\PenilaianTim::TEAMS) as $timKey) {
                $timAverages[$timKey] = \App\Models\PenilaianTim::where('user_id', $user->id)
                    ->where('tim', $timKey)
                    ->avg('nilai');
            }

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
            'pelatihan', 'allPelatihans', 'bobot', 'gradebookData', 'materis', 'tugasList', 'unitKerjas'
        ));
    }

    /**
     * Update bobot nilai konfigurasi.
     */
    public function updateBobot(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'bobot_pretest' => 'required|numeric|min:0|max:100',
            'bobot_quiz' => 'required|numeric|min:0|max:100',
            'bobot_tugas' => 'required|numeric|min:0|max:100',
            'bobot_posttest' => 'required|numeric|min:0|max:100',
            'passing_grade' => 'required|numeric|min:0|max:100',
        ]);

        // Validate total equals 100
        $total = $validated['bobot_pretest'] + $validated['bobot_quiz']
            + $validated['bobot_tugas'] + $validated['bobot_posttest'];

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

        $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $pelatihan->judul) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users, $pelatihan) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['No', 'NIP', 'Nama', 'Unit Kerja', 'Pretest', 'Quiz (Rata-rata)', 'Tugas', 'Post-Test', 'Nilai Akhir', 'Predikat', 'Status']);

            $no = 1;
            foreach ($users as $user) {
                $rekap = RekapNilai::where('user_id', $user->id)
                    ->where('pelatihan_id', $pelatihan->id)
                    ->first();

                if (!$rekap) {
                    $rekap = NilaiService::recalculate($user->id, $pelatihan->id);
                }

                fputcsv($file, [
                    $no++,
                    $user->nip,
                    $user->nama,
                    $user->unitKerja?->nama ?? '-',
                    $rekap->nilai_pretest ?? 0,
                    $rekap->nilai_quiz_rata ?? 0,
                    $rekap->nilai_tugas ?? 0,
                    $rekap->nilai_posttest ?? 0,
                    $rekap->nilai_akhir ?? 0,
                    $rekap->predikat ?? '-',
                    $rekap->status_kelulusan === 'lulus' ? 'LULUS' : 'BELUM LULUS',
                ]);
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

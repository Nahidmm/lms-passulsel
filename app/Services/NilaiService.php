<?php

namespace App\Services;

use App\Models\BobotNilai;
use App\Models\Materi;
use App\Models\PenilaianTim;
use App\Models\RekapNilai;
use App\Models\SesiEvaluasi;
use App\Models\Tugas;
use App\Models\TugasSubmission;

class NilaiService
{
    /**
     * Recalculate and save the composite final grade for a user in a pelatihan.
     */
    public static function recalculate(int $userId, int $pelatihanId): ?RekapNilai
    {
        $bobot = BobotNilai::getOrCreateDefault($pelatihanId);

        // 1. Nilai Pretest: best score from course pretest OR global pretest
        $pretestMateri = Materi::where('pelatihan_id', $pelatihanId)
            ->where('is_pretest', true)
            ->first() ?? Materi::where('is_pretest', true)->first();

        $nilaiPretest = null;
        if ($pretestMateri) {
            $bestPretest = SesiEvaluasi::where('user_id', $userId)
                ->where('materi_id', $pretestMateri->id)
                ->where('status', 'selesai')
                ->max('skor');

            if ($bestPretest !== null) {
                $nilaiPretest = (float) $bestPretest;
            }
        }

        // 2. Identify Post-Test first so it is never counted as a regular quiz
        $posttestMateri = Materi::where('pelatihan_id', $pelatihanId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('is_posttest', true)
                  ->orWhere('judul', 'LIKE', '%posttest%')
                  ->orWhere('judul', 'LIKE', '%post-test%')
                  ->orWhere('judul', 'LIKE', '%ujian akhir%')
                  ->orWhere('judul', 'LIKE', '%evaluasi akhir%');
            })
            ->first();

        $nilaiPosttest = null;
        if ($posttestMateri) {
            $bestPosttest = SesiEvaluasi::where('user_id', $userId)
                ->where('materi_id', $posttestMateri->id)
                ->where('status', 'selesai')
                ->max('skor');

            if ($bestPosttest !== null) {
                $nilaiPosttest = (float) $bestPosttest;
            }
        }

        // 3. Nilai Quiz Formatif: average of best scores per regular quiz (excluding pretest & posttest)
        $quizQuery = Materi::where('pelatihan_id', $pelatihanId)
            ->where('jenis', 'quiz')
            ->where('is_pretest', false)
            ->where('is_active', true);

        if ($posttestMateri) {
            $quizQuery->where('id', '!=', $posttestMateri->id);
        }

        $quizMateris = $quizQuery->get();

        $nilaiQuizRata = null;
        if ($quizMateris->isNotEmpty()) {
            $quizScores = [];
            foreach ($quizMateris as $m) {
                $bestScore = SesiEvaluasi::where('user_id', $userId)
                    ->where('materi_id', $m->id)
                    ->where('status', 'selesai')
                    ->max('skor');

                if ($bestScore !== null) {
                    $quizScores[] = (float) $bestScore;
                }
            }

            if (!empty($quizScores)) {
                $nilaiQuizRata = round(array_sum($quizScores) / count($quizScores), 2);
            }
        }

        // 4. Nilai Tugas (studi kasus & upload sertifikat)
        $tugasList = Tugas::where('pelatihan_id', $pelatihanId)
            ->where('is_active', true)
            ->get();

        $nilaiTugas = null;
        if ($tugasList->isNotEmpty()) {
            $gradedSubs = TugasSubmission::where('user_id', $userId)
                ->whereIn('tugas_id', $tugasList->pluck('id'))
                ->where('status', 'graded')
                ->pluck('nilai');

            if ($gradedSubs->isNotEmpty()) {
                $nilaiTugas = round($gradedSubs->avg(), 2);
            }
        }

        // 5. Penilaian akhir dari masing-masing Tim Pembinaan (satu input final).
        $nilaiTim = self::getTeamScores($userId);

        // Pretest dan posttest adalah nilai mandiri/diagnostik dan tidak masuk Nilai Akhir.
        $qScore = $nilaiQuizRata ?? 0;
        $tScore = $nilaiTugas ?? 0;

        $hasQuiz = $quizMateris->isNotEmpty();
        $bQuiz = $hasQuiz ? $bobot->bobot_quiz : 0;
        $bTugas = $bobot->bobot_tugas;
        $activeWeightSum = $bQuiz + $bTugas;
        $rawNilai = ($bQuiz / 100 * $qScore) + ($bTugas / 100 * $tScore);

        foreach (array_keys(PenilaianTim::TEAMS) as $timKey) {
            $weight = (float) $bobot->{'bobot_' . $timKey};
            $activeWeightSum += $weight;
            $rawNilai += ($weight / 100) * ($nilaiTim[$timKey] ?? 0);
        }

        // Jika tidak ada kuis formatif, bobot komponen yang aktif dinormalisasi ke 100%.
        $nilaiAkhir = $activeWeightSum > 0
            ? round($rawNilai * (100 / $activeWeightSum), 2)
            : 0;

        // 6. Tentukan Predikat & Kelulusan
        $predikat = self::getPredikat($nilaiAkhir);

        // Posttest tetap wajib diselesaikan jika tersedia, walaupun nilainya tidak dibobotkan.
        $hasTakenPosttestIfNeeded = $posttestMateri === null || $nilaiPosttest !== null;
        $hasCompleteTeamScores = collect($nilaiTim)->filter(fn ($nilai) => $nilai !== null)->count()
            === count(PenilaianTim::TEAMS);
        $statusKelulusan = ($nilaiAkhir >= $bobot->passing_grade && $hasTakenPosttestIfNeeded && $hasCompleteTeamScores)
            ? 'lulus'
            : 'tidak_lulus';

        // 7. Simpan ke rekap_nilais
        return RekapNilai::updateOrCreate(
            ['user_id' => $userId, 'pelatihan_id' => $pelatihanId],
            [
                'nilai_pretest' => $nilaiPretest !== null ? round($nilaiPretest, 2) : null,
                'nilai_quiz_rata' => $nilaiQuizRata !== null ? round($nilaiQuizRata, 2) : null,
                'nilai_tugas' => $nilaiTugas !== null ? round($nilaiTugas, 2) : null,
                'nilai_posttest' => $nilaiPosttest !== null ? round($nilaiPosttest, 2) : null,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
                'status_kelulusan' => $statusKelulusan,
            ]
        );
    }

    /**
     * Nilai final tunggal dari empat Tim Pembinaan.
     * Pekan 1 dipertahankan sebagai slot final agar kompatibel dengan tabel lama.
     */
    public static function getTeamScores(int $userId): array
    {
        $scores = array_fill_keys(array_keys(PenilaianTim::TEAMS), null);

        PenilaianTim::where('user_id', $userId)
            ->where('pekan', 1)
            ->get()
            ->each(function (PenilaianTim $penilaian) use (&$scores) {
                if (array_key_exists($penilaian->tim, $scores)) {
                    $scores[$penilaian->tim] = $penilaian->nilai !== null
                        ? (float) $penilaian->nilai
                        : null;
                }
            });

        return $scores;
    }

    /**
     * Determine letter grade (predikat) from final score.
     */
    public static function getPredikat(float $nilaiAkhir): string
    {
        if ($nilaiAkhir >= 85) return 'A';
        if ($nilaiAkhir >= 75) return 'B';
        if ($nilaiAkhir >= 65) return 'C';
        return 'D';
    }
}

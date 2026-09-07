<?php

namespace App\Services;

use App\Models\BobotNilai;
use App\Models\Materi;
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

        // 4. Nilai Tugas (studi kasus hukdis & upload sertifikat)
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

        // 5. Hitung Nilai Akhir Terbobot
        $pScore = $nilaiPretest ?? 0;
        $qScore = $nilaiQuizRata ?? 0;
        $tScore = $nilaiTugas ?? 0;
        $ptScore = $nilaiPosttest ?? 0;

        $nilaiAkhir = round(
            ($bobot->bobot_pretest / 100 * $pScore) +
            ($bobot->bobot_quiz / 100 * $qScore) +
            ($bobot->bobot_tugas / 100 * $tScore) +
            ($bobot->bobot_posttest / 100 * $ptScore),
            2
        );

        // 6. Tentukan Predikat & Kelulusan
        $predikat = self::getPredikat($nilaiAkhir);

        // Peserta lulus jika nilai akhir >= passing grade, dan jika ada posttest harus sudah dikerjakan
        $hasTakenPosttestIfNeeded = ($posttestMateri === null || $nilaiPosttest !== null);
        $statusKelulusan = ($nilaiAkhir >= $bobot->passing_grade && $hasTakenPosttestIfNeeded)
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

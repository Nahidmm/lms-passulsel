<?php

namespace App\Services;

use App\Models\GradebookSetting;
use App\Models\Materi;
use App\Models\Pelatihan;
use App\Models\PenilaianTim;
use App\Models\RekapNilai;
use App\Models\RekapNilaiGlobal;
use App\Models\SesiEvaluasi;
use App\Models\TugasSubmission;
use App\Models\User;
use Illuminate\Support\Collection;

class GlobalGradebookService
{
    public function settings(): GradebookSetting
    {
        return GradebookSetting::global();
    }

    /**
     * Build a participant's complete cross-course gradebook and persist its summary.
     */
    public function calculate(User $user, ?Collection $allCourses = null, bool $persist = true): array
    {
        $settings = $this->settings();
        $enrolledIds = $this->enrolledCourseIds($user);

        $allCourses ??= Pelatihan::query()
            ->with([
                'materis' => fn ($query) => $query->where('is_active', true)->orderBy('urutan'),
                'tugas' => fn ($query) => $query->where('is_active', true)->orderBy('urutan'),
            ])
            ->orderBy('judul')
            ->get();

        $enrolledCourses = $allCourses->whereIn('id', $enrolledIds)->values();
        $materiIds = $enrolledCourses->flatMap(fn (Pelatihan $course) => $course->materis->pluck('id'))->unique();
        $tugasIds = $enrolledCourses->flatMap(fn (Pelatihan $course) => $course->tugas->pluck('id'))->unique();

        $sessionScores = $materiIds->isEmpty()
            ? collect()
            : SesiEvaluasi::query()
                ->where('user_id', $user->id)
                ->whereIn('materi_id', $materiIds)
                ->where('status', 'selesai')
                ->get(['materi_id', 'skor'])
                ->groupBy('materi_id')
                ->map(fn (Collection $sessions) => (float) $sessions->max('skor'));

        $submissionScores = $tugasIds->isEmpty()
            ? collect()
            : TugasSubmission::query()
                ->where('user_id', $user->id)
                ->whereIn('tugas_id', $tugasIds)
                ->where('status', 'graded')
                ->get(['tugas_id', 'nilai'])
                ->groupBy('tugas_id')
                ->map(fn (Collection $submissions) => (float) $submissions->max('nilai'));

        $rekaps = $enrolledIds->isEmpty()
            ? collect()
            : RekapNilai::query()
                ->where('user_id', $user->id)
                ->whereIn('pelatihan_id', $enrolledIds)
                ->get()
                ->keyBy('pelatihan_id');

        $quizEntries = [];
        $taskEntries = [];
        $requiredScores = [];
        $courseScores = [];

        foreach ($allCourses as $course) {
            $enrolled = $enrolledIds->contains($course->id);
            $items = [];

            if ($enrolled) {
                foreach ($course->materis as $materi) {
                    $type = $this->materiGradeType($materi);
                    if ($type === null) {
                        continue;
                    }

                    $score = $sessionScores->has($materi->id)
                        ? (float) $sessionScores->get($materi->id)
                        : null;
                    $required = $type === 'posttest'
                        || ($type === 'quiz' && $settings->bobot_quiz > 0);

                    $item = [
                        'key' => 'materi-'.$materi->id,
                        'label' => $materi->judul,
                        'type' => $type,
                        'score' => $score,
                        'required' => $required,
                    ];
                    $items[] = $item;

                    if ($type === 'quiz') {
                        $quizEntries[] = $item;
                    }
                    if ($required) {
                        $requiredScores[] = $score;
                    }
                }

                foreach ($course->tugas as $tugas) {
                    $score = $submissionScores->has($tugas->id)
                        ? (float) $submissionScores->get($tugas->id)
                        : null;
                    $required = $settings->bobot_tugas > 0;
                    $item = [
                        'key' => 'tugas-'.$tugas->id,
                        'label' => $tugas->judul,
                        'type' => 'tugas',
                        'score' => $score,
                        'required' => $required,
                        'item_weight' => max(0, (float) $tugas->bobot_nilai),
                    ];
                    $items[] = $item;
                    $taskEntries[] = $item;

                    if ($required) {
                        $requiredScores[] = $score;
                    }
                }
            }

            $rekap = $rekaps->get($course->id);
            $courseScores[$course->id] = [
                'pelatihan' => $course,
                'enrolled' => $enrolled,
                'completed' => $rekap?->status_kelulusan === 'lulus',
                'nilai_akhir' => $rekap?->nilai_akhir,
                'status_kelulusan' => $rekap?->status_kelulusan,
                'items' => $items,
            ];
        }

        $quizScore = $this->simpleAverage($quizEntries);
        $taskScore = $this->weightedTaskAverage($taskEntries);
        $teamScores = NilaiService::getTeamScores($user->id);

        $scores = [
            'quiz' => $quizScore,
            'tugas' => $taskScore,
            'mental' => $teamScores['mental'] ?? null,
            'fisik' => $teamScores['fisik'] ?? null,
            'akhlak' => $teamScores['akhlak'] ?? null,
            'kompetensi' => $teamScores['kompetensi'] ?? null,
        ];
        $weights = $this->weightArray($settings);
        $active = [
            'quiz' => ! empty($quizEntries),
            'tugas' => ! empty($taskEntries),
            'mental' => $weights['mental'] > 0,
            'fisik' => $weights['fisik'] > 0,
            'akhlak' => $weights['akhlak'] > 0,
            'kompetensi' => $weights['kompetensi'] > 0,
        ];

        foreach (array_keys(PenilaianTim::TEAMS) as $teamKey) {
            if ($weights[$teamKey] > 0) {
                $requiredScores[] = $teamScores[$teamKey] ?? null;
            }
        }

        $weighted = self::calculateWeightedFinal($scores, $weights, $active);
        $totalItems = count($requiredScores);
        $completedItems = collect($requiredScores)->filter(fn ($score) => $score !== null)->count();
        $isComplete = $enrolledIds->isNotEmpty()
            && $totalItems > 0
            && $completedItems === $totalItems;
        $status = ! $isComplete
            ? 'belum_lengkap'
            : ($weighted['final'] >= $settings->passing_grade ? 'lulus' : 'tidak_lulus');
        $predikat = NilaiService::getPredikat($weighted['final']);

        $summary = [
            'scores' => $scores,
            'weights' => $weights,
            'effective_weights' => $weighted['effective_weights'],
            'contributions' => $weighted['contributions'],
            'nilai_akhir' => $weighted['final'],
            'predikat' => $predikat,
            'status_kelulusan' => $status,
            'item_selesai' => $completedItems,
            'total_item' => $totalItems,
            'is_complete' => $isComplete,
        ];

        if ($persist) {
            RekapNilaiGlobal::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nilai_quiz' => $quizScore,
                    'nilai_tugas' => $taskScore,
                    'nilai_mental' => $teamScores['mental'] ?? null,
                    'nilai_fisik' => $teamScores['fisik'] ?? null,
                    'nilai_akhlak' => $teamScores['akhlak'] ?? null,
                    'nilai_kompetensi' => $teamScores['kompetensi'] ?? null,
                    'nilai_akhir' => $weighted['final'],
                    'predikat' => $predikat,
                    'status_kelulusan' => $status,
                    'item_selesai' => $completedItems,
                    'total_item' => $totalItems,
                ]
            );
        }

        return [
            'courseScores' => $courseScores,
            'teamScores' => $teamScores,
            'summary' => $summary,
        ];
    }

    public function recalculateUser(int $userId): ?array
    {
        $user = User::with('progresPelatihans')->find($userId);

        return $user ? $this->calculate($user) : null;
    }

    public function updateSettings(array $values): GradebookSetting
    {
        $settings = $this->settings();
        $settings->update($values);

        return $settings->refresh();
    }

    /**
     * Apply category percentages exactly once to the global final grade.
     */
    public static function calculateWeightedFinal(array $scores, array $weights, array $active): array
    {
        $activeWeightTotal = 0.0;
        foreach ($weights as $key => $weight) {
            if (($active[$key] ?? false) && $weight > 0) {
                $activeWeightTotal += (float) $weight;
            }
        }

        $contributions = [];
        $effectiveWeights = [];
        $final = 0.0;

        foreach ($weights as $key => $weight) {
            if ($activeWeightTotal <= 0 || ! ($active[$key] ?? false) || $weight <= 0) {
                $contributions[$key] = 0.0;
                $effectiveWeights[$key] = 0.0;

                continue;
            }

            $effectiveWeight = ((float) $weight / $activeWeightTotal) * 100;
            $contribution = ((float) ($scores[$key] ?? 0) * $effectiveWeight) / 100;
            $effectiveWeights[$key] = round($effectiveWeight, 2);
            $contributions[$key] = round($contribution, 2);
            $final += $contribution;
        }

        return [
            'final' => round($final, 2),
            'contributions' => $contributions,
            'effective_weights' => $effectiveWeights,
        ];
    }

    private function enrolledCourseIds(User $user): Collection
    {
        $progress = $user->relationLoaded('progresPelatihans')
            ? $user->progresPelatihans
            : $user->progresPelatihans()->get();

        return $progress->pluck('pelatihan_id')->unique()->values();
    }

    private function materiGradeType(Materi $materi): ?string
    {
        if ($materi->is_pretest) {
            return 'pretest';
        }

        $title = strtolower($materi->judul ?? '');
        if ($materi->is_posttest
            || str_contains($title, 'posttest')
            || str_contains($title, 'post-test')
            || str_contains($title, 'ujian akhir')
            || str_contains($title, 'evaluasi akhir')) {
            return 'posttest';
        }

        return $materi->jenis === 'quiz' ? 'quiz' : null;
    }

    private function simpleAverage(array $entries): ?float
    {
        if (empty($entries)) {
            return null;
        }

        $sum = collect($entries)->sum(fn (array $entry) => (float) ($entry['score'] ?? 0));

        return round($sum / count($entries), 2);
    }

    private function weightedTaskAverage(array $entries): ?float
    {
        if (empty($entries)) {
            return null;
        }

        $weightTotal = collect($entries)->sum('item_weight');
        if ($weightTotal <= 0) {
            return $this->simpleAverage($entries);
        }

        $weightedTotal = collect($entries)->sum(
            fn (array $entry) => (float) ($entry['score'] ?? 0) * (float) $entry['item_weight']
        );

        return round($weightedTotal / $weightTotal, 2);
    }

    private function weightArray(GradebookSetting $settings): array
    {
        return [
            'quiz' => (float) $settings->bobot_quiz,
            'tugas' => (float) $settings->bobot_tugas,
            'mental' => (float) $settings->bobot_mental,
            'fisik' => (float) $settings->bobot_fisik,
            'akhlak' => (float) $settings->bobot_akhlak,
            'kompetensi' => (float) $settings->bobot_kompetensi,
        ];
    }
}

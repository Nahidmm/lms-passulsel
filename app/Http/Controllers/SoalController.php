<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Soal;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function create(Materi $materi)
    {
        if ($materi->jenis !== 'quiz') {
            return redirect()->back()->with('error', 'Hanya materi berjenis kuis yang dapat memiliki soal.');
        }
        $topiks = \App\Models\TopikPelatihan::all();
        return view('admin.soal.create', compact('materi', 'topiks'));
    }

    public function store(Request $request, Materi $materi)
    {
        $tipe = $request->input('tipe', 'pilihan_ganda');

        $rules = [
            'pertanyaan' => 'required|string',
            'pembahasan' => 'nullable|string',
            'tipe'       => 'required|in:pilihan_ganda,multi_select,essay,isian_singkat,menjodohkan,jenis_lainnya',
            'bobot'      => 'nullable|integer|min:1',
        ];

        // Tipe-specific validation
        if (in_array($tipe, ['pilihan_ganda', 'jenis_lainnya'])) {
            $rules['pilihan']       = 'required|array|min:2';
            $rules['pilihan.*']     = 'required|string';
            $rules['jawaban_benar'] = 'required|integer|min:0';
        } elseif ($tipe === 'multi_select') {
            $rules['pilihan']         = 'required|array|min:2';
            $rules['pilihan.*']       = 'required|string';
            $rules['jawaban_benar']   = 'required|array|min:1';
            $rules['jawaban_benar.*'] = 'integer';
        } elseif ($tipe === 'isian_singkat') {
            $rules['jawaban_teks'] = 'required|string';
        } elseif ($tipe === 'menjodohkan') {
            $rules['pasangan_kiri']    = 'required|array|min:2';
            $rules['pasangan_kiri.*']  = 'required|string';
            $rules['pasangan_kanan']   = 'required|array|min:2';
            $rules['pasangan_kanan.*'] = 'required|string';
        }
        // essay: no answer choices needed

        $validated = $request->validate($rules);

        $soal = Soal::create([
            'pertanyaan' => $validated['pertanyaan'],
            'tipe'       => $tipe,
            'bobot'      => $validated['bobot'] ?? 10,
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active'  => $request->input('is_active', '1') !== '0',
            'topik_pelatihan_id' => $request->input('topik_pelatihan_id'),
        ]);

        // Attach to Materi Quiz via pivot
        $materi->soals()->attach($soal->id);

        // Store answer choices based on type
        $this->storeAnswers($soal, $tipe, $request);

        if ($materi->is_pretest) {
            return redirect()->route('admin.pretest.index')
                ->with('success', 'Soal berhasil ditambahkan.');
        }
        return redirect()->route('admin.materi.edit', $materi->id)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Soal $soal)
    {
        $materi = $soal->materis()->first();
        $soal->load('pilihanJawaban');
        $topiks = \App\Models\TopikPelatihan::all();
        return view('admin.soal.edit', compact('soal', 'materi', 'topiks'));
    }

    public function update(Request $request, Soal $soal)
    {
        $tipe = $request->input('tipe', $soal->tipe);

        $rules = [
            'pertanyaan' => 'required|string',
            'pembahasan' => 'nullable|string',
            'tipe'       => 'required|string',
            'bobot'      => 'nullable|integer|min:1',
        ];

        if (in_array($tipe, ['pilihan_ganda', 'jenis_lainnya'])) {
            $rules['pilihan']       = 'required|array|min:2';
            $rules['pilihan.*']     = 'required|string';
            $rules['jawaban_benar'] = 'required|integer|min:0';
        } elseif ($tipe === 'multi_select') {
            $rules['pilihan']         = 'required|array|min:2';
            $rules['pilihan.*']       = 'required|string';
            $rules['jawaban_benar']   = 'required|array|min:1';
            $rules['jawaban_benar.*'] = 'integer';
        } elseif ($tipe === 'isian_singkat') {
            $rules['jawaban_teks'] = 'required|string';
        } elseif ($tipe === 'menjodohkan') {
            $rules['pasangan_kiri']    = 'required|array|min:2';
            $rules['pasangan_kiri.*']  = 'required|string';
            $rules['pasangan_kanan']   = 'required|array|min:2';
            $rules['pasangan_kanan.*'] = 'required|string';
        }

        $request->validate($rules);

        $soal->update([
            'pertanyaan' => $request->input('pertanyaan'),
            'tipe'       => $tipe,
            'bobot'      => $request->input('bobot', 10),
            'pembahasan' => $request->input('pembahasan'),
            'is_active'  => $request->input('is_active', '1') !== '0',
            'topik_pelatihan_id' => $request->input('topik_pelatihan_id'),
        ]);

        // Update pilihan jawaban IN-PLACE (keep same IDs so student answers remain valid)
        $this->updateAnswersInPlace($soal, $tipe, $request);

        $materi = $soal->materis()->first();

        // Auto-resync: recalculate correctness of all existing student answers based on new kunci
        $this->resyncAfterUpdate($soal, $tipe, $materi);

        if ($materi && $materi->is_pretest) {
            return redirect()->route('admin.pretest.index')
                ->with('success', 'Soal berhasil diperbarui.');
        }
        return redirect()->route('admin.materi.edit', $materi->id)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal)
    {
        $materi = $soal->materis()->first();
        $soal->delete();

        if ($materi) {
            if ($materi->is_pretest) {
                return redirect()->route('admin.pretest.index')
                    ->with('success', 'Soal berhasil dihapus.');
            }
            return redirect()->route('admin.materi.edit', $materi->id)
                ->with('success', 'Soal berhasil dihapus.');
        }
        return redirect()->route('admin.pelatihan.index')->with('success', 'Soal berhasil dihapus.');
    }

    public function resync(Soal $soal)
    {
        $tipe = $soal->tipe;
        $materi = $soal->materis()->first();

        // Hanya untuk tipe yang ada kunci jawaban otomatis
        if (!in_array($tipe, ['pilihan_ganda', 'multi_select', 'isian_singkat', 'jenis_lainnya'])) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tipe ' . $tipe . ' tidak dapat disinkronisasi otomatis (memerlukan penilaian manual).'
            ], 422);
        }

        // Ambil kunci jawaban terbaru
        $kunciBenar = \App\Models\PilihanJawaban::where('soal_id', $soal->id)
            ->where('is_correct', true)
            ->get();

        if ($kunciBenar->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kunci jawaban yang ditandai benar untuk soal ini.'
            ], 422);
        }

        // Ambil semua HasilLatihan untuk soal ini
        $hasilList = \App\Models\HasilLatihan::where('soal_id', $soal->id)->get();

        $affectedSessionIds = collect();

        foreach ($hasilList as $hasil) {
            $isCorrect = false;
            $skor = 0;

            if (in_array($tipe, ['pilihan_ganda', 'jenis_lainnya'])) {
                $kunciId = $kunciBenar->first()?->id;
                $isCorrect = $hasil->pilihan_id && $hasil->pilihan_id === $kunciId;
            } elseif ($tipe === 'multi_select') {
                $kunciIds = $kunciBenar->pluck('id')->toArray();
                $isCorrect = $hasil->pilihan_id && in_array($hasil->pilihan_id, $kunciIds);
            } elseif ($tipe === 'isian_singkat') {
                $kunciTeks = strtolower(trim($kunciBenar->first()?->teks ?? ''));
                $jawabanPeserta = strtolower(trim($hasil->jawaban_esai ?? ''));
                $isCorrect = !empty($kunciTeks) && !empty($jawabanPeserta) && $jawabanPeserta === $kunciTeks;
            }

            $skor = $isCorrect ? ($soal->bobot ?? 10) : 0;

            $hasil->update([
                'is_correct' => $isCorrect,
                'skor' => $skor,
            ]);

            $affectedSessionIds->push($hasil->sesi_evaluasi_id);
        }

        // Recalculate skor setiap sesi yang terpengaruh
        $uniqueSessionIds = $affectedSessionIds->unique();

        foreach ($uniqueSessionIds as $sesiId) {
            $sesi = \App\Models\SesiEvaluasi::with(['hasilLatihan.soal', 'materi'])->find($sesiId);
            if (!$sesi) continue;

            $hasilAll = $sesi->hasilLatihan()->with('soal')->get();
            $totalSkor = $hasilAll->sum('skor');
            $maxSkor = $hasilAll->sum(fn($h) => $h->soal ? $h->soal->bobot : 0);
            $skorAkhir = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;
            $benar = $hasilAll->where('is_correct', true)->count();

            $sesi->update([
                'skor' => $skorAkhir,
                'benar' => $benar,
            ]);

            // Recalculate gradebook jika ada pelatihan
            if ($materi && $materi->pelatihan_id) {
                \App\Services\NilaiService::recalculate($sesi->user_id, $materi->pelatihan_id);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi berhasil! ' . $hasilList->count() . ' jawaban peserta telah diperbarui dari ' . $uniqueSessionIds->count() . ' sesi.',
            'affected_sessions' => $uniqueSessionIds->count(),
            'affected_answers' => $hasilList->count(),
        ]);
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Update pilihan jawaban IN-PLACE, keeping existing IDs so that
     * HasilLatihan.pilihan_id references remain valid for student answers.
     *
     * Strategy:
     * - If count of new choices == count of existing choices: update each by position.
     * - If new choices added: create extra ones.
     * - If choices removed: delete extras (from the end).
     */
    private function updateAnswersInPlace(Soal $soal, string $tipe, Request $request): void
    {
        $hurufs = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        if (in_array($tipe, ['pilihan_ganda', 'jenis_lainnya'])) {
            $jawabanBenar = (int) $request->input('jawaban_benar', 0);
            $pilihanBaru  = $request->input('pilihan', []);
            $existing     = $soal->pilihanJawaban()->orderBy('id')->get();

            foreach ($pilihanBaru as $index => $teks) {
                $data = [
                    'huruf'      => $hurufs[$index] ?? ($index + 1),
                    'teks'       => $teks,
                    'is_correct' => $index === $jawabanBenar,
                ];
                if (isset($existing[$index])) {
                    // Update in-place (ID preserved — student answers stay valid)
                    $existing[$index]->update($data);
                } else {
                    // New extra choice added by admin
                    $soal->pilihanJawaban()->create($data);
                }
            }

            // Remove extra old choices if admin removed some options
            if ($existing->count() > count($pilihanBaru)) {
                $toDelete = $existing->slice(count($pilihanBaru));
                foreach ($toDelete as $old) {
                    $old->delete();
                }
            }

        } elseif ($tipe === 'multi_select') {
            $jawabanBenar = array_map('intval', $request->input('jawaban_benar', []));
            $pilihanBaru  = $request->input('pilihan', []);
            $existing     = $soal->pilihanJawaban()->orderBy('id')->get();

            foreach ($pilihanBaru as $index => $teks) {
                $data = [
                    'huruf'      => $hurufs[$index] ?? ($index + 1),
                    'teks'       => $teks,
                    'is_correct' => in_array($index, $jawabanBenar),
                ];
                if (isset($existing[$index])) {
                    $existing[$index]->update($data);
                } else {
                    $soal->pilihanJawaban()->create($data);
                }
            }
            if ($existing->count() > count($pilihanBaru)) {
                foreach ($existing->slice(count($pilihanBaru)) as $old) {
                    $old->delete();
                }
            }

        } elseif ($tipe === 'isian_singkat') {
            $existing = $soal->pilihanJawaban()->orderBy('id')->first();
            $data = ['huruf' => 'A', 'teks' => $request->input('jawaban_teks'), 'is_correct' => true];
            if ($existing) {
                $existing->update($data);
            } else {
                $soal->pilihanJawaban()->create($data);
            }

        } elseif ($tipe === 'menjodohkan') {
            // Menjodohkan: delete+recreate is acceptable (no pilihan_id stored for matching)
            $soal->pilihanJawaban()->delete();
            $kiri  = $request->input('pasangan_kiri', []);
            $kanan = $request->input('pasangan_kanan', []);
            foreach ($kiri as $index => $teksKiri) {
                $soal->pilihanJawaban()->create([
                    'huruf'      => 'L' . ($index + 1),
                    'teks'       => $teksKiri . '|||' . ($kanan[$index] ?? ''),
                    'is_correct' => true,
                ]);
            }
        }
        // essay: no choices needed
    }

    /**
     * After updating a soal, recalculate all existing student HasilLatihan records
     * for this soal and update session scores + gradebook.
     * Since IDs are now preserved by updateAnswersInPlace(), the comparison is accurate.
     */
    private function resyncAfterUpdate(Soal $soal, string $tipe, ?object $materi): void
    {
        if (!in_array($tipe, ['pilihan_ganda', 'multi_select', 'isian_singkat', 'jenis_lainnya'])) {
            return; // Essay/menjodohkan require manual grading
        }

        $kunciBenar = \App\Models\PilihanJawaban::where('soal_id', $soal->id)
            ->where('is_correct', true)
            ->get();

        if ($kunciBenar->isEmpty()) {
            return;
        }

        $hasilList = \App\Models\HasilLatihan::where('soal_id', $soal->id)->get();
        if ($hasilList->isEmpty()) {
            return;
        }

        $affectedSessionIds = collect();

        foreach ($hasilList as $hasil) {
            $isCorrect = false;

            if (in_array($tipe, ['pilihan_ganda', 'jenis_lainnya'])) {
                $kunciId   = $kunciBenar->first()?->id;
                $isCorrect = $hasil->pilihan_id && $hasil->pilihan_id === $kunciId;
            } elseif ($tipe === 'multi_select') {
                $kunciIds  = $kunciBenar->pluck('id')->toArray();
                $isCorrect = $hasil->pilihan_id && in_array($hasil->pilihan_id, $kunciIds);
            } elseif ($tipe === 'isian_singkat') {
                $kunciTeks      = strtolower(trim($kunciBenar->first()?->teks ?? ''));
                $jawabanPeserta = strtolower(trim($hasil->jawaban_esai ?? ''));
                $isCorrect      = !empty($kunciTeks) && !empty($jawabanPeserta) && $jawabanPeserta === $kunciTeks;
            }

            $skor = $isCorrect ? ($soal->bobot ?? 10) : 0;
            $hasil->update(['is_correct' => $isCorrect, 'skor' => $skor]);
            $affectedSessionIds->push($hasil->sesi_evaluasi_id);
        }

        foreach ($affectedSessionIds->unique() as $sesiId) {
            $sesi = \App\Models\SesiEvaluasi::find($sesiId);
            if (!$sesi) continue;

            $hasilAll  = $sesi->hasilLatihan()->with('soal')->get();
            $totalSkor = $hasilAll->sum('skor');
            $maxSkor   = $hasilAll->sum(fn($h) => $h->soal ? $h->soal->bobot : 0);
            $skorAkhir = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;
            $benar     = $hasilAll->where('is_correct', true)->count();

            $sesi->update(['skor' => $skorAkhir, 'benar' => $benar]);

            if ($materi && $materi->pelatihan_id) {
                \App\Services\NilaiService::recalculate($sesi->user_id, $materi->pelatihan_id);
            }
        }
    }

    private function storeAnswers(Soal $soal, string $tipe, Request $request): void
    {
        $hurufs = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        if (in_array($tipe, ['pilihan_ganda', 'jenis_lainnya'])) {
            $jawabanBenar = (int) $request->input('jawaban_benar', 0);
            foreach ($request->input('pilihan', []) as $index => $teks) {
                $soal->pilihanJawaban()->create([
                    'huruf'      => $hurufs[$index] ?? ($index + 1),
                    'teks'       => $teks,
                    'is_correct' => $index === $jawabanBenar,
                ]);
            }
        } elseif ($tipe === 'multi_select') {
            $jawabanBenar = array_map('intval', $request->input('jawaban_benar', []));
            foreach ($request->input('pilihan', []) as $index => $teks) {
                $soal->pilihanJawaban()->create([
                    'huruf'      => $hurufs[$index] ?? ($index + 1),
                    'teks'       => $teks,
                    'is_correct' => in_array($index, $jawabanBenar),
                ]);
            }
        } elseif ($tipe === 'isian_singkat') {
            $soal->pilihanJawaban()->create([
                'huruf'      => 'A',
                'teks'       => $request->input('jawaban_teks'),
                'is_correct' => true,
            ]);
        } elseif ($tipe === 'menjodohkan') {
            $kiri   = $request->input('pasangan_kiri', []);
            $kanan  = $request->input('pasangan_kanan', []);
            foreach ($kiri as $index => $teksKiri) {
                $soal->pilihanJawaban()->create([
                    'huruf'      => 'L' . ($index + 1),
                    'teks'       => $teksKiri . '|||' . ($kanan[$index] ?? ''),
                    'is_correct' => true,
                ]);
            }
        }
        // essay: no choices needed
    }
}

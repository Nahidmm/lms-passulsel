<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Materi;
use App\Models\SesiEvaluasi;
use App\Models\HasilLatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    // Removed global index method

    public function start(Request $request)
    {
        $user = Auth::user();
        $materiId = $request->input('materi_id');
        $materi = Materi::findOrFail($materiId);
        
        if ($materi->jenis !== 'quiz') {
            return back()->with('error', 'Materi bukan berupa kuis.');
        }

        // Check active session globally (a user can only take one quiz at a time)
        $activeSesi = SesiEvaluasi::where('user_id', $user->id)
            ->where('status', 'berlangsung')
            ->first();

        if ($activeSesi) {
            return redirect()->route('peserta.evaluasi.soal', ['sesi' => $activeSesi->id])
                ->with('warning', 'Anda masih memiliki kuis yang sedang berlangsung.');
        }
        
        // Check max attempts
        if ($materi->max_attempts > 0) {
            $attempts = SesiEvaluasi::where('user_id', $user->id)
                ->where('materi_id', $materi->id)
                ->count();
            
            if ($attempts >= $materi->max_attempts) {
                return back()->with('error', 'Anda telah mencapai batas maksimal percobaan kuis ini.');
            }
        }

        $totalSoal = $materi->soals()->where('is_active', true)->count();
        if ($totalSoal === 0) {
            return back()->with('error', 'Belum ada soal tersedia untuk kuis ini.');
        }

        $sesi = SesiEvaluasi::create([
            'user_id' => $user->id,
            'materi_id' => $materi->id,
            'status' => 'berlangsung',
            'mulai_at' => now(),
            'durasi_menit' => $materi->durasi_menit,
            'total_soal' => $totalSoal,
            'current_soal_index' => 0,
            'jawaban_tersimpan' => [],
            'waktu_mulai_soal' => now(),
            'waktu_terakhir_aksi' => now(),
            'streak' => 0,
            'xp_earned' => 0,
            'is_paused' => false,
            'last_activity_at' => now(),
            'tab_blur_count' => 0,
        ]);

        return redirect()->route('peserta.evaluasi.soal', ['sesi' => $sesi->id]);
    }

    public function soal(SesiEvaluasi $sesi)
    {
        $user = Auth::user();
        if ($sesi->user_id !== $user->id || $sesi->status !== 'berlangsung') {
            return redirect()->route('peserta.pelatihan.index');
        }

        $materi = $sesi->materi;
        
        // Check if timeout
        if ($materi->durasi_menit > 0 && $sesi->sisaWaktu <= 0) {
            return $this->processSubmit($sesi, []);
        }

        $soalsQuery = $materi->soals()->with('pilihanJawaban')->where('is_active', true);
        
        if ($materi->acak_soal) {
            $soalsQuery->inRandomOrder();
        }
        
        $soals = $soalsQuery->get();
        $sesi->update([
            'current_soal_index' => max(0, (int) $sesi->current_soal_index),
            'last_activity_at' => now(),
            'waktu_terakhir_aksi' => now(),
        ]);

        return view('peserta.evaluasi.soal', compact('sesi', 'soals', 'materi'));
    }

    public function submit(Request $request, SesiEvaluasi $sesi)
    {
        $user = Auth::user();
        if ($sesi->user_id !== $user->id || $sesi->status !== 'berlangsung') {
            return redirect()->route('peserta.pelatihan.index');
        }

        $jawaban = $request->input('jawaban', []);
        $sesi->update([
            'jawaban_tersimpan' => $jawaban,
            'last_activity_at' => now(),
            'waktu_terakhir_aksi' => now(),
        ]);

        return $this->processSubmit($sesi, $jawaban);
    }

    private function processSubmit(SesiEvaluasi $sesi, array $jawaban)
    {
        $benar = 0;
        $totalSkor = 0;
        $maxSkor = 0;
        $streakSaatIni = 0;
        $streakTertinggi = 0;

        $materi = $sesi->materi;
        $soals = $materi->soals()->where('is_active', true)->get();

        foreach ($soals as $soal) {
            $maxSkor += $soal->bobot;
            $jawabanUser = $jawaban[$soal->id] ?? null;
            $isCorrect = false;

            if ($jawabanUser) {
                if ($soal->tipe === 'pilihan_ganda') {
                    $pilihan = $soal->pilihanJawaban()->find($jawabanUser);
                    if ($pilihan && $pilihan->is_correct) {
                        $isCorrect = true;
                    }
                } elseif ($soal->tipe === 'multi_select' && is_array($jawabanUser)) {
                    $correctIds = $soal->pilihanJawaban()->where('is_correct', true)->pluck('id')->map(fn($id) => (string)$id)->toArray();
                    $userAnsw = array_map('strval', $jawabanUser);
                    sort($correctIds);
                    sort($userAnsw);
                    if ($correctIds === $userAnsw) {
                        $isCorrect = true;
                    }
                } elseif ($soal->tipe === 'isian_singkat') {
                    $kunci = $soal->pilihanJawaban()->first();
                    if ($kunci) {
                        $cleanUser = strtolower(trim(preg_replace('/\s+/', ' ', $jawabanUser)));
                        $cleanKunci = strtolower(trim(preg_replace('/\s+/', ' ', $kunci->teks)));
                        if ($cleanUser === $cleanKunci) {
                            $isCorrect = true;
                        }
                    }
                } elseif ($soal->tipe === 'menjodohkan' && is_array($jawabanUser)) {
                    $allMatch = true;
                    $pilihans = $soal->pilihanJawaban;
                    if ($pilihans->count() > 0) {
                        foreach ($pilihans as $pilihan) {
                            $parts = explode('|||', $pilihan->teks);
                            $kunciKanan = isset($parts[1]) ? strtolower(trim($parts[1])) : '';
                            $userKanan = isset($jawabanUser[$pilihan->id]) ? strtolower(trim($jawabanUser[$pilihan->id])) : '';
                            if ($kunciKanan !== $userKanan) {
                                $allMatch = false;
                                break;
                            }
                        }
                        if ($allMatch) {
                            $isCorrect = true;
                        }
                    }
                }
            }

            if ($isCorrect) {
                $benar++;
                $totalSkor += $soal->bobot;
                $streakSaatIni++;
                $streakTertinggi = max($streakTertinggi, $streakSaatIni);
            } else {
                $streakSaatIni = 0;
            }

            $pilihanId = null;
            $jawabanTeks = null;

            if ($soal->tipe === 'pilihan_ganda' && $jawabanUser) {
                $pilihanId = $jawabanUser;
            } elseif (in_array($soal->tipe, ['isian_singkat', 'essay'])) {
                $jawabanTeks = $jawabanUser;
            } elseif (is_array($jawabanUser)) {
                $jawabanTeks = json_encode($jawabanUser);
            }

            HasilLatihan::create([
                'sesi_evaluasi_id' => $sesi->id,
                'user_id' => $sesi->user_id,
                'soal_id' => $soal->id,
                'pilihan_id' => $pilihanId,
                'jawaban_esai' => $jawabanTeks,
                'is_correct' => $isCorrect,
                'skor' => $isCorrect ? $soal->bobot : 0,
            ]);
        }

        // Calculate 0-100 score scale
        $skorAkhir = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;
        
        // XP = proporsional terhadap poin konfigurasi admin di materi
        // Mode interaktif mendapat bonus 20%
        $poinMaxMateri = $materi->poin ?? 50;
        $multiplier = $materi->mode_tampilan === 'interaktif' ? 1.2 : 1.0;
        $xpEarned = (int) round(($skorAkhir / 100) * $poinMaxMateri * $multiplier);

        $sesi->update([
            'status' => 'selesai',
            'selesai_at' => now(),
            'benar' => $benar,
            'skor' => $skorAkhir,
            'streak' => $streakTertinggi,
            'xp_earned' => $xpEarned,
            'jawaban_tersimpan' => $jawaban,
            'last_activity_at' => now(),
            'waktu_terakhir_aksi' => now(),
        ]);
        
        // Update progres materi for this user
        $progresMateri = \App\Models\ProgresMateri::firstOrCreate(
            ['user_id' => $sesi->user_id, 'materi_id' => $materi->id]
        );
        
        // Mark as selesai only if passing grade is reached
        if ($skorAkhir >= ($materi->passing_grade ?? 0)) {
            $progresMateri->update([
                'status' => 'selesai',
                'tanggal_selesai' => now(),
            ]);
            
            if ($materi->pelatihan_id) {
                \App\Models\ProgresPelatihan::checkCompletion($sesi->user_id, $materi->pelatihan_id);
            }
        }

        // ==========================================
        // PRETEST LOGIC
        // ==========================================
        if ($materi->is_pretest) {
            $user = \App\Models\User::find($sesi->user_id);
            if ($user) {
                // Calculate score per topik
                $topikScores = [];
                $topikTotals = [];
                
                $hasilLatihans = HasilLatihan::where('sesi_evaluasi_id', $sesi->id)->get()->keyBy('soal_id');

                foreach ($soals as $soal) {
                    if ($soal->topik_pelatihan_id) {
                        $topikId = $soal->topik_pelatihan_id;
                        if (!isset($topikTotals[$topikId])) {
                            $topikTotals[$topikId] = 0;
                            $topikScores[$topikId] = 0;
                        }
                        
                        $topikTotals[$topikId] += $soal->bobot;
                        
                        $hasil = $hasilLatihans->get($soal->id);
                        if ($hasil && $hasil->is_correct) {
                            $topikScores[$topikId] += $soal->bobot;
                        }
                    }
                }
                
                // Save to hasil_pretest_topiks
                foreach ($topikTotals as $topikId => $totalBobot) {
                    if ($totalBobot > 0) {
                        $score = round(($topikScores[$topikId] / $totalBobot) * 100, 2);
                        \App\Models\HasilPretestTopik::updateOrCreate(
                            ['user_id' => $user->id, 'topik_pelatihan_id' => $topikId],
                            ['skor' => $score]
                        );
                    }
                }
                
                // Mark user as has taken pretest
                $user->update(['has_taken_pretest' => true]);
            }
        }

        // Recalculate composite gradebook for STRAPSUSPAS
        if ($materi->pelatihan_id) {
            \App\Services\NilaiService::recalculate($sesi->user_id, $materi->pelatihan_id);
        } elseif ($materi->is_pretest) {
            $pelatihanIds = \App\Models\Pelatihan::pluck('id');
            foreach ($pelatihanIds as $pId) {
                \App\Services\NilaiService::recalculate($sesi->user_id, $pId);
            }
        }

        return redirect()->route('peserta.evaluasi.hasil', ['sesi' => $sesi->id]);
    }

    public function hasil(SesiEvaluasi $sesi)
    {
        $user = Auth::user();
        if ($sesi->user_id !== $user->id) {
            abort(403);
        }

        $hasilLatihans = $sesi->hasilLatihan()->with(['soal.pilihanJawaban', 'pilihan'])->get();
        $materi = $sesi->materi;

        return view('peserta.evaluasi.hasil', compact('sesi', 'hasilLatihans', 'materi'));
    }
}

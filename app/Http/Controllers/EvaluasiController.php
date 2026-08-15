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
    public function index()
    {
        $user = Auth::user();
        
        $activeSesi = SesiEvaluasi::where('user_id', $user->id)
            ->where('status', 'berlangsung')
            ->first();
            
        $riwayatSesi = SesiEvaluasi::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('peserta.evaluasi.index', compact('activeSesi', 'riwayatSesi'));
    }

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
            'durasi_menit' => $materi->durasi_menit, // Could be 0 for unlimited
            'total_soal' => $totalSoal,
        ]);

        return redirect()->route('peserta.evaluasi.soal', ['sesi' => $sesi->id]);
    }

    public function soal(SesiEvaluasi $sesi)
    {
        $user = Auth::user();
        if ($sesi->user_id !== $user->id || $sesi->status !== 'berlangsung') {
            return redirect()->route('peserta.evaluasi.index');
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

        return view('peserta.evaluasi.soal', compact('sesi', 'soals', 'materi'));
    }

    public function submit(Request $request, SesiEvaluasi $sesi)
    {
        $user = Auth::user();
        if ($sesi->user_id !== $user->id || $sesi->status !== 'berlangsung') {
            return redirect()->route('peserta.evaluasi.index');
        }

        return $this->processSubmit($sesi, $request->input('jawaban', []));
    }

    private function processSubmit(SesiEvaluasi $sesi, array $jawaban)
    {
        $benar = 0;
        $totalSkor = 0;
        $maxSkor = 0;

        $materi = $sesi->materi;
        $soals = $materi->soals()->where('is_active', true)->get();

        foreach ($soals as $soal) {
            $maxSkor += $soal->bobot;
            $jawabanUser = $jawaban[$soal->id] ?? null;
            $isCorrect = false;

            if ($soal->tipe === 'pilihan_ganda' && $jawabanUser) {
                $pilihan = $soal->pilihanJawaban()->find($jawabanUser);
                if ($pilihan && $pilihan->is_correct) {
                    $isCorrect = true;
                    $benar++;
                    $totalSkor += $soal->bobot;
                }
            }

            HasilLatihan::create([
                'sesi_evaluasi_id' => $sesi->id,
                'user_id' => $sesi->user_id,
                'soal_id' => $soal->id,
                'pilihan_id' => $soal->tipe === 'pilihan_ganda' ? $jawabanUser : null,
                'jawaban_esai' => $soal->tipe === 'esai' ? $jawabanUser : null,
                'is_correct' => $isCorrect,
                'skor' => $isCorrect ? $soal->bobot : 0,
            ]);
        }

        // Calculate 0-100 score scale
        $skorAkhir = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;

        $sesi->update([
            'status' => 'selesai',
            'selesai_at' => now(),
            'benar' => $benar,
            'skor' => $skorAkhir,
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
            
            // Pelatihan completion is calculated dynamically, no table update needed
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

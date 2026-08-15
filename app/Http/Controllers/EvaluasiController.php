<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\SesiEvaluasi;
use App\Models\HasilLatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->jabatan_id) {
            return back()->with('error', 'Anda belum memiliki jabatan yang diatur.');
        }

        $activeSesi = $user->getActiveSesiEvaluasi();
        $riwayatSesi = $user->sesiEvaluasi()->where('status', 'selesai')->orderBy('created_at', 'desc')->get();
        $totalSoalTersedia = Soal::where('jabatan_id', $user->jabatan_id)->where('is_active', true)->count();

        return view('peserta.evaluasi.index', compact('activeSesi', 'riwayatSesi', 'totalSoalTersedia'));
    }

    public function start(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasActiveSesiEvaluasi()) {
            return redirect()->route('peserta.evaluasi.soal', ['sesi' => $user->getActiveSesiEvaluasi()->id]);
        }

        $totalSoal = Soal::where('jabatan_id', $user->jabatan_id)->where('is_active', true)->count();
        if ($totalSoal === 0) {
            return back()->with('error', 'Belum ada soal tersedia untuk jabatan Anda.');
        }

        $sesi = SesiEvaluasi::create([
            'user_id' => $user->id,
            'jabatan_id' => $user->jabatan_id,
            'status' => 'berlangsung',
            'mulai_at' => now(),
            'durasi_menit' => 30, // Default 30 minutes
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

        // Check if timeout
        if ($sesi->sisa_waktu <= 0) {
            return $this->processSubmit($sesi, []);
        }

        $soals = Soal::with('pilihanJawaban')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->get();

        return view('peserta.evaluasi.soal', compact('sesi', 'soals'));
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

        $soals = Soal::where('jabatan_id', $sesi->jabatan_id)->where('is_active', true)->get();

        foreach ($soals as $soal) {
            $maxSkor += $soal->bobot;
            $jawabanUser = $jawaban[$soal->id] ?? null;
            $isCorrect = false;

            if ($soal->tipe === 'pilgan' && $jawabanUser) {
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
                'pilihan_id' => $soal->tipe === 'pilgan' ? $jawabanUser : null,
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

        return redirect()->route('peserta.evaluasi.hasil', ['sesi' => $sesi->id]);
    }

    public function hasil(SesiEvaluasi $sesi)
    {
        $user = Auth::user();
        if ($sesi->user_id !== $user->id) {
            abort(403);
        }

        $hasilLatihans = $sesi->hasilLatihan()->with(['soal.pilihanJawaban', 'pilihan'])->get();

        return view('peserta.evaluasi.hasil', compact('sesi', 'hasilLatihans'));
    }
}

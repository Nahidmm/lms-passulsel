<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Models\SertifikatSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SertifikatController extends Controller
{
    public function download($credential_id)
    {
        $sertifikat = Sertifikat::where('credential_id', $credential_id)->with(['user', 'pelatihan.materis'])->firstOrFail();
        
        // Ensure only the owner or an admin can download it
        if (auth()->user()->id !== $sertifikat->user_id && auth()->user()->role !== 'admin' && auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        $setting = SertifikatSetting::first();
        
        // Generate QR code for verification
        $verifyUrl = route('sertifikat.verify', $credential_id);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        // Calculate Total Poin specifically for THIS pelatihan
        $materiIds = $sertifikat->pelatihan->materis->pluck('id');
        
        $poinMateri = \App\Models\ProgresMateri::where('user_id', $sertifikat->user_id)
            ->whereIn('materi_id', $materiIds)
            ->where('status', 'selesai')
            ->with('materi')
            ->get()
            ->filter(fn($p) => $p->materi && $p->materi->jenis !== 'quiz')
            ->unique('materi_id')
            ->sum(fn($p) => $p->materi->poin);

        $poinKuis = \App\Models\SesiEvaluasi::where('user_id', $sertifikat->user_id)
            ->whereIn('materi_id', $materiIds)
            ->where('status', 'selesai')
            ->groupBy('materi_id')
            ->selectRaw('MAX(xp_earned) as max_xp')
            ->get()
            ->sum('max_xp');

        $totalPoin = $poinMateri + $poinKuis;
            
        // Get all materis
        $materis = $sertifikat->pelatihan->materis;
        $materiScores = [];
        foreach ($materis as $materi) {
            if ($materi->jenis === 'quiz') {
                $maxScore = \App\Models\SesiEvaluasi::where('user_id', $sertifikat->user_id)
                    ->where('materi_id', $materi->id)->where('status', 'selesai')->max('skor');
                $materiScores[] = [
                    'judul' => $materi->judul,
                    'jenis' => 'quiz',
                    'skor' => $maxScore !== null ? number_format($maxScore, 2) : '0.00'
                ];
            } else {
                $isSelesai = \App\Models\ProgresMateri::where('user_id', $sertifikat->user_id)
                    ->where('materi_id', $materi->id)->where('status', 'selesai')->exists();
                $materiScores[] = [
                    'judul' => $materi->judul,
                    'jenis' => $materi->jenis,
                    'skor' => $isSelesai ? '100.00' : '0.00'
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.sertifikat', compact('sertifikat', 'setting', 'qrCode', 'totalPoin', 'materiScores'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('Sertifikat-'.$sertifikat->credential_id.'.pdf');
    }

    public function verify($credential_id)
    {
        $sertifikat = Sertifikat::where('credential_id', $credential_id)->with(['user', 'pelatihan'])->first();
        return view('public.verify_certificate', compact('sertifikat', 'credential_id'));
    }
}

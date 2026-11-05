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

        // Block peserta if sertifikat is deactivated for this pelatihan
        if ($sertifikat->pelatihan && !$sertifikat->pelatihan->sertif_aktif && auth()->user()->isPeserta()) {
            abort(403, 'Penerbitan sertifikat untuk pelatihan ini sedang dinonaktifkan.');
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

    /**
     * Penerbitan Sertifikat secara manual oleh Admin.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pelatihan_id' => 'required|exists:pelatihans,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $pelatihan = \App\Models\Pelatihan::findOrFail($request->pelatihan_id);

        $setting = SertifikatSetting::first();
        if ($setting && !$setting->is_active) {
            return back()->with('error', 'Penerbitan sertifikat sedang dinonaktifkan di Pengaturan Sertifikat.');
        }

        if (!$pelatihan->sertif_aktif) {
            return back()->with('error', "Penerbitan sertifikat untuk pelatihan \"{$pelatihan->judul}\" sedang dinonaktifkan.");
        }

        $credentialId = 'LMS-PAS-' . date('Y') . '-' . strtoupper(substr(md5($user->id . $pelatihan->id . time()), 0, 6));

        $sertifikat = Sertifikat::updateOrCreate(
            ['user_id' => $user->id, 'pelatihan_id' => $pelatihan->id],
            ['credential_id' => $credentialId, 'issued_at' => now()]
        );

        return back()->with('success', "Sertifikat untuk peserta {$user->nama} ({$pelatihan->judul}) berhasil diterbitkan. No. Registrasi: {$credentialId}");
    }

    /**
     * Pencabutan / pembatalan sertifikat oleh Admin.
     */
    public function destroy(Sertifikat $sertifikat)
    {
        $userName = $sertifikat->user->nama ?? 'Peserta';
        $sertifikat->delete();
        return back()->with('success', "Sertifikat untuk {$userName} berhasil dicabut / dibatalkan.");
    }
}

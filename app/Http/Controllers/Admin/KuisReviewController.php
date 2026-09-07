<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilLatihan;
use App\Models\Materi;
use App\Models\SesiEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class KuisReviewController extends Controller
{
    /**
     * Tampilkan daftar semua sesi kuis peserta untuk suatu materi kuis.
     */
    public function indexPeserta(Materi $materi)
    {
        abort_if($materi->jenis !== 'quiz', 404);

        $sesis = SesiEvaluasi::with('user.jabatan')
            ->where('materi_id', $materi->id)
            ->where('status', 'selesai')
            ->orderByDesc('selesai_at')
            ->get();

        $passingGrade = $materi->passing_grade ?? 0;

        return view('admin.kuis.peserta', compact('materi', 'sesis', 'passingGrade'));
    }

    /**
     * Tampilkan detail jawaban peserta pada suatu sesi.
     */
    public function showJawaban(Materi $materi, SesiEvaluasi $sesi)
    {
        abort_if($sesi->materi_id !== $materi->id, 404);

        $hasilLatihans = $sesi->hasilLatihan()
            ->with(['soal.pilihanJawaban', 'pilihan'])
            ->get();

        $passingGrade = $materi->passing_grade ?? 0;

        return view('admin.kuis.detail_jawaban', compact('materi', 'sesi', 'hasilLatihans', 'passingGrade'));
    }

    /**
     * Simpan penilaian manual untuk soal essay / free text.
     */
    public function nilaiManual(Request $request, Materi $materi, SesiEvaluasi $sesi)
    {
        abort_if($sesi->materi_id !== $materi->id, 404);

        $penilaian = $request->input('penilaian', []);

        foreach ($penilaian as $hasilId => $data) {
            $hasil = HasilLatihan::where('id', $hasilId)
                ->where('sesi_evaluasi_id', $sesi->id)
                ->first();

            if (!$hasil) continue;

            $soal = $hasil->soal;
            if (!in_array($soal->tipe, ['essay', 'isian_singkat', 'free_text'])) continue;

            $skorManual = max(0, min((float) ($data['skor'] ?? 0), $soal->bobot));
            $isCorrect  = $skorManual >= $soal->bobot;

            $hasil->update([
                'skor'       => $skorManual,
                'is_correct' => $isCorrect,
                'catatan_admin' => $data['catatan'] ?? null,
            ]);
        }

        // Recalculate skor akhir
        $hasilAll = $sesi->hasilLatihan()->with('soal')->get();
        $totalSkor = $hasilAll->sum('skor');
        $maxSkor   = $hasilAll->sum(fn($h) => $h->soal ? $h->soal->bobot : 0);
        $skorAkhir = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;
        $benar     = $hasilAll->where('is_correct', true)->count();

        $sesi->update([
            'skor'  => $skorAkhir,
            'benar' => $benar,
        ]);

        // Update progres materi
        if ($skorAkhir >= ($materi->passing_grade ?? 0)) {
            \App\Models\ProgresMateri::updateOrCreate(
                ['user_id' => $sesi->user_id, 'materi_id' => $materi->id],
                ['status' => 'selesai', 'tanggal_selesai' => now()]
            );
        }

        // Recalculate composite gradebook
        if ($materi->pelatihan_id) {
            \App\Services\NilaiService::recalculate($sesi->user_id, $materi->pelatihan_id);
        }

        return redirect()
            ->route('admin.kuis.jawaban', [$materi->id, $sesi->id])
            ->with('success', 'Penilaian manual berhasil disimpan. Nilai akhir diperbarui.');
    }

    /**
     * Export daftar nilai peserta kuis ke CSV.
     */
    public function exportNilai(Materi $materi)
    {
        abort_if($materi->jenis !== 'quiz', 404);

        $sesis = SesiEvaluasi::with('user.jabatan')
            ->where('materi_id', $materi->id)
            ->where('status', 'selesai')
            ->orderByDesc('selesai_at')
            ->get();

        $passingGrade = $materi->passing_grade ?? 0;
        $filename = 'nilai_kuis_' . str_replace(' ', '_', $materi->judul) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sesis, $passingGrade, $materi) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['Nama', 'NIP', 'Jabatan', 'Tanggal Kerjakan', 'Waktu (menit)', 'Benar', 'Total Soal', 'Nilai (0-100)', 'Status']);

            foreach ($sesis as $sesi) {
                $durasi = $sesi->mulai_at && $sesi->selesai_at
                    ? round($sesi->mulai_at->diffInSeconds($sesi->selesai_at) / 60, 1)
                    : '-';
                $status = $sesi->skor >= $passingGrade ? 'Lulus' : 'Tidak Lulus';

                fputcsv($file, [
                    $sesi->user->nama ?? '-',
                    $sesi->user->nip ?? '-',
                    $sesi->user->jabatan->nama_jabatan ?? '-',
                    $sesi->selesai_at ? $sesi->selesai_at->format('d/m/Y H:i') : '-',
                    $durasi,
                    $sesi->benar,
                    $sesi->total_soal,
                    $sesi->skor,
                    $status,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

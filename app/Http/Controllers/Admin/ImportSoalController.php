<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Soal;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportSoalController extends Controller
{
    /**
     * Tampilkan form import soal CSV.
     */
    public function create(Materi $materi)
    {
        abort_if($materi->jenis !== 'quiz', 404);
        return view('admin.soal.import', compact('materi'));
    }

    /**
     * Download template CSV kosong.
     */
    public function template(Materi $materi)
    {
        $filename = 'template_soal_kuis.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8

            // Header row
            fputcsv($file, [
                'tipe_soal', 'pertanyaan', 'bobot',
                'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'kunci_jawaban',
                'jawaban_benar_multi', 'jawaban_free_text',
                'pasangan_kiri', 'pasangan_kanan',
            ]);

            // Contoh baris pilihan ganda
            fputcsv($file, [
                'pilihan_ganda', 'Apa ibu kota Indonesia?', 2,
                'Surabaya', 'Jakarta', 'Bandung', 'Medan', 'B',
                '', '', '', '',
            ]);

            // Contoh multi select
            fputcsv($file, [
                'multi_select', 'Pilih kota yang ada di Sulawesi Selatan?', 3,
                'Makassar', 'Bandung', 'Parepare', 'Palopo', '',
                'A,C,D', '', '', '',
            ]);

            // Contoh free text / essay
            fputcsv($file, [
                'essay', 'Jelaskan pengertian demokrasi!', 5,
                '', '', '', '', '',
                '', 'Jawaban bebas, dinilai manual oleh admin.', '', '',
            ]);

            // Contoh fill in the blank
            fputcsv($file, [
                'isian_singkat', 'Ibu kota Indonesia adalah ___', 2,
                '', '', '', '', '',
                '', 'Jakarta', '', '',
            ]);

            // Contoh matching
            fputcsv($file, [
                'menjodohkan', 'Pasangkan negara dengan ibukotanya', 4,
                '', '', '', '', '',
                '', '',
                'Indonesia|Malaysia|Thailand', // pisah dengan |
                'Jakarta|Kuala Lumpur|Bangkok',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Proses upload & import file CSV.
     */
    public function store(Request $request, Materi $materi)
    {
        abort_if($materi->jenis !== 'quiz', 404);

        $request->validate([
            'file_csv'   => 'required|file|mimes:csv,txt|max:2048',
            'mode_import'=> 'required|in:tambah,ganti',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (count($rows) < 2) {
            return back()->with('error', 'File CSV kosong atau hanya berisi header.');
        }

        // Skip header row
        $header = array_shift($rows);

        $tipesValid = ['pilihan_ganda', 'multi_select', 'essay', 'isian_singkat', 'menjodohkan'];
        $errors     = [];
        $imported   = 0;

        DB::beginTransaction();
        try {
            if ($request->mode_import === 'ganti') {
                // Detach & delete soal lama
                $soalIds = $materi->soals()->pluck('soals.id');
                $materi->soals()->detach();
                Soal::whereIn('id', $soalIds)->delete();
            }

            foreach ($rows as $i => $row) {
                $lineNo = $i + 2; // karena header di baris 1
                $row    = array_pad($row, 12, '');

                [$tipe, $pertanyaan, $bobot,
                 $opsiA, $opsiB, $opsiC, $opsiD, $kunci,
                 $kunciMulti, $jawabanFreeText,
                 $pasanganKiri, $pasanganKanan] = $row;

                $tipe      = trim(strtolower($tipe));
                $pertanyaan = trim($pertanyaan);
                $bobot      = (int) trim($bobot) ?: 1;

                if (!in_array($tipe, $tipesValid)) {
                    $errors[] = "Baris {$lineNo}: tipe_soal '{$tipe}' tidak valid. Pilih: " . implode(', ', $tipesValid);
                    continue;
                }
                if (empty($pertanyaan)) {
                    $errors[] = "Baris {$lineNo}: kolom 'pertanyaan' tidak boleh kosong.";
                    continue;
                }

                $soal = Soal::create([
                    'pertanyaan' => $pertanyaan,
                    'tipe'       => $tipe,
                    'bobot'      => $bobot,
                    'is_active'  => true,
                ]);

                // Buat pilihan jawaban sesuai tipe
                match ($tipe) {
                    'pilihan_ganda' => $this->createPilihanGanda($soal, $opsiA, $opsiB, $opsiC, $opsiD, $kunci),
                    'multi_select'  => $this->createMultiSelect($soal, $opsiA, $opsiB, $opsiC, $opsiD, $kunciMulti),
                    'essay'         => $this->createEssay($soal, $jawabanFreeText),
                    'isian_singkat' => $this->createIsianSingkat($soal, $jawabanFreeText),
                    'menjodohkan'   => $this->createMenjodohkan($soal, $pasanganKiri, $pasanganKanan),
                };

                // Attach ke materi
                $materi->soals()->attach($soal->id);
                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }

        $msg = "{$imported} soal berhasil diimport.";
        if (!empty($errors)) {
            $msg .= ' Beberapa baris dilewati: ' . implode(' | ', $errors);
        }

        return redirect()
            ->route('admin.materi.edit', $materi->id)
            ->with('success', $msg);
    }

    // ─────────────────────────────────────────────
    // Helper: buat pilihan jawaban per tipe soal
    // ─────────────────────────────────────────────

    private function createPilihanGanda(Soal $soal, $a, $b, $c, $d, $kunci)
    {
        $kunci   = strtoupper(trim($kunci));
        $options = ['A' => $a, 'B' => $b, 'C' => $c, 'D' => $d];
        foreach ($options as $label => $teks) {
            if (empty(trim($teks))) continue;
            PilihanJawaban::create([
                'soal_id'    => $soal->id,
                'teks'       => trim($teks),
                'huruf'      => $label,
                'is_correct' => ($label === $kunci),
            ]);
        }
    }

    private function createMultiSelect(Soal $soal, $a, $b, $c, $d, $kunciMulti)
    {
        $kunciArr = array_map('strtoupper', array_map('trim', explode(',', $kunciMulti)));
        $options  = ['A' => $a, 'B' => $b, 'C' => $c, 'D' => $d];
        foreach ($options as $label => $teks) {
            if (empty(trim($teks))) continue;
            PilihanJawaban::create([
                'soal_id'    => $soal->id,
                'teks'       => trim($teks),
                'huruf'      => $label,
                'is_correct' => in_array($label, $kunciArr),
            ]);
        }
    }

    private function createEssay(Soal $soal, $jawabanContoh)
    {
        if (!empty(trim($jawabanContoh))) {
            PilihanJawaban::create([
                'soal_id'    => $soal->id,
                'teks'       => trim($jawabanContoh),
                'huruf'      => 'A',
                'is_correct' => true,
            ]);
        }
    }

    private function createIsianSingkat(Soal $soal, $kunciJawaban)
    {
        if (!empty(trim($kunciJawaban))) {
            PilihanJawaban::create([
                'soal_id'    => $soal->id,
                'teks'       => trim($kunciJawaban),
                'huruf'      => 'A',
                'is_correct' => true,
            ]);
        }
    }

    private function createMenjodohkan(Soal $soal, $kiri, $kanan)
    {
        $kiriArr  = array_map('trim', explode('|', $kiri));
        $kananArr = array_map('trim', explode('|', $kanan));
        foreach ($kiriArr as $idx => $itemKiri) {
            if (empty($itemKiri)) continue;
            $itemKanan = $kananArr[$idx] ?? '';
            PilihanJawaban::create([
                'soal_id'    => $soal->id,
                'teks'       => $itemKiri . '|||' . $itemKanan,
                'huruf'      => 'L' . ($idx + 1),
                'is_correct' => true,
            ]);
        }
    }
}

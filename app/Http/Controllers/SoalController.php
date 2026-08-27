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
            'tipe'       => 'required|in:pilihan_ganda,multi_select,essay,isian_singkat,menjodohkan',
            'bobot'      => 'nullable|integer|min:1',
        ];

        // Tipe-specific validation
        if ($tipe === 'pilihan_ganda') {
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

        if ($tipe === 'pilihan_ganda') {
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

        // Clear old choices and re-create
        $soal->pilihanJawaban()->delete();
        $this->storeAnswers($soal, $tipe, $request);

        $materi = $soal->materis()->first();
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

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    private function storeAnswers(Soal $soal, string $tipe, Request $request): void
    {
        $hurufs = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        if ($tipe === 'pilihan_ganda') {
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

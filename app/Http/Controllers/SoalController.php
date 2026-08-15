<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
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
        return view('admin.soal.create', compact('materi'));
    }

    public function store(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'pembahasan' => 'nullable|string',
            'jawaban_benar' => 'required|in:0,1,2,3',
            'pilihan' => 'required|array|size:4',
            'pilihan.*' => 'required|string',
        ]);

        $soal = new Soal([
            'materi_id' => $materi->id,
            'pertanyaan' => $validated['pertanyaan'],
            'tipe' => 'pilgan',
            'bobot' => 10,
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active' => $request->has('is_active') && $request->input('is_active') != '0',
        ]);
        $soal->save();

        $hurufs = ['A', 'B', 'C', 'D'];
        foreach ($request->input('pilihan') as $index => $teks) {
            $soal->pilihanJawaban()->create([
                'huruf' => $hurufs[$index],
                'teks' => $teks,
                'is_correct' => $index == $validated['jawaban_benar'],
            ]);
        }

        return redirect()->route('admin.modul.show', $materi->modul_id)->with('success', 'Soal berhasil ditambahkan ke Kuis.');
    }

    public function edit(Soal $soal)
    {
        return view('admin.soal.edit', compact('soal'));
    }

    public function update(Request $request, Soal $soal)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'pembahasan' => 'nullable|string',
            'jawaban_benar' => 'required|in:0,1,2,3',
            'pilihan' => 'required|array|size:4',
            'pilihan.*' => 'required|string',
        ]);

        $soal->update([
            'pertanyaan' => $validated['pertanyaan'],
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active' => $request->has('is_active') && $request->input('is_active') != '0',
        ]);

        $hurufs = ['A', 'B', 'C', 'D'];
        $existingPilihans = $soal->pilihanJawaban;
        
        foreach ($request->input('pilihan') as $index => $teks) {
            $huruf = $hurufs[$index];
            $isCorrect = $index == $validated['jawaban_benar'];
            
            $pilihan = $existingPilihans->where('huruf', $huruf)->first();
            if ($pilihan) {
                $pilihan->update([
                    'teks' => $teks,
                    'is_correct' => $isCorrect,
                ]);
            } else {
                $soal->pilihanJawaban()->create([
                    'huruf' => $huruf,
                    'teks' => $teks,
                    'is_correct' => $isCorrect,
                ]);
            }
        }

        return redirect()->route('admin.modul.show', $soal->materi->modul_id)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal)
    {
        $modulId = $soal->materi->modul_id;
        $soal->delete();
        return redirect()->route('admin.modul.show', $modulId)->with('success', 'Soal berhasil dihapus.');
    }
}

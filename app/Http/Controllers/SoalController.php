<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Soal;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::with(['soals'])->get();
        return view('admin.soal.index', compact('jabatans'));
    }

    public function create()
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('admin.soal.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatans,id',
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:pilgan,esai',
            'bobot' => 'required|integer|min:1',
            'pembahasan' => 'nullable|string',
            
            // Pilihan ganda validation
            'pilihan.*.huruf' => 'required_if:tipe,pilgan|string|max:1',
            'pilihan.*.teks' => 'required_if:tipe,pilgan|string',
            'kunci_jawaban' => 'required_if:tipe,pilgan|string|max:1',
        ]);

        $soal = new Soal([
            'jabatan_id' => $validated['jabatan_id'],
            'pertanyaan' => $validated['pertanyaan'],
            'tipe' => $validated['tipe'],
            'bobot' => $validated['bobot'],
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);
        $soal->save();

        if ($validated['tipe'] === 'pilgan') {
            foreach ($request->input('pilihan') as $p) {
                $soal->pilihanJawaban()->create([
                    'huruf' => strtoupper($p['huruf']),
                    'teks' => $p['teks'],
                    'is_correct' => strtoupper($p['huruf']) === strtoupper($validated['kunci_jawaban']),
                ]);
            }
        }

        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Soal $soal)
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('admin.soal.edit', compact('soal', 'jabatans'));
    }

    public function update(Request $request, Soal $soal)
    {
        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatans,id',
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:pilgan,esai',
            'bobot' => 'required|integer|min:1',
            'pembahasan' => 'nullable|string',
            
            'pilihan.*.id' => 'nullable|exists:pilihan_jawabans,id',
            'pilihan.*.huruf' => 'required_if:tipe,pilgan|string|max:1',
            'pilihan.*.teks' => 'required_if:tipe,pilgan|string',
            'kunci_jawaban' => 'required_if:tipe,pilgan|string|max:1',
        ]);

        $soal->update([
            'jabatan_id' => $validated['jabatan_id'],
            'pertanyaan' => $validated['pertanyaan'],
            'tipe' => $validated['tipe'],
            'bobot' => $validated['bobot'],
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if ($validated['tipe'] === 'pilgan') {
            // Delete old options not in the current request
            $requestIds = collect($request->input('pilihan'))->pluck('id')->filter()->toArray();
            $soal->pilihanJawaban()->whereNotIn('id', $requestIds)->delete();

            foreach ($request->input('pilihan') as $p) {
                $isCorrect = strtoupper($p['huruf']) === strtoupper($validated['kunci_jawaban']);
                if (isset($p['id']) && $p['id']) {
                    $soal->pilihanJawaban()->where('id', $p['id'])->update([
                        'huruf' => strtoupper($p['huruf']),
                        'teks' => $p['teks'],
                        'is_correct' => $isCorrect,
                    ]);
                } else {
                    $soal->pilihanJawaban()->create([
                        'huruf' => strtoupper($p['huruf']),
                        'teks' => $p['teks'],
                        'is_correct' => $isCorrect,
                    ]);
                }
            }
        } else {
            // If changed to essay, delete all options
            $soal->pilihanJawaban()->delete();
        }

        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal)
    {
        $soal->delete();
        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil dihapus.');
    }
}

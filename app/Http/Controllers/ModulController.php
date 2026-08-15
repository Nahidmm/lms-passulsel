<?php

namespace App\Http\Controllers;

use App\Models\Modul;
use Illuminate\Http\Request;

class ModulController extends Controller
{
    public function index()
    {
        $moduls = Modul::orderBy('urutan')->get();
        return view('admin.modul.index', compact('moduls'));
    }

    public function create()
    {
        return view('admin.modul.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Modul::create($validated);

        return redirect()->route('admin.modul.index')->with('success', 'Modul berhasil ditambahkan.');
    }

    public function show(Modul $modul)
    {
        $materis = $modul->materis()->orderBy('urutan')->get();
        return view('admin.modul.show', compact('modul', 'materis'));
    }

    public function edit(Modul $modul)
    {
        return view('admin.modul.edit', compact('modul'));
    }

    public function update(Request $request, Modul $modul)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $modul->update($validated);

        return redirect()->route('admin.modul.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(Modul $modul)
    {
        $modul->delete();
        return redirect()->route('admin.modul.index')->with('success', 'Modul berhasil dihapus.');
    }

    // =====================================
    // ROLE: PESERTA
    // =====================================
    public function indexPeserta()
    {
        $user = auth()->user();
        $moduls = Modul::where('is_active', true)->orderBy('urutan')->get();

        $modulStatus = [];
        $isLocked = false;

        foreach ($moduls as $index => $modul) {
            $progres = \App\Models\ProgresModul::firstOrCreate(
                ['user_id' => $user->id, 'modul_id' => $modul->id],
                ['status' => 'belum', 'persen' => 0]
            );

            // First module is always unlocked. Subsequent modules locked if previous is not 'selesai'
            if ($index > 0 && $modulStatus[$moduls[$index-1]->id]['status'] !== 'selesai') {
                $isLocked = true;
            } else {
                $isLocked = false;
            }

            $modulStatus[$modul->id] = [
                'modul' => $modul,
                'progres' => $progres,
                'is_locked' => $isLocked,
                'status' => $progres->status
            ];
        }

        return view('peserta.pembelajaran.index', compact('moduls', 'modulStatus'));
    }

    public function showPeserta(Modul $modul)
    {
        $user = auth()->user();
        
        $progres = \App\Models\ProgresModul::firstOrCreate(
            ['user_id' => $user->id, 'modul_id' => $modul->id],
            ['status' => 'belum', 'persen' => 0]
        );

        if ($progres->status === 'belum') {
            $progres->update(['status' => 'sedang', 'persen' => 10]);
        }

        $materis = $modul->materis()->where('is_active', true)->orderBy('urutan')->get();

        return view('peserta.pembelajaran.show_modul', compact('modul', 'progres', 'materis'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use Illuminate\Http\Request;

class PelatihanController extends Controller
{
    // ==========================================
    // ADMIN ROUTES
    // ==========================================

    public function index()
    {
        $pelatihans = Pelatihan::latest()->paginate(10);
        return view('admin.pelatihan.index', compact('pelatihans'));
    }

    public function create()
    {
        return view('admin.pelatihan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $pelatihan = Pelatihan::create($validated);
        return redirect()->route('admin.pelatihan.index')->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    public function show(Pelatihan $pelatihan)
    {
        $pelatihan->load(['materis' => function($q) {
            $q->orderBy('urutan')->with('soals');
        }]);
        return view('admin.pelatihan.builder', compact('pelatihan'));
    }

    public function edit(Pelatihan $pelatihan)
    {
        return view('admin.pelatihan.edit', compact('pelatihan'));
    }

    public function update(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $pelatihan->update($validated);
        return redirect()->route('admin.pelatihan.index')->with('success', 'Pelatihan berhasil diperbarui.');
    }

    public function destroy(Pelatihan $pelatihan)
    {
        $pelatihan->delete();
        return redirect()->route('admin.pelatihan.index')->with('success', 'Pelatihan berhasil dihapus.');
    }

    public function reorder(Request $request, Pelatihan $pelatihan)
    {
        return response()->json(['success' => true]);
    }

    // ==========================================
    // PESERTA ROUTES
    // ==========================================

    public function indexPeserta()
    {
        $pelatihans = Pelatihan::where('is_active', true)->latest()->paginate(10);
        return view('peserta.pelatihan.index', compact('pelatihans'));
    }

    public function showPeserta(Pelatihan $pelatihan)
    {
        if (!$pelatihan->is_active) {
            abort(403);
        }
        
        $user = auth()->user();
        $materis = $pelatihan->materis()->where('is_active', true)->orderBy('urutan')->get();

        // Enforce sequential learning: Materi is locked if previous materi is not completed
        $materiStatus = [];
        $previousCompleted = true; // The first materi is always unlocked by default
        
        $completedCount = 0;
        
        foreach ($materis as $materi) {
            $mProgres = \App\Models\ProgresMateri::firstOrCreate(
                ['user_id' => $user->id, 'materi_id' => $materi->id],
                ['status' => 'belum']
            );
            
            $isLocked = !$previousCompleted;
            
            // Still check explicit manual prerequisite if any
            if ($materi->prasyarat_materi_id) {
                $prasyaratProgres = \App\Models\ProgresMateri::where('user_id', $user->id)
                    ->where('materi_id', $materi->prasyarat_materi_id)
                    ->first();
                if (!$prasyaratProgres || $prasyaratProgres->status !== 'selesai') {
                    $isLocked = true;
                }
            }

            $materiStatus[$materi->id] = [
                'materi' => $materi,
                'progres' => $mProgres,
                'status' => $mProgres->status,
                'is_locked' => $isLocked
            ];
            
            if ($mProgres->status === 'selesai') {
                $completedCount++;
            } else {
                $previousCompleted = false;
            }
        }
        
        $totalMateris = $materis->count();
        $persenProgress = $totalMateris > 0 ? floor(($completedCount / $totalMateris) * 100) : 0;

        return view('peserta.pelatihan.show', compact('pelatihan', 'materis', 'materiStatus', 'persenProgress'));
    }
}

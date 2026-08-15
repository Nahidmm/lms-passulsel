<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Materi;
use App\Models\ProgresModul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    // =====================================
    // ROLE: ADMIN (CRUD)
    // =====================================
    public function create(Modul $modul)
    {
        return view('admin.materi.create', compact('modul'));
    }

    public function store(Request $request, Modul $modul)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:pdf,ppt,pptx,link,video_embed,quiz',
            'urutan' => 'required|integer|min:1',
            'durasi_baca' => 'required|integer|min:1',
            'url_link' => 'nullable|string|max:500',
            'file_upload' => 'nullable|file|mimes:pdf,ppt,pptx|max:10240',
        ]);

        $materi = new Materi($validated);
        $materi->modul_id = $modul->id;
        $materi->is_active = $request->has('is_active');

        if ($request->hasFile('file_upload') && in_array($validated['jenis'], ['pdf', 'ppt', 'pptx'])) {
            $path = $request->file('file_upload')->store('materis', 'public');
            $materi->file_path = $path;
        }

        $materi->save();

        return redirect()->route('admin.modul.show', $modul->id)->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $materi)
    {
        return view('admin.materi.edit', compact('materi'));
    }

    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:pdf,ppt,pptx,link,video_embed,quiz',
            'urutan' => 'required|integer|min:1',
            'durasi_baca' => 'required|integer|min:1',
            'url_link' => 'nullable|string|max:500',
            'file_upload' => 'nullable|file|mimes:pdf,ppt,pptx|max:10240',
        ]);

        $materi->fill($validated);
        $materi->is_active = $request->has('is_active');

        if ($request->hasFile('file_upload') && in_array($validated['jenis'], ['pdf', 'ppt', 'pptx'])) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $path = $request->file('file_upload')->store('materis', 'public');
            $materi->file_path = $path;
        }

        $materi->save();

        return redirect()->route('admin.modul.show', $materi->modul_id)->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Materi $materi)
    {
        $modulId = $materi->modul_id;
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }
        $materi->delete();

        return redirect()->route('admin.modul.show', $modulId)->with('success', 'Materi berhasil dihapus.');
    }

    // =====================================
    // ROLE: PESERTA
    // =====================================
    public function indexPeserta()
    {
        $user = Auth::user();
        if (!$user->jabatan_id) {
            return back()->with('error', 'Anda belum memiliki jabatan yang diatur. Hubungi admin.');
        }

        $materis = Materi::where('jabatan_id', $user->jabatan_id)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // Calculate unlock status (sequential logic)
        $modulStatus = [];
        $isLocked = false;

        foreach ($materis as $index => $materi) {
            $progres = ProgresModul::firstOrCreate(
                ['user_id' => $user->id, 'materi_id' => $materi->id]
            );

            // First module is always unlocked. Subsequent modules locked if previous is not 'selesai'
            if ($index > 0 && $modulStatus[$materis[$index-1]->id]['status'] !== 'selesai') {
                $isLocked = true;
            } else {
                $isLocked = false;
            }

            $modulStatus[$materi->id] = [
                'materi' => $materi,
                'progres' => $progres,
                'is_locked' => $isLocked,
                'status' => $progres->status
            ];
        }

        return view('peserta.pembelajaran.index', compact('materis', 'modulStatus'));
    }

    public function showPeserta(Materi $materi)
    {
        $user = Auth::user();

        $progres = \App\Models\ProgresMateri::firstOrCreate(
            ['user_id' => $user->id, 'materi_id' => $materi->id],
            ['status' => 'belum']
        );

        if ($progres->status === 'belum') {
            $progres->update(['status' => 'sedang']);
        }

        return view('peserta.pembelajaran.show_materi', compact('materi', 'progres'));
    }

    public function updateProgress(Request $request, Materi $materi)
    {
        $user = Auth::user();
        $progres = \App\Models\ProgresMateri::where('user_id', $user->id)->where('materi_id', $materi->id)->firstOrFail();
        
        $progres->update([
            'status' => 'selesai',
            'tanggal_selesai' => now(),
        ]);

        // Option: Check if all materis in modul are selesai, then mark modul as selesai
        $modul = $materi->modul;
        $allMateris = $modul->materis()->where('is_active', true)->pluck('id');
        $completedMateris = \App\Models\ProgresMateri::where('user_id', $user->id)
            ->whereIn('materi_id', $allMateris)
            ->where('status', 'selesai')
            ->count();

        if ($completedMateris === count($allMateris)) {
            $modulProgres = \App\Models\ProgresModul::firstOrCreate(
                ['user_id' => $user->id, 'modul_id' => $modul->id]
            );
            $modulProgres->update([
                'status' => 'selesai',
                'persen' => 100,
                'tanggal_selesai' => now()
            ]);
        } else {
            $modulProgres = \App\Models\ProgresModul::firstOrCreate(
                ['user_id' => $user->id, 'modul_id' => $modul->id]
            );
            $persen = count($allMateris) > 0 ? floor(($completedMateris / count($allMateris)) * 100) : 0;
            $modulProgres->update([
                'status' => 'sedang',
                'persen' => $persen
            ]);
        }

        return redirect()->route('peserta.pembelajaran.show', $materi->modul_id)->with('success', 'Materi berhasil diselesaikan!');
    }
}

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
    public function index()
    {
        $jabatans = Jabatan::with(['materis' => function($q) {
            $q->orderBy('urutan');
        }])->get();
        
        return view('admin.materi.index', compact('jabatans'));
    }

    public function create()
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('admin.materi.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jabatan_id' => 'required|exists:jabatans,id',
            'urutan' => 'required|integer|min:1',
            'jenis' => 'required|in:pdf,ppt,pptx,link,video_embed',
            'durasi_baca' => 'required|integer|min:1',
            'file_upload' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480', // max 20MB
            'url_link' => 'nullable|url|max:500',
        ]);

        $materi = new Materi($validated);

        if ($request->hasFile('file_upload')) {
            $path = $request->file('file_upload')->store('materi', 'public');
            $materi->file_path = $path;
        }

        $materi->is_active = $request->has('is_active');
        $materi->save();

        return redirect()->route('admin.materi.index')->with('success', 'Modul berhasil ditambahkan.');
    }

    public function edit(Materi $materi)
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('admin.materi.edit', compact('materi', 'jabatans'));
    }

    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jabatan_id' => 'required|exists:jabatans,id',
            'urutan' => 'required|integer|min:1',
            'jenis' => 'required|in:pdf,ppt,pptx,link,video_embed',
            'durasi_baca' => 'required|integer|min:1',
            'file_upload' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480',
            'url_link' => 'nullable|url|max:500',
        ]);

        $materi->fill($validated);

        if ($request->hasFile('file_upload')) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $path = $request->file('file_upload')->store('materi', 'public');
            $materi->file_path = $path;
        }

        $materi->is_active = $request->has('is_active');
        $materi->save();

        return redirect()->route('admin.materi.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(Materi $materi)
    {
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }
        $materi->delete();
        return redirect()->route('admin.materi.index')->with('success', 'Modul berhasil dihapus.');
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
        if ($materi->jabatan_id !== $user->jabatan_id) {
            abort(403);
        }

        $progres = ProgresModul::firstOrCreate(
            ['user_id' => $user->id, 'materi_id' => $materi->id]
        );

        if ($progres->status === 'belum') {
            $progres->update(['status' => 'sedang', 'persen' => 10]);
        }

        return view('peserta.pembelajaran.show', compact('materi', 'progres'));
    }

    public function updateProgress(Request $request, Materi $materi)
    {
        $user = Auth::user();
        $progres = ProgresModul::where('user_id', $user->id)->where('materi_id', $materi->id)->firstOrFail();
        
        $progres->update([
            'status' => 'selesai',
            'persen' => 100,
            'tanggal_selesai' => now(),
        ]);

        return redirect()->route('peserta.pembelajaran.index')->with('success', 'Modul berhasil diselesaikan!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Materi;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    // =====================================
    // ROLE: ADMIN (CRUD)
    // =====================================
    public function create(Request $request, Pelatihan $pelatihan)
    {
        $jenis = $request->query('jenis');
        if ($jenis === 'quiz') {
            return view('admin.materi.create_quiz', compact('pelatihan'));
        }
        return view('admin.materi.create', compact('pelatihan'));
    }

    public function store(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:pdf,ppt,pptx,link,video_embed,quiz',
            'urutan' => 'required|integer|min:1',
            'durasi_baca' => 'required|integer|min:0',
            'url_link' => 'nullable|string|max:500',
            'file_upload' => 'nullable|file|mimes:pdf,ppt,pptx|max:10240',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'durasi_menit' => 'nullable|integer|min:0',
            'max_attempts' => 'nullable|integer|min:0',
            'acak_soal' => 'boolean',
            'acak_jawaban' => 'boolean',
            'tampilkan_feedback' => 'boolean',
            'strict_anti_cheat' => 'boolean',
            'prasyarat_materi_id' => 'nullable|exists:materis,id',
        ]);

        $materi = new Materi($validated);
        $materi->pelatihan_id = $pelatihan->id;
        $materi->is_active = $request->has('is_active');
        $materi->acak_soal = $request->has('acak_soal');
        $materi->acak_jawaban = $request->has('acak_jawaban');
        $materi->tampilkan_feedback = $request->has('tampilkan_feedback');
        $materi->strict_anti_cheat = $request->has('strict_anti_cheat');

        if ($request->hasFile('file_upload') && in_array($validated['jenis'], ['pdf', 'ppt', 'pptx'])) {
            $path = $request->file('file_upload')->store('materis', 'public');
            $materi->file_path = $path;
        }

        $materi->save();

        if ($materi->jenis === 'quiz') {
            return redirect()->route('admin.materi.edit', $materi->id)->with('success', 'Kuis berhasil dibuat! Sekarang tambahkan soal-soal kuis.');
        }
        return redirect()->route('admin.pelatihan.show', $pelatihan->id)->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $materi)
    {
        if ($materi->jenis === 'quiz') {
            $materi->load(['soals' => fn($q) => $q->orderBy('created_at')->with('pilihanJawaban')]);
        }
        return view('admin.materi.edit', compact('materi'));
    }

    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:pdf,ppt,pptx,link,video_embed,quiz',
            'urutan' => 'required|integer|min:1',
            'durasi_baca' => 'required|integer|min:0',
            'url_link' => 'nullable|string|max:500',
            'file_upload' => 'nullable|file|mimes:pdf,ppt,pptx|max:10240',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'durasi_menit' => 'nullable|integer|min:0',
            'max_attempts' => 'nullable|integer|min:0',
            'acak_soal' => 'boolean',
            'acak_jawaban' => 'boolean',
            'tampilkan_feedback' => 'boolean',
            'strict_anti_cheat' => 'boolean',
            'prasyarat_materi_id' => 'nullable|exists:materis,id',
        ]);

        $materi->fill($validated);
        $materi->is_active = $request->has('is_active');
        $materi->acak_soal = $request->has('acak_soal');
        $materi->acak_jawaban = $request->has('acak_jawaban');
        $materi->tampilkan_feedback = $request->has('tampilkan_feedback');
        $materi->strict_anti_cheat = $request->has('strict_anti_cheat');

        if ($request->hasFile('file_upload') && in_array($validated['jenis'], ['pdf', 'ppt', 'pptx'])) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $path = $request->file('file_upload')->store('materis', 'public');
            $materi->file_path = $path;
        }

        $materi->save();

        if ($materi->jenis === 'quiz') {
            return redirect()->route('admin.materi.edit', $materi->id)->with('success', 'Konfigurasi kuis berhasil disimpan.');
        }
        return redirect()->route('admin.pelatihan.show', $materi->pelatihan_id)->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Materi $materi)
    {
        $pelatihanId = $materi->pelatihan_id;
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }
        $materi->delete();

        return redirect()->route('admin.pelatihan.show', $pelatihanId)->with('success', 'Materi berhasil dihapus.');
    }

    // =====================================
    // ROLE: PESERTA
    // =====================================

    public function showPeserta(Materi $materi)
    {
        $user = Auth::user();

        // Enforce Sequential Learning Security Check
        $pelatihan = $materi->pelatihan;
        $allMateris = $pelatihan->materis()->where('is_active', true)->orderBy('urutan')->get();
        
        $currentIndex = $allMateris->search(fn($m) => $m->id === $materi->id);
        if ($currentIndex > 0) {
            $previousMateri = $allMateris[$currentIndex - 1];
            $prevProgres = \App\Models\ProgresMateri::where('user_id', $user->id)
                ->where('materi_id', $previousMateri->id)
                ->first();
                
            if (!$prevProgres || $prevProgres->status !== 'selesai') {
                return redirect()->route('peserta.pelatihan.show', $pelatihan->id)
                    ->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
            }
        }
        
        // Also check explicit manual prerequisite if any
        if ($materi->prasyarat_materi_id) {
            $prasyaratProgres = \App\Models\ProgresMateri::where('user_id', $user->id)
                ->where('materi_id', $materi->prasyarat_materi_id)
                ->first();
            if (!$prasyaratProgres || $prasyaratProgres->status !== 'selesai') {
                return redirect()->route('peserta.pelatihan.show', $pelatihan->id)
                    ->with('error', 'Materi ini terkunci oleh prasyarat khusus.');
            }
        }

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

        return redirect()->route('peserta.pelatihan.show', $materi->pelatihan_id)->with('success', 'Materi berhasil diselesaikan!');
    }
}

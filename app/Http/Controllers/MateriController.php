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
            'mode_tampilan' => 'nullable|string|in:standard,interaktif',
            'sub_mode' => 'nullable|string|in:standard,time_attack,practice',
            'timer_per_soal' => 'nullable|integer|min:0',
            'sound_enabled' => 'boolean',
            'leaderboard_enabled' => 'boolean',
            'bonus_kecepatan_enabled' => 'boolean',
            'animasi_enabled' => 'boolean',
            'badge_enabled' => 'boolean',
            'show_answer_review' => 'boolean',
            'theme_name' => 'nullable|string|max:50',
            'prasyarat_materi_id' => 'nullable|exists:materis,id',
        ]);

        $materi = new Materi($validated);
        $materi->pelatihan_id = $pelatihan->id;
        $materi->is_active = $request->has('is_active');
        $materi->acak_soal = $request->has('acak_soal');
        $materi->acak_jawaban = $request->has('acak_jawaban');
        $materi->tampilkan_feedback = $request->has('tampilkan_feedback');
        $materi->strict_anti_cheat = $request->has('strict_anti_cheat');
        $materi->mode_tampilan = $request->input('mode_tampilan', 'standard');
        
        if ($materi->mode_tampilan === 'standard') {
            $materi->sub_mode = 'standard';
            $materi->timer_per_soal = 0;
            $materi->sound_enabled = false;
            $materi->leaderboard_enabled = false;
            $materi->bonus_kecepatan_enabled = false;
            $materi->animasi_enabled = false;
            $materi->badge_enabled = false;
            $materi->show_answer_review = false;
            $materi->theme_name = 'default';
        } else {
            $materi->sub_mode = $request->input('sub_mode', 'standard');
            $materi->timer_per_soal = $request->input('timer_per_soal', 0);
            $materi->sound_enabled = $request->has('sound_enabled');
            $materi->leaderboard_enabled = $request->has('leaderboard_enabled');
            $materi->bonus_kecepatan_enabled = $request->has('bonus_kecepatan_enabled');
            $materi->animasi_enabled = $request->has('animasi_enabled');
            $materi->badge_enabled = $request->has('badge_enabled');
            $materi->show_answer_review = $request->has('show_answer_review');
            $materi->theme_name = $request->input('theme_name');
        }

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

    public function show(Materi $materi)
    {
        return $this->edit($materi);
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
            'mode_tampilan' => 'nullable|string|in:standard,interaktif',
            'sub_mode' => 'nullable|string|in:standard,time_attack,practice',
            'timer_per_soal' => 'nullable|integer|min:0',
            'sound_enabled' => 'boolean',
            'leaderboard_enabled' => 'boolean',
            'bonus_kecepatan_enabled' => 'boolean',
            'animasi_enabled' => 'boolean',
            'badge_enabled' => 'boolean',
            'show_answer_review' => 'boolean',
            'theme_name' => 'nullable|string|max:50',
            'prasyarat_materi_id' => 'nullable|exists:materis,id',
        ]);

        $materi->fill($validated);
        $materi->is_active = $request->has('is_active');
        $materi->acak_soal = $request->has('acak_soal');
        $materi->acak_jawaban = $request->has('acak_jawaban');
        $materi->tampilkan_feedback = $request->has('tampilkan_feedback');
        $materi->strict_anti_cheat = $request->has('strict_anti_cheat');
        $materi->mode_tampilan = $request->input('mode_tampilan', $materi->mode_tampilan ?? 'standard');
        
        if ($materi->mode_tampilan === 'standard') {
            $materi->sub_mode = 'standard';
            $materi->timer_per_soal = 0;
            $materi->sound_enabled = false;
            $materi->leaderboard_enabled = false;
            $materi->bonus_kecepatan_enabled = false;
            $materi->animasi_enabled = false;
            $materi->badge_enabled = false;
            $materi->show_answer_review = false;
            $materi->theme_name = 'default';
        } else {
            $materi->sub_mode = $request->input('sub_mode', $materi->sub_mode ?? 'standard');
            $materi->timer_per_soal = $request->input('timer_per_soal', $materi->timer_per_soal ?? 0);
            $materi->sound_enabled = $request->has('sound_enabled');
            $materi->leaderboard_enabled = $request->has('leaderboard_enabled');
            $materi->bonus_kecepatan_enabled = $request->has('bonus_kecepatan_enabled');
            $materi->animasi_enabled = $request->has('animasi_enabled');
            $materi->badge_enabled = $request->has('badge_enabled');
            $materi->show_answer_review = $request->has('show_answer_review');
            $materi->theme_name = $request->input('theme_name', $materi->theme_name);
        }

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

        \App\Models\ProgresPelatihan::checkCompletion($user->id, $materi->pelatihan_id);

        return redirect()->route('peserta.pelatihan.show', $materi->pelatihan_id)->with('success', 'Materi berhasil diselesaikan!');
    } 
    public function previewQuiz(Materi $materi)
    {
        $soals = $materi->soals()->get();
        $sesi = (object)[
            'id' => 'preview',
            'xp_earned' => 0,
            'sisaWaktu' => $materi->durasi_menit ? $materi->durasi_menit * 60 : 3600
        ];
        return view('peserta.evaluasi.soal', compact('materi', 'soals', 'sesi'));
    }
}



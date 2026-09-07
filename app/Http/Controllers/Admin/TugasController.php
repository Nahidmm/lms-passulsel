<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function create(Pelatihan $pelatihan)
    {
        return view('admin.tugas.create', compact('pelatihan'));
    }

    public function store(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:tugas_umum,upload_sertifikat',
            'file_lampiran' => 'nullable|file|mimes:pdf,docx,doc,pptx,ppt,zip|max:10240',
            'deadline' => 'nullable|date',
            'bobot_nilai' => 'required|numeric|min:0|max:100',
            'format_file_diizinkan' => 'nullable|string',
            'max_file_size_mb' => 'nullable|integer|min:1|max:50',
            'urutan' => 'required|integer|min:1',
        ]);

        $tugas = new Tugas($validated);
        $tugas->pelatihan_id = $pelatihan->id;
        $tugas->is_active = $request->has('is_active');

        if ($request->hasFile('file_lampiran')) {
            $path = $request->file('file_lampiran')->store('tugas_lampiran', 'public');
            $tugas->file_lampiran = $path;
        }

        $tugas->save();

        return redirect()->route('admin.pelatihan.show', $pelatihan->id)
            ->with('success', 'Penugasan berhasil ditambahkan.');
    }

    public function edit(Tugas $tugas)
    {
        $pelatihan = $tugas->pelatihan;
        return view('admin.tugas.edit', compact('tugas', 'pelatihan'));
    }

    public function update(Request $request, Tugas $tugas)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:tugas_umum,upload_sertifikat',
            'file_lampiran' => 'nullable|file|mimes:pdf,docx,doc,pptx,ppt,zip|max:10240',
            'deadline' => 'nullable|date',
            'bobot_nilai' => 'required|numeric|min:0|max:100',
            'format_file_diizinkan' => 'nullable|string',
            'max_file_size_mb' => 'nullable|integer|min:1|max:50',
            'urutan' => 'required|integer|min:1',
        ]);

        $tugas->fill($validated);
        $tugas->is_active = $request->has('is_active');

        if ($request->hasFile('file_lampiran')) {
            if ($tugas->file_lampiran) {
                Storage::disk('public')->delete($tugas->file_lampiran);
            }
            $path = $request->file('file_lampiran')->store('tugas_lampiran', 'public');
            $tugas->file_lampiran = $path;
        }

        $tugas->save();

        return redirect()->route('admin.pelatihan.show', $tugas->pelatihan_id)
            ->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function destroy(Tugas $tugas)
    {
        $pelatihanId = $tugas->pelatihan_id;

        if ($tugas->file_lampiran) {
            Storage::disk('public')->delete($tugas->file_lampiran);
        }
        $tugas->delete();

        return redirect()->route('admin.pelatihan.show', $pelatihanId)
            ->with('success', 'Penugasan berhasil dihapus.');
    }
}

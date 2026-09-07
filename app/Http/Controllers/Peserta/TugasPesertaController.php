<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasPesertaController extends Controller
{
    /**
     * Tampilkan detail tugas dan formulir pengumpulan untuk peserta.
     */
    public function show(Tugas $tugas)
    {
        $user = auth()->user();
        $pelatihan = $tugas->pelatihan;

        // Cek submission sebelumnya
        $submission = TugasSubmission::where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->first();

        return view('peserta.tugas.show', compact('tugas', 'pelatihan', 'submission'));
    }

    /**
     * Submit atau kirim revisi tugas.
     */
    public function submit(Request $request, Tugas $tugas)
    {
        $user = auth()->user();

        $rules = [
            'file_tugas' => 'nullable|file|max:' . (($tugas->max_file_size_mb ?? 10) * 1024),
            'catatan_peserta' => 'nullable|string|max:2000',
        ];

        if ($tugas->tipe === 'upload_sertifikat') {
            $rules['nomor_sertifikat'] = 'nullable|string|max:255';
            $rules['tanggal_sertifikat'] = 'nullable|date';
            $rules['penyelenggara'] = 'nullable|string|max:255';
        }

        $existingSubmission = TugasSubmission::where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->first();

        // Jika belum pernah submit, file wajib diupload
        if (!$existingSubmission) {
            $rules['file_tugas'] = 'required|file|max:' . (($tugas->max_file_size_mb ?? 10) * 1024);
        }

        $validated = $request->validate($rules);

        $data = [
            'catatan_peserta' => $validated['catatan_peserta'] ?? null,
            'status' => 'submitted',
        ];

        // Hitung apakah terlambat
        if ($tugas->deadline && now()->gt($tugas->deadline)) {
            $data['is_late'] = true;
        } else {
            $data['is_late'] = false;
        }

        if ($tugas->tipe === 'upload_sertifikat') {
            $data['nomor_sertifikat'] = $validated['nomor_sertifikat'] ?? null;
            $data['tanggal_sertifikat'] = $validated['tanggal_sertifikat'] ?? null;
            $data['penyelenggara'] = $validated['penyelenggara'] ?? null;
        }

        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file_tugas');
            
            // Hapus file lama jika ada
            if ($existingSubmission && $existingSubmission->file_path) {
                Storage::disk('public')->delete($existingSubmission->file_path);
            }

            $path = $file->store('tugas_submissions', 'public');
            $data['file_path'] = $path;
            $data['file_nama_asli'] = $file->getClientOriginalName();
        }

        $submission = TugasSubmission::updateOrCreate(
            ['tugas_id' => $tugas->id, 'user_id' => $user->id],
            $data
        );

        // Recalculate rekap nilai
        NilaiService::recalculate($user->id, $tugas->pelatihan_id);

        return redirect()->route('peserta.tugas.show', $tugas->id)
            ->with('success', 'Tugas berhasil dikumpulkan dan menunggu penilaian oleh instruktur.');
    }
}

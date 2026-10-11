<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Services\NilaiService;
use Illuminate\Http\Request;

class TugasGradingController extends Controller
{
    /**
     * Daftar submission peserta untuk tugas tertentu.
     */
    public function index(Tugas $tugas)
    {
        $submissions = $tugas->submissions()
            ->with(['user.unitKerja'])
            ->orderByRaw("FIELD(status, 'submitted', 'need_revision', 'graded')")
            ->latest()
            ->paginate(20);

        return view('admin.tugas.grading', compact('tugas', 'submissions'));
    }

    /**
     * Simpan nilai & feedback untuk submission.
     */
    public function grade(Request $request, TugasSubmission $submission)
    {
        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback_instruktur' => 'nullable|string|max:2000',
            'status' => 'required|in:graded,need_revision',
        ]);

        $submission->update([
            'nilai' => $validated['nilai'],
            'feedback_instruktur' => $validated['feedback_instruktur'],
            'status' => $validated['status'],
            'dinilai_oleh' => auth()->id(),
            'dinilai_at' => now(),
        ]);

        // Kirim notifikasi ke peserta
        $tugas = $submission->tugas;
        if ($validated['status'] === 'graded') {
            Notification::kirim(
                $submission->user_id,
                'Tugas Telah Diverifikasi',
                "Tugas \"{$tugas->judul}\" Anda telah selesai dinilai dengan nilai {$validated['nilai']}.",
                'success',
                'check-circle',
                route('peserta.tugas.show', $tugas->id)
            );
        } else {
            Notification::kirim(
                $submission->user_id,
                'Tugas Perlu Revisi',
                "Tugas \"{$tugas->judul}\" Anda perlu diperbaiki. Baca catatan instruktur di halaman tugas.",
                'warning',
                'alert-circle',
                route('peserta.tugas.show', $tugas->id)
            );
        }

        // Recalculate composite grade
        NilaiService::recalculate($submission->user_id, $submission->tugas->pelatihan_id);

        return redirect()->route('admin.tugas.submissions', $submission->tugas_id)
            ->with('success', 'Nilai berhasil disimpan untuk ' . $submission->user->nama . '.');
    }
}

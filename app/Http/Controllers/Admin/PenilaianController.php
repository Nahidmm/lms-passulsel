<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SesiEvaluasi;
use App\Models\Materi;

class PenilaianController extends Controller
{
    /**
     * Daftar seluruh peserta yang bisa dinilai.
     */
    public function index()
    {
        $users = User::with(['jabatan'])
            ->where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->orderBy('nama')
            ->get()
            ->map(function ($user) {
                $sesiSelesai = SesiEvaluasi::where('user_id', $user->id)
                    ->where('status', 'selesai')
                    ->count();

                $user->total_kuis_dikerjakan = $sesiSelesai;
                $user->rata_nilai = $user->getRataRataSkor();
                $user->total_poin = $user->getTotalPoin();
                return $user;
            });

        return view('admin.penilaian.index', compact('users'));
    }

    /**
     * Detail nilai seorang peserta: semua kuis yang sudah dikerjakan + materi selesai.
     */
    public function show(User $user)
    {
        abort_if($user->role !== 'peserta', 404);

        // Riwayat kuis
        $sesis = SesiEvaluasi::with('materi')
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->orderByDesc('selesai_at')
            ->get()
            ->map(function ($sesi) {
                $sesi->passing_grade = $sesi->materi->passing_grade ?? 0;
                $sesi->durasi_menit = $sesi->mulai_at && $sesi->selesai_at
                    ? round($sesi->mulai_at->diffInSeconds($sesi->selesai_at) / 60, 1)
                    : null;
                return $sesi;
            });

        // Riwayat materi selesai dibaca (non-kuis)
        $progresMateri = \App\Models\ProgresMateri::with('materi')
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->whereHas('materi', fn($q) => $q->where('jenis', '!=', 'quiz'))
            ->orderByDesc('tanggal_selesai')
            ->get();

        $totalPoinMateri = $progresMateri->unique('materi_id')->sum(fn($p) => $p->materi->poin ?? 0);
        $totalPoinEvaluasi = $sesis->groupBy('materi_id')->map(fn($group) => $group->max('skor'))->sum();

        $rataRata   = $sesis->avg('skor') ?? 0;
        $totalLulus = $sesis->filter(fn($s) => $s->skor >= $s->passing_grade)->count();

        return view('admin.penilaian.show', compact('user', 'sesis', 'rataRata', 'totalLulus', 'progresMateri', 'totalPoinMateri', 'totalPoinEvaluasi'));
    }
}

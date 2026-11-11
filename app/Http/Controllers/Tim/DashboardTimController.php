<?php

namespace App\Http\Controllers\Tim;

use App\Http\Controllers\Controller;
use App\Models\PenilaianTim;
use App\Models\Pelatihan;
use App\Models\User;
use App\Services\NilaiService;
use Illuminate\Http\Request;

class DashboardTimController extends Controller
{
    public function dashboard(Request $request)
    {
        $tim = auth()->user();
        $pesertas = User::where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->orderBy('nama')
            ->get();

        $pretest = \App\Models\Materi::where('is_pretest', true)->first();

        foreach ($pesertas as $p) {
            $p->nilai_pretest = null;
            if ($pretest) {
                $p->nilai_pretest = \App\Models\SesiEvaluasi::where('user_id', $p->id)
                    ->where('materi_id', $pretest->id)
                    ->where('status', 'selesai')
                    ->max('skor');
            }

            $p->nilai_pelatihan = \App\Models\RekapNilai::where('user_id', $p->id)->avg('nilai_akhir');
        }

        $penilaian = PenilaianTim::where('tim', $tim->tim)
            ->where('pekan', 1)
            ->get()
            ->keyBy('user_id');

        return view('tim.dashboard', compact('tim', 'pesertas', 'penilaian'));
    }

    public function storePenilaian(Request $request)
    {
        $tim = auth()->user();

        $validated = $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|array',
            'catatan.*' => 'nullable|string|max:2000',
        ]);

        foreach ($validated['nilai'] as $userId => $nilai) {
            if ($nilai === null || $nilai === '') {
                continue;
            }

            PenilaianTim::updateOrCreate(
                ['tim' => $tim->tim, 'user_id' => $userId, 'pekan' => 1],
                [
                    'nilai' => $nilai,
                    'catatan' => $validated['catatan'][$userId] ?? null,
                    'dinilai_oleh' => $tim->id,
                ]
            );

            foreach (Pelatihan::pluck('id') as $pelatihanId) {
                NilaiService::recalculate((int) $userId, (int) $pelatihanId);
            }
        }

        return back()->with('success', 'Penilaian akhir Tim Pembinaan berhasil disimpan dan gradebook telah diperbarui.');
    }
}

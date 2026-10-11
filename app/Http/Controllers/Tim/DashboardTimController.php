<?php

namespace App\Http\Controllers\Tim;

use App\Http\Controllers\Controller;
use App\Models\PenilaianTim;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardTimController extends Controller
{
    public function dashboard(Request $request)
    {
        $tim = auth()->user();
        $pekan = max(1, (int) $request->get('pekan', 1));

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
            ->where('pekan', $pekan)
            ->get()
            ->keyBy('user_id');

        return view('tim.dashboard', compact('tim', 'pesertas', 'pekan', 'penilaian'));
    }

    public function storePenilaian(Request $request)
    {
        $tim = auth()->user();

        $validated = $request->validate([
            'pekan' => 'required|integer|min:1',
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
                ['tim' => $tim->tim, 'user_id' => $userId, 'pekan' => $validated['pekan']],
                [
                    'nilai' => $nilai,
                    'catatan' => $validated['catatan'][$userId] ?? null,
                    'dinilai_oleh' => $tim->id,
                ]
            );
        }

        return back()->with('success', 'Penilaian pekan berhasil disimpan.');
    }
}

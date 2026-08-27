<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Materi;
use App\Models\TopikPelatihan;
use App\Models\Pelatihan;
use App\Models\Soal;

class PretestController extends Controller
{
    public function index()
    {
        // Get or Create the Global Pretest Materi
        $pretest = Materi::where('is_pretest', true)->first();
        if (!$pretest) {
            $pretest = Materi::create([
                'judul' => 'Pretest Awal',
                'deskripsi' => 'Tes awal untuk menentukan rekomendasi pelatihan.',
                'jenis' => 'quiz',
                'is_pretest' => true,
                'is_active' => true,
                'passing_grade' => 0,
            ]);
        }

        $topiks = TopikPelatihan::with('pelatihan')->get();
        $pelatihans = Pelatihan::all();
        $soals = $pretest->soals()->with('topik')->get();

        return view('admin.pretest.index', compact('pretest', 'topiks', 'pelatihans', 'soals'));
    }

    public function updateSetting(Request $request)
    {
        $pretest = Materi::where('is_pretest', true)->firstOrFail();
        $pretest->update([
            'judul' => $request->input('judul'),
            'deskripsi' => $request->input('deskripsi'),
            'durasi_menit' => $request->input('durasi_menit', 0),
            'mode_tampilan' => $request->input('mode_tampilan', 'standard'),
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Pengaturan Pretest berhasil disimpan.');
    }

    public function storeTopik(Request $request)
    {
        $request->validate([
            'nama_topik' => 'required|string',
            'batas_nilai' => 'required|integer|min:0|max:100',
            'pelatihan_id' => 'nullable|exists:pelatihans,id',
        ]);

        TopikPelatihan::create($request->only('nama_topik', 'batas_nilai', 'pelatihan_id'));

        return back()->with('success', 'Topik Rekomendasi berhasil ditambahkan.');
    }

    public function updateTopik(Request $request, TopikPelatihan $topik)
    {
        $request->validate([
            'nama_topik' => 'required|string',
            'batas_nilai' => 'required|integer|min:0|max:100',
            'pelatihan_id' => 'nullable|exists:pelatihans,id',
        ]);

        $topik->update($request->only('nama_topik', 'batas_nilai', 'pelatihan_id'));

        return back()->with('success', 'Topik Rekomendasi berhasil diperbarui.');
    }

    public function destroyTopik(TopikPelatihan $topik)
    {
        $topik->delete();
        return back()->with('success', 'Topik berhasil dihapus.');
    }
}

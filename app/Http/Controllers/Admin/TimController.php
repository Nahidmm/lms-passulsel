<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenilaianTim;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TimController extends Controller
{
    public function index()
    {
        $tims = User::where('role', 'tim')->orderBy('tim')->get();
        $teams = PenilaianTim::TEAMS;

        return view('admin.tim.index', compact('tims', 'teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tim' => 'required|in:mental,fisik,akhlak,kompetensi',
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:30',
            'password' => 'nullable|string|min:6',
            'anggota_tim' => 'nullable|string',
        ]);

        $data = [
            'role' => 'tim',
            'tim' => $validated['tim'],
            'nama' => $validated['nama'],
            'nip' => $validated['nip'],
            'anggota_tim' => $validated['anggota_tim'] ?? null,
            'status_akun' => 'approved',
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user = User::updateOrCreate(
            ['tim' => $validated['tim']],
            $data
        );

        return back()->with('success', "Akun {$user->nama} berhasil disimpan.");
    }

    public function rekapPenilaian()
    {
        $pesertas = User::where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->orderBy('nama')
            ->get();

        $teams = PenilaianTim::TEAMS;

        $nilaiTim = [];
        foreach ($pesertas as $p) {
            $nilaiTim[$p->id] = [];
            foreach (array_keys($teams) as $timKey) {
                $nilaiTim[$p->id][$timKey] = PenilaianTim::where('user_id', $p->id)
                    ->where('tim', $timKey)
                    ->avg('nilai');
            }
        }

        return view('admin.tim.penilaian', compact('pesertas', 'teams', 'nilaiTim'));
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'tim') {
            abort(403);
        }
        $user->delete();

        return back()->with('success', 'Akun Tim berhasil dihapus.');
    }
}

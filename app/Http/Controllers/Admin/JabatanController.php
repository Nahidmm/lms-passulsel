<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::withCount('users')->orderBy('kode_eselon')->get();
        return view('admin.jabatan.index', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_eselon'       => 'required|string|max:20',
            'nama_jabatan'      => 'required|string|max:200',
            'tupoksi_deskripsi' => 'nullable|string',
        ]);

        Jabatan::create($validated);

        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate([
            'kode_eselon'       => 'required|string|max:20',
            'nama_jabatan'      => 'required|string|max:200',
            'tupoksi_deskripsi' => 'nullable|string',
        ]);

        $jabatan->update($validated);

        return back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan)
    {
        if ($jabatan->users()->count() > 0) {
            return back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh ' . $jabatan->users()->count() . ' pengguna.');
        }

        $jabatan->delete();
        return back()->with('success', 'Jabatan berhasil dihapus.');
    }

    public function toggleActive(Jabatan $jabatan)
    {
        $jabatan->update(['is_active' => !$jabatan->is_active]);
        return back()->with('success', 'Status jabatan berhasil diubah.');
    }
}

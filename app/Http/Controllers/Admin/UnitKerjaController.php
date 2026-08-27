<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $unitKerjas = \App\Models\UnitKerja::latest()->paginate(10);
        return view('admin.unit_kerja.index', compact('unitKerjas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255|unique:unit_kerjas,nama_unit',
        ]);

        \App\Models\UnitKerja::create($request->only('nama_unit'));

        return back()->with('success', 'Unit Kerja berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\UnitKerja $unitKerja)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255|unique:unit_kerjas,nama_unit,' . $unitKerja->id,
        ]);

        $unitKerja->update($request->only('nama_unit'));

        return back()->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function destroy(\App\Models\UnitKerja $unitKerja)
    {
        // Check if there are users associated with this unit kerja
        if (\App\Models\User::where('unit_kerja_id', $unitKerja->id)->exists()) {
            return back()->with('error', 'Tidak dapat menghapus Unit Kerja karena sedang digunakan oleh user.');
        }

        $unitKerja->delete();

        return back()->with('success', 'Unit Kerja berhasil dihapus.');
    }
}

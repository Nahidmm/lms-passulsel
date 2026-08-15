<?php

namespace App\Http\Controllers;

use App\Models\AcademicEvent;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index()
    {
        $events = AcademicEvent::orderBy('tgl_mulai')->get();
        $liburs = HariLibur::orderBy('tanggal')->get();
        
        return view('admin.kalender.index', compact('events', 'liburs'));
    }

    public function create()
    {
        return view('admin.kalender.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jenis' => 'required|in:libur,ujian,pembelajaran,lainnya',
            'warna' => 'required|string|max:20',
        ]);

        AcademicEvent::create($validated);

        return redirect()->route('admin.kalender.index')->with('success', 'Event berhasil ditambahkan.');
    }

    public function edit(AcademicEvent $kalender)
    {
        return view('admin.kalender.edit', compact('kalender'));
    }

    public function update(Request $request, AcademicEvent $kalender)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jenis' => 'required|in:libur,ujian,pembelajaran,lainnya',
            'warna' => 'required|string|max:20',
        ]);

        $kalender->update($validated);

        return redirect()->route('admin.kalender.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(AcademicEvent $kalender)
    {
        $kalender->delete();
        return redirect()->route('admin.kalender.index')->with('success', 'Event berhasil dihapus.');
    }
}

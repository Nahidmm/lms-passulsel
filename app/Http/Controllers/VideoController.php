<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::with(['videos' => function($q) {
            $q->orderBy('urutan');
        }])->get();
        return view('admin.video.index', compact('jabatans'));
    }

    public function create()
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('admin.video.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jabatan_id' => 'required|exists:jabatans,id',
            'urutan' => 'required|integer|min:1',
            'url' => 'required|url|max:500',
            'durasi_menit' => 'nullable|integer|min:1',
        ]);

        $video = new Video($validated);
        $video->is_active = $request->has('is_active');
        $video->save();

        return redirect()->route('admin.video.index')->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit(Video $video)
    {
        $jabatans = Jabatan::where('is_active', true)->get();
        return view('admin.video.edit', compact('video', 'jabatans'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jabatan_id' => 'required|exists:jabatans,id',
            'urutan' => 'required|integer|min:1',
            'url' => 'required|url|max:500',
            'durasi_menit' => 'nullable|integer|min:1',
        ]);

        $video->fill($validated);
        $video->is_active = $request->has('is_active');
        $video->save();

        return redirect()->route('admin.video.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('admin.video.index')->with('success', 'Video berhasil dihapus.');
    }
}

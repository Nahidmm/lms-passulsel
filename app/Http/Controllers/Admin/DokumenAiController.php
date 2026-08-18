<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenAi;
use App\Services\RAGService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenAiController extends Controller
{
    public function index()
    {
        $dokumens = DokumenAi::withCount('chunks')->latest()->get();
        return view('admin.dokumen-ai.index', compact('dokumens'));
    }

    public function store(Request $request, RAGService $ragService)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'nullable|string|max:100',
            'file_pdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $filePath = $request->file('file_pdf')->store('dokumen_ai', 'public');

        $dokumen = DokumenAi::create([
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'file_path' => $filePath,
        ]);

        // Process PDF and generate embeddings (synchronously for now based on the plan query)
        $success = $ragService->processPdf($dokumen);

        if (!$success) {
            return back()->with('error', 'Dokumen berhasil diupload tetapi gagal diproses oleh sistem AI (cek log).');
        }

        return back()->with('success', 'Dokumen berhasil ditambahkan dan siap digunakan oleh AI.');
    }

    public function destroy(DokumenAi $dokumen_ai)
    {
        // Delete file
        if (Storage::disk('public')->exists($dokumen_ai->file_path)) {
            Storage::disk('public')->delete($dokumen_ai->file_path);
        }

        // Chunks are cascade deleted by DB foreign key constraint
        $dokumen_ai->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}

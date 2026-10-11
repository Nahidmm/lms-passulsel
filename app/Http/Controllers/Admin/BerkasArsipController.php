<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use Barryvdh\DomPDF\Facade\Pdf;

class BerkasArsipController extends Controller
{
    public function index()
    {
        $pelatihans = Pelatihan::with(['tugas.submissions.user.unitKerja'])
            ->whereHas('tugas.submissions')
            ->orderBy('judul')
            ->get();

        return view('admin.berkas.index', compact('pelatihans'));
    }

    public function exportPdf()
    {
        $pelatihans = Pelatihan::with(['tugas.submissions.user.unitKerja'])
            ->whereHas('tugas.submissions')
            ->orderBy('judul')
            ->get();

        $pdf = Pdf::loadView('pdf.berkas-arsip', compact('pelatihans'))->setPaper('a4', 'portrait');

        return $pdf->download('Arsip-Berkas-Peserta-' . date('Y-m-d') . '.pdf');
    }
}

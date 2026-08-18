<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SesiEvaluasi;
use App\Models\ProgresMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatistikController extends Controller
{
    public function indexPeserta()
    {
        $user = Auth::user();
        
        $progresMateris = ProgresMateri::with('materi.pelatihan')
            ->where('user_id', $user->id)
            ->get();
            
        $riwayatEvaluasi = SesiEvaluasi::with('materi')
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->get();

        // Chart Data: Quiz scores
        $riwayatAsc = $riwayatEvaluasi->reverse()->values(); // chronologically
        $chartLabels = [];
        $chartData = [];

        foreach ($riwayatAsc as $eval) {
            $title = $eval->materi ? mb_strimwidth($eval->materi->judul, 0, 15, '...') : 'Kuis';
            $chartLabels[] = $title . ' (' . $eval->created_at->format('d/m') . ')';
            $chartData[] = $eval->skor;
        }

        return view('peserta.statistik.index', compact('progresMateris', 'riwayatEvaluasi', 'chartLabels', 'chartData'));
    }

    public function indexAdmin(Request $request)
    {
        $query = User::with(['jabatan', 'progresMateri', 'sesiEvaluasi' => function($q) {
                $q->where('status', 'selesai');
            }])
            ->where('role', 'peserta')
            ->where('status_akun', 'approved');

        $sort = $request->get('sort', 'nama');
        $direction = $request->get('direction', 'asc');

        $users = $query->get()->map(function ($user) {
            $user->materi_selesai = $user->getMateriSelesaiCount();
            $user->rata_nilai = $user->getRataRataSkor();
            return $user;
        });

        // Sorting collection
        if ($sort === 'nilai') {
            $users = $direction === 'asc' ? $users->sortBy('rata_nilai') : $users->sortByDesc('rata_nilai');
        } elseif ($sort === 'modul') {
            $users = $direction === 'asc' ? $users->sortBy('materi_selesai') : $users->sortByDesc('materi_selesai');
        } else {
            $users = $direction === 'asc' ? $users->sortBy('nama') : $users->sortByDesc('nama');
        }

        return view('admin.statistik.index', compact('users', 'sort', 'direction'));
    }

    public function exportCsv()
    {
        $users = User::with(['jabatan'])
            ->where('role', 'peserta')
            ->where('status_akun', 'approved')
            ->get()
            ->map(function ($user) {
                $user->materi_selesai = $user->getMateriSelesaiCount();
                $user->rata_nilai     = $user->getRataRataSkor();
                $user->total_poin     = $user->getTotalPoin();
                return $user;
            });

        $totalMateri = \App\Models\Materi::where('is_active', true)->count();

        $filename = 'laporan-peserta-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users, $totalMateri) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens correctly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'No', 'NIP', 'Nama', 'Jabatan', 'Golongan',
                'Materi Selesai', 'Total Materi', 'Rata-rata Nilai Kuis',
                'Total Poin', 'Level Badge', 'Status'
            ]);

            foreach ($users as $i => $user) {
                if ($user->materi_selesai === $totalMateri && $totalMateri > 0 && $user->rata_nilai >= 70) {
                    $status = 'Kompeten';
                } elseif ($user->materi_selesai > 0 || $user->rata_nilai > 0) {
                    $status = 'In Progress';
                } else {
                    $status = 'Belum Mulai';
                }

                fputcsv($handle, [
                    $i + 1,
                    $user->nip,
                    $user->nama,
                    $user->jabatan->nama_jabatan ?? '-',
                    $user->golongan ?? '-',
                    $user->materi_selesai,
                    $totalMateri,
                    $user->rata_nilai > 0 ? number_format($user->rata_nilai, 1) : '-',
                    $user->total_poin,
                    $user->badge,
                    $status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

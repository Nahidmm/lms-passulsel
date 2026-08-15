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

        return view('peserta.statistik.index', compact('progresMateris', 'riwayatEvaluasi'));
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
}

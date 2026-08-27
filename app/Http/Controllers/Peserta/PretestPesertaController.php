<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PretestPesertaController extends Controller
{
    public function take()
    {
        $user = auth()->user();
        
        if ($user->has_taken_pretest) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah menyelesaikan Pretest.');
        }

        $pretest = \App\Models\Materi::where('is_pretest', true)->where('is_active', true)->first();
        if (!$pretest) {
            return redirect()->route('dashboard')->with('error', 'Pretest belum dikonfigurasi oleh Admin.');
        }

        return view('peserta.pretest.take', compact('pretest'));
    }
}

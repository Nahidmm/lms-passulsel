<?php

namespace App\Http\Controllers;

use App\Models\SertifikatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use App\Models\Pelatihan;
class SertifikatSettingController extends Controller
{
    public function edit()
    {
        $setting = SertifikatSetting::first() ?? new SertifikatSetting();
        return view('admin.sertifikat.setting', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_penandatangan' => 'required|string|max:255',
            'jabatan_penandatangan' => 'required|string|max:255',
            'tempat_tanda_tangan' => 'required|string|max:255',
            'logo_instansi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'tipe_ttd' => 'required|in:qr,image,both',
        ]);

        $setting = SertifikatSetting::first() ?? new SertifikatSetting();
        $setting->fill($validated);

        if ($request->hasFile('logo_instansi')) {
            if ($setting->logo_instansi) {
                Storage::disk('public')->delete($setting->logo_instansi);
            }


            $setting->logo_instansi = $request->file('logo_instansi')->store('sertifikat', 'public');
        }

        if ($request->hasFile('ttd_image')) {
            if ($setting->ttd_image) {
                Storage::disk('public')->delete($setting->ttd_image);
            }
            $setting->ttd_image = $request->file('ttd_image')->store('sertifikat', 'public');
        }

        $setting->save();

        return redirect()->back()->with('success', 'Konfigurasi sertifikat berhasil disimpan.');
    }
    public function preview()
    {
        $setting = SertifikatSetting::first() ?? new SertifikatSetting();
        
        // Dummy data for preview
        $sertifikat = new \stdClass();
        $sertifikat->credential_id = 'LMS-PREVIEW-12345';
        $sertifikat->issued_at = now();
        
        $sertifikat->user = new \stdClass();
        $sertifikat->user->nama = 'JOHN DOE (CONTOH PREVIEW)';
        
        $sertifikat->pelatihan = new \stdClass();
        $sertifikat->pelatihan->judul = 'Pelatihan Contoh Untuk Preview Sertifikat';
        $sertifikat->pelatihan->materi_count = 5;

        // Dummy QR
        $verifyUrl = route('sertifikat.verify', $sertifikat->credential_id);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $totalPoin = 1500;
        $quizScores = [
            ['judul' => 'Kuis Modul 1: Pengenalan', 'skor' => 95],
            ['judul' => 'Kuis Modul 2: Implementasi', 'skor' => 88],
            ['judul' => 'Kuis Akhir Pelatihan', 'skor' => 100],
        ];

        $pdf = Pdf::loadView('pdf.sertifikat', compact('sertifikat', 'setting', 'qrCode', 'totalPoin', 'quizScores'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->stream('Sertifikat-Preview.pdf');
    }
}

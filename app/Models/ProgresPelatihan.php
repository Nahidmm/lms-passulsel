<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class ProgresPelatihan extends Model
{
    protected $fillable = [
        'user_id',
        'pelatihan_id',
        'status',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_selesai' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    /**
     * Checks if all active materis in a pelatihan are completed by the user.
     * If so, marks the pelatihan as 'selesai'.
     */
    public static function checkCompletion(int $userId, int $pelatihanId): void
    {
        $progres = self::where('user_id', $userId)
            ->where('pelatihan_id', $pelatihanId)
            ->where('status', 'aktif')
            ->first();

        if (!$progres) {
            return;
        }

        // Get total active materis for this pelatihan
        $totalMateris = Materi::where('pelatihan_id', $pelatihanId)
            ->where('is_active', true)
            ->count();

        if ($totalMateris === 0) {
            return; // No materis, don't auto-complete
        }

        // Get total completed materis for this user in this pelatihan
        $completedMateris = ProgresMateri::where('user_id', $userId)
            ->whereHas('materi', function ($q) use ($pelatihanId) {
                $q->where('pelatihan_id', $pelatihanId)->where('is_active', true);
            })
            ->where('status', 'selesai')
            ->count();

        if ($completedMateris >= $totalMateris) {
            $progres->update([
                'status' => 'selesai',
                'tanggal_selesai' => now(),
            ]);

            // Generate Certificate
            $credentialId = 'LMS-PAS-' . date('Y') . '-' . strtoupper(substr(md5($userId . $pelatihanId . time()), 0, 6));
            Sertifikat::firstOrCreate(
                ['user_id' => $userId, 'pelatihan_id' => $pelatihanId],
                ['credential_id' => $credentialId, 'issued_at' => now()]
            );

            // Notify the user that they completed the pelatihan
            $pelatihan = Pelatihan::find($pelatihanId);
            Notification::kirim(
                $userId,
                'Pelatihan Selesai! ðŸŽ‰',
                "Selamat! Anda berhasil menyelesaikan pelatihan \"{$pelatihan->judul}\". Poin Anda telah diperbarui.",
                'success',
                'award',
                route('peserta.pelatihan.show', $pelatihanId)
            );
        }
    }
}


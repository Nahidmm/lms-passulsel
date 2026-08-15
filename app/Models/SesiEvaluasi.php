<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiEvaluasi extends Model
{
    protected $fillable = [
        'user_id', 'jabatan_id', 'status', 'mulai_at',
        'selesai_at', 'durasi_menit', 'total_soal', 'benar', 'skor',
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'selesai_at' => 'datetime',
        'skor' => 'float',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function jabatan() { return $this->belongsTo(Jabatan::class); }
    public function hasilLatihan() { return $this->hasMany(HasilLatihan::class); }

    public function isBerlangsung(): bool { return $this->status === 'berlangsung'; }
    public function isSelesai(): bool { return $this->status === 'selesai'; }

    public function getSisaWaktuAttribute(): int
    {
        if (!$this->mulai_at || !$this->isBerlangsung()) return 0;
        $batas = $this->mulai_at->addMinutes($this->durasi_menit);
        return max(0, now()->diffInSeconds($batas, false) * -1);
    }
}

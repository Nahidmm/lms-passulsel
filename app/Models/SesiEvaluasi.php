<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiEvaluasi extends Model
{
    protected $fillable = [
        'user_id', 'materi_id', 'status', 'mulai_at',
        'selesai_at', 'durasi_menit', 'total_soal', 'benar', 'skor',
        'current_soal_index', 'jawaban_tersimpan', 'waktu_mulai_soal',
        'waktu_terakhir_aksi', 'streak', 'xp_earned', 'is_paused',
        'last_activity_at', 'tab_blur_count'
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'selesai_at' => 'datetime',
        'waktu_mulai_soal' => 'datetime',
        'waktu_terakhir_aksi' => 'datetime',
        'last_activity_at' => 'datetime',
        'jawaban_tersimpan' => 'array',
        'skor' => 'float',
        'is_paused' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function materi() { return $this->belongsTo(Materi::class); }
    public function hasilLatihan() { return $this->hasMany(HasilLatihan::class); }

    public function isBerlangsung(): bool { return $this->status === 'berlangsung'; }
    public function isSelesai(): bool { return $this->status === 'selesai'; }

    public function getSisaWaktuAttribute(): int
    {
        if (!$this->mulai_at || !$this->isBerlangsung()) return 0;
        $batas = $this->mulai_at->copy()->addMinutes($this->durasi_menit);
        if ($this->is_paused) {
            $ref = $this->last_activity_at ?: now();
            return max(0, $ref->diffInSeconds($batas, false));
        }
        return max(0, now()->diffInSeconds($batas, false));
    }

    public function getPauseCountAttribute(): int
    {
        $data = $this->jawaban_tersimpan;
        if (is_array($data) && isset($data['_pause_count'])) {
            return (int) $data['_pause_count'];
        }
        return 0;
    }

    public function incrementPauseCount(): int
    {
        $data = is_array($this->jawaban_tersimpan) ? $this->jawaban_tersimpan : [];
        $current = isset($data['_pause_count']) ? (int) $data['_pause_count'] : 0;
        $data['_pause_count'] = $current + 1;
        $this->jawaban_tersimpan = $data;
        $this->save();
        return $data['_pause_count'];
    }
}

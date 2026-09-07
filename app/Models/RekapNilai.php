<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapNilai extends Model
{
    protected $fillable = [
        'user_id', 'pelatihan_id',
        'nilai_pretest', 'nilai_quiz_rata', 'nilai_tugas', 'nilai_posttest',
        'nilai_akhir', 'predikat', 'status_kelulusan'
    ];

    protected $casts = [
        'nilai_pretest' => 'float',
        'nilai_quiz_rata' => 'float',
        'nilai_tugas' => 'float',
        'nilai_posttest' => 'float',
        'nilai_akhir' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function getNilaiQuizAttribute(): ?float
    {
        return $this->nilai_quiz_rata;
    }

    public function getStatusLulusAttribute(): bool
    {
        return $this->status_kelulusan === 'lulus';
    }

    public function getPredikatLabelAttribute(): string
    {
        return match($this->predikat) {
            'A' => 'Sangat Kompeten',
            'B' => 'Kompeten',
            'C' => 'Cukup Kompeten',
            'D' => 'Belum Kompeten',
            default => '-',
        };
    }

    public function getPredikatColorAttribute(): string
    {
        return match($this->predikat) {
            'A' => 'text-emerald-700 bg-emerald-100',
            'B' => 'text-blue-700 bg-blue-100',
            'C' => 'text-yellow-700 bg-yellow-100',
            'D' => 'text-red-700 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }
}

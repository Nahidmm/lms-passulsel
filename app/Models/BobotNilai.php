<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BobotNilai extends Model
{
    protected $fillable = [
        'pelatihan_id',
        'bobot_pretest', 'bobot_quiz', 'bobot_tugas', 'bobot_posttest',
        'bobot_mental', 'bobot_fisik', 'bobot_akhlak', 'bobot_kompetensi',
        'passing_grade'
    ];

    protected $casts = [
        'bobot_pretest' => 'float',
        'bobot_quiz' => 'float',
        'bobot_tugas' => 'float',
        'bobot_posttest' => 'float',
        'bobot_mental' => 'float',
        'bobot_fisik' => 'float',
        'bobot_akhlak' => 'float',
        'bobot_kompetensi' => 'float',
        'passing_grade' => 'float',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    /**
     * Get or create default bobot for a pelatihan.
     */
    public static function getOrCreateDefault(int $pelatihanId): self
    {
        return self::firstOrCreate(
            ['pelatihan_id' => $pelatihanId],
            [
                'bobot_pretest' => 0.00,
                'bobot_quiz' => 20.00,
                'bobot_tugas' => 40.00,
                'bobot_posttest' => 0.00,
                'bobot_mental' => 10.00,
                'bobot_fisik' => 10.00,
                'bobot_akhlak' => 10.00,
                'bobot_kompetensi' => 10.00,
                'passing_grade' => 65.00,
            ]
        );
    }
}

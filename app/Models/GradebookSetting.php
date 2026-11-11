<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradebookSetting extends Model
{
    protected $fillable = [
        'key',
        'bobot_quiz',
        'bobot_tugas',
        'bobot_mental',
        'bobot_fisik',
        'bobot_akhlak',
        'bobot_kompetensi',
        'passing_grade',
    ];

    protected $casts = [
        'bobot_quiz' => 'float',
        'bobot_tugas' => 'float',
        'bobot_mental' => 'float',
        'bobot_fisik' => 'float',
        'bobot_akhlak' => 'float',
        'bobot_kompetensi' => 'float',
        'passing_grade' => 'float',
    ];

    public static function global(): self
    {
        return self::firstOrCreate(
            ['key' => 'global'],
            [
                'bobot_quiz' => 20,
                'bobot_tugas' => 40,
                'bobot_mental' => 10,
                'bobot_fisik' => 10,
                'bobot_akhlak' => 10,
                'bobot_kompetensi' => 10,
                'passing_grade' => 65,
            ]
        );
    }
}

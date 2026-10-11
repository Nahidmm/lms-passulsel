<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianTim extends Model
{
    protected $table = 'penilaian_tim';

    protected $fillable = [
        'tim', 'user_id', 'pekan', 'nilai', 'catatan', 'dinilai_oleh',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public const TEAMS = [
        'mental' => 'Tim Pembinaan Mental',
        'fisik' => 'Tim Pembinaan Fisik',
        'akhlak' => 'Tim Pembinaan Akhlak',
        'kompetensi' => 'Tim Pembinaan Kompetensi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    public static function teamLabel(string $key): string
    {
        return self::TEAMS[$key] ?? $key;
    }
}

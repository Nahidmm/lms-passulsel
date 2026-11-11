<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapNilaiGlobal extends Model
{
    protected $fillable = [
        'user_id',
        'nilai_quiz',
        'nilai_tugas',
        'nilai_mental',
        'nilai_fisik',
        'nilai_akhlak',
        'nilai_kompetensi',
        'nilai_akhir',
        'predikat',
        'status_kelulusan',
        'item_selesai',
        'total_item',
    ];

    protected $casts = [
        'nilai_quiz' => 'float',
        'nilai_tugas' => 'float',
        'nilai_mental' => 'float',
        'nilai_fisik' => 'float',
        'nilai_akhlak' => 'float',
        'nilai_kompetensi' => 'float',
        'nilai_akhir' => 'float',
        'item_selesai' => 'integer',
        'total_item' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

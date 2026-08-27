<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopikPelatihan extends Model
{
    protected $fillable = ['nama_topik', 'batas_nilai', 'pelatihan_id'];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'topik_pelatihan_id');
    }

    public function hasilPretests()
    {
        return $this->hasMany(HasilPretestTopik::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $fillable = ['pelatihan_id', 'judul', 'deskripsi', 'urutan', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class)->orderBy('urutan');
    }

    public function progresModul()
    {
        return $this->hasMany(ProgresModul::class);
    }
}

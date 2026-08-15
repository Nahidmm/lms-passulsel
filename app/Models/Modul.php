<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'urutan', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function progresModul()
    {
        return $this->hasMany(ProgresModul::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'gambar_thumbnail', 'is_active'];

    public function materis()
    {
        return $this->hasMany(Materi::class)->orderBy('urutan');
    }
}

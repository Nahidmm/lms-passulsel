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

    public function topikPelatihans()
    {
        return $this->hasMany(TopikPelatihan::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class)->orderBy('urutan');
    }

    public function bobotNilai()
    {
        return $this->hasOne(BobotNilai::class);
    }

    public function rekapNilais()
    {
        return $this->hasMany(RekapNilai::class);
    }

    /**
     * Get all course items (materi + tugas) sorted by urutan.
     */
    public function getCourseItemsAttribute()
    {
        $materis = $this->materis->map(function ($m) {
            $m->item_type = 'materi';
            return $m;
        });

        $tugas = $this->tugas->map(function ($t) {
            $t->item_type = 'tugas';
            return $t;
        });

        return $materis->merge($tugas)->sortBy('urutan')->values();
    }
}

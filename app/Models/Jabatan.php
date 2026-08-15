<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = ['kode_eselon', 'nama_jabatan', 'tupoksi_deskripsi', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function users() { return $this->hasMany(User::class); }
    public function materis() { return $this->hasMany(Materi::class)->orderBy('urutan'); }
    public function videos() { return $this->hasMany(Video::class)->orderBy('urutan'); }
    public function soals() { return $this->hasMany(Soal::class); }
    public function sesiEvaluasi() { return $this->hasMany(SesiEvaluasi::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = ['pertanyaan', 'tipe', 'pembahasan', 'bobot', 'is_active', 'topik_pelatihan_id'];
    protected $casts = ['is_active' => 'boolean'];

    public function materis() { return $this->belongsToMany(Materi::class, 'materi_soal', 'soal_id', 'materi_id'); }
    public function pilihanJawaban() { return $this->hasMany(PilihanJawaban::class); }
    public function hasilLatihan() { return $this->hasMany(HasilLatihan::class); }
    public function topik() { return $this->belongsTo(TopikPelatihan::class, 'topik_pelatihan_id'); }

    public function getJawabanBenar(): ?PilihanJawaban
    {
        return $this->pilihanJawaban()->where('is_correct', true)->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = ['pertanyaan', 'tipe', 'materi_id', 'pembahasan', 'bobot', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function materi() { return $this->belongsTo(Materi::class); }
    public function pilihanJawaban() { return $this->hasMany(PilihanJawaban::class); }
    public function hasilLatihan() { return $this->hasMany(HasilLatihan::class); }

    public function getJawabanBenar(): ?PilihanJawaban
    {
        return $this->pilihanJawaban()->where('is_correct', true)->first();
    }
}

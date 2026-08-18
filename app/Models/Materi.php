<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'jenis', 'file_path', 'url_link',
        'modul_id', 'urutan', 'is_active', 'durasi_baca', 'poin',
        'prasyarat_materi_id', 'passing_grade', 'durasi_menit',
        'max_attempts', 'acak_soal', 'acak_jawaban',
        'tampilkan_feedback', 'strict_anti_cheat'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'acak_soal' => 'boolean',
        'acak_jawaban' => 'boolean',
        'tampilkan_feedback' => 'boolean',
        'strict_anti_cheat' => 'boolean',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }
    public function soals() { return $this->belongsToMany(Soal::class, 'materi_soal', 'materi_id', 'soal_id'); }
    public function prasyarat() { return $this->belongsTo(Materi::class, 'prasyarat_materi_id'); }
    public function progresMateris() { return $this->hasMany(ProgresMateri::class); }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : $this->url_link;
    }
}

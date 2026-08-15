<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'jenis', 'file_path', 'url_link',
        'modul_id', 'urutan', 'is_active', 'durasi_baca',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function modul() { return $this->belongsTo(Modul::class); }
    public function soals() { return $this->hasMany(Soal::class); }
    public function progresMateris() { return $this->hasMany(ProgresMateri::class); }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : $this->url_link;
    }
}

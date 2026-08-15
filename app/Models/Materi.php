<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'jenis', 'file_path', 'url_link',
        'jabatan_id', 'urutan', 'is_active', 'durasi_baca',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function jabatan() { return $this->belongsTo(Jabatan::class); }
    public function progresModul() { return $this->hasMany(ProgresModul::class); }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : $this->url_link;
    }
}

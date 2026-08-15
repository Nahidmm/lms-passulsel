<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'url', 'jabatan_id', 'urutan', 'durasi_menit', 'thumbnail', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jabatan() { return $this->belongsTo(Jabatan::class); }

    public function getEmbedUrlAttribute(): string
    {
        // Convert YouTube watch URL to embed URL
        $url = $this->url;
        if (str_contains($url, 'youtube.com/watch?v=')) {
            $id = explode('v=', $url)[1];
            $id = explode('&', $id)[0];
            return "https://www.youtube.com/embed/{$id}";
        }
        if (str_contains($url, 'youtu.be/')) {
            $id = explode('youtu.be/', $url)[1];
            $id = explode('?', $id)[0];
            return "https://www.youtube.com/embed/{$id}";
        }
        return $url;
    }
}

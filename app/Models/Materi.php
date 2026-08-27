<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'jenis', 'is_pretest', 'file_path', 'url_link',
        'modul_id', 'pelatihan_id', 'urutan', 'is_active', 'durasi_baca', 'poin',
        'prasyarat_materi_id', 'passing_grade', 'durasi_menit',
        'max_attempts', 'acak_soal', 'acak_jawaban',
        'tampilkan_feedback', 'strict_anti_cheat',
        'mode_tampilan', 'sub_mode', 'timer_per_soal',
        'sound_enabled', 'leaderboard_enabled', 'bonus_kecepatan_enabled',
        'animasi_enabled', 'badge_enabled', 'show_answer_review', 'theme_name'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pretest' => 'boolean',
        'acak_soal' => 'boolean',
        'acak_jawaban' => 'boolean',
        'tampilkan_feedback' => 'boolean',
        'strict_anti_cheat' => 'boolean',
        'sound_enabled' => 'boolean',
        'leaderboard_enabled' => 'boolean',
        'bonus_kecepatan_enabled' => 'boolean',
        'animasi_enabled' => 'boolean',
        'badge_enabled' => 'boolean',
        'show_answer_review' => 'boolean',
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

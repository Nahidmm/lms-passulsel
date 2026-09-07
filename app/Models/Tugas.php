<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'pelatihan_id', 'judul', 'deskripsi', 'tipe',
        'file_lampiran', 'deadline', 'bobot_nilai',
        'format_file_diizinkan', 'max_file_size_mb',
        'urutan', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deadline' => 'datetime',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function submissions()
    {
        return $this->hasMany(TugasSubmission::class);
    }

    public function getFileLampiranUrlAttribute(): ?string
    {
        return $this->file_lampiran ? asset('storage/' . $this->file_lampiran) : null;
    }

    public function isDeadlinePassed(): bool
    {
        return $this->deadline && now()->isAfter($this->deadline);
    }

    public function getAllowedExtensions(): array
    {
        return array_map('trim', explode(',', $this->format_file_diizinkan));
    }

    /**
     * Get pending (ungraded) submission count.
     */
    public function getPendingCountAttribute(): int
    {
        return $this->submissions()->where('status', 'submitted')->count();
    }
}

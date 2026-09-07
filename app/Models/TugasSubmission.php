<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasSubmission extends Model
{
    protected $fillable = [
        'tugas_id', 'user_id', 'file_path', 'file_nama_asli',
        'catatan_peserta', 'nomor_sertifikat', 'tanggal_sertifikat',
        'penyelenggara', 'nilai', 'feedback_instruktur',
        'status', 'dinilai_oleh', 'dinilai_at', 'is_late'
    ];

    protected $casts = [
        'tanggal_sertifikat' => 'date',
        'dinilai_at' => 'datetime',
        'is_late' => 'boolean',
        'nilai' => 'float',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'Menunggu Review',
            'need_revision' => 'Perlu Revisi',
            'graded' => 'Dinilai',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'text-yellow-600 bg-yellow-100',
            'need_revision' => 'text-orange-600 bg-orange-100',
            'graded' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilLatihan extends Model
{
    protected $fillable = [
        'sesi_evaluasi_id', 'user_id', 'soal_id',
        'pilihan_id', 'jawaban_esai', 'catatan_admin', 'is_correct', 'skor',
        'is_skipped', 'response_time_seconds', 'answer_order', 'feedback_shown'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'is_skipped' => 'boolean',
        'feedback_shown' => 'boolean',
        'answer_order' => 'array',
    ];

    public function sesiEvaluasi() { return $this->belongsTo(SesiEvaluasi::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function soal() { return $this->belongsTo(Soal::class); }
    public function pilihan() { return $this->belongsTo(PilihanJawaban::class, 'pilihan_id'); }
}

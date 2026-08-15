<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresMateri extends Model
{
    protected $fillable = ['user_id', 'materi_id', 'status', 'tanggal_selesai'];

    protected $casts = ['tanggal_selesai' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function materi() { return $this->belongsTo(Materi::class); }

    public function isSelesai(): bool { return $this->status === 'selesai'; }
}

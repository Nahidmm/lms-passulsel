<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    protected $fillable = ['soal_id', 'huruf', 'teks', 'is_correct'];
    protected $casts = ['is_correct' => 'boolean'];

    public function soal() { return $this->belongsTo(Soal::class); }
}

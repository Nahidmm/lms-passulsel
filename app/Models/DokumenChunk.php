<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenChunk extends Model
{
    use HasFactory;

    protected $fillable = ['dokumen_ai_id', 'chunk_text', 'embedding'];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function dokumenAi()
    {
        return $this->belongsTo(DokumenAi::class, 'dokumen_ai_id');
    }
}

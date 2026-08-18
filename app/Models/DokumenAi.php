<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenAi extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'file_path', 'tipe'];

    public function chunks()
    {
        return $this->hasMany(DokumenChunk::class);
    }
}

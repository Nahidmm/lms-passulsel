<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicEvent extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'tgl_mulai', 'tgl_selesai', 'jenis', 'warna'];
    protected $casts = ['tgl_mulai' => 'date', 'tgl_selesai' => 'date'];
}

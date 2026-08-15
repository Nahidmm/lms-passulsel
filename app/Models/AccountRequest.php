<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    protected $fillable = [
        'nip', 'nama', 'email', 'golongan', 'jabatan_id',
        'pesan', 'status', 'diproses_oleh', 'alasan_tolak', 'tanggal_proses',
    ];

    protected $casts = ['tanggal_proses' => 'datetime'];

    public function jabatan() { return $this->belongsTo(Jabatan::class); }
    public function diprosesByUser() { return $this->belongsTo(User::class, 'diproses_oleh'); }
}

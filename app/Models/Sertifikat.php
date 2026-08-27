<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $fillable = [
        'user_id',
        'pelatihan_id',
        'credential_id',
        'issued_at'
    ];
    
    protected $casts = [
        'issued_at' => 'datetime'
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function pelatihan() {
        return $this->belongsTo(Pelatihan::class);
    }
    //
}


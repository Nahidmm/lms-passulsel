<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilPretestTopik extends Model
{
    protected $fillable = ['user_id', 'topik_pelatihan_id', 'skor'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topik()
    {
        return $this->belongsTo(TopikPelatihan::class, 'topik_pelatihan_id');
    }
}

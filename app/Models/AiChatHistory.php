<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatHistory extends Model
{
    protected $fillable = ['user_id', 'role', 'content', 'session_id'];

    public function user() { return $this->belongsTo(User::class); }
}

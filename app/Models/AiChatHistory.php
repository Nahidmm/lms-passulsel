<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'content',
        'session_id',
        'references'
    ];

    protected $casts = [
        'references' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
}

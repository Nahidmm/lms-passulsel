<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['kode', 'nama', 'deskripsi', 'grup', 'urutan'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}

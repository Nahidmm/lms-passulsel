<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nama', 'deskripsi', 'is_default', 'base_role'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Check if this role is protected (cannot be deleted or have permissions edited).
     */
    public function isProtected(): bool
    {
        return $this->is_default && $this->base_role === 'superadmin';
    }
}

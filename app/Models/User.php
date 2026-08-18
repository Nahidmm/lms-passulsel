<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nip', 'nama', 'email', 'golongan', 'jabatan_id', 'role',
        'status_akun', 'password', 'avatar', 'force_change_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'force_change_password' => 'boolean',
    ];

    // Use NIP for auth
    public function getAuthIdentifierName(): string { return 'nip'; }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function progresMateri()
    {
        return $this->hasMany(ProgresMateri::class);
    }

    public function sesiEvaluasi()
    {
        return $this->hasMany(SesiEvaluasi::class);
    }

    public function aiChatHistories()
    {
        return $this->hasMany(AiChatHistory::class);
    }

    public function hasilLatihan()
    {
        return $this->hasMany(HasilLatihan::class);
    }

    public function progresPelatihans()
    {
        return $this->hasMany(ProgresPelatihan::class);
    }

    public function getActivePelatihan()
    {
        return $this->progresPelatihans()->where('status', 'aktif')->first();
    }

    // ==========================================
    // RBAC: Custom Roles & Permissions
    // ==========================================

    /**
     * Custom roles assigned by superadmin (many-to-many).
     */
    public function customRoles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Get all permissions this user has.
     * Superadmin always has all permissions.
     * Other users get permissions from their base role's default Role record
     * plus any custom roles assigned to them.
     */
    public function allPermissions(): \Illuminate\Support\Collection
    {
        if ($this->isSuperadmin()) {
            return Permission::all();
        }

        // Collect permissions from the user's base role default Role record
        $baseRole = Role::where('base_role', $this->role)->where('is_default', true)->first();
        $basePermissions = $baseRole ? $baseRole->permissions : collect();

        // Collect permissions from custom roles
        $customPermissions = $this->customRoles->flatMap(fn($role) => $role->permissions);

        return $basePermissions->merge($customPermissions)->unique('id');
    }

    /**
     * Check if the user has a specific permission by kode.
     */
    public function hasPermission(string $kode): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }
        return $this->allPermissions()->contains('kode', $kode);
    }

    // ==========================================
    // Role Helpers
    // ==========================================
    public function isSuperadmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool { return in_array($this->role, ['admin', 'superadmin']); }
    public function isPeserta(): bool { return $this->role === 'peserta'; }
    public function isApproved(): bool { return $this->status_akun === 'approved'; }

    public function hasActiveSesiEvaluasi(): bool
    {
        return $this->sesiEvaluasi()->where('status', 'berlangsung')->exists();
    }

    public function getActiveSesiEvaluasi()
    {
        return $this->sesiEvaluasi()->where('status', 'berlangsung')->first();
    }

    public function getMateriSelesaiCount(): int
    {
        return $this->progresMateri()->where('status', 'selesai')->count();
    }

    public function getRataRataSkor(): float
    {
        $avg = $this->sesiEvaluasi()->where('status', 'selesai')->avg('skor');
        return round($avg ?? 0, 1);
    }

    public function getProgresKeseluruhan(): int
    {
        $total = \App\Models\Materi::where('is_active', true)->count();
        if ($total === 0) return 0;
        $selesai = $this->getMateriSelesaiCount();
        return (int) round(($selesai / $total) * 100);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $initials = strtoupper(substr($this->nama, 0, 2));
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&background=003366&color=C5A02E&bold=true&size=80';
    }

    // ==========================================
    // Gamifikasi: Scoring & Badge
    // ==========================================
    
    public function getTotalPoin(): int
    {
        // Avoid N+1 issues when called on collections by checking if total_poin attribute is already calculated via DB Query
        if (array_key_exists('total_poin', $this->attributes)) {
            return (int) $this->attributes['total_poin'];
        }

        // Sum 'poin' from materi that have been completed (ensure unique materi_id)
        $poinMateri = \App\Models\ProgresMateri::where('user_id', $this->id)
            ->where('status', 'selesai')
            ->join('materis', 'progres_materis.materi_id', '=', 'materis.id')
            ->distinct('progres_materis.materi_id')
            ->sum('materis.poin');

        // Sum max 'skor' for each unique quiz (materi_id)
        $poinEvaluasi = \App\Models\SesiEvaluasi::where('user_id', $this->id)
            ->where('status', 'selesai')
            ->groupBy('materi_id')
            ->selectRaw('MAX(skor) as max_skor')
            ->get()
            ->sum('max_skor');
        
        return (int) $poinMateri + (int) $poinEvaluasi;
    }

    /**
     * Get Badge based on Total Poin.
     */
    public function getBadgeAttribute(): string
    {
        $poin = $this->getTotalPoin();
        if ($poin > 1000) return 'Platinum / Expert';
        if ($poin > 500) return 'Gold / Mahir';
        if ($poin > 200) return 'Silver / Aktif';
        return 'Bronze / Pemula';
    }

    /**
     * Get Tailwind color class for the badge.
     */
    public function getBadgeColorAttribute(): string
    {
        $poin = $this->getTotalPoin();
        if ($poin > 1000) return 'text-slate-800 bg-slate-200 border-slate-400'; // Platinum
        if ($poin > 500) return 'text-yellow-700 bg-yellow-100 border-yellow-400'; // Gold
        if ($poin > 200) return 'text-gray-600 bg-gray-100 border-gray-300'; // Silver
        return 'text-amber-800 bg-amber-100 border-amber-500'; // Bronze (Copper-ish)
    }

    /**
     * Get Lucide icon for the badge.
     */
    public function getBadgeIconAttribute(): string
    {
        $poin = $this->getTotalPoin();
        if ($poin > 1000) return 'diamond'; // Platinum
        if ($poin > 500) return 'award'; // Gold
        if ($poin > 200) return 'medal'; // Silver
        return 'shield'; // Bronze
    }
}


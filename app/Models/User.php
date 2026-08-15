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

    // Helpers
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
}

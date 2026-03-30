<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // --- Role Helpers ---

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'        => 'Administrator',
            'technician'   => 'Technician',
            'receptionist' => 'Receptionist',
            default        => ucfirst($this->role),
        };
    }
}

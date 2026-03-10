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
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
        ];
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ─── Helpers de rol ───────────────────────────────────────────
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esEmpleado(): bool
    {
        return $this->rol === 'empleado';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'rol', 'tienda_id', 'activo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
        ];
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function orders() { return $this->hasMany(Order::class); }

    // ─── Helpers de rol ───────────────────────────────────────────
    public function esSuperAdmin(): bool { return $this->rol === 'superadmin'; }
    public function esAdmin(): bool      { return in_array($this->rol, ['admin', 'superadmin']); }
    public function esEmpleado(): bool   { return $this->rol === 'empleado'; }

    // La tienda activa: superadmin puede cambiar via sesión
    public function tiendaActiva(): ?Tienda
    {
        if ($this->esSuperAdmin()) {
            $id = session('tienda_activa_id');
            return $id ? Tienda::find($id) : Tienda::first();
        }
        return $this->tienda;
    }

    public function tiendaActivaId(): ?int
    {
        return $this->tiendaActiva()?->id;
    }
}

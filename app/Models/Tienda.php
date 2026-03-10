<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Tienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'ciudad', 'direccion', 'telefono', 'logo_path', 'activa',
    ];

    protected function casts(): array
    {
        return ['activa' => 'boolean'];
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function users()     { return $this->hasMany(User::class); }
    public function products()  { return $this->hasMany(Product::class); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function orders()    { return $this->hasMany(Order::class); }
    public function cajas()     { return $this->hasMany(Caja::class); }

    // ─── Helpers ──────────────────────────────────────────────────
    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    // Nombre completo: "Kiosco Del Centro — Santa Fe"
    public function nombreCompleto(): string
    {
        return $this->ciudad
            ? "{$this->nombre} — {$this->ciudad}"
            : $this->nombre;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_costo',
        'precio_venta',
        'stock',
        'image_path',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio_costo'  => 'decimal:2',
            'precio_venta'  => 'decimal:2',
            'activo'        => 'boolean',
        ];
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes útiles ────────────────────────────────────────────
    // Productos disponibles para vender en el POS
    public function scopeActivos($query)
    {
        return $query->where('activo', true)->where('stock', '>', 0);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function tieneStock(): bool
    {
        return $this->stock > 0;
    }

    public function margenGanancia(): float
    {
        if ($this->precio_costo == 0) return 0;
        return round((($this->precio_venta - $this->precio_costo) / $this->precio_costo) * 100, 2);
    }
}

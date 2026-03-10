<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tienda_id', 'nombre', 'descripcion',
        'precio_costo', 'precio_venta', 'stock', 'image_path', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio_costo' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'activo'       => 'boolean',
        ];
    }

    // ─── Scope global: solo productos de la tienda activa ─────────
    protected static function booted(): void
    {
        static::addGlobalScope('tienda', function ($query) {
            if (auth()->check()) {
                $tid = auth()->user()->tiendaActivaId();
                if ($tid) $query->where('products.tienda_id', $tid);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->tienda_id) {
                $model->tienda_id = auth()->user()->tiendaActivaId();
            }
        });
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function tienda()     { return $this->belongsTo(Tienda::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('activo', true)->where('stock', '>', 0);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function tieneStock(): bool { return $this->stock > 0; }

    public function margenGanancia(): float
    {
        if ($this->precio_costo == 0) return 0;
        return round((($this->precio_venta - $this->precio_costo) / $this->precio_costo) * 100, 2);
    }
}

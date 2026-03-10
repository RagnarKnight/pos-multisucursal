<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'total',
        'metodo_pago',
        'comprobante_path',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class); // nullable
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeDeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeFiadas($query)
    {
        return $query->where('metodo_pago', 'fiado');
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function esFiada(): bool
    {
        return $this->metodo_pago === 'fiado';
    }
}

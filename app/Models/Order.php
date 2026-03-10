<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'tienda_id', 'user_id', 'customer_id',
        'total', 'metodo_pago', 'comprobante_path', 'notas',
    ];

    protected function casts(): array
    {
        return ['total' => 'decimal:2'];
    }

    // ─── Scope global ─────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope('tienda', function ($query) {
            if (auth()->check()) {
                $tid = auth()->user()->tiendaActivaId();
                if ($tid) $query->where('orders.tienda_id', $tid);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->tienda_id) {
                $model->tienda_id = auth()->user()->tiendaActivaId();
            }
        });
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function tienda()   { return $this->belongsTo(Tienda::class); }
    public function user()     { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(OrderItem::class); }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeDeHoy($query)  { return $query->whereDate('created_at', today()); }
    public function scopeFiadas($query) { return $query->where('metodo_pago', 'fiado'); }
    public function esFiada(): bool     { return $this->metodo_pago === 'fiado'; }
}

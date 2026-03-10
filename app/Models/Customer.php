<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['tienda_id', 'nombre', 'telefono', 'saldo_deudor', 'notas'];

    protected function casts(): array
    {
        return ['saldo_deudor' => 'decimal:2'];
    }

    // ─── Scope global ─────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope('tienda', function ($query) {
            if (auth()->check()) {
                $tid = auth()->user()->tiendaActivaId();
                if ($tid) $query->where('customers.tienda_id', $tid);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->tienda_id) {
                $model->tienda_id = auth()->user()->tiendaActivaId();
            }
        });
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function tienda()  { return $this->belongsTo(Tienda::class); }
    public function orders()  { return $this->hasMany(Order::class); }

    // ─── Helpers ──────────────────────────────────────────────────
    public function agregarDeuda(float $monto): void
    {
        $this->increment('saldo_deudor', $monto);
    }

    public function saldar(float $monto): void
    {
        $this->update(['saldo_deudor' => max(0, $this->saldo_deudor - $monto)]);
    }

    public function debeAlgo(): bool { return $this->saldo_deudor > 0; }
}

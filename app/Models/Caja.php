<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'tienda_id', 'user_id', 'monto_apertura', 'monto_cierre',
        'total_efectivo', 'total_transfer', 'total_fiado',
        'notas_cierre', 'abierta_at', 'cerrada_at',
    ];

    protected $casts = [
        'abierta_at'     => 'datetime',
        'cerrada_at'     => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_cierre'   => 'decimal:2',
    ];

    // ─── Scope global ─────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope('tienda', function ($query) {
            if (auth()->check()) {
                $tid = auth()->user()->tiendaActivaId();
                if ($tid) $query->where('cajas.tienda_id', $tid);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->tienda_id) {
                $model->tienda_id = auth()->user()->tiendaActivaId();
            }
        });
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function user()   { return $this->belongsTo(User::class); }

    // ─── Helpers ──────────────────────────────────────────────────
    public function estaAbierta(): bool { return is_null($this->cerrada_at); }

    public function diferencia(): float
    {
        if (is_null($this->monto_cierre)) return 0;
        return $this->monto_cierre - ($this->monto_apertura + $this->total_efectivo);
    }
}

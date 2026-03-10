<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'monto_apertura', 'monto_cierre',
        'total_efectivo', 'total_transfer', 'total_fiado',
        'notas_cierre', 'abierta_at', 'cerrada_at',
    ];

    protected $casts = [
        'abierta_at'  => 'datetime',
        'cerrada_at'  => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_cierre'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estaAbierta(): bool
    {
        return is_null($this->cerrada_at);
    }

    // Diferencia entre lo que debería haber (apertura + efectivo) y lo real
    public function diferencia(): float
    {
        if (is_null($this->monto_cierre)) return 0;
        $esperado = $this->monto_apertura + $this->total_efectivo;
        return $this->monto_cierre - $esperado;
    }
}

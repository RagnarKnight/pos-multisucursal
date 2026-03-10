<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'saldo_deudor',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'saldo_deudor' => 'decimal:2',
        ];
    }

    // ─── Relaciones ───────────────────────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    // Sumar deuda (cuando se vende fiado)
    public function agregarDeuda(float $monto): void
    {
        $this->increment('saldo_deudor', $monto);
    }

    // Saldar (pago parcial o total)
    public function saldar(float $monto): void
    {
        $nuevo = max(0, $this->saldo_deudor - $monto);
        $this->update(['saldo_deudor' => $nuevo]);
    }

    public function debeAlgo(): bool
    {
        return $this->saldo_deudor > 0;
    }
}

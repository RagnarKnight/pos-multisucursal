<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('monto_apertura', 10, 2)->default(0);  // efectivo en caja al abrir
            $table->decimal('monto_cierre',   10, 2)->nullable();  // efectivo real al cerrar
            $table->decimal('total_efectivo', 10, 2)->nullable();  // suma de ventas efectivo
            $table->decimal('total_transfer', 10, 2)->nullable();  // suma de ventas transfer
            $table->decimal('total_fiado',    10, 2)->nullable();  // suma de ventas fiado
            $table->text('notas_cierre')->nullable();
            $table->timestamp('abierta_at');
            $table->timestamp('cerrada_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');               // "Kiosco Del Centro"
            $table->string('ciudad')->nullable();   // "Santa Fe"
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('logo_path')->nullable(); // ruta en storage/public
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_laborales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->unique()->constrained('clientes');
            $table->string('empresa', 180);
            $table->string('cargo_ocupacion', 120);
            $table->unsignedInteger('tiempo_laborando_meses')->nullable();
            $table->decimal('sueldo', 18, 2);
            $table->string('telefono_empresa', 25)->nullable();
            $table->string('direccion_empresa', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_laborales');
    }
};

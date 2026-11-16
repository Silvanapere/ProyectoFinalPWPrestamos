<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renovaciones_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renovacion_id')->constrained('renovaciones');
            $table->foreignId('prestamo_anterior_id')->unique()->constrained('prestamos');
            $table->string('base_saldo', 25);
            $table->decimal('monto_cancelado', 18, 2);
            $table->decimal('descuento', 18, 2)->default(0);
            $table->foreignId('recibo_id')->nullable()->constrained('recibos_ingreso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renovaciones_detalle');
    }
};

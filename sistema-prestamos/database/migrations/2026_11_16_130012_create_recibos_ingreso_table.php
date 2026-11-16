<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recibos_ingreso', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 35)->unique();
            $table->foreignId('prestamo_id')->constrained('prestamos');
            $table->dateTime('fecha');
            $table->string('tipo_pago', 20);
            $table->decimal('monto_recibido', 18, 2);
            $table->decimal('total_mora', 18, 2)->default(0);
            $table->decimal('total_interes', 18, 2)->default(0);
            $table->decimal('total_seguro', 18, 2)->default(0);
            $table->decimal('total_capital', 18, 2)->default(0);
            $table->decimal('total_gasto_cierre', 18, 2)->default(0);
            $table->decimal('balance_posterior', 18, 2);
            $table->string('formato_impreso', 10)->nullable();
            $table->string('estado', 15)->default('Activo');
            $table->foreignId('creado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['prestamo_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recibos_ingreso');
    }
};

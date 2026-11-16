<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renovaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('prestamo_nuevo_id')->unique()->constrained('prestamos');
            $table->dateTime('fecha');
            $table->decimal('total_saldo_cancelado', 18, 2);
            $table->decimal('desembolso_final', 18, 2);
            $table->foreignId('creado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renovaciones');
    }
};

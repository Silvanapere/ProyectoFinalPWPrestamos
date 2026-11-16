<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recibos_ingreso_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recibo_id')->constrained('recibos_ingreso');
            // Nullable para abonos directos a capital que no se asignan a una cuota.
            $table->foreignId('cuota_id')->nullable()->constrained('prestamos_cuotas');
            $table->decimal('mora_aplicada', 18, 2)->default(0);
            $table->decimal('interes_aplicado', 18, 2)->default(0);
            $table->decimal('seguro_aplicado', 18, 2)->default(0);
            $table->decimal('capital_aplicado', 18, 2)->default(0);
            $table->decimal('gasto_cierre_aplicado', 18, 2)->default(0);
            $table->timestamps();
            $table->index('cuota_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recibos_ingreso_detalle');
    }
};

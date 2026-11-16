<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos_cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos');
            $table->unsignedInteger('numero_cuota');
            $table->date('fecha_pago');
            $table->decimal('capital', 18, 2);
            $table->decimal('interes', 18, 2)->default(0);
            $table->decimal('seguro', 18, 2)->default(0);
            $table->decimal('gasto_cierre', 18, 2)->default(0);
            $table->decimal('mora_acumulada', 18, 2)->default(0);
            $table->decimal('valor_cuota', 18, 2);
            $table->decimal('capital_pagado', 18, 2)->default(0);
            $table->decimal('interes_pagado', 18, 2)->default(0);
            $table->decimal('seguro_pagado', 18, 2)->default(0);
            $table->decimal('mora_pagada', 18, 2)->default(0);
            $table->decimal('gasto_cierre_pagado', 18, 2)->default(0);
            $table->decimal('balance', 18, 2);
            $table->string('estado', 20)->default('Pendiente');
            $table->foreignId('creado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->unique(['prestamo_id', 'numero_cuota']);
            $table->index(['fecha_pago', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos_cuotas');
    }
};

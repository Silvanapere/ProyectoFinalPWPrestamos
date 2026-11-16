<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 35)->unique();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('tipo_prestamo_id')->constrained('tipos_prestamo');
            $table->decimal('monto', 18, 2);
            $table->unsignedInteger('plazo');
            $table->decimal('tasa', 9, 4);
            $table->foreignId('tipo_plazo_id')->constrained('tipos_plazo');
            $table->foreignId('tipo_amortizacion_id')->constrained('tipos_amortizacion');
            $table->boolean('incluye_sabado')->default(true);
            $table->boolean('incluye_domingo')->default(true);
            $table->string('tipo_mora', 15)->default('porcentaje');
            $table->decimal('valor_mora', 18, 4)->default(0);
            $table->string('seguro_tipo', 15)->default('fijo');
            $table->decimal('seguro_monto', 18, 2)->default(0);
            $table->decimal('gasto_cierre', 18, 2)->default(0);
            $table->boolean('gasto_cierre_financiado')->default(false);
            $table->decimal('desembolso', 18, 2);
            $table->date('fecha_apertura');
            $table->date('fecha_primer_pago');
            $table->decimal('capital_pendiente', 18, 2);
            $table->string('estado', 20)->default('Activo');
            $table->foreignId('creado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['cliente_id', 'estado']);
        });

        DB::statement('ALTER TABLE prestamos ADD CONSTRAINT CK_prestamos_monto_plazo CHECK (monto > 0 AND plazo > 0 AND tasa >= 0 AND capital_pendiente >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};

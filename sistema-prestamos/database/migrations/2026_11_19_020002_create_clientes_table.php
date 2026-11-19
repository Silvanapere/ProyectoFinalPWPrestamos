<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_documento_id')
                ->constrained('tipos_documento');

            $table->string('numero_documento', 30)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);

            $table->foreignId('nacionalidad_id')
                ->constrained('nacionalidades');

            $table->date('fecha_nacimiento');
            $table->string('estado_civil', 30);
            $table->string('correo', 150);
            $table->string('direccion', 255);
            $table->string('referencia_direccion', 255);

            $table->decimal('limite_credito', 18, 2)->nullable();
            $table->string('clasificacion_crediticia', 20)->nullable();

            $table->string('estado', 10)->default('Activo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

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
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->string('numero_documento', 30)->unique();
            $table->string('nombres', 120);
            $table->string('apellidos', 120);
            $table->foreignId('nacionalidad_id')->constrained('nacionalidades');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('estado_civil', 35)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('direccion', 300);
            $table->string('referencia_direccion', 300)->nullable();
            $table->decimal('limite_credito', 18, 2)->default(0);
            $table->string('estado', 15)->default('Activo');
            $table->foreignId('creado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['estado', 'apellidos']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

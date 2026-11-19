<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
public function up(): void
{
    Schema::table('tipos_prestamo', function (Blueprint $table) {
        $table->string('nombre', 100)->unique();
        $table->string('descripcion', 255);
        $table->string('estado', 10)->default('Activo');
    });
}

public function down(): void
{
    Schema::table('tipos_prestamo', function (Blueprint $table) {
        $table->dropUnique('tipos_prestamo_nombre_unique');
        $table->dropColumn([
            'nombre',
            'descripcion',
            'estado'
        ]);
    });
}

};

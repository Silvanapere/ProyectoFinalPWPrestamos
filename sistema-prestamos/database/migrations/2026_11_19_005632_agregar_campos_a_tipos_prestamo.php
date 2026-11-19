
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tipos_prestamo', 'nombre')) {
            Schema::table('tipos_prestamo', function (Blueprint $table) {
                $table->string('nombre', 100)->unique();
            });
        }

        if (!Schema::hasColumn('tipos_prestamo', 'descripcion')) {
            Schema::table('tipos_prestamo', function (Blueprint $table) {
                $table->string('descripcion', 255);
            });
        }

        if (!Schema::hasColumn('tipos_prestamo', 'estado')) {
            Schema::table('tipos_prestamo', function (Blueprint $table) {
                $table->string('estado', 10)->default('Activo');
            });
        }
    }

    public function down(): void
    {
        
    }
};

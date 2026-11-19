<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['clientes', 'clientes_telefonos', 'clientes_garantes', 'clientes_laborales', 'clientes_familiares', 'clientes_referencias'] as $nombre) {
            Schema::table($nombre, function (Blueprint $table): void {
                $table->foreignId('creado_por')->nullable()->constrained('users');
                $table->foreignId('modificado_por')->nullable()->constrained('users');
            });
        }
        Schema::create('clientes_documentos_limpieza', function (Blueprint $table): void {
            $table->id();
            $table->string('ruta', 255)->unique();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_documentos_limpieza');
        foreach (['clientes', 'clientes_telefonos', 'clientes_garantes', 'clientes_laborales', 'clientes_familiares', 'clientes_referencias'] as $nombre) {
            Schema::table($nombre, function (Blueprint $table): void {
                $table->dropForeign(['creado_por']);
                $table->dropForeign(['modificado_por']);
                $table->dropColumn(['creado_por', 'modificado_por']);
            });
        }
    }
};

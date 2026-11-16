<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_telefonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('tipo', 20);
            $table->string('numero', 25);
            $table->boolean('principal')->default(false);
            $table->timestamps();
            $table->unique(['cliente_id', 'numero']);
        });

        // SQL Server: permite solo un teléfono principal por persona.
        DB::statement('CREATE UNIQUE INDEX UX_clientes_telefonos_principal ON clientes_telefonos(cliente_id) WHERE principal = 1');
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_telefonos');
    }
};

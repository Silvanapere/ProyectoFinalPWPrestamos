<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_garantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->unique()
                ->constrained('clientes');

            $table->foreignId('garante_id')
                ->constrained('clientes');

            $table->date('fecha_asignacion');

            $table->timestamps();
        });
        DB::statement('
    ALTER TABLE clientes_garantes
    ADD CONSTRAINT CK_clientes_garantes_distintos
    CHECK (cliente_id <> garante_id)
');
    }
    

    public function down(): void
    {
        Schema::dropIfExists('clientes_garantes');
    }
};

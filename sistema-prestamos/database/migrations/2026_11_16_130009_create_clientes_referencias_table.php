<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_referencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('nombre', 150);
            $table->string('telefono', 25);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_referencias');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteLaboral extends Model
{
    protected $table = 'clientes_laborales';

    protected $fillable = [
        'cliente_id',
        'empresa',
        'cargo_ocupacion',
        'tiempo_laborando_meses',
        'sueldo',
        'telefono_empresa',
        'direccion_empresa',
    ];

    protected function casts(): array
    {
        return [
            'tiempo_laborando_meses' => 'integer',
            'sueldo' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}

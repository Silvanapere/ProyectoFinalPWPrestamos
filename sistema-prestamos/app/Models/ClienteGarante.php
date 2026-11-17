<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteGarante extends Model
{
    protected $table = 'clientes_garantes';

    protected $fillable = [
        'cliente_id',
        'garante_id',
        'fecha_asignacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function garante(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'garante_id');
    }
}

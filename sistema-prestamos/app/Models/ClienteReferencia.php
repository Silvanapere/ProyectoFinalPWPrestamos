<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteReferencia extends Model
{
    protected $table = 'clientes_referencias';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'telefono',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}

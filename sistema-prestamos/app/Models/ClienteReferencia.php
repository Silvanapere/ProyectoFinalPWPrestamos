<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteReferencia extends Model
{
    use AuditaUsuario;

    protected $table = 'clientes_referencias';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'telefono',
    ];

    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'cliente_id'
        );
    }
}

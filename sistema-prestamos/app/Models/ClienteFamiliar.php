<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFamiliar extends Model
{
    protected $table = 'clientes_familiares';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'nombre',
        'parentesco',
        'telefono',
        'direccion'
    ];

    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'cliente_id'
        );
    }
}

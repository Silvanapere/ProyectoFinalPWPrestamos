<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipos_documento';

    protected $fillable = [
        'nombre',
        'mascara',
        'longitud'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'tipo_documento_id');
    }
}

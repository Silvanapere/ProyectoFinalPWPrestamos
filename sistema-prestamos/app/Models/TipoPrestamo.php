<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPrestamo extends Model
{
    protected $table = 'tipos_prestamo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];
}

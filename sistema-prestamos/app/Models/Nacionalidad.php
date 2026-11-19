<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    protected $table = 'nacionalidades';

    protected $fillable = [
        'nombre'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'nacionalidad_id');
    }
}

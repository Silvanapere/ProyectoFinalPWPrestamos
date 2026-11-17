<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nacionalidad extends Model
{
    protected $table = 'nacionalidades';

    protected $fillable = [
        'nombre',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'nacionalidad_id');
    }
}

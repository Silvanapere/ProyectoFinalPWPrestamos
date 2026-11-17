<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPlazo extends Model
{
    protected $table = 'tipos_plazo';

    protected $fillable = [
        'nombre',
        'dias_referencia',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'dias_referencia' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'tipo_plazo_id');
    }
}

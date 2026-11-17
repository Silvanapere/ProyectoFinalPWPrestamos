<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoAmortizacion extends Model
{
    protected $table = 'tipos_amortizacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'tipo_amortizacion_id');
    }
}

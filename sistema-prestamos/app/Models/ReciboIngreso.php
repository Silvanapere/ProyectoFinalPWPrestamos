<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReciboIngreso extends Model
{
    protected $table = 'recibos_ingreso';

    protected $fillable = [
        'numero',
        'prestamo_id',
        'fecha',
        'tipo_pago',
        'monto_recibido',
        'total_mora',
        'total_interes',
        'total_seguro',
        'total_capital',
        'total_gasto_cierre',
        'balance_posterior',
        'formato_impreso',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'monto_recibido' => 'decimal:2',
            'total_mora' => 'decimal:2',
            'total_interes' => 'decimal:2',
            'total_seguro' => 'decimal:2',
            'total_capital' => 'decimal:2',
            'total_gasto_cierre' => 'decimal:2',
            'balance_posterior' => 'decimal:2',
        ];
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(ReciboIngresoDetalle::class, 'recibo_id');
    }

    public function renovacionesDetalle(): HasMany
    {
        return $this->hasMany(RenovacionDetalle::class, 'recibo_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}

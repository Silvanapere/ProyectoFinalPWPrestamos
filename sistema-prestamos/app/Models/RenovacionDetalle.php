<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenovacionDetalle extends Model
{
    protected $table = 'renovaciones_detalle';

    protected $fillable = [
        'renovacion_id',
        'prestamo_anterior_id',
        'base_saldo',
        'monto_cancelado',
        'descuento',
        'recibo_id',
    ];

    protected function casts(): array
    {
        return [
            'monto_cancelado' => 'decimal:2',
            'descuento' => 'decimal:2',
        ];
    }

    public function renovacion(): BelongsTo
    {
        return $this->belongsTo(Renovacion::class, 'renovacion_id');
    }

    public function prestamoAnterior(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_anterior_id');
    }

    public function recibo(): BelongsTo
    {
        return $this->belongsTo(ReciboIngreso::class, 'recibo_id');
    }
}

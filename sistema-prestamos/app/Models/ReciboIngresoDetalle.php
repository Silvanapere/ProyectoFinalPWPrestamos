<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReciboIngresoDetalle extends Model
{
    protected $table = 'recibos_ingreso_detalle';

    protected $fillable = [
        'recibo_id',
        'cuota_id',
        'mora_aplicada',
        'interes_aplicado',
        'seguro_aplicado',
        'capital_aplicado',
        'gasto_cierre_aplicado',
    ];

    protected function casts(): array
    {
        return [
            'mora_aplicada' => 'decimal:2',
            'interes_aplicado' => 'decimal:2',
            'seguro_aplicado' => 'decimal:2',
            'capital_aplicado' => 'decimal:2',
            'gasto_cierre_aplicado' => 'decimal:2',
        ];
    }

    public function recibo(): BelongsTo
    {
        return $this->belongsTo(ReciboIngreso::class, 'recibo_id');
    }

    public function cuota(): BelongsTo
    {
        return $this->belongsTo(PrestamoCuota::class, 'cuota_id');
    }
}

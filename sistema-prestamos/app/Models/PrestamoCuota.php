<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrestamoCuota extends Model
{
    protected $table = 'prestamos_cuotas';

    protected $fillable = [
        'prestamo_id',
        'numero_cuota',
        'fecha_pago',
        'capital',
        'interes',
        'seguro',
        'gasto_cierre',
        'mora_acumulada',
        'valor_cuota',
        'capital_pagado',
        'interes_pagado',
        'seguro_pagado',
        'mora_pagada',
        'gasto_cierre_pagado',
        'balance',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'numero_cuota' => 'integer',
            'fecha_pago' => 'date',
            'capital' => 'decimal:2',
            'interes' => 'decimal:2',
            'seguro' => 'decimal:2',
            'gasto_cierre' => 'decimal:2',
            'mora_acumulada' => 'decimal:2',
            'valor_cuota' => 'decimal:2',
            'capital_pagado' => 'decimal:2',
            'interes_pagado' => 'decimal:2',
            'seguro_pagado' => 'decimal:2',
            'mora_pagada' => 'decimal:2',
            'gasto_cierre_pagado' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }

    public function detallesRecibo(): HasMany
    {
        return $this->hasMany(ReciboIngresoDetalle::class, 'cuota_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}

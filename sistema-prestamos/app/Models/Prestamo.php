<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prestamo extends Model
{
    protected $table = 'prestamos';

    protected $fillable = [
        'numero',
        'cliente_id',
        'tipo_prestamo_id',
        'monto',
        'plazo',
        'tasa',
        'tipo_plazo_id',
        'tipo_amortizacion_id',
        'incluye_sabado',
        'incluye_domingo',
        'tipo_mora',
        'valor_mora',
        'seguro_tipo',
        'seguro_monto',
        'gasto_cierre',
        'gasto_cierre_financiado',
        'desembolso',
        'fecha_apertura',
        'fecha_primer_pago',
        'capital_pendiente',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'plazo' => 'integer',
            'tasa' => 'decimal:4',
            'incluye_sabado' => 'boolean',
            'incluye_domingo' => 'boolean',
            'valor_mora' => 'decimal:4',
            'seguro_monto' => 'decimal:2',
            'gasto_cierre' => 'decimal:2',
            'gasto_cierre_financiado' => 'boolean',
            'desembolso' => 'decimal:2',
            'fecha_apertura' => 'date',
            'fecha_primer_pago' => 'date',
            'capital_pendiente' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function tipoPrestamo(): BelongsTo
    {
        return $this->belongsTo(TipoPrestamo::class, 'tipo_prestamo_id');
    }

    public function tipoPlazo(): BelongsTo
    {
        return $this->belongsTo(TipoPlazo::class, 'tipo_plazo_id');
    }

    public function tipoAmortizacion(): BelongsTo
    {
        return $this->belongsTo(TipoAmortizacion::class, 'tipo_amortizacion_id');
    }

    public function cuotas(): HasMany
    {
        return $this->hasMany(PrestamoCuota::class, 'prestamo_id');
    }

    public function recibos(): HasMany
    {
        return $this->hasMany(ReciboIngreso::class, 'prestamo_id');
    }

    public function renovacionDeOrigen(): HasOne
    {
        return $this->hasOne(Renovacion::class, 'prestamo_nuevo_id');
    }

    public function renovacionComoAnterior(): HasOne
    {
        return $this->hasOne(RenovacionDetalle::class, 'prestamo_anterior_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}

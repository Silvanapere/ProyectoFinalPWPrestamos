<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Renovacion extends Model
{
    protected $table = 'renovaciones';

    protected $fillable = [
        'cliente_id',
        'prestamo_nuevo_id',
        'fecha',
        'total_saldo_cancelado',
        'desembolso_final',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'total_saldo_cancelado' => 'decimal:2',
            'desembolso_final' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function prestamoNuevo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_nuevo_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(RenovacionDetalle::class, 'renovacion_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}

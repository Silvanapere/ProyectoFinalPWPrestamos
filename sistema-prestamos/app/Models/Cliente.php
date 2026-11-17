<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'nombres',
        'apellidos',
        'nacionalidad_id',
        'fecha_nacimiento',
        'estado_civil',
        'correo',
        'direccion',
        'referencia_direccion',
        'limite_credito',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'limite_credito' => 'decimal:2',
        ];
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function nacionalidad(): BelongsTo
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }

    public function telefonos(): HasMany
    {
        return $this->hasMany(ClienteTelefono::class, 'cliente_id');
    }

    public function familiares(): HasMany
    {
        return $this->hasMany(ClienteFamiliar::class, 'cliente_id');
    }

    public function datosLaborales(): HasOne
    {
        return $this->hasOne(ClienteLaboral::class, 'cliente_id');
    }

    public function referencias(): HasMany
    {
        return $this->hasMany(ClienteReferencia::class, 'cliente_id');
    }

    public function vinculosGarantes(): HasMany
    {
        return $this->hasMany(ClienteGarante::class, 'cliente_id');
    }

    public function vinculosComoGarante(): HasMany
    {
        return $this->hasMany(ClienteGarante::class, 'garante_id');
    }

    public function garantes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'clientes_garantes', 'cliente_id', 'garante_id')
            ->withPivot('fecha_asignacion')
            ->withTimestamps();
    }

    public function clientesGarantizados(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'clientes_garantes', 'garante_id', 'cliente_id')
            ->withPivot('fecha_asignacion')
            ->withTimestamps();
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'cliente_id');
    }

    public function renovaciones(): HasMany
    {
        return $this->hasMany(Renovacion::class, 'cliente_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}

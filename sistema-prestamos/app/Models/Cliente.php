<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'clasificacion_crediticia',
        'documento_archivo',
        'estado'
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'limite_credito' => 'decimal:2'
        ];
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(
            TipoDocumento::class,
            'tipo_documento_id'
        );
    }

    public function nacionalidad()
    {
        return $this->belongsTo(
            Nacionalidad::class,
            'nacionalidad_id'
        );
    }

    public function telefonos()
    {
        return $this->hasMany(
            ClienteTelefono::class,
            'cliente_id'
        );
    }

    public function familiares()
    {
        return $this->hasMany(
            ClienteFamiliar::class,
            'cliente_id'
        );
    }

    public function datosLaborales()
    {
        return $this->hasOne(
            ClienteLaboral::class,
            'cliente_id'
        );
    }

    public function referencias()
    {
        return $this->hasMany(
            ClienteReferencia::class,
            'cliente_id'
        );
    }

    public function relacionGarante()
    {
        return $this->hasOne(
            ClienteGarante::class,
            'cliente_id'
        );
    }

    public function clientesGarantizados()
    {
        return $this->hasMany(
            ClienteGarante::class,
            'garante_id'
        );
    }
}

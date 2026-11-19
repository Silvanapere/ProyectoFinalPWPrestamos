<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteLaboral extends Model
{
    use AuditaUsuario;

    protected $table = 'clientes_laborales';

    protected $fillable = [
        'cliente_id',
        'empresa',
        'cargo_ocupacion',
        'tiempo_laborando',
        'sueldo',
        'telefono_empresa',
        'direccion_empresa',
    ];

    protected function casts(): array
    {
        return [
            'tiempo_laborando' => 'integer',
            'sueldo' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'cliente_id'
        );
    }
}

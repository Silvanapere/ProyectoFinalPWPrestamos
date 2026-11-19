<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteTelefono extends Model
{
    use AuditaUsuario;

    protected $table = 'clientes_telefonos';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'numero',
        'principal',
    ];

    protected function casts(): array
    {
        return [
            'principal' => 'boolean',
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

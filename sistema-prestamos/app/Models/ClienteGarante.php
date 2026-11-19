<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteGarante extends Model
{
    protected $table = 'clientes_garantes';

    protected $fillable = [
        'cliente_id',
        'garante_id',
        'fecha_asignacion'
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'date'
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'cliente_id'
        );
    }

    public function garante()
    {
        return $this->belongsTo(
            Cliente::class,
            'garante_id'
        );
    }
}

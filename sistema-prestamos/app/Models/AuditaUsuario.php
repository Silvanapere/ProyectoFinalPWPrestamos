<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait AuditaUsuario
{
    protected static function bootAuditaUsuario(): void
    {
        static::creating(function (Model $modelo): void {
            $modelo->creado_por = Auth::id();
            $modelo->modificado_por = Auth::id();
        });

        static::updating(function (Model $modelo): void {
            $modelo->modificado_por = Auth::id();
        });
    }
}

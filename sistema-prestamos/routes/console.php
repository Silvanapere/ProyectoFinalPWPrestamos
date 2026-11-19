<?php

use App\Services\DocumentosClientes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('clientes:limpiar-documentos', function () {
    $pendientes = app(DocumentosClientes::class)->reintentar();
    $this->info("Documentos pendientes de limpieza: {$pendientes}");

    return $pendientes === 0 ? 0 : 1;
})->purpose('Reintentar la eliminación de documentos sustituidos sin borrar archivos referenciados');

Schedule::command('clientes:limpiar-documentos')->hourly()->withoutOverlapping();

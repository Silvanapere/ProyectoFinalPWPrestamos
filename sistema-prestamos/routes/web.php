<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipoPrestamoController;
use App\Http\Controllers\ClienteController;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/clientes', [ClienteController::class, 'index'])
    ->name('clientes.index');

Route::get('/clientes/crear', [ClienteController::class, 'create'])
    ->name('clientes.create');

Route::get('/clientes/buscar-documento', [ClienteController::class, 'buscarDocumento'])
    ->name('clientes.buscar-documento');

Route::post('/clientes', [ClienteController::class, 'store'])
    ->name('clientes.store');

Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])
    ->name('clientes.edit');

Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])
    ->name('clientes.update');
    
Route::patch('/clientes/{cliente}/estado', [ClienteController::class, 'cambiarEstado'])
    ->name('clientes.estado');

Route::get('/tipos-prestamo', [TipoPrestamoController::class, 'index'])
    ->name('tipos-prestamo.index');

Route::post('/tipos-prestamo', [TipoPrestamoController::class, 'store'])
    ->name('tipos-prestamo.store');

Route::put('/tipos-prestamo/{id}', [TipoPrestamoController::class, 'update'])
    ->name('tipos-prestamo.update');

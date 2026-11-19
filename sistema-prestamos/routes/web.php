<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipoPrestamoController;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/clientes', function () {
    return view('clientes.index');
})->name('clientes.index');

Route::get('/clientes/crear', function () {
    return view('clientes.create');
})->name('clientes.create');

Route::get('/tipos-prestamo', [TipoPrestamoController::class, 'index'])
    ->name('tipos-prestamo.index');

    Route::post('/tipos-prestamo', [TipoPrestamoController::class, 'store'])
    ->name('tipos-prestamo.store');

    Route::put('/tipos-prestamo/{id}', [TipoPrestamoController::class, 'update'])
    ->name('tipos-prestamo.update');
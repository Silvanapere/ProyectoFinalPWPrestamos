<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/clientes', function () {
    return view('clientes.index');
})->name('clientes.index');

Route::get('/clientes/crear', function () {
    return view('clientes.create');
})->name('clientes.create');
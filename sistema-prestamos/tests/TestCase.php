<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::verificarBasePruebas();
    }

    public static function verificarBasePruebas(): void
    {
        if (! app()->environment('testing')
            || config('database.default') !== 'sqlsrv'
            || config('database.connections.sqlsrv.database') !== 'GestionPrestamos_Test'
            || config('database.connections.sqlsrv.url')) {
            throw new \RuntimeException('Operación bloqueada: se requiere sqlsrv / GestionPrestamos_Test sin DB_URL.');
        }
        $baseReal = DB::selectOne('SELECT DB_NAME() AS nombre')->nombre;
        if ($baseReal !== 'GestionPrestamos_Test') {
            throw new \RuntimeException('Operación bloqueada: la base real no es GestionPrestamos_Test.');
        }
    }
}

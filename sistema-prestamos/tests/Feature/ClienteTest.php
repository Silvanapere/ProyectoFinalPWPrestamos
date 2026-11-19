<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Nacionalidad;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    private int $tipoCedulaId;
    private int $nacionalidadId;
    private bool $transaccionIniciada = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Protección: jamás ejecutar estas pruebas sobre GestionPrestamos.
        $nombreConfigurado = config('database.connections.sqlsrv.database');
        $conexionPredeterminada = config('database.default');

        if ($conexionPredeterminada !== 'sqlsrv' || $nombreConfigurado !== 'GestionPrestamos_Test') {
            $this->fail(
                'PRUEBAS BLOQUEADAS: configura DB_CONNECTION=sqlsrv y ' .
                'DB_DATABASE=GestionPrestamos_Test en .env.testing. ' .
                'Conexión actual: ' . $conexionPredeterminada . '; base: ' . ($nombreConfigurado ?? '(sin definir)')
            );
        }

        DB::beginTransaction();
        $this->transaccionIniciada = true;

        $tipo = TipoDocumento::firstOrCreate(
            ['nombre' => 'Cédula'],
            ['mascara' => '000-0000000-0', 'longitud' => 13]
        );
        $nacionalidad = Nacionalidad::firstOrCreate(['nombre' => 'Dominicana']);

        $this->tipoCedulaId = $tipo->id;
        $this->nacionalidadId = $nacionalidad->id;
    }

    protected function tearDown(): void
    {
        if ($this->transaccionIniciada) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    private function datosPersona(string $documento, string $nombre = 'Cliente Prueba'): array
    {
        return [
            'tipo_documento_id' => $this->tipoCedulaId,
            'numero_documento' => $documento,
            'nombres' => $nombre,
            'apellidos' => 'Automatizado',
            'nacionalidad_id' => $this->nacionalidadId,
            'fecha_nacimiento' => '1995-05-10',
            'estado_civil' => 'Soltero/a',
            'correo' => 'pruebas@example.com',
            'direccion' => 'Calle de pruebas',
            'referencia_direccion' => 'Frente al parque',
            'telefonos' => [
                ['tipo' => 'Celular', 'numero' => '8095550101', 'principal' => '1'],
            ],
            'laboral' => [
                'empresa' => 'Empresa de pruebas',
                'cargo_ocupacion' => 'Analista',
                'tiempo_laborando' => 24,
                'sueldo' => 35000,
                'telefono_empresa' => '8095550199',
                'direccion_empresa' => 'Zona industrial',
            ],
        ];
    }

    private function solicitudValida(
        string $documentoCliente = '001-0000000-1',
        string $documentoGarante = '002-0000000-2'
    ): array {
        return array_merge($this->datosPersona($documentoCliente), [
            'limite_credito' => 100000,
            'clasificacion_crediticia' => 'A',
            'referencias' => [
                ['nombre' => 'Referencia 1', 'telefono' => '8095550111'],
                ['nombre' => 'Referencia 2', 'telefono' => '8095550222'],
                ['nombre' => 'Referencia 3', 'telefono' => '8095550333'],
            ],
            'garante' => $this->datosPersona($documentoGarante, 'Garante Prueba'),
        ]);
    }

    private function registrarCliente(): Cliente
    {
        $respuesta = $this->post(route('clientes.store'), $this->solicitudValida());
        $respuesta->assertRedirect(route('clientes.index'));

        return Cliente::where('numero_documento', '001-0000000-1')->firstOrFail();
    }

    public function test_registro_crea_cliente_y_garante_con_relaciones(): void
    {
        $this->registrarCliente();

        $this->assertSame(2, Cliente::count());
        $cliente = Cliente::where('numero_documento', '001-0000000-1')->firstOrFail();
        $this->assertSame('002-0000000-2', $cliente->relacionGarante->garante->numero_documento);
        $this->assertCount(3, $cliente->referencias);
        $this->assertCount(1, $cliente->telefonos);
        $this->assertSame('Empresa de pruebas', $cliente->datosLaborales->empresa);
    }

    public function test_no_permite_registrar_el_mismo_cliente_dos_veces(): void
    {
        $this->registrarCliente();

        $this->post(route('clientes.store'), $this->solicitudValida())
            ->assertSessionHasErrors('numero_documento');
        $this->assertSame(2, Cliente::count());
    }

    public function test_rechaza_que_el_cliente_sea_su_propio_garante(): void
    {
        $datos = $this->solicitudValida('001-0000000-1', '001-0000000-1');
        $this->post(route('clientes.store'), $datos)
            ->assertSessionHasErrors('garante.numero_documento');
        $this->assertSame(0, Cliente::count());
    }

    public function test_rechaza_cedula_con_formato_incorrecto(): void
    {
        $datos = $this->solicitudValida();
        $datos['numero_documento'] = '12345678901';

        $this->post(route('clientes.store'), $datos)
            ->assertSessionHasErrors('numero_documento');
        $this->assertSame(0, Cliente::count());
    }

    public function test_exige_tres_referencias(): void
    {
        $datos = $this->solicitudValida();
        array_pop($datos['referencias']);

        $this->post(route('clientes.store'), $datos)
            ->assertSessionHasErrors('referencias');
        $this->assertSame(0, Cliente::count());
    }

    public function test_exige_un_solo_telefono_principal(): void
    {
        $datos = $this->solicitudValida();
        $datos['telefonos'][] = ['tipo' => 'Casa', 'numero' => '8095550102', 'principal' => '1'];

        $this->post(route('clientes.store'), $datos)
            ->assertSessionHasErrors('telefonos');
        $this->assertSame(0, Cliente::count());
    }

    public function test_busqueda_encuentra_garante_existente(): void
    {
        $this->registrarCliente();

        $this->getJson(route('clientes.buscar-documento', [
            'tipo_documento_id' => $this->tipoCedulaId,
            'numero_documento' => '002-0000000-2',
        ]))->assertOk()->assertJsonPath('encontrado', true)
            ->assertJsonPath('ya_es_cliente', false)
            ->assertJsonPath('persona.nombres', 'Garante Prueba');
    }

    public function test_garante_existente_puede_convertirse_en_cliente_sin_duplicarse(): void
    {
        $this->registrarCliente();
        $datos = $this->solicitudValida('002-0000000-2', '003-0000000-3');
        $datos['nombres'] = 'Garante Prueba';

        $this->post(route('clientes.store'), $datos)
            ->assertRedirect(route('clientes.index'));

        $this->assertSame(3, Cliente::count());
        $persona = Cliente::where('numero_documento', '002-0000000-2')->firstOrFail();
        $this->assertSame(1, Cliente::where('numero_documento', '002-0000000-2')->count());
        $this->assertSame('003-0000000-3', $persona->relacionGarante->garante->numero_documento);
    }

    public function test_modificar_cliente_actualiza_relaciones_sin_crear_otra_persona(): void
    {
        $cliente = $this->registrarCliente();
        $datos = $this->solicitudValida();
        unset($datos['tipo_documento_id'], $datos['numero_documento'], $datos['garante']);
        $datos['nombres'] = 'Nombre Actualizado';
        $datos['estado'] = 'Inactivo';
        $datos['laboral']['sueldo'] = 42000;
        $datos['telefonos'][] = ['tipo' => 'Casa', 'numero' => '8095550102', 'principal' => '0'];
        $datos['referencias'][0]['nombre'] = 'Referencia Actualizada';

        $this->put(route('clientes.update', $cliente->id), $datos)
            ->assertRedirect(route('clientes.index'));

        $cliente->refresh();
        $this->assertSame('Nombre Actualizado', $cliente->nombres);
        $this->assertSame('Inactivo', $cliente->estado);
        $this->assertSame('42000.00', $cliente->datosLaborales->fresh()->sueldo);
        $this->assertCount(2, $cliente->telefonos()->get());
        $this->assertSame('Referencia Actualizada', $cliente->referencias()->first()->nombre);
        $this->assertSame(2, Cliente::count());
    }

    public function test_activar_e_inactivar_no_elimina_clientes(): void
    {
        $cliente = $this->registrarCliente();

        $this->patch(route('clientes.estado', $cliente->id))
            ->assertRedirect(route('clientes.index'));
        $this->assertSame('Inactivo', $cliente->fresh()->estado);

        $this->patch(route('clientes.estado', $cliente->id))
            ->assertRedirect(route('clientes.index'));
        $this->assertSame('Activo', $cliente->fresh()->estado);
        $this->assertSame(2, Cliente::count());
    }
}

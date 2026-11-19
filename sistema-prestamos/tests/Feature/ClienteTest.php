<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Nacionalidad;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Rules\TelefonoDominicano;
use App\Services\DocumentosClientes;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    private int $tipoCedulaId;

    private int $nacionalidadId;

    private bool $transaccionIniciada = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Nunca ejecutar estas pruebas sobre la base de producción.
        $nombreConfigurado = config('database.connections.sqlsrv.database');
        $conexionPredeterminada = config('database.default');

        if ($conexionPredeterminada !== 'sqlsrv' || $nombreConfigurado !== 'GestionPrestamos_Test') {
            $this->fail(
                'PRUEBAS BLOQUEADAS: configura DB_CONNECTION=sqlsrv y '.
                'DB_DATABASE=GestionPrestamos_Test en .env.testing. '.
                'Conexión actual: '.$conexionPredeterminada.'; base: '.($nombreConfigurado ?? '(sin definir)')
            );
        }

        if (DB::connection()->getDatabaseName() !== 'GestionPrestamos_Test') {
            $this->fail('PRUEBAS BLOQUEADAS: la conexión real no apunta a GestionPrestamos_Test.');
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

        // Las rutas de clientes ahora requieren iniciar sesión.
        $usuario = User::create([
            'name' => 'Administrador de pruebas',
            'email' => 'admin@prestamos.local',
            'password' => Hash::make('ClaveDePruebaSoloParaTests'),
        ]);
        $this->actingAs($usuario);
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

    private function contenidoJpegFicticio(): string
    {
        return base64_decode(
            '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAACAAIDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwDqqKKK4TsP/9k='
        );
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

    public function test_registro_guarda_misma_imagen_ficticia_para_cliente_y_garante(): void
    {
        Storage::fake('local');

        // JPEG ficticio pequeño: el mismo contenido para ambas personas.
        $contenido = $this->contenidoJpegFicticio();

        $datos = $this->solicitudValida();
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('cedula.jpg', $contenido);
        $datos['documento_garante'] = UploadedFile::fake()->createWithContent('cedula.jpg', $contenido);

        $this->post(route('clientes.store'), $datos)
            ->assertRedirect(route('clientes.index'));

        $cliente = Cliente::where('numero_documento', '001-0000000-1')->firstOrFail();
        $garante = $cliente->relacionGarante->garante;

        $this->assertSame(2, Cliente::count());
        $this->assertNotNull($cliente->documento_archivo);
        $this->assertNotNull($garante->documento_archivo);
        $this->assertNotSame($cliente->documento_archivo, $garante->documento_archivo);
        Storage::disk('local')->assertExists($cliente->documento_archivo);
        Storage::disk('local')->assertExists($garante->documento_archivo);
        $this->assertSame($contenido, Storage::disk('local')->get($cliente->documento_archivo));
        $this->assertSame($contenido, Storage::disk('local')->get($garante->documento_archivo));

        $this->get(route('clientes.documento', $cliente->id))->assertOk();
        $this->get(route('clientes.documento', $garante->id))->assertOk();
    }

    public function test_usuario_no_autorizado_no_puede_ver_documento(): void
    {
        $cliente = $this->registrarCliente();
        $cliente->update(['documento_archivo' => 'documentos_clientes/ejemplo.jpg']);

        // La ruta auth debe enviar al login si no hay sesión.
        Auth::logout();
        $this->get(route('clientes.documento', $cliente->id))
            ->assertRedirect(route('login'));

        // Una cuenta sin permiso no debe ver el documento, aunque conozca la URL.
        $usuario = User::create([
            'name' => 'Usuario sin permiso',
            'email' => 'sin-permiso@example.com',
            'password' => Hash::make('ClaveDePruebaSoloParaTests'),
        ]);
        $this->actingAs($usuario);
        $this->get(route('clientes.documento', $cliente->id))->assertForbidden();
    }

    public function test_reemplazar_cedula_conserva_nueva_y_elimina_anterior(): void
    {
        Storage::fake('local');
        $cliente = $this->registrarCliente();

        $datos = $this->solicitudValida();
        unset($datos['tipo_documento_id'], $datos['numero_documento'], $datos['garante']);
        $datos['estado'] = 'Activo';

        $contenido = $this->contenidoJpegFicticio();
        $datos['documento_archivo'] = UploadedFile::fake()
            ->createWithContent('cedula-anterior.jpg', $contenido);
        $this->put(route('clientes.update', $cliente->id), $datos)
            ->assertRedirect(route('clientes.index'));

        $rutaAnterior = $cliente->fresh()->documento_archivo;
        $this->assertNotNull($rutaAnterior);
        Storage::disk('local')->assertExists($rutaAnterior);

        $datos['documento_archivo'] = UploadedFile::fake()
            ->createWithContent('cedula-nueva.jpg', $contenido);
        $this->put(route('clientes.update', $cliente->id), $datos)
            ->assertRedirect(route('clientes.index'));

        $rutaNueva = $cliente->fresh()->documento_archivo;
        $this->assertNotNull($rutaNueva);
        $this->assertNotSame($rutaAnterior, $rutaNueva);
        Storage::disk('local')->assertExists($rutaNueva);
        Storage::disk('local')->assertMissing($rutaAnterior);
        $this->assertSame(2, Cliente::count());
    }

    public function test_no_permite_registrar_cliente_sin_nombre(): void
    {
        $datos = $this->solicitudValida();

        $datos['nombres'] = '';

        $this->post(route('clientes.store'), $datos)
            ->assertSessionHasErrors('nombres');

        $this->assertSame(0, Cliente::count());
    }

    public function test_rechaza_campos_obligatorios_vacios(): void
    {
        $campos = [
            'apellidos',
            'correo',
            'direccion',
            'fecha_nacimiento',
            'limite_credito',
            'clasificacion_crediticia',
        ];

        foreach ($campos as $campo) {
            $datos = $this->solicitudValida();
            $datos[$campo] = '';

            $this->post(route('clientes.store'), $datos)
                ->assertSessionHasErrors($campo);

            $this->assertSame(0, Cliente::count());
        }
    }

    public function test_rechaza_valores_invalidos_del_cliente(): void
    {
        $casos = [
            ['correo', 'correo-invalido'],
            ['fecha_nacimiento', '2030-01-01'],
            ['limite_credito', -100],
            ['clasificacion_crediticia', 'Z'],
            ['laboral.sueldo', -500],
        ];

        foreach ($casos as [$campo, $valor]) {
            $datos = $this->solicitudValida();

            data_set($datos, $campo, $valor);

            $this->post(route('clientes.store'), $datos)
                ->assertSessionHasErrors($campo);

            $this->assertSame(0, Cliente::count());
        }
    }

    public function test_telefonos_admiten_formatos_dominicanos_y_rechazan_texto(): void
    {
        foreach (['8095550101', '829-555-0101', '(849) 555-0101', '+1 (809) 555-0101', '18095550101'] as $numero) {
            $validador = Validator::make(['telefono' => $numero], [
                'telefono' => [new TelefonoDominicano],
            ]);
            $this->assertFalse($validador->fails(), $numero);
        }
        foreach (['abc', '809', '809555010199', '0005550101'] as $numero) {
            $datos = $this->solicitudValida();
            $datos['telefonos'][0]['numero'] = $numero;
            $this->post(route('clientes.store'), $datos)->assertSessionHasErrors('telefonos.0.numero');
        }
        $this->assertSame(0, Cliente::count());
    }

    public function test_errores_del_garante_identifican_sus_campos(): void
    {
        $datos = $this->solicitudValida();
        $datos['garante']['nombres'] = '';
        $this->post(route('clientes.store'), $datos)
            ->assertSessionHasErrors('garante.nombres')
            ->assertSessionHas('errors', fn ($errores) => str_contains($errores->first('garante.nombres'), 'del garante'));
        $this->assertSame(0, Cliente::count());
    }

    public function test_rechaza_cero_principales_y_familiares_repetidos(): void
    {
        $datos = $this->solicitudValida();
        $datos['telefonos'][0]['principal'] = '0';
        $this->post(route('clientes.store'), $datos)->assertSessionHasErrors('telefonos');
        $datos = $this->solicitudValida();
        $familiar = ['tipo' => 'Cercano', 'nombre' => 'Familiar ficticio', 'parentesco' => 'Hermano', 'telefono' => '8295550101', 'direccion' => 'Calle ficticia'];
        $datos['familiares'] = [$familiar, $familiar];
        $this->post(route('clientes.store'), $datos)->assertSessionHasErrors('familiares');
        $this->assertSame(0, Cliente::count());
    }

    public function test_cliente_existente_se_reutiliza_como_garante_sin_sobrescribirlo(): void
    {
        $cliente = $this->registrarCliente();
        $datos = $this->solicitudValida('003-0000000-3', $cliente->numero_documento);
        $datos['garante']['nombres'] = 'No debe sobrescribir';
        $this->post(route('clientes.store'), $datos)->assertRedirect(route('clientes.index'));
        $this->assertSame(3, Cliente::count());
        $this->assertSame('Cliente Prueba', $cliente->fresh()->nombres);
        $this->assertSame($cliente->id, (int) Cliente::where('numero_documento', '003-0000000-3')->firstOrFail()->relacionGarante->garante_id);
    }

    public function test_editor_de_garante_conserva_relaciones_y_sustituye_documento(): void
    {
        Storage::fake('local');
        $cliente = $this->registrarCliente();
        $garante = $cliente->relacionGarante->garante;
        Storage::disk('local')->put('documentos_clientes/anterior.jpg', $this->contenidoJpegFicticio());
        $garante->update(['documento_archivo' => 'documentos_clientes/anterior.jpg']);
        $this->get(route('garantes.edit', $garante))->assertOk()->assertSee('Modificar garante')->assertDontSee('name="limite_credito"', false);
        $datos = $this->datosPersona($garante->numero_documento, 'Garante Actualizado');
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('nuevo.jpg', $this->contenidoJpegFicticio());
        $datos['limite_credito'] = 999999;
        $datos['estado'] = 'Inactivo';
        $this->put(route('garantes.update', $garante), $datos)->assertRedirect(route('clientes.index'));
        $garante->refresh();
        $this->assertSame('Garante Actualizado', $garante->nombres);
        $this->assertNull($garante->limite_credito);
        $this->assertSame('Activo', $garante->estado);
        $this->assertSame($garante->id, (int) $cliente->fresh()->relacionGarante->garante_id);
        $this->assertFalse($garante->relacionGarante()->exists());
        Storage::disk('local')->assertMissing('documentos_clientes/anterior.jpg');
        Storage::disk('local')->assertExists($garante->documento_archivo);
        $this->assertSame(2, Cliente::count());
    }

    public function test_carga_rechaza_formato_y_tamano_invalidos_para_ambas_personas(): void
    {
        Storage::fake('local');
        foreach (['documento_archivo', 'documento_garante'] as $campo) {
            foreach ([UploadedFile::fake()->create('archivo.pdf', 10, 'application/pdf'), UploadedFile::fake()->create('grande.jpg', 5121, 'image/jpeg')] as $archivo) {
                $datos = $this->solicitudValida();
                $datos[$campo] = $archivo;
                $this->post(route('clientes.store'), $datos)->assertSessionHasErrors($campo);
            }
        }
        $this->assertSame(0, Cliente::count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_fallo_db_despues_de_guardar_imagen_revierte_y_limpia(): void
    {
        Storage::fake('local');
        $fallar = true;
        Event::listen('eloquent.updated: '.Cliente::class, function ($persona) use (&$fallar) {
            if ($fallar && $persona->documento_archivo) {
                $fallar = false;
                throw new \RuntimeException('Fallo de BD simulado después del archivo');
            }
        });
        $datos = $this->solicitudValida();
        $datos['documento_garante'] = UploadedFile::fake()->createWithContent('garante.jpg', $this->contenidoJpegFicticio());
        $this->withoutExceptionHandling();
        try {
            $this->post(route('clientes.store'), $datos);
            $this->fail('Se esperaba el fallo simulado.');
        } catch (\RuntimeException $error) {
            $this->assertSame('Fallo de BD simulado después del archivo', $error->getMessage());
        }
        $this->assertSame(0, Cliente::count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_fallo_de_almacenamiento_conserva_datos_y_documento_anterior(): void
    {
        $disco = Storage::fake('local');
        $cliente = $this->registrarCliente();
        $disco->put('documentos_clientes/anterior.jpg', $this->contenidoJpegFicticio());
        $cliente->update(['documento_archivo' => 'documentos_clientes/anterior.jpg']);
        $simulado = \Mockery::mock($disco);
        $simulado->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('local')->andReturn($simulado);
        $datos = $this->solicitudValida();
        $datos['nombres'] = 'No debe persistir';
        $datos['estado'] = 'Inactivo';
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('nuevo.jpg', $this->contenidoJpegFicticio());
        $this->put(route('clientes.update', $cliente), $datos)->assertSessionHasErrors('documento_archivo');
        $this->assertSame('Cliente Prueba', $cliente->fresh()->nombres);
        $this->assertSame('Activo', $cliente->fresh()->estado);
        $this->assertSame('documentos_clientes/anterior.jpg', $cliente->fresh()->documento_archivo);
        $disco->assertExists('documentos_clientes/anterior.jpg');
        $this->assertCount(1, $disco->allFiles());
    }

    public function test_conversion_de_garante_elimina_documento_anterior(): void
    {
        Storage::fake('local');
        $cliente = $this->registrarCliente();
        $garante = $cliente->relacionGarante->garante;
        Storage::disk('local')->put('documentos_clientes/anterior.jpg', $this->contenidoJpegFicticio());
        $garante->update(['documento_archivo' => 'documentos_clientes/anterior.jpg']);
        $datos = $this->solicitudValida($garante->numero_documento, '003-0000000-3');
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('nuevo.jpg', $this->contenidoJpegFicticio());
        $this->post(route('clientes.store'), $datos)->assertRedirect(route('clientes.index'));
        Storage::disk('local')->assertMissing('documentos_clientes/anterior.jpg');
        Storage::disk('local')->assertExists($garante->fresh()->documento_archivo);
        $this->assertSame(3, Cliente::count());
    }

    public function test_auditoria_registra_usuario_y_fechas_en_personas_y_relaciones(): void
    {
        $cliente = $this->registrarCliente();
        foreach ([$cliente, $cliente->relacionGarante->garante, $cliente->relacionGarante, $cliente->telefonos->first(), $cliente->datosLaborales, $cliente->referencias->first()] as $registro) {
            $this->assertSame(auth()->id(), (int) $registro->creado_por);
            $this->assertSame(auth()->id(), (int) $registro->modificado_por);
            $this->assertNotNull($registro->created_at);
            $this->assertNotNull($registro->updated_at);
        }
        $creadoPor = $cliente->creado_por;
        $otroUsuario = User::factory()->create();
        $this->actingAs($otroUsuario);
        $cliente->update(['nombres' => 'Cambio ficticio auditado']);
        $this->assertSame((int) $creadoPor, (int) $cliente->fresh()->creado_por);
        $this->assertSame($otroUsuario->id, (int) $cliente->fresh()->modificado_por);
    }

    public function test_usuario_sin_permiso_no_consulta_ni_modifica_el_modulo(): void
    {
        $cliente = $this->registrarCliente();
        $garante = $cliente->relacionGarante->garante;
        $this->actingAs(User::factory()->create());
        foreach (['clientes.index', 'clientes.create'] as $ruta) {
            $this->get(route($ruta))->assertForbidden();
        }
        $this->getJson(route('clientes.buscar-documento', ['tipo_documento_id' => $this->tipoCedulaId, 'numero_documento' => $cliente->numero_documento]))->assertForbidden();
        $this->get(route('clientes.edit', $cliente))->assertForbidden();
        $this->get(route('garantes.edit', $garante))->assertForbidden();
        $this->post(route('clientes.store'), $this->solicitudValida())->assertForbidden();
        $this->put(route('clientes.update', $cliente), [])->assertForbidden();
        $this->put(route('garantes.update', $garante), [])->assertForbidden();
        $this->patch(route('clientes.estado', $cliente))->assertForbidden();
        $this->assertSame('Activo', $cliente->fresh()->estado);
        $this->assertSame(2, Cliente::count());
    }

    public function test_vistas_renderizan_listado_editores_y_documento_ausente(): void
    {
        $this->get(route('clientes.index'))->assertOk()->assertSee('No hay clientes registrados.');
        $cliente = $this->registrarCliente();
        $this->get(route('clientes.index'))->assertOk()->assertSee('seleccionar-cliente', false)->assertSee('sin-resultados-modal', false);
        $this->get(route('clientes.create'))->assertOk()->assertDontSee('novalidate', false);
        $this->get(route('clientes.edit', $cliente))->assertOk();
        $this->get(route('clientes.documento', $cliente))->assertNotFound();
        $this->get(route('garantes.edit', $cliente))->assertNotFound();
    }

    public function test_eliminacion_fallida_queda_pendiente_y_se_reintenta_sin_borrar_referenciados(): void
    {
        $disco = Storage::fake('local');
        $cliente = $this->registrarCliente();
        $disco->put('documentos_clientes/anterior.jpg', $this->contenidoJpegFicticio());
        $cliente->update(['documento_archivo' => 'documentos_clientes/anterior.jpg']);
        $fallar = true;
        $simulado = \Mockery::mock($disco);
        $simulado->shouldReceive('delete')->andReturnUsing(function ($ruta) use (&$fallar, $disco) {
            return $fallar ? false : $disco->delete($ruta);
        });
        Storage::shouldReceive('disk')->with('local')->andReturn($simulado);
        $datos = $this->solicitudValida();
        $datos['estado'] = 'Activo';
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('nuevo.jpg', $this->contenidoJpegFicticio());
        $this->put(route('clientes.update', $cliente), $datos)->assertRedirect(route('clientes.index'));
        $rutaNueva = $cliente->fresh()->documento_archivo;
        $disco->assertExists($rutaNueva);
        $disco->assertExists('documentos_clientes/anterior.jpg');
        $this->assertTrue(DB::table('clientes_documentos_limpieza')->where('ruta', 'documentos_clientes/anterior.jpg')->exists());
        DB::table('clientes_documentos_limpieza')->insert(['ruta' => $rutaNueva, 'updated_at' => now()->subHours(2)]);
        DB::table('clientes_documentos_limpieza')->where('ruta', 'documentos_clientes/anterior.jpg')->update(['updated_at' => now()->subHours(2)]);
        $fallar = false;
        $this->assertSame(0, app(DocumentosClientes::class)->reintentar());
        $disco->assertMissing('documentos_clientes/anterior.jpg');
        $disco->assertExists($rutaNueva);
    }

    public function test_fallo_de_segundo_archivo_limpia_el_primero_sin_crear_personas(): void
    {
        $disco = Storage::fake('local');
        $simulado = \Mockery::mock($disco);
        $llamadas = 0;
        $simulado->shouldReceive('putFileAs')->andReturnUsing(function (...$argumentos) use ($disco, &$llamadas) {
            return ++$llamadas === 2 ? false : $disco->putFileAs(...$argumentos);
        });
        Storage::shouldReceive('disk')->with('local')->andReturn($simulado);
        $datos = $this->solicitudValida();
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('cliente.jpg', $this->contenidoJpegFicticio());
        $datos['documento_garante'] = UploadedFile::fake()->createWithContent('garante.jpg', $this->contenidoJpegFicticio());
        $this->post(route('clientes.store'), $datos)->assertSessionHasErrors('documento_garante');
        $this->assertSame(0, Cliente::count());
        $this->assertSame([], $disco->allFiles());
        $this->assertSame(0, DB::table('clientes_documentos_limpieza')->count());
    }

    public function test_rnc_pasaporte_y_busqueda_con_tipo_incompatible(): void
    {
        foreach ([['RNC', '101234567', '102345678', '000000000', 9], ['Pasaporte', 'AB123456', 'CD123456', 'AAAAAAAA', 20]] as [$nombre, $numero, $numeroGarante, $mascara, $longitud]) {
            $tipo = TipoDocumento::firstOrCreate(['nombre' => $nombre], ['mascara' => $mascara, 'longitud' => $longitud]);
            $datos = $this->solicitudValida($numero, $numeroGarante);
            $datos['tipo_documento_id'] = $tipo->id;
            $datos['garante']['tipo_documento_id'] = $tipo->id;
            $this->post(route('clientes.store'), $datos)->assertRedirect(route('clientes.index'));
            $this->getJson(route('clientes.buscar-documento', ['tipo_documento_id' => $tipo->id, 'numero_documento' => strtolower($numero)]))
                ->assertOk()->assertJsonPath('encontrado', true)->assertJsonPath('ya_es_cliente', true);
        }
        $pasaporte = TipoDocumento::where('nombre', 'Pasaporte')->firstOrFail();
        $this->getJson(route('clientes.buscar-documento', ['tipo_documento_id' => $pasaporte->id, 'numero_documento' => '101234567']))
            ->assertUnprocessable()->assertJsonValidationErrors('tipo_documento_id');
        $this->assertSame(4, Cliente::count());
    }

    public function test_familiares_se_guardan_auditan_y_actualizan_sin_duplicados(): void
    {
        $datos = $this->solicitudValida();
        $datos['familiares'] = [['tipo' => 'Cercano', 'nombre' => 'Familiar ficticio', 'parentesco' => 'Hermano', 'telefono' => '(829) 555-0101', 'direccion' => 'Calle ficticia']];
        $this->post(route('clientes.store'), $datos)->assertRedirect(route('clientes.index'));
        $cliente = Cliente::where('numero_documento', $datos['numero_documento'])->firstOrFail();
        $familiar = $cliente->familiares()->firstOrFail();
        $this->assertSame(auth()->id(), (int) $familiar->creado_por);
        $this->assertSame('Cercano', $familiar->tipo);
        $datos['estado'] = 'Activo';
        $datos['familiares'][0]['nombre'] = 'Familiar actualizado';
        $this->put(route('clientes.update', $cliente), $datos)->assertRedirect(route('clientes.index'));
        $this->assertSame(1, $cliente->familiares()->count());
        $this->assertSame('Familiar actualizado', $cliente->familiares()->firstOrFail()->nombre);
    }

    public function test_invitado_no_puede_operar_clientes_ni_garantes(): void
    {
        $cliente = $this->registrarCliente();
        $garante = $cliente->relacionGarante->garante;
        Auth::logout();
        foreach (['clientes.index', 'clientes.create'] as $ruta) {
            $this->get(route($ruta))->assertRedirect(route('login'));
        }
        $this->get(route('clientes.edit', $cliente))->assertRedirect(route('login'));
        $this->get(route('garantes.edit', $garante))->assertRedirect(route('login'));
        $this->post(route('clientes.store'), $this->solicitudValida())->assertRedirect(route('login'));
        $this->put(route('clientes.update', $cliente), [])->assertRedirect(route('login'));
        $this->put(route('garantes.update', $garante), [])->assertRedirect(route('login'));
        $this->patch(route('clientes.estado', $cliente))->assertRedirect(route('login'));
        $this->assertSame(2, Cliente::count());
    }

    public function test_colision_de_unicidad_se_informa_sin_dejar_archivos(): void
    {
        Storage::fake('local');
        $fallar = true;
        Event::listen('eloquent.created: '.Cliente::class, function () use (&$fallar) {
            if ($fallar) {
                $fallar = false;
                throw new UniqueConstraintViolationException('sqlsrv', 'insert simulado', [], new \PDOException('Colisión de unicidad simulada'));
            }
        });
        $datos = $this->solicitudValida();
        $datos['documento_archivo'] = UploadedFile::fake()->createWithContent('cliente.jpg', $this->contenidoJpegFicticio());
        $this->post(route('clientes.store'), $datos)->assertSessionHasErrors('numero_documento');
        $this->assertSame(0, Cliente::count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_no_permite_trasladar_datos_laborales_mediante_campos_no_autorizados(): void
    {
        $cliente = $this->registrarCliente();
        $garante = $cliente->relacionGarante->garante;
        $datos = $this->solicitudValida();
        $datos['estado'] = 'Activo';
        $datos['laboral']['cliente_id'] = $garante->id;
        $this->put(route('clientes.update', $cliente), $datos)->assertSessionHasErrors('laboral');
        $this->assertSame($cliente->id, (int) $cliente->datosLaborales()->firstOrFail()->cliente_id);
        $this->assertSame($garante->id, (int) $garante->datosLaborales()->firstOrFail()->cliente_id);
        $this->assertSame(2, Cliente::count());
    }
}

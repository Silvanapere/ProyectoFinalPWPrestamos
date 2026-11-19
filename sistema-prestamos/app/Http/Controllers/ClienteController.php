<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Nacionalidad;
use App\Models\TipoDocumento;
use App\Rules\TelefonoDominicano;
use App\Services\DocumentosClientes;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with([
            'tipoDocumento',
            'nacionalidad',
            'telefonos',
            'datosLaborales',
            'referencias',
            'familiares',
            'relacionGarante.garante.telefonos',
        ])
            ->whereHas('relacionGarante')
            ->orderBy('nombres')
            ->get();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();
        $nacionalidades = Nacionalidad::orderBy('nombre')->get();

        return view('clientes.create', compact('tiposDocumento', 'nacionalidades'));
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load([
            'tipoDocumento',
            'nacionalidad',
            'telefonos',
            'datosLaborales',
            'referencias',
            'familiares',
            'relacionGarante.garante.telefonos',
            'relacionGarante.garante.datosLaborales',
        ]);

        if (! $cliente->relacionGarante) {
            return redirect()->route('clientes.index')
                ->with('error', 'La persona seleccionada no tiene un registro de cliente completo.');
        }

        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();
        $nacionalidades = Nacionalidad::orderBy('nombre')->get();

        return view('clientes.edit', compact(
            'cliente',
            'tiposDocumento',
            'nacionalidades'
        ));
    }

    public function buscarDocumento(Request $request)
    {
        $datos = $request->validate([
            'tipo_documento_id' => 'required|integer|exists:tipos_documento,id',
            'numero_documento' => 'required|string|max:30',
        ]);

        $numero = $this->normalizarDocumento($datos['numero_documento']);
        $this->validarFormatoDocumento((int) $datos['tipo_documento_id'], $numero, 'numero_documento');

        $persona = Cliente::with([
            'tipoDocumento', 'nacionalidad', 'telefonos', 'datosLaborales',
            'referencias', 'familiares', 'relacionGarante',
        ])->where('numero_documento', $numero)->first();

        if ($persona && (int) $persona->tipo_documento_id !== (int) $datos['tipo_documento_id']) {
            throw ValidationException::withMessages([
                'tipo_documento_id' => 'Este número pertenece a otro tipo de documento. Verifica el tipo seleccionado.',
            ]);
        }

        return response()->json([
            'encontrado' => $persona !== null,
            'persona' => $persona,
            'ya_es_cliente' => $persona?->relacionGarante !== null,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'tipo_documento_id' => 'required|integer|exists:tipos_documento,id',
            'numero_documento' => 'required|string|max:30',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'nacionalidad_id' => 'required|integer|exists:nacionalidades,id',
            'fecha_nacimiento' => 'required|date|before:today',
            'estado_civil' => 'required|string|max:30',
            'correo' => 'required|email|max:150',
            'direccion' => 'required|string|max:255',
            'referencia_direccion' => 'required|string|max:255',
            'limite_credito' => 'required|numeric|min:0',
            'clasificacion_crediticia' => 'required|in:A,B,C',
            'telefonos' => 'required|array|min:1',
            'telefonos.*.tipo' => 'required|in:Celular,Casa,Trabajo',
            'telefonos.*.numero' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'telefonos.*.principal' => 'required|boolean',
            'laboral' => 'required|array:empresa,cargo_ocupacion,tiempo_laborando,sueldo,telefono_empresa,direccion_empresa',
            'laboral.empresa' => 'required|string|max:150',
            'laboral.cargo_ocupacion' => 'required|string|max:100',
            'laboral.tiempo_laborando' => 'required|integer|min:0',
            'laboral.sueldo' => 'required|numeric|min:0',
            'laboral.telefono_empresa' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'laboral.direccion_empresa' => 'required|string|max:255',
            'referencias' => 'required|array|size:3',
            'referencias.*.nombre' => 'required|string|max:150',
            'referencias.*.telefono' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'familiares' => 'sometimes|array|max:2',
            'familiares.*.tipo' => 'required|in:Cercano,No convive',
            'familiares.*.nombre' => 'required|string|max:150',
            'familiares.*.parentesco' => 'required|string|max:50',
            'familiares.*.telefono' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'familiares.*.direccion' => 'required|string|max:255',
            'garante' => 'required|array',
            'garante.tipo_documento_id' => 'required|integer|exists:tipos_documento,id',
            'garante.numero_documento' => 'required|string|max:30',
            'documento_archivo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'documento_garante' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',

        ]);

        $numeroCliente = $this->normalizarDocumento($datos['numero_documento']);
        $numeroGarante = $this->normalizarDocumento($datos['garante']['numero_documento']);
        $this->validarFormatoDocumento((int) $datos['tipo_documento_id'], $numeroCliente, 'numero_documento');
        $this->validarFormatoDocumento((int) $datos['garante']['tipo_documento_id'], $numeroGarante, 'garante.numero_documento');

        if ($numeroCliente === $numeroGarante) {
            throw ValidationException::withMessages([
                'garante.numero_documento' => 'El cliente no puede ser su propio garante.',
            ]);
        }

        $this->validarPrincipal($datos['telefonos'], 'telefonos');
        if (isset($datos['familiares'])) {
            $tipos = array_column($datos['familiares'], 'tipo');
            if (count($tipos) !== count(array_unique($tipos))) {
                throw ValidationException::withMessages([
                    'familiares' => 'No puedes repetir el tipo de familiar.',
                ]);
            }
        }

        try {
            return app(DocumentosClientes::class)->ejecutar(function (&$rutasNuevas, &$rutasAnteriores) use (
                $request,
                $datos,
                $numeroCliente,
                $numeroGarante
            ) {
                $cliente = Cliente::where('numero_documento', $numeroCliente)->lockForUpdate()->first();

                if ($cliente && $cliente->relacionGarante()->exists()) {
                    throw ValidationException::withMessages([
                        'numero_documento' => 'Esta persona ya está registrada como cliente. Debes modificar su registro.',
                    ]);
                }

                if ($cliente) {
                    if ((int) $cliente->tipo_documento_id !== (int) $datos['tipo_documento_id']) {
                        throw ValidationException::withMessages([
                            'tipo_documento_id' => 'El documento existente pertenece a otro tipo de documento.',
                        ]);
                    }
                    // Reutilizamos los datos de la persona existente sin sobrescribirlos.
                    $cliente->update([
                        'limite_credito' => $datos['limite_credito'],
                        'clasificacion_crediticia' => $datos['clasificacion_crediticia'],
                    ]);
                } else {
                    $cliente = Cliente::create([
                        'tipo_documento_id' => $datos['tipo_documento_id'],
                        'numero_documento' => $numeroCliente,
                        'nombres' => $datos['nombres'],
                        'apellidos' => $datos['apellidos'],
                        'nacionalidad_id' => $datos['nacionalidad_id'],
                        'fecha_nacimiento' => $datos['fecha_nacimiento'],
                        'estado_civil' => $datos['estado_civil'],
                        'correo' => $datos['correo'],
                        'direccion' => $datos['direccion'],
                        'referencia_direccion' => $datos['referencia_direccion'],
                        'limite_credito' => $datos['limite_credito'],
                        'clasificacion_crediticia' => $datos['clasificacion_crediticia'],
                        'estado' => 'Activo',
                    ]);
                    $cliente->telefonos()->createMany($datos['telefonos']);
                    $cliente->datosLaborales()->create($datos['laboral']);
                }

                // Las referencias corresponden al registro como cliente.
                $cliente->referencias()->delete();
                $cliente->referencias()->createMany($datos['referencias']);

                foreach ($datos['familiares'] ?? [] as $familiar) {
                    $cliente->familiares()->updateOrCreate(
                        ['tipo' => $familiar['tipo']],
                        $familiar
                    );
                }

                $garante = Cliente::where('numero_documento', $numeroGarante)->lockForUpdate()->first();
                if ($garante) {
                    if ($request->hasFile('documento_garante')) {
                        throw ValidationException::withMessages(['documento_garante' => 'El garante ya existe. Sustituye su documento desde Modificar garante.']);
                    }
                    if ((int) $garante->tipo_documento_id !== (int) $datos['garante']['tipo_documento_id']) {
                        throw ValidationException::withMessages([
                            'garante.tipo_documento_id' => 'El documento del garante pertenece a otro tipo de documento.',
                        ]);
                    }
                } else {
                    $nuevosDatos = Validator::make(['garante' => $request->input('garante', [])], [
                        'garante.nombres' => 'required|string|max:100',
                        'garante.apellidos' => 'required|string|max:100',
                        'garante.nacionalidad_id' => 'required|integer|exists:nacionalidades,id',
                        'garante.fecha_nacimiento' => 'required|date|before:today',
                        'garante.estado_civil' => 'required|string|max:30',
                        'garante.correo' => 'required|email|max:150',
                        'garante.direccion' => 'required|string|max:255',
                        'garante.referencia_direccion' => 'required|string|max:255',
                        'garante.telefonos' => 'required|array|min:1',
                        'garante.telefonos.*.tipo' => 'required|in:Celular,Casa,Trabajo',
                        'garante.telefonos.*.numero' => ['required', 'string', 'max:20', new TelefonoDominicano],
                        'garante.telefonos.*.principal' => 'required|boolean',
                        'garante.laboral' => 'required|array:empresa,cargo_ocupacion,tiempo_laborando,sueldo,telefono_empresa,direccion_empresa',
                        'garante.laboral.empresa' => 'required|string|max:150',
                        'garante.laboral.cargo_ocupacion' => 'required|string|max:100',
                        'garante.laboral.tiempo_laborando' => 'required|integer|min:0',
                        'garante.laboral.sueldo' => 'required|numeric|min:0',
                        'garante.laboral.telefono_empresa' => ['required', 'string', 'max:20', new TelefonoDominicano],
                        'garante.laboral.direccion_empresa' => 'required|string|max:255',
                    ], [], $this->atributosGarante())->validate()['garante'];
                    $this->validarPrincipal($nuevosDatos['telefonos'], 'garante.telefonos');
                    $garante = Cliente::create([
                        'tipo_documento_id' => $datos['garante']['tipo_documento_id'],
                        'numero_documento' => $numeroGarante,
                        'nombres' => $nuevosDatos['nombres'],
                        'apellidos' => $nuevosDatos['apellidos'],
                        'nacionalidad_id' => $nuevosDatos['nacionalidad_id'],
                        'fecha_nacimiento' => $nuevosDatos['fecha_nacimiento'],
                        'estado_civil' => $nuevosDatos['estado_civil'],
                        'correo' => $nuevosDatos['correo'],
                        'direccion' => $nuevosDatos['direccion'],
                        'referencia_direccion' => $nuevosDatos['referencia_direccion'],
                        'estado' => 'Activo',
                    ]);
                    $garante->telefonos()->createMany($nuevosDatos['telefonos']);
                    $garante->datosLaborales()->create($nuevosDatos['laboral']);
                    if ($request->hasFile('documento_garante')) {
                        app(DocumentosClientes::class)->sustituir($garante, $request->file('documento_garante'), $rutasNuevas, $rutasAnteriores);
                    }
                }

                $cliente->relacionGarante()->create([
                    'garante_id' => $garante->id,
                    'fecha_asignacion' => now()->toDateString(),
                ]);

                if ($request->hasFile('documento_archivo')) {
                    app(DocumentosClientes::class)->sustituir($cliente, $request->file('documento_archivo'), $rutasNuevas, $rutasAnteriores);
                }

                return redirect()->route('clientes.index')
                    ->with('success', 'Cliente y garante registrados correctamente.');
            }, ['documento_archivo' => $request->file('documento_archivo'), 'documento_garante' => $request->file('documento_garante')]);
        } catch (UniqueConstraintViolationException $error) {
            throw ValidationException::withMessages([
                'numero_documento' => 'El cliente o garante fue registrado por otra operación. Vuelve a buscar ambos documentos antes de guardar.',
            ]);
        }
    }

    public function update(Request $request, Cliente $cliente, bool $modoGarante = false)
    {
        if (! $modoGarante && ! $cliente->relacionGarante()->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'La persona no tiene un registro de cliente completo.');
        }

        $reglas = [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'nacionalidad_id' => 'required|exists:nacionalidades,id',
            'fecha_nacimiento' => 'required|date|before:today',
            'estado_civil' => 'required|string|max:30',
            'correo' => 'required|email|max:150',
            'direccion' => 'required|string|max:255',
            'referencia_direccion' => 'required|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',

            'limite_credito' => 'required|numeric|min:0',
            'clasificacion_crediticia' => 'required|in:A,B,C',

            'telefonos' => 'required|array|min:1',
            'telefonos.*.tipo' => 'required|in:Celular,Casa,Trabajo',
            'telefonos.*.numero' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'telefonos.*.principal' => 'required|boolean',

            'laboral' => 'required|array:empresa,cargo_ocupacion,tiempo_laborando,sueldo,telefono_empresa,direccion_empresa',
            'laboral.empresa' => 'required|string|max:150',
            'laboral.cargo_ocupacion' => 'required|string|max:100',
            'laboral.tiempo_laborando' => 'required|integer|min:0',
            'laboral.sueldo' => 'required|numeric|min:0',
            'laboral.telefono_empresa' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'laboral.direccion_empresa' => 'required|string|max:255',

            'referencias' => 'required|array|size:3',
            'referencias.*.nombre' => 'required|string|max:150',
            'referencias.*.telefono' => ['required', 'string', 'max:20', new TelefonoDominicano],

            'familiares' => 'sometimes|array|max:2',
            'familiares.*.tipo' => 'required|in:Cercano,No convive',
            'familiares.*.nombre' => 'required|string|max:150',
            'familiares.*.parentesco' => 'required|string|max:50',
            'familiares.*.telefono' => ['required', 'string', 'max:20', new TelefonoDominicano],
            'familiares.*.direccion' => 'required|string|max:255',

            'documento_archivo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',

        ];
        if ($modoGarante) {
            unset($reglas['estado'], $reglas['limite_credito'], $reglas['clasificacion_crediticia']);
            foreach (array_keys($reglas) as $campo) {
                if (str_starts_with($campo, 'referencias') || str_starts_with($campo, 'familiares')) {
                    unset($reglas[$campo]);
                }
            }
        }
        $datos = $request->validate($reglas);

        $principales = collect($datos['telefonos'])
            ->filter(fn ($telefono) => (int) $telefono['principal'] === 1)
            ->count();

        if ($principales !== 1) {
            throw ValidationException::withMessages([
                'telefonos' => 'Debes seleccionar exactamente un teléfono principal.',
            ]);
        }

        $tiposFamiliares = array_column($datos['familiares'] ?? [], 'tipo');

        if (count($tiposFamiliares) !== count(array_unique($tiposFamiliares))) {
            throw ValidationException::withMessages([
                'familiares' => 'No puedes repetir el tipo de familiar.',
            ]);
        }

        app(DocumentosClientes::class)->ejecutar(function (&$rutasNuevas, &$rutasAnteriores) use ($cliente, $datos, $request, $modoGarante) {
            $cliente = Cliente::whereKey($cliente->id)->lockForUpdate()->firstOrFail();

            $atributos = [
                'nombres' => $datos['nombres'],
                'apellidos' => $datos['apellidos'],
                'nacionalidad_id' => $datos['nacionalidad_id'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'estado_civil' => $datos['estado_civil'],
                'correo' => $datos['correo'],
                'direccion' => $datos['direccion'],
                'referencia_direccion' => $datos['referencia_direccion'],

            ];
            if (! $modoGarante) {
                $atributos += array_intersect_key($datos, array_flip(['estado', 'limite_credito', 'clasificacion_crediticia']));
            }
            $cliente->update($atributos);

            $cliente->telefonos()->delete();
            $cliente->telefonos()->createMany(array_values($datos['telefonos']));

            $cliente->datosLaborales()->updateOrCreate(
                ['cliente_id' => $cliente->id],
                $datos['laboral']
            );

            if (! $modoGarante) {
                $cliente->referencias()->delete();
                $cliente->referencias()->createMany(array_values($datos['referencias']));
                $cliente->familiares()->delete();
                if (! empty($datos['familiares'])) {
                    $cliente->familiares()->createMany(array_values($datos['familiares']));
                }
            }
            if ($request->hasFile('documento_archivo')) {
                app(DocumentosClientes::class)->sustituir($cliente, $request->file('documento_archivo'), $rutasNuevas, $rutasAnteriores);
            }
        }, ['documento_archivo' => $request->file('documento_archivo')]);

        return redirect()->route('clientes.index')
            ->with('success', $modoGarante ? 'Garante actualizado correctamente.' : 'Cliente actualizado correctamente.');
    }

    public function editGarante(Cliente $garante): View
    {
        abort_unless($garante->clientesGarantizados()->exists(), 404);
        $garante->load(['tipoDocumento', 'nacionalidad', 'telefonos', 'datosLaborales', 'referencias', 'familiares']);

        return view('clientes.edit', [
            'cliente' => $garante,
            'modoGarante' => true,
            'tiposDocumento' => TipoDocumento::orderBy('nombre')->get(),
            'nacionalidades' => Nacionalidad::orderBy('nombre')->get(),
        ]);
    }

    public function updateGarante(Request $request, Cliente $garante): RedirectResponse
    {
        abort_unless($garante->clientesGarantizados()->exists(), 404);

        return $this->update($request, $garante, true);
    }

    private function atributosGarante(): array
    {
        return collect([
            'nombres' => 'nombres', 'apellidos' => 'apellidos', 'nacionalidad_id' => 'nacionalidad',
            'fecha_nacimiento' => 'fecha de nacimiento', 'estado_civil' => 'estado civil',
            'correo' => 'correo', 'direccion' => 'dirección', 'referencia_direccion' => 'referencia de dirección',
            'telefonos' => 'teléfonos', 'telefonos.*.numero' => 'número de teléfono',
            'telefonos.*.tipo' => 'tipo de teléfono', 'telefonos.*.principal' => 'teléfono principal',
            'laboral' => 'datos laborales', 'laboral.empresa' => 'empresa',
            'laboral.cargo_ocupacion' => 'cargo', 'laboral.tiempo_laborando' => 'tiempo laborando',
            'laboral.sueldo' => 'sueldo', 'laboral.telefono_empresa' => 'teléfono de empresa',
            'laboral.direccion_empresa' => 'dirección de empresa',
        ])->mapWithKeys(fn ($etiqueta, $campo) => ['garante.'.$campo => $etiqueta.' del garante'])->all();
    }

    private function normalizarDocumento(string $numero): string
    {
        return strtoupper(trim($numero));
    }

    private function validarFormatoDocumento(int $tipoId, string $numero, string $campo): void
    {
        $tipo = TipoDocumento::findOrFail($tipoId);
        $patron = match ($tipo->nombre) {
            'Cédula' => '/^\d{3}-\d{7}-\d$/',
            'RNC' => '/^\d{9}$/',
            'Pasaporte' => '/^[A-Z0-9]{6,20}$/',
            default => null,
        };

        if ($patron === null || ! preg_match($patron, $numero)) {
            throw ValidationException::withMessages([
                $campo => 'El documento no cumple con el formato establecido para '.$tipo->nombre.'.',
            ]);
        }
    }

    private function validarPrincipal(array $telefonos, string $campo): void
    {
        $principales = collect($telefonos)->where('principal', true)->count();
        if ($principales !== 1) {
            throw ValidationException::withMessages([
                $campo => 'Debes indicar exactamente un teléfono principal.',
            ]);
        }
    }

    public function cambiarEstado(Cliente $cliente)
    {
        if (! $cliente->relacionGarante()->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'Esta persona no tiene un registro de cliente completo.');
        }

        $cliente->update([
            'estado' => $cliente->estado === 'Activo' ? 'Inactivo' : 'Activo',
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Estado del cliente actualizado correctamente.');
    }

    public function verDocumento(Cliente $cliente)
    {
        Gate::authorize('ver-documentos-clientes');

        if (! $cliente->documento_archivo) {
            abort(404, 'Este cliente no tiene un documento registrado.');
        }

        $disco = Storage::disk('local');
        $ruta = $cliente->documento_archivo;

        if (! $disco->exists($ruta)) {
            abort(404, 'No se encontró el documento.');
        }

        return response()->file($disco->path($ruta), [
            'Content-Type' => $disco->mimeType($ruta),
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

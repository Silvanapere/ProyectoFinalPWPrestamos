<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Nacionalidad;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ClienteController extends Controller
{
    public function index()
    {
    $clientes = \App\Models\Cliente::with([
        'tipoDocumento',
        'nacionalidad',
        'telefonos',
        'datosLaborales',
        'referencias',
        'familiares',
        'relacionGarante.garante.telefonos'
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
            'relacionGarante.garante.datosLaborales'
        ]);

        if (!$cliente->relacionGarante) {
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
            'telefonos.*.numero' => 'required|string|max:20',
            'telefonos.*.principal' => 'required|boolean',
            'laboral' => 'required|array',
            'laboral.empresa' => 'required|string|max:150',
            'laboral.cargo_ocupacion' => 'required|string|max:100',
            'laboral.tiempo_laborando' => 'required|integer|min:0',
            'laboral.sueldo' => 'required|numeric|min:0',
            'laboral.telefono_empresa' => 'required|string|max:20',
            'laboral.direccion_empresa' => 'required|string|max:255',
            'referencias' => 'required|array|size:3',
            'referencias.*.nombre' => 'required|string|max:150',
            'referencias.*.telefono' => 'required|string|max:20',
            'familiares' => 'sometimes|array|max:2',
            'familiares.*.tipo' => 'required|in:Cercano,No convive',
            'familiares.*.nombre' => 'required|string|max:150',
            'familiares.*.parentesco' => 'required|string|max:50',
            'familiares.*.telefono' => 'required|string|max:20',
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

        $rutasNuevas = [];

        try {
            return DB::transaction(function () use (
                $request,
                $datos,
                $numeroCliente,
                $numeroGarante,
                &$rutasNuevas
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
                if ((int) $garante->tipo_documento_id !== (int) $datos['garante']['tipo_documento_id']) {
                    throw ValidationException::withMessages([
                        'garante.tipo_documento_id' => 'El documento del garante pertenece a otro tipo de documento.',
                    ]);
                }
            } else {
                $nuevosDatos = Validator::make($request->input('garante', []), [
                    'nombres' => 'required|string|max:100',
                    'apellidos' => 'required|string|max:100',
                    'nacionalidad_id' => 'required|integer|exists:nacionalidades,id',
                    'fecha_nacimiento' => 'required|date|before:today',
                    'estado_civil' => 'required|string|max:30',
                    'correo' => 'required|email|max:150',
                    'direccion' => 'required|string|max:255',
                    'referencia_direccion' => 'required|string|max:255',
                    'telefonos' => 'required|array|min:1',
                    'telefonos.*.tipo' => 'required|in:Celular,Casa,Trabajo',
                    'telefonos.*.numero' => 'required|string|max:20',
                    'telefonos.*.principal' => 'required|boolean',
                    'laboral' => 'required|array',
                    'laboral.empresa' => 'required|string|max:150',
                    'laboral.cargo_ocupacion' => 'required|string|max:100',
                    'laboral.tiempo_laborando' => 'required|integer|min:0',
                    'laboral.sueldo' => 'required|numeric|min:0',
                    'laboral.telefono_empresa' => 'required|string|max:20',
                    'laboral.direccion_empresa' => 'required|string|max:255',
                ])->validate();
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
                $rutaDocumentoGarante = $request->file('documento_garante')
                    ->store('documentos_clientes', 'local');

                $garante->update([
                    'documento_archivo' => $rutaDocumentoGarante,
                ]);
            }
            }

            $cliente->relacionGarante()->create([
                'garante_id' => $garante->id,
                'fecha_asignacion' => now()->toDateString(),
            ]);

            if ($request->hasFile('documento_archivo')) {
            $rutaDocumento = $request->file('documento_archivo')
                ->store('documentos_clientes', 'local');

            $cliente->update([
                'documento_archivo' => $rutaDocumento,
            ]);
        }

                       return redirect()->route('clientes.index')
                ->with('success', 'Cliente y garante registrados correctamente.');

        });

    } catch (\Throwable $e) {
        foreach ($rutasNuevas as $ruta) {
            Storage::disk('local')->delete($ruta);
        }

        throw $e;
    }
}


public function update(Request $request, Cliente $cliente)
{
    if (!$cliente->relacionGarante()->exists()) {
        return redirect()->route('clientes.index')
            ->with('error', 'La persona no tiene un registro de cliente completo.');
    }

    $datos = $request->validate([
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
        'telefonos.*.numero' => 'required|string|max:20',
        'telefonos.*.principal' => 'required|boolean',

        'laboral' => 'required|array',
        'laboral.empresa' => 'required|string|max:150',
        'laboral.cargo_ocupacion' => 'required|string|max:100',
        'laboral.tiempo_laborando' => 'required|integer|min:0',
        'laboral.sueldo' => 'required|numeric|min:0',
        'laboral.telefono_empresa' => 'required|string|max:20',
        'laboral.direccion_empresa' => 'required|string|max:255',

        'referencias' => 'required|array|size:3',
        'referencias.*.nombre' => 'required|string|max:150',
        'referencias.*.telefono' => 'required|string|max:20',

        'familiares' => 'sometimes|array|max:2',
        'familiares.*.tipo' => 'required|in:Cercano,No convive',
        'familiares.*.nombre' => 'required|string|max:150',
        'familiares.*.parentesco' => 'required|string|max:50',
        'familiares.*.telefono' => 'required|string|max:20',
        'familiares.*.direccion' => 'required|string|max:255',

        'documento_archivo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
    
    ]);

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

    DB::transaction(function () use ($cliente, $datos) {

        $cliente->update([
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'nacionalidad_id' => $datos['nacionalidad_id'],
            'fecha_nacimiento' => $datos['fecha_nacimiento'],
            'estado_civil' => $datos['estado_civil'],
            'correo' => $datos['correo'],
            'direccion' => $datos['direccion'],
            'referencia_direccion' => $datos['referencia_direccion'],
            'estado' => $datos['estado'],
            'limite_credito' => $datos['limite_credito'],
            'clasificacion_crediticia' => $datos['clasificacion_crediticia'],
        ]);

        $cliente->telefonos()->delete();
        $cliente->telefonos()->createMany(array_values($datos['telefonos']));

        $cliente->datosLaborales()->updateOrCreate(
            ['cliente_id' => $cliente->id],
            $datos['laboral']
        );

        $cliente->referencias()->delete();
        $cliente->referencias()->createMany(array_values($datos['referencias']));

        $cliente->familiares()->delete();

        if (!empty($datos['familiares'])) {
            $cliente->familiares()->createMany(array_values($datos['familiares']));
        }
    });

    if ($request->hasFile('documento_archivo')) {
    $rutaAnterior = $cliente->documento_archivo;

    $rutaNueva = $request->file('documento_archivo')
        ->store('documentos_clientes', 'local');

    try {
        $cliente->update([
            'documento_archivo' => $rutaNueva,
        ]);
    } catch (\Throwable $e) {
        Storage::disk('local')->delete($rutaNueva);
        throw $e;
    }

    if ($rutaAnterior) {
        Storage::disk('local')->delete($rutaAnterior);
    }
}

    return redirect()->route('clientes.index')
        ->with('success', 'Cliente actualizado correctamente.');
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

        if ($patron === null || !preg_match($patron, $numero)) {
            throw ValidationException::withMessages([
                $campo => 'El documento no cumple con el formato establecido para ' . $tipo->nombre . '.',
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
    if (!$cliente->relacionGarante()->exists()) {
        return redirect()->route('clientes.index')
            ->with('error', 'Esta persona no tiene un registro de cliente completo.');
    }

    $cliente->update([
        'estado' => $cliente->estado === 'Activo' ? 'Inactivo' : 'Activo'
    ]);

    return redirect()->route('clientes.index')
        ->with('success', 'Estado del cliente actualizado correctamente.');
}



public function verDocumento(Cliente $cliente)
{
    Gate::authorize('ver-documentos-clientes');

    if (!$cliente->documento_archivo) {
        abort(404, 'Este cliente no tiene un documento registrado.');
    }

    $disco = Storage::disk('local');
    $ruta = $cliente->documento_archivo;

    if (!$disco->exists($ruta)) {
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
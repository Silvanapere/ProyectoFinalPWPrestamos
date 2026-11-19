@extends('layouts.app')

@section('title', 'Clientes | Gestión de Préstamos')
@section('page-class', 'pagina-clientes-index')

@push('styles')
<link href="{{ asset('css/clientes.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="d-flex">

    <!-- MENÚ LATERAL -->
    @include('partials.menu')

    <!-- CONTENIDO -->
    <main class="contenido">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif       

        <div class="d-flex justify-content-between
                    align-items-center flex-wrap gap-3 mb-4">

            <div>
                <small class="text-secondary">
                    Mantenimientos / Clientes
                </small>

                <h2 class="fw-bold mt-2">
                    Clientes y garantes
                </h2>

                <p class="text-secondary">
                    Administra las personas registradas.
                </p>
            </div>

           <a href="{{ route('clientes.create') }}"
   class="btn btn-principal">
    <i class="bi bi-plus-lg me-2"></i>
    Nuevo cliente
</a>

        </div>

        <!-- RESUMEN -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="tarjeta resumen">
                    <small>Total de clientes</small>
                    <h2 class="fw-bold mt-2">{{ $clientes->count() }}</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tarjeta">
                    <small>Clientes activos</small>
                    <h2 class="fw-bold mt-2">{{ $clientes->where('estado', 'Activo')->count() }}</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tarjeta">
                    <small>Clientes inactivos</small>
                    <h2 class="fw-bold mt-2">{{ $clientes->where('estado', 'Inactivo')->count() }}</h2>
                </div>
            </div>

        </div>

        <!-- LISTADO -->
        <section class="tarjeta">

            <div class="d-flex justify-content-between
                        align-items-center flex-wrap gap-3 mb-4">

                <h5 class="fw-bold mb-0">
                    Listado de clientes
                </h5>

            <select id="filtro-estado"
                            class="form-select"
                            style="max-width: 180px;">
                    <option value="Todos">Todos los estados</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
             </select>

                <input type="search"
                id="buscar-cliente"
                class="form-control"
                style="max-width: 270px;"
                placeholder="Buscar cliente...">
                

            </div>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>CLIENTE</th>
                            <th>DOCUMENTO</th>
                            <th>TELÉFONO</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                            <th>GESTIÓN</th>
                        </tr>
                    </thead>

                    
<tbody id="tabla-clientes">
    @forelse ($clientes as $cliente)
        <tr data-estado="{{ $cliente->estado }}">
            <td class="fw-semibold">
                {{ $cliente->nombres }} {{ $cliente->apellidos }}
            </td>

            <td>
                {{ $cliente->numero_documento }}
            </td>

            <td>
                {{ $cliente->telefonos->firstWhere('principal', true)?->numero ?? 'Sin teléfono principal' }}
            </td>

            <td>
                <span class="estado">
                    {{ $cliente->estado }}
                </span>
            </td>

            <td>
                <button class="btn btn-sm btn-light"
                    type="button"
                    title="Consultar"
                    data-bs-toggle="modal"
                    data-bs-target="#consultarCliente{{ $cliente->id }}">
                <i class="bi bi-eye"></i>
            </button>

                <button class="btn btn-sm btn-light"
                    type="button"
                    title="Modificar"
                    data-bs-toggle="modal"
                    data-bs-target="#modalSeleccionarCliente">
                <i class="bi bi-pencil"></i>
            </button>
            </td>
                <td>
        <div class="dropdown">
            <button
                class="btn btn-sm btn-light dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-three-dots"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form
                        action="{{ route('clientes.estado', $cliente->id) }}"
                        method="POST"
                        onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este cliente?')">

                        @csrf
                        @method('PATCH')

                        <button type="submit" class="dropdown-item">
                            @if ($cliente->estado === 'Activo')
                                <i class="bi bi-person-x me-2"></i>
                                Inactivar cliente
                            @else
                                <i class="bi bi-person-check me-2"></i>
                                Activar cliente
                            @endif
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-secondary py-4">
                No hay clientes registrados.
            </td>
        </tr>
    @endforelse
</tbody>


                </table>
            </div>        

        </section>

@foreach ($clientes as $cliente)
<div class="modal fade"
     id="consultarCliente{{ $cliente->id }}"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold">
                        Consultar cliente
                    </h5>
                    <small class="text-secondary">
                        Información registrada en el sistema
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body px-4">

                <h6 class="fw-bold mb-3">Datos personales</h6>

                <p><strong>Nombre:</strong>
                    {{ $cliente->nombres }} {{ $cliente->apellidos }}
                </p>

                
                    <p><strong>Documento:</strong>
                        {{ $cliente->tipoDocumento?->nombre }}
                        - {{ $cliente->numero_documento }}
                    </p>

                   
                        @can('ver-documentos-clientes')
                            @if ($cliente->documento_archivo)
                                <div class="mb-4">
                                    <button type="button"
                                            class="btn btn-outline-dark btn-sm rounded-3"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#documentoCliente{{ $cliente->id }}"
                                            aria-expanded="false"
                                            aria-controls="documentoCliente{{ $cliente->id }}">
                                        <i class="bi bi-card-image me-1"></i>
                                        Ver documento de identidad
                                    </button>

                                    <div class="collapse mt-3"
                                        id="documentoCliente{{ $cliente->id }}">
                                        <div class="p-3 rounded-4 text-center"
                                            style="background-color: #f8f6f2; border: 1px solid #e9ddcd;">

                                            <img
                                                src="{{ route('clientes.documento', $cliente->id) }}"
                                                alt="Documento de identidad del cliente"
                                                class="img-fluid rounded-3"
                                                style="max-height: 350px; object-fit: contain;"
                                                loading="lazy"
                                                referrerpolicy="no-referrer">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-secondary small">
                                    No tiene un documento de identidad cargado.
                                </p>
                            @endif
                        @endcan



                <p><strong>Nacionalidad:</strong>
                    {{ $cliente->nacionalidad?->nombre }}
                </p>

                <p><strong>Fecha de nacimiento:</strong>
                    {{ $cliente->fecha_nacimiento?->format('d/m/Y') }}
                </p>

                <p><strong>Estado civil:</strong>
                    {{ $cliente->estado_civil }}
                </p>

                <p><strong>Correo:</strong>
                    {{ $cliente->correo }}
                </p>

                <p><strong>Dirección:</strong>
                    {{ $cliente->direccion }}
                </p>

                <p><strong>Referencia de dirección:</strong>
                    {{ $cliente->referencia_direccion }}
                </p>

                <p><strong>Estado:</strong>
                    {{ $cliente->estado }}
                </p>

                <hr>

                <h6 class="fw-bold mb-3">Teléfonos</h6>

                @foreach ($cliente->telefonos as $telefono)
                    <p>
                        {{ $telefono->tipo }}:
                        {{ $telefono->numero }}
                        @if ($telefono->principal)
                            <span class="text-secondary">(Principal)</span>
                        @endif
                    </p>
                @endforeach

                <hr>

                <h6 class="fw-bold mb-3">Datos laborales</h6>

                @if ($cliente->datosLaborales)
                    <p><strong>Empresa:</strong>
                        {{ $cliente->datosLaborales->empresa }}
                    </p>
                    <p><strong>Cargo:</strong>
                        {{ $cliente->datosLaborales->cargo_ocupacion }}
                    </p>
                    <p><strong>Tiempo laborando:</strong>
                        {{ $cliente->datosLaborales->tiempo_laborando }} meses
                    </p>
                    <p><strong>Sueldo:</strong>
                        RD$ {{ number_format($cliente->datosLaborales->sueldo, 2) }}
                    </p>
                    <p><strong>Teléfono laboral:</strong>
                        {{ $cliente->datosLaborales->telefono_empresa }}
                    </p>
                    <p><strong>Dirección laboral:</strong>
                        {{ $cliente->datosLaborales->direccion_empresa }}
                    </p>
                @else
                    <p class="text-secondary">Sin datos laborales.</p>
                @endif

                <hr>

                <h6 class="fw-bold mb-3">Datos crediticios</h6>

                <p><strong>Límite de crédito:</strong>
                    RD$ {{ number_format($cliente->limite_credito ?? 0, 2) }}
                </p>
                <p><strong>Clasificación:</strong>
                    {{ $cliente->clasificacion_crediticia ?? 'Sin clasificar' }}
                </p>

                <hr>

                <h6 class="fw-bold mb-3">Referencias personales</h6>

                @forelse ($cliente->referencias as $referencia)
                    <p>
                        {{ $referencia->nombre }}
                        — {{ $referencia->telefono }}
                    </p>
                @empty
                    <p class="text-secondary">Sin referencias registradas.</p>
                @endforelse

                <hr>

                <h6 class="fw-bold mb-3">Garante asignado</h6>

                @if ($cliente->relacionGarante?->garante)
                    @php
                        $garante = $cliente->relacionGarante->garante;
                    @endphp

                    <p><strong>Nombre:</strong>
                        {{ $garante->nombres }} {{ $garante->apellidos }}
                    </p>
                    
                    <p><strong>Documento:</strong>
                        {{ $garante->numero_documento }}
                    </p>

                    @can('ver-documentos-clientes')
                        @if ($garante->documento_archivo)
                            <div class="mb-4">
                                <button type="button"
                                        class="btn btn-outline-dark btn-sm rounded-3"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#documentoGarante{{ $cliente->id }}"
                                        aria-expanded="false"
                                        aria-controls="documentoGarante{{ $cliente->id }}">
                                    <i class="bi bi-card-image me-1"></i>
                                    Ver documento del garante
                                </button>

                                <div class="collapse mt-3"
                                    id="documentoGarante{{ $cliente->id }}">
                                    <div class="p-3 rounded-4 text-center"
                                        style="background-color: #f8f6f2; border: 1px solid #e9ddcd;">

                                        <img
                                            src="{{ route('clientes.documento', $garante->id) }}"
                                            alt="Documento de identidad del garante"
                                            class="img-fluid rounded-3"
                                            style="max-height: 350px; object-fit: contain;"
                                            loading="lazy">
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-secondary small">
                                El garante no tiene un documento cargado.
                            </p>
                        @endif
                    @endcan

                    <p><strong>Correo:</strong>
                        {{ $garante->correo }}
                    </p>
                    <p><strong>Dirección:</strong>
                        {{ $garante->direccion }}
                    </p>

                    @foreach ($garante->telefonos as $telefono)
                        <p><strong>Teléfono:</strong>
                            {{ $telefono->numero }}
                        </p>
                    @endforeach
                @else
                    <p class="text-secondary">Sin garante asignado.</p>
                @endif

                <hr>

                <h6 class="fw-bold mb-3">Familiares</h6>

                @forelse ($cliente->familiares as $familiar)
                    <p>
                        <strong>{{ $familiar->tipo }}:</strong>
                        {{ $familiar->nombre }}
                        ({{ $familiar->parentesco }})
                        — {{ $familiar->telefono }}
                        — {{ $familiar->direccion }}
                    </p>
                @empty
                    <p class="text-secondary">
                        Sin familiares registrados.
                    </p>
                @endforelse

            </div>

            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
@endforeach


<!-- Modal para seleccionar cliente -->
<div class="modal fade" id="modalSeleccionarCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold">
                        Seleccionar cliente
                    </h5>
                    <small class="text-secondary">
                        Busca el cliente que deseas modificar.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-4">

                <input type="search"
                       id="buscar-cliente-modal"
                       class="form-control mb-3"
                       placeholder="Buscar por nombre o documento...">

                <div id="lista-clientes-modal" class="list-group">

                    @forelse ($clientes as $cliente)
    <a href="{{ route('clientes.edit', $cliente->id) }}"
       class="list-group-item list-group-item-action py-3">

        <div class="fw-semibold">
            {{ $cliente->nombres }} {{ $cliente->apellidos }}
        </div>

        <small class="text-secondary">
            {{ $cliente->numero_documento }}
        </small>
    </a>
@empty
    <p class="text-secondary text-center py-3">
        No hay clientes registrados.
    </p>
@endforelse

                </div>

            </div>
        </div>
    </div>
</div>

    </main>

</div>
@endsection

@push('scripts')
<script>
    const buscador = document.getElementById('buscar-cliente');

    buscador.addEventListener('input', function() {
        const texto = this.value.toLowerCase().trim();

        document.querySelectorAll('#tabla-clientes tr').forEach(function(fila) {
            const contenido = fila.textContent.toLowerCase();

            fila.style.display = contenido.includes(texto)
                ? ''
                : 'none';
        });
    });
</script>
<script>
    (function () {
        const buscador = document.getElementById('buscar-cliente');
        const filtroEstado = document.getElementById('filtro-estado');
        const filas = document.querySelectorAll('#tabla-clientes tr');

        function filtrarClientes() {
            const texto = buscador.value.toLowerCase().trim();
            const estadoSeleccionado = filtroEstado.value;

            filas.forEach(function (fila) {
                const columnas = fila.querySelectorAll('td');

                if (columnas.length < 5) {
                    return;
                }

                const contenido = fila.textContent.toLowerCase();
                const estadoCliente = columnas[3].textContent.trim();

                const coincideBusqueda = contenido.includes(texto);

                const coincideEstado =
                    estadoSeleccionado === 'Todos' ||
                    estadoCliente === estadoSeleccionado;

                fila.style.display =
                    coincideBusqueda && coincideEstado ? '' : 'none';
            });
        }

        buscador.addEventListener('input', filtrarClientes);
        filtroEstado.addEventListener('change', filtrarClientes);
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

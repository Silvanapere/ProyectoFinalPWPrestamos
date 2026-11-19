<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"

          content="width=device-width, initial-scale=1.0">

    <title>Tipos de préstamo | Gestión de Préstamos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"

          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"

          rel="stylesheet">

    <style>

        body {

            background: #f8f6f2;

            color: #292724;

            font-family: Arial, sans-serif;

        }

        .menu {

            width: 85px;

            min-height: 100vh;

            background: #292724;

            padding: 25px 10px;

            border-radius: 0 25px 25px 0;

            flex-shrink: 0;

        }

        .menu a {

            display: block;

            text-align: center;

            color: #c9c3bd;

            font-size: 22px;

            padding: 12px;

            margin-bottom: 15px;

            border-radius: 14px;

        }

        .menu a:hover,

        .menu a.activo {

            background: #bda68d;

            color: #292724;

        }

        .contenido {

            flex: 1;

            min-width: 0;

            padding: 35px;

        }

        .tarjeta {

            background: white;

            border-radius: 20px;

            padding: 25px;

        }

        .resumen {

            background: #e9ddcd;

        }

        .btn-principal {

            background: #292724;

            color: white;

            border-radius: 12px;

            padding: 11px 20px;

        }

        .btn-principal:hover {

            background: #594c40;

            color: white;

        }

        .form-control {

            border-radius: 12px;

            padding: 11px;

        }

        .table th {

            color: #8e8a84;

            font-size: 12px;

            font-weight: 500;

        }

        .table td {

            padding-top: 18px;

            padding-bottom: 18px;

        }

        .estado {

            background: #ede7dd;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;

        }

        @media (max-width: 650px) {

            .menu {

                width: 65px;

            }

            .contenido {

                padding: 18px;

            }

        }

    </style>

</head>

<body>

<div class="d-flex">
<!-- MENÚ LATERAL -->
    <aside class="menu">

        <a href="/" title="Inicio">

            <i class="bi bi-grid-1x2-fill"></i>

        </a>

        <a href="{{ route('clientes.index') }}"

   title="Clientes y garantes">

    <i class="bi bi-people"></i>

</a>

        <a href="#" class="activo"

           title="Tipos de préstamo">

            <i class="bi bi-tags"></i>

        </a>

        <a href="#" title="Apertura de préstamos">

            <i class="bi bi-cash-stack"></i>

        </a>

        <a href="#" title="Recibos de ingreso">

            <i class="bi bi-receipt"></i>

        </a>

        <a href="#" title="Renovaciones">

            <i class="bi bi-arrow-repeat"></i>

        </a>

    </aside>
<!-- CONTENIDO -->
    <main class="contenido">
        @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

        <div class="d-flex justify-content-between

                    align-items-center flex-wrap gap-3 mb-4">

            <div>

                <small class="text-secondary">

                    Mantenimientos / Tipos de préstamo

                </small>

                <h2 class="fw-bold mt-2">

                    Tipos de préstamo

                </h2>

                <p class="text-secondary mb-0">

                    Administra las modalidades de préstamo.

                </p>

            </div>

            <button type="button"

        class="btn btn-principal"

        data-bs-toggle="modal"

        data-bs-target="#modalNuevoTipo">

    <i class="bi bi-plus-lg me-2"></i>

    Nuevo tipo

</button>

        </div>
<!-- RESUMEN -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">

                <div class="tarjeta resumen">

                    <small>Total de tipos</small>

                    <h2 class="fw-bold mt-2">{{ $tipos->count() }}</h2>

                </div>

            </div>

            <div class="col-md-4">

                <div class="tarjeta">

                    <small>Tipos activos</small>

                    <h2 class="fw-bold mt-2">{{ $tipos->where('estado', 'Activo')->count() }}</h2>

                </div>

            </div>

            <div class="col-md-4">

                <div class="tarjeta">

                    <small>Tipos inactivos</small>

                    <h2 class="fw-bold mt-2">{{ $tipos->where('estado', 'Inactivo')->count() }}</h2>

                </div>

            </div>

        </div>
<!-- LISTADO DE TIPOS -->
        <section class="tarjeta">

            <div class="d-flex justify-content-between

                        align-items-center flex-wrap gap-3 mb-4">

                <h5 class="fw-bold mb-0">

                    Listado de tipos de préstamo

                </h5>

                <input type="search"
                    id="buscar-tipo"
                    class="form-control"
                    style="max-width: 270px;"
                    placeholder="Buscar tipo...">

            </div>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>NOMBRE</th>

                            <th>DESCRIPCIÓN</th>

                            <th>ESTADO</th>

                            <th>ACCIONES</th>

                        </tr>

                    </thead>

                    
<tbody>
    @forelse ($tipos as $tipo)
        <tr>
            <td class="fw-semibold">
                {{ $tipo->nombre }}
            </td>

            <td>{{ $tipo->descripcion }}</td>

            <td>
                <span class="estado">
                    {{ $tipo->estado }}
                </span>
            </td>

            <td>
                <button type="button"
                        class="btn btn-sm btn-light"
                        data-bs-toggle="modal"
                        data-bs-target="#modalConsultaTipo"
                        title="Modificar">
                    <i class="bi bi-pencil"></i>
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4"
                class="text-center text-secondary py-4">
                Todavía no hay tipos de préstamo registrados.
            </td>
        </tr>
    @endforelse
</tbody>


                </table>

            </div>

            

        </section>

    </main>

</div>
<!-- Modal: Nuevo tipo de préstamo -->

<div class="modal fade" id="modalNuevoTipo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">

        <form action="{{ route('tipos-prestamo.store') }}"
              method="POST"
              class="modal-content border-0 rounded-4">

            @csrf

            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold">
                        Nuevo tipo de préstamo
                    </h5>
                    <small class="text-secondary">
                        Complete la información del tipo.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4">

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           maxlength="100"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion"
                              class="form-control"
                              rows="3"
                              maxlength="255"
                              required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado"
                            class="form-select"
                            required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn btn-principal">
                    Guardar
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modal: Consultar tipos de préstamo -->
<div class="modal fade" id="modalConsultaTipo" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0 px-4 pt-4">

                <div>

                    <h5 class="modal-title fw-bold">

                        Seleccionar tipo de préstamo

                    </h5>

                    <small class="text-secondary">

                        Busque el tipo que desea modificar.

                    </small>

                </div>

                <button type="button"

                        class="btn-close"

                        data-bs-dismiss="modal">

                </button>

            </div>

            <div class="modal-body px-4 pb-4">

                <input type="search"

                       id="buscar-tipo-modal"

                       class="form-control mb-3"

                       placeholder="Buscar tipo de préstamo...">

                <div id="lista-tipos-modal" class="list-group">

                    
                    @forelse ($tipos as $tipo)

                        <button type="button"
                                class="list-group-item list-group-item-action btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarTipo"
                                data-nombre="{{ $tipo->nombre }}"
                                data-descripcion="{{ $tipo->descripcion }}"
                                data-estado="{{ $tipo->estado }}"
                                data-url="{{ route('tipos-prestamo.update', $tipo->id) }}">

                            {{ $tipo->nombre }}

                        </button>

                    @empty

                        <p class="text-secondary text-center py-3">
                            No hay tipos de préstamo registrados.
                        </p>

                    @endforelse


                </div>

            </div>

        </div>

    </div>

</div>
<!-- Modal: Modificar tipo de préstamo -->
<div class="modal fade" id="modalEditarTipo" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <form id="formEditarTipo"
              method="POST"
              class="modal-content border-0 rounded-4">

            @csrf
            @method('PUT')

            <!-- Encabezado -->
            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold">
                        Modificar tipo de préstamo
                    </h5>

                    <small class="text-secondary">
                        Actualice la información del tipo.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <!-- Campos -->
            <div class="modal-body px-4">

                <div class="mb-3">
                    <label for="editar-nombre"
                           class="form-label">
                        Nombre
                    </label>

                    <input type="text"
                           id="editar-nombre"
                           name="nombre"
                           class="form-control"
                           maxlength="100"
                           required>
                </div>

                <div class="mb-3">
                    <label for="editar-descripcion"
                           class="form-label">
                        Descripción
                    </label>

                    <textarea id="editar-descripcion"
                              name="descripcion"
                              class="form-control"
                              rows="3"
                              maxlength="255"
                              required></textarea>
                </div>

                <div class="mb-3">
                    <label for="editar-estado"
                           class="form-label">
                        Estado
                    </label>

                    <select id="editar-estado"
                            name="estado"
                            class="form-select"
                            required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

            </div>

            <!-- Botones -->
            <div class="modal-footer border-0 px-4 pb-4">

                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn btn-principal">
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
    const modalEditar = document.getElementById('modalEditarTipo');

    modalEditar.addEventListener('show.bs.modal', function(evento) {

        const boton = evento.relatedTarget;

        document.getElementById('editar-nombre').value =
            boton.dataset.nombre;

        document.getElementById('editar-descripcion').value =
            boton.dataset.descripcion;

        document.getElementById('editar-estado').value =
            boton.dataset.estado;

        document.getElementById('formEditarTipo').action =
            boton.dataset.url;

    });
</script>


<script>
    const buscarModal = document.getElementById('buscar-tipo-modal');

    buscarModal.addEventListener('input', function() {
        const texto = this.value.toLowerCase().trim();

        document.querySelectorAll('#lista-tipos-modal button').forEach(function(boton) {
            const nombre = boton.dataset.nombre.toLowerCase();
            boton.style.display = nombre.includes(texto) ? '' : 'none';
        });
    });

    document.getElementById('modalConsultaTipo').addEventListener('show.bs.modal', function() {
        buscarModal.value = '';
        document.querySelectorAll('#lista-tipos-modal button').forEach(function(boton) {
            boton.style.display = '';
        });
    });
</script>

<script>
    const buscador = document.getElementById('buscar-tipo');
    const filas = document.querySelectorAll('table tbody tr');

    buscador.addEventListener('input', function() {
        const texto = this.value.toLowerCase().trim();

        filas.forEach(function(fila) {
            const contenido = fila.textContent.toLowerCase();

            fila.style.display = contenido.includes(texto)
                ? ''
                : 'none';
        });
    });
</script>

</body>

</html>

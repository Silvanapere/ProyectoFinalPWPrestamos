<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar cliente | Gestión de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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

        .menu a:hover, .menu a.activo {
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
            padding: 28px;
            margin-bottom: 22px;
        }

        .titulo-seccion {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 22px;
        }

        .titulo-seccion i {
            color: #9a8067;
            margin-right: 10px;
        }

        .form-label {
            font-size: 13px;
            color: #68615a;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 12px;
        }

        .btn-principal {
            background: #292724;
            color: white;
            border-radius: 12px;
            padding: 11px 24px;
        }

        .btn-principal:hover {
            background: #594c40;
            color: white;
        }

        .info {
            background: #f8f6f2;
            border-radius: 12px;
            padding: 14px;
            color: #68615a;
        }

        @media (max-width: 650px) {
            .menu { width: 65px; }
            .contenido { padding: 18px; }
            .tarjeta { padding: 20px; }
        }
    </style>
</head>
<body>
    @php
        $telefonosFormulario = old('telefonos', $cliente->telefonos->map(function ($telefono) {
            return [
                'tipo' => $telefono->tipo,
                'numero' => $telefono->numero,
                'principal' => $telefono->principal ? 1 : 0,
            ];
        })->all());

        $laboral = $cliente->datosLaborales;
        $garante = $cliente->relacionGarante?->garante;

        $estadosCiviles = ['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a', 'Unión libre'];
        $estadosCliente = ['Activo', 'Inactivo'];
        $tiposTelefono = ['Celular', 'Casa', 'Trabajo'];
        $clasificaciones = ['A', 'B', 'C'];

        $camposLaborales = [
            'empresa' => 'Empresa',
            'cargo_ocupacion' => 'Cargo u ocupación',
            'tiempo_laborando' => 'Tiempo laborando (meses)',
            'sueldo' => 'Sueldo (RD$)',
            'telefono_empresa' => 'Teléfono de empresa',
            'direccion_empresa' => 'Dirección de empresa',
        ];

        $tiposFamiliares = ['Cercano', 'No convive'];
        $camposFamiliares = [
            'nombre' => 'Nombre',
            'parentesco' => 'Parentesco',
            'telefono' => 'Teléfono',
            'direccion' => 'Dirección',
        ];
    @endphp

    <div class="d-flex">
        <aside class="menu">
            <a href="/" title="Inicio"><i class="bi bi-grid-1x2-fill"></i></a>
            <a href="{{ route('clientes.index') }}" class="activo" title="Clientes">
                <i class="bi bi-people"></i>
            </a>
            <a href="{{ route('tipos-prestamo.index') }}" title="Tipos de préstamo">
                <i class="bi bi-tags"></i>
            </a>
        </aside>

        <main class="contenido">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <small class="text-secondary">Mantenimientos / Clientes / Modificar</small>
                    <h2 class="fw-bold mt-2">Modificar cliente</h2>
                    <p class="text-secondary mb-0">Actualiza los datos del registro existente.</p>
                </div>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Volver</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>No se pudo actualizar el cliente.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" id="formEditarCliente" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <section class="tarjeta">
                    <h5 class="titulo-seccion"><i class="bi bi-person"></i>Datos personales</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nombres">Nombres *</label>
                            <input id="nombres" name="nombres" class="form-control" maxlength="100"
                                   value="{{ old('nombres', $cliente->nombres) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="apellidos">Apellidos *</label>
                            <input id="apellidos" name="apellidos" class="form-control" maxlength="100"
                                   value="{{ old('apellidos', $cliente->apellidos) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de documento</label>
                            <input class="form-control" value="{{ $cliente->tipoDocumento?->nombre }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Número de documento</label>
                            <input class="form-control" value="{{ $cliente->numero_documento }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="nacionalidad_id">Nacionalidad *</label>
                            <select id="nacionalidad_id" name="nacionalidad_id" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach ($nacionalidades as $nacionalidad)
                                    <option value="{{ $nacionalidad->id }}"
                                            @selected(old('nacionalidad_id', $cliente->nacionalidad_id) == $nacionalidad->id)>
                                        {{ $nacionalidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento *</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                   max="{{ now()->subDay()->format('Y-m-d') }}"
                                   value="{{ old('fecha_nacimiento', $cliente->fecha_nacimiento?->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="estado_civil">Estado civil *</label>
                            <select id="estado_civil" name="estado_civil" class="form-select" required>
                                @foreach ($estadosCiviles as $opcionCivil)
                                    <option value="{{ $opcionCivil }}"
                                            @selected(old('estado_civil', $cliente->estado_civil) === $opcionCivil)>
                                        {{ $opcionCivil }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="correo">Correo *</label>
                            <input type="email" id="correo" name="correo" class="form-control" maxlength="150"
                                   value="{{ old('correo', $cliente->correo) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="estado">Estado *</label>
                            <select id="estado" name="estado" class="form-select" required>
                                @foreach ($estadosCliente as $opcionEstado)
                                    <option value="{{ $opcionEstado }}"
                                            @selected(old('estado', $cliente->estado) === $opcionEstado)>
                                        {{ $opcionEstado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="direccion">Dirección *</label>
                            <input id="direccion" name="direccion" class="form-control" maxlength="255"
                                   value="{{ old('direccion', $cliente->direccion) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="referencia_direccion">Referencia de dirección *</label>
                            <textarea id="referencia_direccion" name="referencia_direccion" class="form-control"
                                      rows="2" maxlength="255" required>{{ old('referencia_direccion', $cliente->referencia_direccion) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion"><i class="bi bi-telephone"></i>Teléfonos</h5>
                    <div id="lista-telefonos">
                        @foreach ($telefonosFormulario as $i => $telefono)
                            <div class="row g-3 mb-3 telefono-fila">
                                <div class="col-md-3">
                                    <label class="form-label">Tipo *</label>
                                    <select name="telefonos[{{ $i }}][tipo]" class="form-select" required>
                                        @foreach ($tiposTelefono as $tipo)
                                            <option value="{{ $tipo }}" @selected(($telefono['tipo'] ?? '') === $tipo)>
                                                {{ $tipo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Número *</label>
                                    <input name="telefonos[{{ $i }}][numero]" class="form-control" maxlength="20"
                                           value="{{ $telefono['numero'] ?? '' }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label d-block">Principal</label>
                                    <input type="hidden" class="telefono-principal"
                                           name="telefonos[{{ $i }}][principal]"
                                           value="{{ !empty($telefono['principal']) ? '1' : '0' }}">
                                    <input type="radio" name="principal_visual" class="form-check-input radio-principal"
                                           @checked(!empty($telefono['principal']))> Sí
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2 quitar-telefono">
                                        Quitar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="agregarTelefono" class="btn btn-sm btn-outline-secondary">
                        + Agregar teléfono
                    </button>
                    <p class="text-secondary small mt-2 mb-0">Marca exactamente un teléfono como principal.</p>
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion">
                        <i class="bi bi-card-image"></i>
                        Documento de identidad
                    </h5>

                    <label for="documento_archivo" class="form-label">
                        Subir o reemplazar documento
                    </label>

                    <input
                        type="file"
                        id="documento_archivo"
                        name="documento_archivo"
                        class="form-control"
                        accept=".jpg,.jpeg,.png"
                    >

                    <small class="text-secondary">
                        JPG o PNG. Máximo 5 MB.
                        Deja este campo vacío para conservar el documento actual.
                    </small>
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion"><i class="bi bi-briefcase"></i>Datos laborales</h5>
                    <div class="row g-3">
                        @foreach ($camposLaborales as $campo => $etiqueta)
                            <div class="col-md-6">
                                <label class="form-label">{{ $etiqueta }} *</label>
                                <input name="laboral[{{ $campo }}]" class="form-control"
                                       @if (in_array($campo, ['tiempo_laborando', 'sueldo']))
                                           type="number" min="0" step="{{ $campo === 'sueldo' ? '0.01' : '1' }}"
                                       @else
                                           type="text"
                                       @endif
                                       value="{{ old('laboral.' . $campo, $laboral?->$campo) }}" required>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion"><i class="bi bi-wallet2"></i>Datos crediticios</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="limite_credito">Límite de crédito (RD$) *</label>
                            <input type="number" id="limite_credito" name="limite_credito" min="0" step="0.01"
                                   class="form-control" value="{{ old('limite_credito', $cliente->limite_credito) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="clasificacion_crediticia">Clasificación *</label>
                            <select id="clasificacion_crediticia" name="clasificacion_crediticia" class="form-select" required>
                                @foreach ($clasificaciones as $clasificacion)
                                    <option value="{{ $clasificacion }}"
                                            @selected(old('clasificacion_crediticia', $cliente->clasificacion_crediticia) === $clasificacion)>
                                        {{ $clasificacion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion"><i class="bi bi-person-lines-fill"></i>Referencias personales</h5>
                    @for ($i = 0; $i < 3; $i++)
                        @php
                            $referencia = $cliente->referencias->values()->get($i);
                        @endphp
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Referencia {{ $i + 1 }}: nombre *</label>
                                <input name="referencias[{{ $i }}][nombre]" class="form-control" maxlength="150"
                                       value="{{ old('referencias.' . $i . '.nombre', $referencia?->nombre) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono *</label>
                                <input name="referencias[{{ $i }}][telefono]" class="form-control" maxlength="20"
                                       value="{{ old('referencias.' . $i . '.telefono', $referencia?->telefono) }}" required>
                            </div>
                        </div>
                    @endfor
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion"><i class="bi bi-people"></i>Garante asignado</h5>
                    <div class="info">
                        @if ($garante)
                            <strong>{{ $garante->nombres }} {{ $garante->apellidos }}</strong>
                            <div>Documento: {{ $garante->numero_documento }}</div>
                            <div>Correo: {{ $garante->correo }}</div>
                            <div>
                                Teléfono:
                                {{ $garante->telefonos->firstWhere('principal', true)?->numero ?? $garante->telefonos->first()?->numero ?? 'Sin teléfono' }}
                            </div>
                            <small>Los datos y la asignación del garante no se modifican desde esta pantalla.</small>
                        @else
                            Sin garante asignado.
                        @endif
                    </div>
                </section>

                <section class="tarjeta">
                    <h5 class="titulo-seccion">
                        <i class="bi bi-house-heart"></i>Información familiar
                        <small class="text-secondary fw-normal">(Opcional)</small>
                    </h5>
                    @foreach ($tiposFamiliares as $i => $tipoFamiliar)
                        @php
                            $familiar = $cliente->familiares->firstWhere('tipo', $tipoFamiliar);
                        @endphp
                        <h6 class="mt-3">
                            {{ $tipoFamiliar === 'Cercano' ? 'Familiar cercano' : 'Familiar que no convive' }}
                        </h6>
                        <div class="row g-3 mb-3 familiar-grupo" data-indice="{{ $i }}" data-tipo="{{ $tipoFamiliar }}">
                            @foreach ($camposFamiliares as $campo => $etiqueta)
                                @php
                                    $longitud = match ($campo) {
                                        'direccion' => 255,
                                        'telefono' => 20,
                                        'parentesco' => 50,
                                        default => 150,
                                    };
                                @endphp
                                <div class="col-md-3">
                                    <label class="form-label">{{ $etiqueta }}</label>
                                    <input class="form-control familiar-input" data-campo="{{ $campo }}"
                                           maxlength="{{ $longitud }}"
                                           value="{{ old('familiares.' . $i . '.' . $campo, $familiar?->$campo) }}">
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </section>

                <div class="d-flex justify-content-end gap-3 mb-4">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-principal">
                        <i class="bi bi-check-lg me-2"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        const formulario = document.getElementById('formEditarCliente');
        const listaTelefonos = document.getElementById('lista-telefonos');

        let indiceTelefono = Math.max(
            -1,
            ...Array.from(listaTelefonos.querySelectorAll('.telefono-fila input[name$="[numero]"]'))
                .map(campo => Number(campo.name.match(/telefonos\[(\d+)\]/)?.[1] ?? -1))
        ) + 1;

        document.getElementById('agregarTelefono').addEventListener('click', function () {
            const indice = indiceTelefono++;
            const fila = document.createElement('div');
            fila.className = 'row g-3 mb-3 telefono-fila';
            fila.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">Tipo *</label>
                    <select name="telefonos[${indice}][tipo]" class="form-select" required>
                        <option value="Celular">Celular</option>
                        <option value="Casa">Casa</option>
                        <option value="Trabajo">Trabajo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número *</label>
                    <input name="telefonos[${indice}][numero]" class="form-control" maxlength="20" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">Principal</label>
                    <input type="hidden" class="telefono-principal"
                           name="telefonos[${indice}][principal]" value="0">
                    <input type="radio" name="principal_visual" class="form-check-input radio-principal"> Sí
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2 quitar-telefono">Quitar</button>
                </div>
            `;
            listaTelefonos.appendChild(fila);
        });

        listaTelefonos.addEventListener('click', function (evento) {
            if (evento.target.classList.contains('quitar-telefono')) {
                evento.target.closest('.telefono-fila').remove();
            }
        });

        function prepararFamiliares() {
            document.querySelectorAll('.familiar-grupo').forEach(function (grupo) {
                const entradas = Array.from(grupo.querySelectorAll('.familiar-input'));
                const tieneDatos = entradas.some(campo => campo.value.trim() !== '');

                grupo.querySelector('.tipo-familiar')?.remove();

                entradas.forEach(function (campo) {
                    campo.name = tieneDatos
                        ? `familiares[${grupo.dataset.indice}][${campo.dataset.campo}]`
                        : '';
                    campo.required = tieneDatos;
                });

                if (tieneDatos) {
                    const tipo = document.createElement('input');
                    tipo.type = 'hidden';
                    tipo.className = 'tipo-familiar';
                    tipo.name = `familiares[${grupo.dataset.indice}][tipo]`;
                    tipo.value = grupo.dataset.tipo;
                    grupo.appendChild(tipo);
                }
            });
        }

        document.querySelectorAll('.familiar-input').forEach(function (campo) {
            campo.addEventListener('input', prepararFamiliares);
        });

        prepararFamiliares();

        formulario.addEventListener('submit', function (evento) {
            const filas = Array.from(listaTelefonos.querySelectorAll('.telefono-fila'));
            const principales = filas.filter(fila => fila.querySelector('.radio-principal').checked);

            if (filas.length === 0 || principales.length !== 1) {
                evento.preventDefault();
                alert('Debes tener al menos un teléfono y marcar exactamente uno como principal.');
                return;
            }

            filas.forEach(function (fila) {
                fila.querySelector('.telefono-principal').value =
                    fila.querySelector('.radio-principal').checked ? '1' : '0';
            });

            prepararFamiliares();
        });
    </script>
</body>
</html>

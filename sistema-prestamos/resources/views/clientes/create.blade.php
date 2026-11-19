<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar cliente | Gestión de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f6f2; color: #292724; font-family: Arial, sans-serif; }
        .menu { width: 85px; min-height: 100vh; background: #292724; padding: 25px 10px; border-radius: 0 25px 25px 0; flex-shrink: 0; }
        .menu a { display: block; text-align: center; color: #c9c3bd; font-size: 22px; padding: 12px; margin-bottom: 15px; border-radius: 14px; }
        .menu a:hover, .menu a.activo { background: #bda68d; color: #292724; }
        .contenido { flex: 1; min-width: 0; padding: 35px; }
        .tarjeta { background: white; border-radius: 20px; padding: 28px; margin-bottom: 22px; }
        .titulo-seccion { font-size: 17px; font-weight: bold; margin-bottom: 22px; }
        .titulo-seccion i { color: #9a8067; margin-right: 10px; }
        .form-label { font-size: 13px; color: #68615a; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 12px; }
        .btn-principal { background: #292724; color: white; border-radius: 12px; padding: 11px 24px; }
        .btn-principal:hover { background: #594c40; color: white; }
        .info { background: #f8f6f2; border-radius: 12px; padding: 14px; color: #68615a; }
        .subtitulo { font-size: 14px; font-weight: bold; color: #594c40; margin-top: 18px; }
        @media (max-width: 650px) { .menu { width: 65px; } .contenido { padding: 18px; } .tarjeta { padding: 20px; } }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="menu">
        <a href="/" title="Inicio"><i class="bi bi-grid-1x2-fill"></i></a>
        <a href="{{ route('clientes.index') }}" class="activo" title="Clientes"><i class="bi bi-people"></i></a>
        <a href="{{ route('tipos-prestamo.index') }}" title="Tipos de préstamo"><i class="bi bi-tags"></i></a>
        <a href="#" title="Préstamos"><i class="bi bi-cash-stack"></i></a>
        <a href="#" title="Recibos"><i class="bi bi-receipt"></i></a>
        <a href="#" title="Renovaciones"><i class="bi bi-arrow-repeat"></i></a>
    </aside>
    <main class="contenido">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <small class="text-secondary">Mantenimientos / Clientes / Registro</small>
                <h2 class="fw-bold mt-2">Nuevo cliente</h2>
                <p class="text-secondary mb-0">Complete los datos de la persona y su garante.</p>
            </div>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>No se pudo guardar el cliente.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.store') }}" method="POST" id="formCliente">
            @csrf
            <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-person"></i>Datos personales</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label for="nombres" class="form-label">Nombres *</label><input id="nombres" name="nombres" class="form-control" maxlength="100" value="{{ old('nombres') }}" required></div>
                    <div class="col-md-6"><label for="apellidos" class="form-label">Apellidos *</label><input id="apellidos" name="apellidos" class="form-control" maxlength="100" value="{{ old('apellidos') }}" required></div>
                    <div class="col-md-6">
                        <label for="tipo_documento_id" class="form-label">Tipo de documento *</label>
                        <select id="tipo_documento_id" name="tipo_documento_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach ($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_documento_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="numero_documento" class="form-label">Número de documento *</label>
                        <div class="input-group">
                            <input id="numero_documento" name="numero_documento" class="form-control" maxlength="30" value="{{ old('numero_documento') }}" required placeholder="Según tipo seleccionado">
                            <button class="btn btn-outline-secondary" type="button" id="buscarCliente">Buscar</button>
                        </div>
                        <small id="mensajeCliente" class="text-secondary"></small>
                    </div>
                    <div class="col-md-4">
                        <label for="nacionalidad_id" class="form-label">Nacionalidad *</label>
                        <select id="nacionalidad_id" name="nacionalidad_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach ($nacionalidades as $nacionalidad)
                                <option value="{{ $nacionalidad->id }}" {{ old('nacionalidad_id') == $nacionalidad->id ? 'selected' : '' }}>{{ $nacionalidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><label for="fecha_nacimiento" class="form-label">Fecha de nacimiento *</label><input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" max="{{ now()->subDay()->format('Y-m-d') }}" value="{{ old('fecha_nacimiento') }}" required></div>
                    <div class="col-md-4">
                        <label for="estado_civil" class="form-label">Estado civil *</label>
                        <select id="estado_civil" name="estado_civil" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach (['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a', 'Unión libre'] as $estado)
                                <option value="{{ $estado }}" {{ old('estado_civil') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

                        <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-people"></i>Datos del garante</h5>
                <p class="text-secondary small">Busca primero por documento. Si ya está registrado, sus datos aparecerán en modo consulta. Si no existe, completa sus datos.</p>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Tipo de documento *</label>
                        <select id="garante_tipo_documento_id" name="garante[tipo_documento_id]" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('garante.tipo_documento_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5"><label class="form-label">Número de documento *</label><input id="garante_numero_documento" name="garante[numero_documento]" class="form-control" maxlength="30" value="{{ old('garante.numero_documento') }}" required></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-outline-secondary w-100" id="buscarGarante">Buscar</button></div>
                </div>
                <div id="mensajeGarante" class="info mt-3">Ingresa el documento del garante y pulsa Buscar.</div>
                <div id="datosGarante" class="mt-3" hidden>
                    <h6 class="subtitulo" id="tituloDatosGarante">Información del garante</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nombres *</label><input name="garante[nombres]" class="form-control garante-detalle" maxlength="100" value="{{ old('garante.nombres') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Apellidos *</label><input name="garante[apellidos]" class="form-control garante-detalle" maxlength="100" value="{{ old('garante.apellidos') }}" required></div>
                        <div class="col-md-4"><label class="form-label">Nacionalidad *</label><select name="garante[nacionalidad_id]" class="form-select garante-detalle" required><option value="">Seleccione</option>@foreach($nacionalidades as $nacionalidad)<option value="{{ $nacionalidad->id }}" {{ old('garante.nacionalidad_id') == $nacionalidad->id ? 'selected' : '' }}>{{ $nacionalidad->nombre }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">Fecha de nacimiento *</label><input type="date" name="garante[fecha_nacimiento]" class="form-control garante-detalle" max="{{ now()->subDay()->format('Y-m-d') }}" value="{{ old('garante.fecha_nacimiento') }}" required></div>
                        <div class="col-md-4"><label class="form-label">Estado civil *</label><select name="garante[estado_civil]" class="form-select garante-detalle" required><option value="">Seleccione</option>@foreach (['Soltero/a','Casado/a','Divorciado/a','Viudo/a','Unión libre'] as $estado)<option value="{{ $estado }}" {{ old('garante.estado_civil') === $estado ? 'selected' : '' }}>{{ $estado }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label class="form-label">Correo electrónico *</label><input type="email" name="garante[correo]" class="form-control garante-detalle" maxlength="150" value="{{ old('garante.correo') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Dirección *</label><input name="garante[direccion]" class="form-control garante-detalle" maxlength="255" value="{{ old('garante.direccion') }}" required></div>
                        <div class="col-12"><label class="form-label">Referencia de dirección *</label><textarea name="garante[referencia_direccion]" class="form-control garante-detalle" maxlength="255" rows="2" required>{{ old('garante.referencia_direccion') }}</textarea></div>
                    </div>
                    <h6 class="subtitulo">Teléfono del garante *</h6>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Tipo</label><select name="garante[telefonos][0][tipo]" class="form-select garante-detalle" required>@foreach(['Celular','Casa','Trabajo'] as $tipo)<option value="{{ $tipo }}" {{ old('garante.telefonos.0.tipo','Celular') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>@endforeach</select></div>
                        <div class="col-md-8"><label class="form-label">Número</label><input name="garante[telefonos][0][numero]" class="form-control garante-detalle" maxlength="20" value="{{ old('garante.telefonos.0.numero') }}" required></div>
                        <input type="hidden" name="garante[telefonos][0][principal]" value="1" class="garante-detalle">
                    </div>
                    <h6 class="subtitulo">Datos laborales del garante *</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Empresa</label><input name="garante[laboral][empresa]" class="form-control garante-detalle" maxlength="150" value="{{ old('garante.laboral.empresa') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Cargo u ocupación</label><input name="garante[laboral][cargo_ocupacion]" class="form-control garante-detalle" maxlength="100" value="{{ old('garante.laboral.cargo_ocupacion') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Tiempo laborando (meses)</label><input type="number" name="garante[laboral][tiempo_laborando]" class="form-control garante-detalle" min="0" step="1" value="{{ old('garante.laboral.tiempo_laborando') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Sueldo (RD$)</label><input type="number" name="garante[laboral][sueldo]" class="form-control garante-detalle" min="0" step="0.01" value="{{ old('garante.laboral.sueldo') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Teléfono empresa</label><input name="garante[laboral][telefono_empresa]" class="form-control garante-detalle" maxlength="20" value="{{ old('garante.laboral.telefono_empresa') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Dirección empresa</label><input name="garante[laboral][direccion_empresa]" class="form-control garante-detalle" maxlength="255" value="{{ old('garante.laboral.direccion_empresa') }}" required></div>
                    </div>
                </div>
            </section>


            <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-geo-alt"></i>Información de contacto</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="correo">Correo electrónico *</label><input type="email" id="correo" name="correo" class="form-control" maxlength="150" value="{{ old('correo') }}" required></div>
                    <div class="col-md-6"><label class="form-label" for="direccion">Dirección *</label><input id="direccion" name="direccion" class="form-control" maxlength="255" value="{{ old('direccion') }}" required></div>
                    <div class="col-12"><label for="referencia_direccion" class="form-label">Referencia de dirección *</label><textarea id="referencia_direccion" name="referencia_direccion" class="form-control" rows="2" maxlength="255" required>{{ old('referencia_direccion') }}</textarea></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h6 class="subtitulo mt-0 mb-0">Teléfonos *</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="agregarTelefono">+ Agregar teléfono</button>
                </div>
                <p class="small text-secondary">Debes tener al menos un teléfono y seleccionar uno como principal.</p>
                <div id="telefonosCliente" data-next="1">
                    @foreach (old('telefonos', [['tipo'=>'Celular','numero'=>'','principal'=>'1']]) as $i => $telefono)
                        <div class="row g-2 align-items-end mb-2 telefono-item">
                            <div class="col-md-3"><label class="form-label">Tipo</label><select name="telefonos[{{ $i }}][tipo]" class="form-select" required>@foreach(['Celular','Casa','Trabajo'] as $tipo)<option value="{{ $tipo }}" {{ ($telefono['tipo'] ?? '') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>@endforeach</select></div>
                            <div class="col-md-5"><label class="form-label">Número</label><input name="telefonos[{{ $i }}][numero]" class="form-control" maxlength="20" value="{{ $telefono['numero'] ?? '' }}" required></div>
                            <div class="col-md-3"><label class="form-label d-block">Principal</label><input type="hidden" class="principal-hidden" name="telefonos[{{ $i }}][principal]" value="{{ !empty($telefono['principal']) ? '1' : '0' }}"><input type="radio" name="telefono_principal_visual" class="form-check-input principal-radio" {{ !empty($telefono['principal']) ? 'checked' : '' }}> <span class="small">Sí</span></div>
                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger quitar-telefono" title="Quitar teléfono"><i class="bi bi-x-lg"></i></button></div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-briefcase"></i>Datos laborales</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Empresa *</label><input name="laboral[empresa]" class="form-control" maxlength="150" value="{{ old('laboral.empresa') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Cargo u ocupación *</label><input name="laboral[cargo_ocupacion]" class="form-control" maxlength="100" value="{{ old('laboral.cargo_ocupacion') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Tiempo laborando (meses) *</label><input type="number" name="laboral[tiempo_laborando]" class="form-control" min="0" step="1" value="{{ old('laboral.tiempo_laborando') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Sueldo mensual (RD$) *</label><input type="number" name="laboral[sueldo]" class="form-control" min="0" step="0.01" value="{{ old('laboral.sueldo') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Teléfono de empresa *</label><input name="laboral[telefono_empresa]" class="form-control" maxlength="20" value="{{ old('laboral.telefono_empresa') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Dirección de empresa *</label><input name="laboral[direccion_empresa]" class="form-control" maxlength="255" value="{{ old('laboral.direccion_empresa') }}" required></div>
                </div>
            </section>

            <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-wallet2"></i>Datos crediticios</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Límite de crédito (RD$) *</label><input type="number" name="limite_credito" class="form-control" min="0" step="0.01" value="{{ old('limite_credito') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Clasificación *</label><select name="clasificacion_crediticia" class="form-select" required><option value="">Seleccione</option>@foreach(['A','B','C'] as $clase)<option value="{{ $clase }}" {{ old('clasificacion_crediticia') === $clase ? 'selected' : '' }}>{{ $clase }}</option>@endforeach</select></div>
                </div>
            </section>

            <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-person-lines-fill"></i>Referencias personales</h5>
                @for($i = 0; $i < 3; $i++)
                    <h6 class="subtitulo">Referencia {{ $i + 1 }} *</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Nombre completo *</label><input name="referencias[{{ $i }}][nombre]" class="form-control" maxlength="150" value="{{ old('referencias.'.$i.'.nombre') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Teléfono *</label><input name="referencias[{{ $i }}][telefono]" class="form-control" maxlength="20" value="{{ old('referencias.'.$i.'.telefono') }}" required></div>
                    </div>
                    @if ($i < 2)<hr>@endif
                @endfor
            </section>


            <section class="tarjeta">
                <h5 class="titulo-seccion"><i class="bi bi-house-heart"></i>Información familiar <small class="text-secondary fw-normal">(Opcional)</small></h5>
                <p class="text-secondary small">Completa todos los datos del familiar que decidas registrar.</p>
                @foreach ([0 => 'Cercano', 1 => 'No convive'] as $i => $tipo)
                    <h6 class="subtitulo">Familiar {{ $tipo === 'Cercano' ? 'cercano' : 'que no vive con el cliente' }}</h6>
                    <div class="row g-3 mb-3 familiar-grupo" data-indice="{{ $i }}" data-tipo="{{ $tipo }}">
                        <div class="col-md-3"><label class="form-label">Nombre completo</label><input data-campo="nombre" class="form-control familiar-input" maxlength="150" value="{{ old('familiares.'.$i.'.nombre') }}"></div>
                        <div class="col-md-3"><label class="form-label">Parentesco</label><input data-campo="parentesco" class="form-control familiar-input" maxlength="50" value="{{ old('familiares.'.$i.'.parentesco') }}"></div>
                        <div class="col-md-3"><label class="form-label">Teléfono</label><input data-campo="telefono" class="form-control familiar-input" maxlength="20" value="{{ old('familiares.'.$i.'.telefono') }}"></div>
                        <div class="col-md-3"><label class="form-label">Dirección</label><input data-campo="direccion" class="form-control familiar-input" maxlength="255" value="{{ old('familiares.'.$i.'.direccion') }}"></div>
                    </div>
                @endforeach
            </section>

            <div class="d-flex justify-content-end gap-3 mb-4">
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check-lg me-2"></i>Guardar cliente</button>
            </div>
        </form>
    </main>
</div>
<script>
    const buscarUrl = @json(route('clientes.buscar-documento'));
    const form = document.getElementById('formCliente');
    const mensajeCliente = document.getElementById('mensajeCliente');
    const mensajeGarante = document.getElementById('mensajeGarante');
    const datosGarante = document.getElementById('datosGarante');
    let garanteComprobado = false;
    let documentoGaranteComprobado = '';
    let tipoGaranteComprobado = '';
    let clienteYaRegistrado = false;

    function configurarDocumento(idTipo, idNumero) {
    const selector = document.getElementById(idTipo);
    const campo = document.getElementById(idNumero);

    function esCedula() {
        return selector.selectedOptions[0]?.textContent.trim().toLowerCase() === 'cédula';
    }

    function darFormato(valor) {
        const digitos = valor.replace(/\D/g, '').slice(0, 11);

        if (digitos.length <= 3) return digitos;
        if (digitos.length <= 10) {
            return digitos.slice(0, 3) + '-' + digitos.slice(3);
        }

        return digitos.slice(0, 3) + '-' +
               digitos.slice(3, 10) + '-' +
               digitos.slice(10);
    }

    function actualizarTipo() {
        if (esCedula()) {
            campo.placeholder = '000-0000000-0';
            campo.maxLength = 13;
            campo.inputMode = 'numeric';
            campo.value = darFormato(campo.value);
        } else {
            campo.placeholder = 'Número de documento';
            campo.maxLength = 30;
            campo.inputMode = 'text';
        }
    }

    campo.addEventListener('input', function() {
        if (esCedula()) {
            campo.value = darFormato(campo.value);
        }
    });

    selector.addEventListener('change', actualizarTipo);
    actualizarTipo();
}

configurarDocumento('tipo_documento_id', 'numero_documento');
configurarDocumento('garante_tipo_documento_id', 'garante_numero_documento');

    function asignarValor(nombre, valor) {
        const campo = Array.from(form.elements).find(elemento => elemento.name === nombre);
        if (campo && valor !== null && valor !== undefined) campo.value = valor;
    }

    function leerValor(nombre) {
        const campo = Array.from(form.elements).find(elemento => elemento.name === nombre);
        return campo ? campo.value : '';
    }

    function fechaSimple(valor) {
        return valor ? String(valor).slice(0, 10) : '';
    }

    async function consultarDocumento(tipo, numero) {
        const url = new URL(buscarUrl, window.location.origin);
        url.searchParams.set('tipo_documento_id', tipo);
        url.searchParams.set('numero_documento', numero);
        const respuesta = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        const datos = await respuesta.json();
        if (!respuesta.ok) {
            const error = datos.errors ? Object.values(datos.errors).flat().join(' ') : (datos.message || 'No se pudo consultar el documento.');
            throw new Error(error);
        }
        return datos;
    }

    function mostrarGarante(existe, persona = null) {
        datosGarante.hidden = false;
        datosGarante.querySelectorAll('.garante-detalle').forEach(campo => {
            campo.disabled = existe;
            campo.required = !existe && campo.type !== 'hidden';
        });
        document.getElementById('tituloDatosGarante').textContent = existe ? 'Garante existente (solo lectura)' : 'Registrar nuevo garante';
        if (existe && persona) {
            const campos = {
                'nombres': persona.nombres, 'apellidos': persona.apellidos,
                'nacionalidad_id': persona.nacionalidad_id, 'fecha_nacimiento': fechaSimple(persona.fecha_nacimiento),
                'estado_civil': persona.estado_civil, 'correo': persona.correo,
                'direccion': persona.direccion, 'referencia_direccion': persona.referencia_direccion,
                'telefonos][0][tipo': persona.telefonos?.[0]?.tipo,
                'telefonos][0][numero': persona.telefonos?.[0]?.numero,
                'laboral][empresa': persona.datos_laborales?.empresa,
                'laboral][cargo_ocupacion': persona.datos_laborales?.cargo_ocupacion,
                'laboral][tiempo_laborando': persona.datos_laborales?.tiempo_laborando,
                'laboral][sueldo': persona.datos_laborales?.sueldo,
                'laboral][telefono_empresa': persona.datos_laborales?.telefono_empresa,
                'laboral][direccion_empresa': persona.datos_laborales?.direccion_empresa
            };
            Object.entries(campos).forEach(([clave, valor]) => asignarValor('garante[' + clave + ']', valor ?? ''));
        } else if (!existe) {
            datosGarante.querySelectorAll('.garante-detalle').forEach(campo => {
                if (campo.type !== 'hidden') campo.value = '';
            });
        }
    }

    document.getElementById('buscarCliente').addEventListener('click', async () => {
        const tipo = leerValor('tipo_documento_id');
        const numero = leerValor('numero_documento').trim();
        if (!tipo || !numero) { mensajeCliente.textContent = 'Selecciona el tipo y escribe el documento.'; return; }
        mensajeCliente.textContent = 'Buscando...';
        try {
            const datos = await consultarDocumento(tipo, numero);
            if (datos.encontrado) {
                const persona = datos.persona;
                clienteYaRegistrado = datos.ya_es_cliente;
                if (clienteYaRegistrado) {
                    mensajeCliente.textContent = 'Esta persona ya está registrada como cliente. Debes modificar su registro.';
                    return;
                }
                for (const campo of ['nombres','apellidos','nacionalidad_id','estado_civil','correo','direccion','referencia_direccion']) {
                    asignarValor(campo, persona[campo]);
                }
                asignarValor('fecha_nacimiento', fechaSimple(persona.fecha_nacimiento));
                if (persona.telefonos?.length) {
                    const primario = persona.telefonos.find(t => t.principal) || persona.telefonos[0];
                    asignarValor('telefonos[0][tipo]', primario.tipo);
                    asignarValor('telefonos[0][numero]', primario.numero);
                }
                if (persona.datos_laborales) {
                    for (const campo of ['empresa','cargo_ocupacion','tiempo_laborando','sueldo','telefono_empresa','direccion_empresa']) {
                        asignarValor('laboral[' + campo + ']', persona.datos_laborales[campo]);
                    }
                }
                mensajeCliente.textContent = 'Persona encontrada como garante. Se cargaron sus datos sin crear otro registro.';
            } else {
                clienteYaRegistrado = false;
                mensajeCliente.textContent = 'Documento disponible. Completa los datos del cliente.';
            }
        } catch (error) { mensajeCliente.textContent = error.message; }
    });

    async function buscarGarante() {
        const tipo = leerValor('garante[tipo_documento_id]');
        const numero = leerValor('garante[numero_documento]').trim();
        const numeroCliente = leerValor('numero_documento').trim();
        garanteComprobado = false;
        if (!tipo || !numero) { mensajeGarante.textContent = 'Selecciona el tipo y escribe el documento del garante.'; return; }
        if (numero.toUpperCase() === numeroCliente.toUpperCase()) {
            mensajeGarante.textContent = 'El cliente no puede ser su propio garante.';
            datosGarante.hidden = true;
            return;
        }
        mensajeGarante.textContent = 'Buscando garante...';
        try {
            const datos = await consultarDocumento(tipo, numero);
            if (datos.encontrado) {
                mostrarGarante(true, datos.persona);
                mensajeGarante.textContent = 'Garante encontrado. Sus datos se muestran en solo lectura.';
            } else {
                mostrarGarante(false);
                mensajeGarante.textContent = 'Garante no encontrado. Completa los datos para crearlo.';
            }
            garanteComprobado = true;
            documentoGaranteComprobado = numero;
            tipoGaranteComprobado = tipo;
        } catch (error) { mensajeGarante.textContent = error.message; datosGarante.hidden = true; }
    }
    document.getElementById('buscarGarante').addEventListener('click', buscarGarante);
    document.getElementById('garante_numero_documento').addEventListener('input', () => { garanteComprobado = false; datosGarante.hidden = true; });
    document.getElementById('garante_tipo_documento_id').addEventListener('change', () => { garanteComprobado = false; datosGarante.hidden = true; });
document.getElementById('numero_documento').addEventListener('input', function() {
    clienteYaRegistrado = false;
    mensajeCliente.textContent = '';

    const documentoCliente = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    const documentoGarante = document.getElementById('garante_numero_documento')
        .value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

    if (documentoCliente && documentoCliente === documentoGarante) {
        garanteComprobado = false;
        datosGarante.hidden = true;
        mensajeGarante.textContent = 'El cliente no puede ser su propio garante.';
    }
});    document.getElementById('tipo_documento_id').addEventListener('change', () => { clienteYaRegistrado = false; mensajeCliente.textContent = ''; });

    let siguienteTelefono = Math.max(0, ...Array.from(document.querySelectorAll('#telefonosCliente .telefono-item')).map(fila => {
        const nombre = fila.querySelector('input[name$="[numero]"]')?.name || '';
        const coincidencia = nombre.match(/telefonos\[(\d+)\]/);
        return coincidencia ? Number(coincidencia[1]) + 1 : 0;
    }));
    document.getElementById('agregarTelefono').addEventListener('click', () => {
        const indice = siguienteTelefono++;
        const fila = document.createElement('div');
        fila.className = 'row g-2 align-items-end mb-2 telefono-item';
        fila.innerHTML = `<div class="col-md-3"><label class="form-label">Tipo</label><select class="form-select" name="telefonos[${indice}][tipo]" required><option value="Celular">Celular</option><option value="Casa">Casa</option><option value="Trabajo">Trabajo</option></select></div><div class="col-md-5"><label class="form-label">Número</label><input class="form-control" name="telefonos[${indice}][numero]" maxlength="20" required></div><div class="col-md-3"><label class="form-label d-block">Principal</label><input type="hidden" class="principal-hidden" name="telefonos[${indice}][principal]" value="0"><input type="radio" class="form-check-input principal-radio" name="telefono_principal_visual"> <span class="small">Sí</span></div><div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger quitar-telefono"><i class="bi bi-x-lg"></i></button></div>`;
        document.getElementById('telefonosCliente').appendChild(fila);
    });
    document.getElementById('telefonosCliente').addEventListener('click', evento => {
        const boton = evento.target.closest('.quitar-telefono');
        if (!boton) return;
        const filas = document.querySelectorAll('#telefonosCliente .telefono-item');
        if (filas.length === 1) { alert('Debes mantener al menos un teléfono.'); return; }
        const fila = boton.closest('.telefono-item');
        const eraPrincipal = fila.querySelector('.principal-radio').checked;
        fila.remove();
        if (eraPrincipal) document.querySelector('#telefonosCliente .principal-radio').checked = true;
        actualizarPrincipales();
    });
    document.getElementById('telefonosCliente').addEventListener('change', evento => {
        if (evento.target.classList.contains('principal-radio')) actualizarPrincipales();
    });
    function actualizarPrincipales() {
        document.querySelectorAll('#telefonosCliente .telefono-item').forEach(fila => {
            fila.querySelector('.principal-hidden').value = fila.querySelector('.principal-radio').checked ? '1' : '0';
        });
    }

    function prepararFamiliares() {
        document.querySelectorAll('.familiar-grupo').forEach(grupo => {
            const entradas = Array.from(grupo.querySelectorAll('.familiar-input'));
            const hayDatos = entradas.some(campo => campo.value.trim() !== '');
            let campoTipo = grupo.querySelector('.tipo-familiar-hidden');
            if (campoTipo) campoTipo.remove();
            entradas.forEach(campo => {
                campo.name = hayDatos ? `familiares[${grupo.dataset.indice}][${campo.dataset.campo}]` : '';
                campo.required = hayDatos;
            });
            if (hayDatos) {
                campoTipo = document.createElement('input');
                campoTipo.type = 'hidden';
                campoTipo.className = 'tipo-familiar-hidden';
                campoTipo.name = `familiares[${grupo.dataset.indice}][tipo]`;
                campoTipo.value = grupo.dataset.tipo;
                grupo.appendChild(campoTipo);
            }
        });
    }
    document.querySelectorAll('.familiar-input').forEach(campo => campo.addEventListener('input', prepararFamiliares));
    prepararFamiliares();

    form.addEventListener('submit', evento => {
        actualizarPrincipales();
        prepararFamiliares();
        if (clienteYaRegistrado) { evento.preventDefault(); alert('Este documento ya pertenece a un cliente.'); return; }
        if (!garanteComprobado || documentoGaranteComprobado !== leerValor('garante[numero_documento]').trim() || tipoGaranteComprobado !== leerValor('garante[tipo_documento_id]')) {
            evento.preventDefault();
            mensajeGarante.textContent = 'Antes de guardar, debes buscar el documento del garante.';
            mensajeGarante.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (leerValor('numero_documento').trim().toUpperCase() === leerValor('garante[numero_documento]').trim().toUpperCase()) {
            evento.preventDefault(); alert('El cliente no puede ser su propio garante.'); return;
        }
        const principales = Array.from(document.querySelectorAll('#telefonosCliente .principal-radio')).filter(radio => radio.checked);
        if (principales.length !== 1) { evento.preventDefault(); alert('Selecciona un teléfono principal.'); }
    });
    actualizarPrincipales();

    // Si hubo errores de validación, conserva visibles los datos del garante nuevo.
    @if (old('garante.nombres'))
        mostrarGarante(false);
        @foreach (['nombres','apellidos','nacionalidad_id','fecha_nacimiento','estado_civil','correo','direccion','referencia_direccion'] as $campo)
            asignarValor('garante[{{ $campo }}]', @json(old('garante.'.$campo)));
        @endforeach
        @foreach (['tipo','numero'] as $campo)
            asignarValor('garante[telefonos][0][{{ $campo }}]', @json(old('garante.telefonos.0.'.$campo)));
        @endforeach
        @foreach (['empresa','cargo_ocupacion','tiempo_laborando','sueldo','telefono_empresa','direccion_empresa'] as $campo)
            asignarValor('garante[laboral][{{ $campo }}]', @json(old('garante.laboral.'.$campo)));
        @endforeach
    @endif
</script>
</body>
</html>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registrar cliente | Gestión de Préstamos</title>

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

        .form-control,
        .form-select {
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

        @media (max-width: 650px) {
            .menu {
                width: 65px;
            }

            .contenido {
                padding: 18px;
            }

            .tarjeta {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="d-flex">

    <!-- MENÚ -->
    <aside class="menu">

        <a href="/" title="Inicio">
            <i class="bi bi-grid-1x2-fill"></i>
        </a>

        <a href="{{ route('clientes.index') }}"
           class="activo" title="Clientes">
            <i class="bi bi-people"></i>
        </a>

        <a href="#" title="Tipos de préstamo">
            <i class="bi bi-tags"></i>
        </a>

        <a href="#" title="Préstamos">
            <i class="bi bi-cash-stack"></i>
        </a>

        <a href="#" title="Recibos">
            <i class="bi bi-receipt"></i>
        </a>

        <a href="#" title="Renovaciones">
            <i class="bi bi-arrow-repeat"></i>
        </a>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido">

        <div class="d-flex justify-content-between
                    align-items-center flex-wrap gap-3 mb-4">

            <div>
                <small class="text-secondary">
                    Mantenimientos / Clientes / Registro
                </small>

                <h2 class="fw-bold mt-2">Nuevo cliente</h2>

                <p class="text-secondary mb-0">
                    Complete los datos de la persona.
                </p>
            </div>

            <a href="{{ route('clientes.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Volver
            </a>
        </div>

        <!-- DATOS PERSONALES -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-person"></i>
                Datos personales
            </h5>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nombres</label>
                    <input type="text" class="form-control"
                           placeholder="Ingrese los nombres">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control"
                           placeholder="Ingrese los apellidos">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tipo de documento</label>
                    <select class="form-select">
                        <option>Seleccione</option>
                        <option>Cédula</option>
                        <option>RNC</option>
                        <option>Pasaporte</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Número de documento</label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nacionalidad</label>
                    <select class="form-select">
                        <option>Seleccione</option>
                        <option>Dominicana</option>
                        <option>Otra</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado civil</label>
                    <select class="form-select">
                        <option>Seleccione</option>
                        <option>Soltero/a</option>
                        <option>Casado/a</option>
                        <option>Divorciado/a</option>
                        <option>Viudo/a</option>
                        <option>Unión libre</option>
                    </select>
                </div>

            </div>
        </section>

        <!-- CONTACTO -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-geo-alt"></i>
                Información de contacto
            </h5>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control"
                           placeholder="correo@ejemplo.com">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono principal</label>
                    <input type="tel" class="form-control"
                           placeholder="809-000-0000">
                </div>

                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control"
                           placeholder="Calle, sector y ciudad">
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Referencia de dirección
                    </label>
                    <textarea class="form-control" rows="2"
                              placeholder="Indicaciones para llegar"></textarea>
                </div>

            </div>
        </section>

        <!-- DATOS LABORALES -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-briefcase"></i>
                Datos laborales
            </h5>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cargo u ocupación</label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Tiempo laborando (meses)
                    </label>
                    <input type="number" class="form-control" min="0">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sueldo mensual (RD$)</label>
                    <input type="number" class="form-control"
                           min="0" step="0.01">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono de la empresa</label>
                    <input type="tel" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dirección de la empresa</label>
                    <input type="text" class="form-control">
                </div>

            </div>
        </section>

        <!-- DATOS CREDITICIOS -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-wallet2"></i>
                Datos crediticios
            </h5>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">
                        Límite de crédito (RD$)
                    </label>
                    <input type="number" class="form-control"
                           min="0" step="0.01">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Clasificación</label>
                    <select class="form-select">
                        <option>Seleccione</option>
                        <option>A</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </div>

            </div>
        </section>

        <!-- REFERENCIAS PERSONALES -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-person-lines-fill"></i>
                Referencias personales
            </h5>

            @for ($i = 1; $i <= 3; $i++)

                <h6 class="mt-3 mb-3">
                    Referencia {{ $i }}
                </h6>

                <div class="row g-3 mb-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Nombre completo
                        </label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Teléfono
                        </label>
                        <input type="tel" class="form-control">
                    </div>

                </div>

                @if ($i < 3)
                    <hr>
                @endif

            @endfor

        </section>

        <!-- GARANTE -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-people"></i>
                Datos del garante
            </h5>

            <p class="text-secondary small">
                El garante podrá buscarse por documento
                para evitar registros duplicados.
            </p>

            <div class="row g-3">

                <div class="col-md-5">
                    <label class="form-label">
                        Tipo de documento
                    </label>
                    <select class="form-select">
                        <option>Seleccione</option>
                        <option>Cédula</option>
                        <option>RNC</option>
                        <option>Pasaporte</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label">
                        Número de documento
                    </label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100"
                            type="button" disabled>
                        Buscar
                    </button>
                </div>

            </div>

            <div class="alert mt-4 mb-0"
                 style="background: #f8f6f2; color: #68615a;">
                Aquí mostraremos los datos del garante
                existente o el formulario para uno nuevo.
            </div>

        </section>

        <!-- FAMILIARES -->
        <section class="tarjeta">

            <h5 class="titulo-seccion">
                <i class="bi bi-house-heart"></i>
                Información familiar
                <small class="text-secondary fw-normal">
                    (Opcional)
                </small>
            </h5>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Parentesco</label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control">
                </div>

            </div>
        </section>

        <!-- ACCIONES -->
        <div class="d-flex justify-content-end gap-3 mb-4">

            <a href="{{ route('clientes.index') }}"
               class="btn btn-outline-secondary px-4">
                Cancelar
            </a>

            <button type="button"
                    class="btn btn-principal" disabled>
                <i class="bi bi-check-lg me-2"></i>
                Guardar cliente
            </button>

        </div>

    </main>

</div>

</body>
</html>

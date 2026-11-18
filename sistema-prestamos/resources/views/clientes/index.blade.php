
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Clientes | Gestión de Préstamos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f8f6f2;
            color: #292724;
            font-family: Arial, sans-serif;
        }

        .menu {
            width: 85px;
            min-height: 100vh;
            background-color: #292724;
            padding: 25px 10px;
            border-radius: 0 25px 25px 0;
        }

        .menu a {
            color: #c9c3bd;
            display: block;
            text-align: center;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 14px;
            font-size: 22px;
        }

        .menu a:hover,
        .menu a.activo {
            background-color: #bda68d;
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
            border: none;
        }

        .resumen {
            background-color: #e9ddcd;
        }

        .btn-principal {
            background-color: #292724;
            color: white;
            border-radius: 12px;
            padding: 11px 20px;
        }

        .btn-principal:hover {
            background-color: #594c40;
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
            background-color: #ede7dd;
            border-radius: 20px;
            padding: 6px 12px;
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

        <a href="#" class="activo" title="Clientes">
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

    <!-- CONTENIDO -->
    <main class="contenido">

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
                    <h2 class="fw-bold mt-2">125</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tarjeta">
                    <small>Clientes activos</small>
                    <h2 class="fw-bold mt-2">118</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tarjeta">
                    <small>Clientes inactivos</small>
                    <h2 class="fw-bold mt-2">7</h2>
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

                <input type="search"
                       class="form-control"
                       style="max-width: 270px;"
                       placeholder="Buscar cliente..."
                       disabled>

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
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>María Pérez</td>
                            <td>***-****123</td>
                            <td>809-000-0001</td>
                            <td>
                                <span class="estado">
                                    Activo
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light"
                                        type="button"
                                        title="Consultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light"
                                        type="button"
                                        title="Modificar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td>Juan Rodríguez</td>
                            <td>***-****456</td>
                            <td>829-000-0002</td>
                            <td>
                                <span class="estado">
                                    Activo
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light"
                                        type="button"
                                        title="Consultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light"
                                        type="button"
                                        title="Modificar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td>Ana López</td>
                            <td>***-****789</td>
                            <td>849-000-0003</td>
                            <td>
                                <span class="estado">
                                    Inactivo
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light"
                                        type="button"
                                        title="Consultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light"
                                        type="button"
                                        title="Modificar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

            <small class="text-secondary">
                Datos de demostración.
            </small>

        </section>

    </main>

</div>

</body>
</html>

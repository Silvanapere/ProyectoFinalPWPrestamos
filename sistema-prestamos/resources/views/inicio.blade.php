
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Gestión de Préstamos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f8f6f2;
            color: #292724;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .sistema {
            display: flex;
            min-height: 100vh;
        }

        .menu {
            width: 85px;
            background-color: #292724;
            border-radius: 0 25px 25px 0;
            padding: 25px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }

        .logo {
            width: 48px;
            height: 48px;
            background-color: #bda68d;
            color: #292724;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            margin-bottom: 25px;
        }

        .menu a {
            color: #c9c3bd;
            font-size: 22px;
            text-decoration: none;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
        }

        .menu a:hover,
        .menu a.activo {
            background-color: #bda68d;
            color: #292724;
        }

        .contenido {
            flex: 1;
            padding: 30px;
            min-width: 0;
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .subtitulo {
            color: #918b84;
            font-size: 13px;
        }

        .busqueda {
            background: white;
            border: 0;
            border-radius: 15px;
            padding: 12px 18px;
            width: 230px;
        }

        .distribucion {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 25px;
        }

        .tarjeta {
            background-color: white;
            border-radius: 23px;
            padding: 25px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.025);
        }

        .resumen {
            background-color: #e9ddcd;
            min-height: 280px;
        }

        .etiqueta {
            color: #756b61;
            font-size: 13px;
        }

        .grafico {
            display: flex;
            align-items: end;
            gap: 12px;
            height: 135px;
            margin-top: 35px;
            border-bottom: 1px solid #bda68d;
        }

        .barra {
            flex: 1;
            background-color: #bda68d;
            border-radius: 8px 8px 0 0;
        }

        .barra.destacada {
            background-color: #49423b;
        }

        .estadisticas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 22px;
        }

        .icono-tarjeta {
            width: 45px;
            height: 45px;
            background-color: #f0e9e1;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .numero {
            font-size: 25px;
            font-weight: bold;
        }

        .accesos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .acceso {
            text-align: center;
            padding: 25px 15px;
        }

        .acceso i {
            font-size: 27px;
            color: #9a8067;
        }

        .prestamo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 0;
            border-bottom: 1px solid #eeeae5;
        }

        .avatar {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            background-color: #e9ddcd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6b5744;
            flex-shrink: 0;
        }

        .estado {
            font-size: 11px;
            background-color: #f0e9e1;
            padding: 5px 9px;
            border-radius: 20px;
        }

        @media (max-width: 1100px) {
            .distribucion {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .menu {
                width: 65px;
            }

            .contenido {
                padding: 18px;
            }

            .estadisticas,
            .accesos {
                grid-template-columns: 1fr;
            }

            .busqueda {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="sistema">

    <!-- MENÚ LATERAL -->
    <aside class="menu">

        <div class="logo">
            <i class="bi bi-wallet2"></i>
        </div>

        <a href="#" class="activo" title="Inicio">
            <i class="bi bi-grid-1x2-fill"></i>
        </a>

        <a href="{{ route('clientes.index') }}" title="Clientes y garantes">
    <i class="bi bi-people"></i>
</a>

        <a href="{{ route('tipos-prestamo.index') }}"
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

        <a href="#" title="Consultas">
            <i class="bi bi-search"></i>
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin-top: auto;">
    @csrf

    <button type="submit"
            title="Cerrar sesión"
            style="background: transparent; border: none; color: #c9c3bd;
                   font-size: 22px; width: 48px; height: 48px; cursor: pointer;">
        <i class="bi bi-box-arrow-right"></i>
    </button>
</form>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="contenido">

        <header class="encabezado">

            <div>
                <span class="subtitulo">Panel principal</span>
                <h3 class="fw-bold mb-0">Dashboard</h3>
            </div>

            <div class="d-flex align-items-center gap-3">
                <input type="text"
                       class="busqueda"
                       placeholder="Buscar..."
                       disabled>

                <div class="avatar">A</div>
            </div>

        </header>

        <div class="distribucion">

            <!-- COLUMNA CENTRAL -->
            <main>

                <section class="tarjeta resumen">

                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="etiqueta">
                                Resumen general
                            </span>

                            <h4 class="fw-bold mt-2">
                                Control de préstamos
                            </h4>

                            <p class="etiqueta">
                                Visualización de actividad mensual
                            </p>
                        </div>

                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>

                    <!-- GRÁFICO DECORATIVO -->
                    <div class="grafico">
                        <div class="barra" style="height: 35%;"></div>
                        <div class="barra" style="height: 60%;"></div>
                        <div class="barra" style="height: 48%;"></div>
                        <div class="barra destacada" style="height: 85%;"></div>
                        <div class="barra" style="height: 70%;"></div>
                        <div class="barra" style="height: 55%;"></div>
                        <div class="barra" style="height: 95%;"></div>
                    </div>

                    <div class="d-flex justify-content-between mt-2 etiqueta">
                        <span>Ene</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Abr</span>
                        <span>May</span>
                        <span>Jun</span>
                        <span>Jul</span>
                    </div>

                </section>

                <!-- TARJETAS DE ESTADÍSTICAS -->
                <section class="estadisticas">

                    <div class="tarjeta">
                        <div class="icono-tarjeta">
                            <i class="bi bi-people"></i>
                        </div>

                        <p class="etiqueta mb-1">
                            Clientes registrados
                        </p>
                        <div class="numero">125</div>
                        <span class="etiqueta">Total de clientes</span>
                    </div>

                    <div class="tarjeta">
                        <div class="icono-tarjeta">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <p class="etiqueta mb-1">
                            Préstamos activos
                        </p>
                        <div class="numero">48</div>
                        <span class="etiqueta">En circulación</span>
                    </div>

                    <div class="tarjeta">
                        <div class="icono-tarjeta">
                            <i class="bi bi-cash-coin"></i>
                        </div>

                        <p class="etiqueta mb-1">
                            Cobros del mes
                        </p>
                        <div class="numero">RD$ 85K</div>
                        <span class="etiqueta">Monto de ejemplo</span>
                    </div>

                </section>

                <!-- ACCESOS RÁPIDOS -->
                <h5 class="fw-bold mt-5 mb-3">
                    Accesos rápidos
                </h5>

                <section class="accesos">

                    <div class="tarjeta acceso">
                        <i class="bi bi-person-plus"></i>
                        <h6 class="mt-3">Clientes</h6>
                        <p class="etiqueta mb-0">
                            Gestión de clientes
                        </p>
                    </div>

                    <div class="tarjeta acceso">
                        <i class="bi bi-tags"></i>
                        <h6 class="mt-3">Tipos de préstamo</h6>
                        <p class="etiqueta mb-0">
                            Catálogo de préstamos
                        </p>
                    </div>

                    <div class="tarjeta acceso">
                        <i class="bi bi-file-earmark-text"></i>
                        <h6 class="mt-3">Consultas</h6>
                        <p class="etiqueta mb-0">
                            Información registrada
                        </p>
                    </div>

                </section>

            </main>

            <!-- PANEL DERECHO -->
            <aside>

                <div class="tarjeta">

                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            Próximos vencimientos
                        </h6>
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <p class="etiqueta mt-2">
                        Préstamos pendientes de pago
                    </p>

                    <div class="prestamo">
                        <div class="avatar">MP</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">María Pérez</div>
                            <div class="etiqueta">RD$ 5,200</div>
                        </div>
                        <span class="estado">3 días</span>
                    </div>

                    <div class="prestamo">
                        <div class="avatar">JR</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">Juan Rodríguez</div>
                            <div class="etiqueta">RD$ 8,500</div>
                        </div>
                        <span class="estado">5 días</span>
                    </div>

                    <div class="prestamo">
                        <div class="avatar">AL</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">Ana López</div>
                            <div class="etiqueta">RD$ 3,900</div>
                        </div>
                        <span class="estado">7 días</span>
                    </div>

                    <p class="etiqueta mt-3 mb-0">
                        Datos de demostración
                    </p>

                </div>

                <div class="tarjeta mt-4"
                     style="background-color: #e9ddcd;">

                    <div class="icono-tarjeta">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <h6 class="fw-bold">
                        Gestión simplificada
                    </h6>

                    <p class="etiqueta mb-0">
                        Administra clientes, préstamos,
                        pagos y renovaciones desde
                        un solo lugar.
                    </p>

                </div>

            </aside>

        </div>

    </div>

</div>

</body>
</html>

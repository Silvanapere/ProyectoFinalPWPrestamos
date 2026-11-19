@extends('layouts.app')

@section('title', 'Dashboard | Gestión de Préstamos')
@section('page-class', 'pagina-inicio')

@push('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="sistema">

    <!-- MENÚ LATERAL -->
    @include('partials.menu')

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
@endsection

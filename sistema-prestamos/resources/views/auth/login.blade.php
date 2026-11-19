
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Sistema de Préstamos</title>

    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>

<div class="login-contenedor">

    <div class="panel-formulario">

        <div class="marca">
            <div class="marca-icono">$</div>
            SISTEMA DE PRÉSTAMOS
        </div>

        <div class="formulario">

            <h1>Bienvenido</h1>

            <p class="descripcion">
                Inicia sesión para acceder a la gestión
                de clientes y préstamos.
            </p>

            <form action="{{ route('login.attempt') }}" method="POST">
                @csrf

                <div class="campo">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="correo@ejemplo.com"
                        required
                    >
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="campo">
                    <label for="password">Contraseña</label>

                    <div class="contrasena">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            required
                        >

                        <button
                            type="button"
                            class="ver-contrasena"
                            onclick="mostrarContrasena()"
                        >
                            Mostrar
                        </button>
                    </div>
                </div>

                <button type="submit" class="boton-login">
                    Iniciar sesión
                </button>
            </form>

        </div>

        <div class="pie">
            © {{ date('Y') }} Sistema de Gestión de Préstamos
        </div>

    </div>

    <div class="panel-visual">

        <div class="ilustracion">

            <div class="ventana">

                <div class="ventana-titulo">
                    Panel financiero
                </div>

                <div class="ventana-texto">
                    Resumen general de operaciones
                </div>

                <div class="estadisticas">
                    <div class="estadistica">
                        <span>Clientes</span>
                        <strong>128</strong>
                    </div>

                    <div class="estadistica">
                        <span>Préstamos</span>
                        <strong>64</strong>
                    </div>
                </div>

                <div class="grafico">
                    <div class="barra"></div>
                    <div class="barra"></div>
                    <div class="barra"></div>
                    <div class="barra"></div>
                    <div class="barra"></div>
                    <div class="barra"></div>
                </div>

            </div>

            <div class="mensaje-visual">
                <h2>Todo bajo control.</h2>
                <p>
                    Administra clientes, préstamos y operaciones
                    desde un solo lugar.
                </p>
            </div>

        </div>
    </div>

</div>

<script>
    function mostrarContrasena() {
        const campo = document.getElementById('password');
        const boton = document.querySelector('.ver-contrasena');

        if (campo.type === 'password') {
            campo.type = 'text';
            boton.textContent = 'Ocultar';
        } else {
            campo.type = 'password';
            boton.textContent = 'Mostrar';
        }
    }
</script>

</body>
</html>

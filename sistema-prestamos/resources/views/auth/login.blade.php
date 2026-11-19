
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Sistema de Préstamos</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f8f6f2;
            color: #292724;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .login-contenedor {
            width: 100%;
            max-width: 1200px;
            min-height: 650px;
            background: white;
            border: 2px solid #bda68d;
            border-radius: 30px;
            display: grid;
            grid-template-columns: 42% 58%;
            overflow: hidden;
            box-shadow: 0 20px 65px rgba(41, 39, 36, 0.06);
        }

        .panel-formulario {
            padding: 45px 65px;
            display: flex;
            flex-direction: column;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: bold;
        }

        .marca-icono {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: #bda68d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .formulario {
            margin: auto 0;
            padding: 50px 0;
        }

        h1 {
            font-size: 38px;
            letter-spacing: -1.5px;
            margin: 0 0 12px;
        }

        .descripcion {
            color: #918b84;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 35px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: bold;
        }

        .campo {
            margin-bottom: 24px;
        }

        input {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #e8e2db;
            background: #f8f6f2;
            color: #292724;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #bda68d;
        }

        .contrasena {
            position: relative;
        }

        .contrasena input {
            padding-right: 65px;
        }

        .ver-contrasena {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #756b61;
            cursor: pointer;
            font-size: 12px;
        }

        .boton-login {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 16px;
            background: #292724;
            color: white;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .boton-login:hover {
            background: #49423b;
        }

        .error {
            color: #a33a3a;
            font-size: 12px;
            margin-top: 8px;
        }

        .pie {
            color: #918b84;
            font-size: 12px;
        }

        .panel-visual {
            background: #e9ddcd;
            padding: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .panel-visual::before {
            content: "";
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: #f3ebe0;
            position: absolute;
            right: -130px;
            top: -150px;
        }

        .ilustracion {
            position: relative;
            width: 100%;
            max-width: 440px;
        }

        .ventana {
            background: #fff;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 25px 50px rgba(73, 66, 59, 0.12);
        }

        .ventana-titulo {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .ventana-texto {
            font-size: 12px;
            color: #918b84;
        }

        .estadisticas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 25px;
        }

        .estadistica {
            background: #f8f6f2;
            padding: 20px;
            border-radius: 15px;
        }

        .estadistica span {
            font-size: 12px;
            color: #756b61;
        }

        .estadistica strong {
            display: block;
            margin-top: 12px;
            font-size: 26px;
        }

        .grafico {
            height: 140px;
            display: flex;
            align-items: flex-end;
            gap: 15px;
            margin-top: 25px;
            border-bottom: 1px solid #e8e2db;
        }

        .barra {
            flex: 1;
            background: #bda68d;
            border-radius: 7px 7px 0 0;
        }

        .barra:nth-child(1) { height: 35%; }
        .barra:nth-child(2) { height: 62%; }
        .barra:nth-child(3) { height: 48%; }
        .barra:nth-child(4) { height: 83%; background: #49423b; }
        .barra:nth-child(5) { height: 67%; }
        .barra:nth-child(6) { height: 95%; }

        .mensaje-visual {
            text-align: center;
            margin-top: 35px;
        }

        .mensaje-visual h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .mensaje-visual p {
            color: #756b61;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 850px) {
            .login-contenedor {
                grid-template-columns: 1fr;
            }

            .panel-formulario {
                min-height: 600px;
            }

            .panel-visual {
                display: none;
            }
        }

        @media (max-width: 500px) {
            body {
                padding: 12px;
            }

            .panel-formulario {
                padding: 30px;
            }

            h1 {
                font-size: 32px;
            }
        }
    </style>
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

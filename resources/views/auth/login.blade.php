<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    @vite([
        'resources/css/app.css',
        'resources/css/login.css',
        'resources/js/app.js'
    ])
</head>
<body>

<div class="login-container">

    {{-- IZQUIERDA --}}
    <div class="login-left">

        <div class="logo-container-wrapper">

            <img src="{{ asset('images/logof.png') }}"
                 class="logo-login"
                 alt="Logo DCM">

        </div>

        <h1 class="login-title">
            Iniciar sesión
        </h1>

        <p class="login-subtitle">
            Ingresa tus credenciales para acceder al sistema
        </p>





        {{-- FORMULARIO --}}
        <form action="{{ url('/login') }}"
              method="POST">

            @csrf

            {{-- ERROR --}}
            @if ($errors->has('usuario_login'))

                <div class="alert-error">

                    {{ $errors->first('usuario_login') }}

                </div>

            @endif



            {{-- USUARIO --}}
            <div class="form-group mb-3">

                <label class="form-label fw-semibold">
                    Usuario
                </label>

                <input type="text"
                       class="form-control"
                       name="usuario_login"
                       placeholder="Ej. admin"
                       value="{{ old('usuario_login') }}"
                       required
                       autofocus>

            </div>



            {{-- PASSWORD --}}
            <div class="form-group">

                <label class="form-label">
                    Contraseña
                </label>

                <div class="password-box">

                    <input type="password"
                           class="form-control"
                           name="password"
                           placeholder="••••••••"
                           required>

                    

                </div>

            </div>



            {{-- RECORDAR --}}
            <div class="remember-box">

                <input type="checkbox"
                       name="remember"
                       id="remember">

                <label for="remember">
                    Recordarme
                </label>

            </div>



            {{-- BOTON --}}
            <button type="submit"
                    class="btn-login">

                Iniciar sesión

            </button>

        </form>

    </div>





    {{-- DERECHA --}}
    <div class="login-right">

        <div class="energy-text">

            Energía que<br>
            impulsa el futuro

        </div>

    </div>

</div>

</body>
</html>
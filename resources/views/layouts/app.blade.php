<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCM Sistema</title>

    {{-- ¡ESTA ES LA LÍNEA QUE FALTA Y REPARA TU SCRIPT! --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>
<body>

    {{-- SIDEBAR --}}
    <div class="sidebar">

        {{-- LOGO --}}
        <div class="logo">

            <img src="{{ asset('images/logoo.png') }}" alt="Logo">

        </div>

        {{-- MENÚ --}}
        <div class="menu">

            <a href="{{ url('/dashboard') }}"
               class="{{ request()->is('dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard

            </a>

            <a href="{{ url('/proyectos') }}"
               class="{{ request()->is('proyectos') ? 'active' : '' }}">

                <i class="bi bi-folder-fill"></i>
                Proyectos

            </a>

            <a href="{{ url('/mantenimientos') }}"
               class="{{ request()->is('mantenimientos') ? 'active' : '' }}">

                <i class="bi bi-tools"></i>
                Mantenimientos

            </a>

            <a href="{{ url('/inventario') }}"
               class="{{ request()->is('inventario') ? 'active' : '' }}">

                <i class="bi bi-box-seam-fill"></i>
                Inventario

            </a>

            <a href="{{ url('/reportes') }}"
               class="{{ request()->is('reportes') ? 'active' : '' }}">

                <i class="bi bi-bar-chart-fill"></i>
                Reportes

            </a>

            @if(auth()->check() && auth()->user()->rol == 'admin')

                <a href="{{ url('/usuarios') }}"
                   class="{{ request()->is('usuarios') ? 'active' : '' }}">

                    <i class="bi bi-people-fill"></i>
                    Usuarios

                </a>

            @endif

        </div>

        {{-- CERRAR SESIÓN --}}
        <div class="logout-box">

    <form method="POST"
      action="{{ route('logout') }}">

    @csrf

    <button type="submit"
            class="logout-btn">

        <i class="bi bi-box-arrow-right"></i>
        Cerrar sesión

    </button>

</form>

</div>

    </div>





    {{-- CONTENIDO --}}
        <div class="main-content">

            {{-- TOPBAR --}}
            <div class="topbar"></div>

            <div class="content">

                @yield('contenido')

            </div>

        </div>

        {{-- Directiva para inyectar scripts específicos de cada vista --}}
        @stack('scripts')

    </body>
</html>
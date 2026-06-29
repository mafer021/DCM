@extends('layouts.app')

@section('contenido')

<div class="welcome-box mb-5">

    <h1 class="welcome-title">
        Bienvenido a DCM Soluciones Energéticas,
        <span>
            {{ auth()->user()->usuario_login }}
        </span>
    </h1>

    <p class="welcome-subtitle">
        Sistema de gestión y monitoreo energético.
    </p>

</div>



<div class="row g-4">

    {{-- KPI 1 --}}
    <div class="col-md-4">

        <div class="kpi-card">

            <div class="kpi-icon kpi-blue">
                📁
            </div>

            <div>
                <div class="kpi-title">
                    Total proyectos
                </div>

                <div class="kpi-number">{{ $totalProyectos }}</div>
            </div>

        </div>

    </div>

    {{-- KPI 2 --}}
    <div class="col-md-4">

        <div class="kpi-card">

            <div class="kpi-icon kpi-green">
                📦
            </div>

            <div>
                <div class="kpi-title">
                    Productos inventario
                </div>

                <div class="kpi-number">{{ $totalProductos }}</div>
            </div>

        </div>

    </div>

    {{-- KPI 3 --}}
    <div class="col-md-4">

        <div class="kpi-card">

            <div class="kpi-icon kpi-orange">
                🔧
            </div>

            <div>
                <div class="kpi-title">
                    Mantenimientos
                </div>

                <div class="kpi-number">{{ $totalMantenimientos }}</div>
            </div>

        </div>

    </div>

</div>

{{-- ALERTAS --}}
<div class="row mt-5 g-4">

    {{-- STOCK BAJO --}}
    <div class="col-md-6">

        <div class="dashboard-card">

            <div class="dashboard-title">
                ⚠️ Alertas de inventario
            </div>

            <table class="table">

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
    @forelse($productosBajos as $prod)
        <tr>
            <td>{{ $prod->nombre }}</td>
            <td>{{ $prod->stock }}</td>
            <td>
                <span class="badge-stock">{{ str_replace('_', ' ', $prod->estado) }}</span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center py-4">
                <i class="bi bi-check-circle text-success fs-4"></i>
                <p class="text-muted mt-2">¡Todo al día! No hay alertas de inventario.</p>
            </td>
        </tr>
    @endforelse
</tbody>

            </table>

        </div>

    </div>

    {{-- MANTENIMIENTOS --}}
    <div class="col-md-6">

        <div class="dashboard-card">

            <div class="dashboard-title">
                🛠️ Próximos mantenimientos
            </div>

            <table class="table">

                <thead>
                    <tr>
                        <th>Proyecto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
    @forelse($proximosMantenimientos as $proy)
        @php
            $fechaMantenimiento = \Carbon\Carbon::parse($proy->proximo_mantenimiento);
            $esVencido = $fechaMantenimiento->lt(\Carbon\Carbon::today());
        @endphp
        <tr class="{{ $esVencido ? 'table-danger' : '' }}">
            <td>{{ $proy->nombre }}</td>
            <td>{{ $fechaMantenimiento->format('d/m/Y') }}</td>
            <td>
                <span class="badge {{ $esVencido ? 'bg-danger' : 'bg-warning' }}">
                    {{ $esVencido ? 'Vencido' : 'Próximo' }}
                </span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center py-4">
                <i class="bi bi-calendar-check text-success fs-4"></i>
                <p class="text-muted mt-2">No hay mantenimientos próximos.</p>
            </td>
        </tr>
    @endforelse
</tbody>

            </table>

        </div>

    </div>

</div>

@endsection
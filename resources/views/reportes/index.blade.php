@extends('layouts.app')

@section('contenido')

{{-- ===================================================== --}}
{{-- ENCABEZADO --}}
{{-- ===================================================== --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="fw-bold mb-0">
        Reportes
    </h2>

</div>





{{-- ===================================================== --}}
{{-- KPIs --}}
{{-- ===================================================== --}}
<div class="row">

    {{-- TOTAL PROYECTOS --}}
    <div class="col-md-4 mb-4">

        <div class="kpi-card">

            <div class="kpi-icon bg-primary-soft">

                <i class="bi bi-folder2-open text-primary"></i>

            </div>

            <div>

                <h6 class="kpi-title">
                    Total de proyectos
                </h6>

                <h2 class="kpi-number">{{ $totalProyectos }}</h2>

            </div>

        </div>

    </div>

    {{-- TOTAL MANTENIMIENTOS --}}
    <div class="col-md-4 mb-4">

        <div class="kpi-card">

            <div class="kpi-icon bg-success-soft">

                <i class="bi bi-tools text-success"></i>

            </div>

            <div>

                <h6 class="kpi-title">
                    Total de mantenimientos
                </h6>

                <h2 class="kpi-number">{{ $totalMantenimientos }}</h2>

            </div>

        </div>

    </div>

    {{-- TOTAL PRODUCTOS --}}
    <div class="col-md-4 mb-4">

        <div class="kpi-card">

            <div class="kpi-icon bg-warning-soft">

                <i class="bi bi-box-seam text-warning"></i>

            </div>

            <div>

                <h6 class="kpi-title">
                    Cantidad de productos
                </h6>

                <h2 class="kpi-number">{{ $totalProductos }}</h2>

            </div>

        </div>

    </div>

</div>







{{-- ===================================================== --}}
{{-- TABLAS --}}
{{-- ===================================================== --}}
<div class="row">

    {{-- ÚLTIMOS MANTENIMIENTOS --}}
    <div class="col-md-7 mb-4">

        <div class="card border-0 shadow-sm report-card">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-0">
                    Últimos mantenimientos
                </h5>

            </div>

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Proyecto</th>
                            <th>Técnico</th>
                            <th>Fecha</th>
                            <th>Estado</th>

                        </tr>

                    </thead>

                    <tbody>
    @foreach($ultimosMantenimientos as $manto)
    <tr>
        {{-- Accedemos a la relación 'proyecto' y luego a su 'nombre' --}}
        <td>{{ $manto->proyecto ? $manto->proyecto->nombre : 'Sin proyecto' }}</td>
        
        {{-- Accedemos a la relación 'tecnico' y luego a su 'name' (o el campo que tenga el nombre) --}}
        <td>{{ $manto->tecnico ? $manto->tecnico->name : 'N/A' }}</td>
        
        <td>{{ $manto->fecha_mantenimiento ? \Carbon\Carbon::parse($manto->fecha_mantenimiento)->format('d/m/Y') : 'N/A' }}</td>
        
        <td>
            <span class="badge {{ $manto->estado == 'realizado' ? 'bg-success' : 'bg-warning' }}">
                {{ ucfirst($manto->estado) }}
            </span>
        </td>
    </tr>
    @endforeach
</tbody>

                </table>

            </div>

        </div>

    </div>






    {{-- PRODUCTOS CON STOCK BAJO --}}
<div class="col-md-5 mb-4">
    <div class="card border-0 shadow-sm report-card">
        <div class="card-header bg-white border-0">
            <h5 class="fw-bold mb-0">Productos con stock bajo</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
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
                                <span class="badge {{ $prod->estado == 'agotado' ? 'bg-danger' : 'bg-warning' }}">
                                    {{ str_replace('_', ' ', $prod->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                ¡Todo al día! No hay alertas de inventario.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>







{{-- ===================================================== --}}
{{-- ACTIVIDAD RECIENTE --}}
{{-- ===================================================== --}}
<div class="card border-0 shadow-sm report-card">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold mb-0">
            Actividad reciente
        </h5>

    </div>

    <div class="card-body">

        <div class="card-body">
    @foreach($actividadReciente as $actividad)
        <div class="activity-item">
            <div class="activity-icon {{ str_contains($actividad['titulo'], 'Proyecto') ? 'primary' : 'success' }}">
                <i class="bi {{ str_contains($actividad['titulo'], 'Proyecto') ? 'bi-folder-plus' : 'bi-box-arrow-in-down' }}"></i>
            </div>
            <div>
                <h6 class="mb-1">{{ $actividad['titulo'] }}</h6>
                <small class="text-muted">
                    {{ $actividad['detalle'] }} • {{ $actividad['fecha']->diffForHumans() }}
                </small>
            </div>
        </div>
    @endforeach
</div>

    </div>

</div>

@endsection
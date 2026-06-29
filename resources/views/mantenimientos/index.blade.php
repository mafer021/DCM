@extends('layouts.app')

@section('contenido')

{{-- ENCABEZADO --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Mantenimientos</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoMantenimiento">
        <i class="bi bi-plus-circle me-2"></i> Registrar mantenimiento
    </button>
</div>

{{-- BUSCADOR --}}
<div class="row mb-4">
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            {{-- Añadimos el id="buscadorMantenimientos" para vincularlo con el script --}}
            <input type="text" id="buscadorMantenimientos" class="form-control border-start-0" placeholder="Buscar por proyecto o por técnico...">
        </div>
    </div>
</div>

{{-- MENSAJES DE ÉXITO --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- TABLA --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Proyecto</th>
                    <th>Cliente</th>
                    <th>Fecha mantenimiento</th>
                    <th>Técnico</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mantenimientos as $mantenimiento)
                    {{-- Guardamos los atributos data para el buscador dinámico por filas --}}
                    <tr class="fila-mantenimiento" 
                        data-proyecto="{{ $mantenimiento->proyecto->nombre ?? '' }}" 
                        data-tecnico="{{ ($mantenimiento->tecnico->nombre ?? '') . ' ' . ($mantenimiento->tecnico->apellido_paterno ?? '') }}">
                        
                        <td class="fw-semibold">{{ $mantenimiento->proyecto->nombre ?? 'No especificado' }}</td>
                        <td>{{ $mantenimiento->proyecto->cliente ?? 'No especificado' }}</td>
                        <td>{{ date('d/m/Y', strtotime($mantenimiento->fecha_mantenimiento)) }}</td>
                        <td>{{ ($mantenimiento->tecnico->nombre ?? '') . ' ' . ($mantenimiento->tecnico->apellido_paterno ?? '') }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success fw-bold p-2 border border-success-subtle rounded">
                                <i class="bi bi-check-circle-fill me-1"></i> Realizado
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">

                                {{-- VER HISTORIAL (Llama a JS mandando los parámetros del proyecto) --}}
                                <button class="btn btn-sm btn-light" 
                                        onclick="verHistorialProyecto({{ $mantenimiento->proyecto_id }}, '{{ addslashes($mantenimiento->proyecto->nombre ?? '') }}')">
                                    👁️
                                </button>

                                {{-- EDITAR (Llama a JS inyectando todos los datos sin duplicar modales) --}}
                                <button class="btn btn-sm btn-light" 
                                        onclick="abrirModalEditarMantenimiento(
                                            {{ $mantenimiento->id }},
                                            '{{ addslashes($mantenimiento->proyecto->nombre ?? '') }}',
                                            {{ $mantenimiento->tecnico_id }},
                                            '{{ $mantenimiento->fecha_mantenimiento }}',
                                            {{ json_encode($mantenimiento->observaciones ?? '') }}
                                        )">
                                    ✏️
                                </button>
                                {{-- ELIMINAR --}}
    <form action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" 
          method="POST" 
          style="display:inline-block;" 
          onsubmit="return confirm('¿Estás seguro de eliminar este registro? No se puede deshacer.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-light" title="Eliminar registro">
            🗑️
        </button>
    </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay mantenimientos registrados en la bitácora.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================================================== --}}
{{-- MODAL REGISTRAR MANTENIMIENTO --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalNuevoMantenimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h4 class="fw-bold mb-0">Registrar mantenimiento</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- Agregamos la etiqueta <form> conectada a tu ruta .store --}}
            <form action="{{ route('mantenimientos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- AÑADE ESTO PARA MOSTRAR ERRORES --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <script>
            // Este script fuerza a que el modal se abra automáticamente si hubo errores
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('modalNuevoMantenimiento')).show();
            });
        </script>
    @endif
                    <div class="row">
                        {{-- PROYECTO --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Proyecto</label>
                            {{-- PROYECTO (Añade el 'selected' para que recuerde el proyecto) --}}
<select name="proyecto_id" class="form-select" required>
    <option value="">Seleccionar proyecto</option>
    @foreach($proyectos as $p)
        <option value="{{ $p->id }}" {{ old('proyecto_id') == $p->id ? 'selected' : '' }}>
            {{ $p->nombre }} ({{ $p->cliente }})
        </option>
    @endforeach
</select>
                        </div>

                        {{-- TECNICO --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Técnico responsable</label>
                            <select name="tecnico_id" class="form-select" required>
    <option value="">Seleccionar técnico</option>
    @foreach($tecnicos as $t)
        <option value="{{ $t->id }}" {{ old('tecnico_id') == $t->id ? 'selected' : '' }}>
            {{ $t->nombre }} {{ $t->apellido_paterno }}
        </option>
    @endforeach
</select>
                        </div>

                        {{-- FECHA --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Fecha mantenimiento</label>
                            {{-- Ponemos la fecha de hoy por defecto automáticamente --}}
                            <input type="date" name="fecha_mantenimiento" class="form-control" 
       value="{{ old('fecha_mantenimiento', date('Y-m-d')) }}" required>
                        </div>

                        {{-- OBSERVACIONES --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="4">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> Guardar mantenimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- MODAL DETALLE MANTENIMIENTO (HISTORIAL) --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalDetalleMantenimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                {{-- Añadimos id="historialModalTitulo" para inyectar dinámicamente el nombre del proyecto --}}
                <h4 class="fw-bold mb-0" id="historialModalTitulo">Historial de mantenimientos</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha mantenimiento</th>
                                <th>Técnico</th>
                                <th>Observaciones</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        {{-- Ponemos id="tablaHistorialContenido" para que JS pinte aquí las filas --}}
                        <tbody id="tablaHistorialContenido">
                            {{-- Filas dinámicas vía AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- MODAL EDITAR MANTENIMIENTO --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalEditarMantenimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h4 class="fw-bold mb-0">Editar mantenimiento</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- Añadimos id="formEditarMantenimiento" para cambiar el action con JS --}}
            <form id="formEditarMantenimiento" method="POST">
                @csrf
                @method('PUT')
                {{-- Solo muestra errores si hay errores de validación activos --}}
@if ($errors->any() && session()->has('error_edicion'))
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new bootstrap.Modal(document.getElementById('modalEditarMantenimiento')).show();
        });
    </script>
@endif
                <div class="modal-body">
                    <div class="row">
                        {{-- PROYECTO (Solo lectura) --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Proyecto</label>
                            <input type="text" id="edit_proyecto_nombre" class="form-control bg-light" readonly>
                        </div>

                        {{-- TECNICO --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Técnico responsable</label>
                            <select name="tecnico_id" id="edit_tecnico_id" class="form-select" required>
    @foreach($tecnicos as $t)
        {{-- Aquí comparamos contra el 'old' si existe, si no, es carga inicial --}}
        <option value="{{ $t->id }}" {{ old('tecnico_id') == $t->id ? 'selected' : '' }}>
            {{ $t->nombre }} {{ $t->apellido_paterno }}
        </option>
    @endforeach
</select>
                        </div>

                        {{-- FECHA (Solo lectura) --}}
<div class="col-md-6 mb-4">
    <label class="form-label fw-semibold">Fecha mantenimiento</label>
    <input type="date" name="fecha_mantenimiento" id="edit_fecha_mantenimiento" 
           class="form-control bg-light" readonly>
    <small class="text-muted">Si la fecha es incorrecta, elimina el registro.</small>
</div>

                        {{-- OBSERVACIONES --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Observaciones</label>
                            <textarea name="observaciones" id="edit_observaciones" 
          class="form-control" rows="4">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @vite('resources/js/mantenimientos.js')
@endpush
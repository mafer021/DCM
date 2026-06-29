@extends('layouts.app')

@section('contenido')

{{-- ENCABEZADO --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Proyectos</h2>
    
    {{-- Mantenemos el ID del modal que tú pusiste: #modalNuevoProyecto --}}
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto" onclick="abrirModalCrear()">
        + Nuevo proyecto
    </button>
</div>

{{-- Alerta flotante de éxito --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div>
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- FILTROS --}}
<div class="row mb-4">
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            {{-- Le agregamos un ID para cuando hagamos el buscador con JavaScript --}}
            <input type="text" id="inputBuscarProyecto" class="form-control border-start-0" placeholder="Buscar por nombre, cliente y estado....">
        </div>
    </div>
</div>

{{-- TABLA --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="tablaProyectos">
            <thead class="table-light">
                <tr>
                    <th>Proyecto</th>
                    <th>Cliente</th>
                    <th>Tipo instalación</th>
                    <th>Fecha instalación</th>
                    <th>Próx. mantenimiento</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    {{-- Recorremos los proyectos reales que vienen del controlador --}}
    @forelse($proyectos as $proyecto)
        {{-- MODIFICACIÓN AQUÍ: Añadimos la clase y los atributos data --}}
        <tr class="fila-proyecto" 
            data-nombre="{{ $proyecto->nombre }}" 
            data-cliente="{{ $proyecto->cliente }}" 
            data-estado="{{ $proyecto->estado }}">

            {{-- 1. Nombre del proyecto --}}
            <td class="fw-semibold">{{ $proyecto->nombre }}</td>

            {{-- 2. Cliente --}}
            <td>{{ $proyecto->cliente }}</td>

            {{-- 3. Tipo de instalación --}}
            <td>{{ $proyecto->tipoInstalacion->nombre ?? 'No especificado' }}</td>

            {{-- 4. Fecha de instalación formateada simple --}}
            <td>{{ date('d/m/Y', strtotime($proyecto->fecha_instalacion)) }}</td>

            {{-- 5. Próximo mantenimiento formateado simple --}}
            <td>{{ date('d/m/Y', strtotime($proyecto->proximo_mantenimiento)) }}</td>

           {{-- 6. Estado Dinámico calculado al instante --}}
<td>
    @php
    $fechaMantenimiento = \Carbon\Carbon::parse($proyecto->proximo_mantenimiento);
    $hoy = \Carbon\Carbon::today();
    $en30Dias = \Carbon\Carbon::today()->addDays(30);

    // 1. Si la fecha ya pasó (es menor a hoy)
    if ($fechaMantenimiento->lt($hoy)) {
        $estadoVisual = 'vencido';
    } 
    // 2. Si la fecha está entre HOY y dentro de 30 días
    elseif ($fechaMantenimiento->between($hoy, $en30Dias)) {
        $estadoVisual = 'proximo';
    } 
    // 3. De lo contrario, está al día
    else {
        $estadoVisual = 'al_dia';
    }
@endphp

    @if($estadoVisual == 'vencido')
        <span class="badge bg-danger-subtle text-danger fw-bold p-2 border border-danger-subtle rounded">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Vencido
        </span>
    @elseif($estadoVisual == 'proximo')
        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold p-2 border border-warning-subtle rounded">
            <i class="bi bi-clock-history me-1"></i> Próximo
        </span>
    @else
        <span class="badge bg-success-subtle text-success fw-bold p-2 border border-success-subtle rounded">
            <i class="bi bi-check-circle-fill me-1"></i> Al día
        </span>
    @endif
</td>

            {{-- 7. Botones de Acciones --}}
            <td>
                {{-- BOTÓN DEL OJITO REPARADO --}}
                <button class="btn btn-sm btn-light" 
                        onclick="verDetallesProyecto(
                            {{ $proyecto->id }}, 
                            '{{ addslashes($proyecto->nombre) }}', 
                            '{{ addslashes($proyecto->cliente) }}', 
                            '{{ $proyecto->tipoInstalacion->nombre ?? 'No asignado' }}', 
                            '{{ $proyecto->fecha_instalacion }}', 
                            '{{ $proyecto->proximo_mantenimiento }}', 
                            '{{ $proyecto->estado }}',
                            {{ json_encode($proyecto->direccion) }}, 
                            {{ json_encode($proyecto->descripcion ?? '') }},
                            {{ json_encode($proyecto->documentos) }}
                        )">
                    👁️
                </button>
                
                {{-- BOTÓN EDITAR PROYECTO --}}
                <button class="btn btn-sm btn-light" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalEditarProyecto"
                        onclick="abrirModalEditar(
                            {{ $proyecto->id }}, 
                            '{{ $proyecto->nombre }}', 
                            '{{ $proyecto->cliente }}', 
                            {{ $proyecto->tipo_instalacion_id }}, 
                            '{{ $proyecto->fecha_instalacion }}', 
                            {{ json_encode($proyecto->direccion) }}, 
                            {{ json_encode($proyecto->descripcion) }}
                        )">
                    ✏️
                </button>
            </td>
        </tr>
    @empty
        {{-- Por si la base de datos está vacía --}}
        <tr>
            <td colspan="7" class="text-center text-muted py-4">
                No se encontraron proyectos registrados.
            </td>
        </tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>




{{-- ===================================================== --}}
{{-- MODAL NUEVO PROYECTO --}}
{{-- ===================================================== --}}
<div class="modal fade @if($errors->any() && !session('error_en_editar')) show @endif" 
     id="modalNuevoProyecto" 
     tabindex="-1" 
     aria-labelledby="modalNuevoProyectoLabel" 
     style="@if($errors->any() && !session('error_en_editar')) display: block; @else display: none; @endif"
     aria-hidden="@if($errors->any() && !session('error_en_editar')) false @else true @endif">
     
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            
            <form action="{{ route('proyectos.store') }}" method="POST" id="formNuevoProyecto">
                @csrf

                <div class="modal-header">
                    <div>
                        <h4 class="fw-bold mb-0" id="modalNuevoProyectoLabel">Nuevo proyecto</h4>
                        <small class="text-muted">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        
                        {{-- NOMBRE PROYECTO --}}
                        <div class="col-md-6 mb-4">
                            <label for="nuevo_nombre" class="form-label fw-semibold">
                                Nombre proyecto <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nuevo_nombre" name="nombre" 
                                   value="{{ old('nombre') }}" required maxlength="150">
                            <small class="text-muted d-block mt-1">Si el cliente tiene varias sucursales, incluye la zona o un número.</small>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CLIENTE --}}
                        <div class="col-md-6 mb-4">
                            <label for="nuevo_cliente" class="form-label fw-semibold">
                                Cliente <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('cliente') is-invalid @enderror" 
                                   id="nuevo_cliente" name="cliente" 
                                   value="{{ old('cliente') }}" required maxlength="150">
                            @error('cliente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TIPO INSTALACIÓN --}}
                        <div class="col-md-6 mb-4">
                            <label for="nuevo_tipo_instalacion_id" class="form-label fw-semibold">
                                Tipo instalación <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('tipo_instalacion_id') is-invalid @enderror" 
                                    id="nuevo_tipo_instalacion_id" name="tipo_instalacion_id" required>
                                <option value="" selected disabled>Seleccionar tipo</option>
                                @foreach($tiposInstalacion as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_instalacion_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_instalacion_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FECHA INSTALACIÓN --}}
                        <div class="col-md-6 mb-4">
                            <label for="nuevo_fecha_instalacion" class="form-label fw-semibold">
                                Fecha instalación <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('fecha_instalacion') is-invalid @enderror" 
                                   id="nuevo_fecha_instalacion" name="fecha_instalacion" 
                                   value="{{ old('fecha_instalacion') }}" required>
                            @error('fecha_instalacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PRÓXIMO MANTENIMIENTO --}}
                        <div class="col-md-6 mb-4">
                            <label for="nuevo_proximo_mantenimiento" class="form-label fw-semibold">Próximo mantenimiento</label>
                            <input type="date" class="form-control bg-light" id="nuevo_proximo_mantenimiento" readonly>
                            <small class="text-muted">Se calcula automáticamente a un año.</small>
                        </div>

                        {{-- DIRECCIÓN --}}
                        <div class="col-md-6 mb-4">
                            <label for="nuevo_direccion" class="form-label fw-semibold">
                                Dirección <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                      id="nuevo_direccion" name="direccion" rows="3" required>{{ old('direccion') }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DESCRIPCIÓN (Cambiado a col-md-12 para abarcar todo el ancho abajo limpio) --}}
                        <div class="col-md-12 mb-4">
                            <label for="nuevo_descripcion" class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="nuevo_descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                        <i class="bi bi-floppy"></i> Guardar proyecto
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Pequeño fondo oscuro de Bootstrap por si hay errores al recargar --}}
@if($errors->any())
    <div class="modal-backdrop fade show"></div>
@endif



{{-- ===================================================== --}}
{{-- MODAL DETALLE PROYECTO --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalDetalleProyecto" tabindex="-1" aria-labelledby="modalDetalleProyectoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 bg-light">

            <div class="modal-header bg-white border-0 pt-4 px-4">
                {{-- ID para cambiar el título dinámicamente --}}
                <h3 class="fw-bold text-dark mb-0" id="det_modal_titulo">Proyecto</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">

                {{-- INFORMACION DEL PROYECTO (DISEÑO TIPO TARJETA) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-secondary mb-4">Información del proyecto</h5>

                        <div class="row g-3">
                            {{-- CLIENTE --}}
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-5 fw-semibold text-muted">Cliente:</div>
                                    <div class="col-sm-7 text-dark fw-medium" id="det_cliente"></div>
                                </div>
                            </div>

                            {{-- TIPO DE INSTALACIÓN --}}
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-5 fw-semibold text-muted">Tipo de instalación:</div>
                                    <div class="col-sm-7 text-dark fw-medium" id="det_tipo_instalacion"></div>
                                </div>
                            </div>

                            {{-- FECHA DE INSTALACIÓN --}}
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-5 fw-semibold text-muted">Fecha de instalación:</div>
                                    <div class="col-sm-7 text-dark fw-medium" id="det_fecha_instalacion"></div>
                                </div>
                            </div>

                            {{-- PRÓXIMO MANTENIMIENTO --}}
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-5 fw-semibold text-muted">Próximo mantenimiento:</div>
                                    <div class="col-sm-7">
                                        <span class="text-dark fw-medium" id="det_proximo_mantenimiento"></span>
                                        <span class="text-muted small ms-1" id="det_dias_restantes"></span>
                                        <span class="badge ms-2 px-2" id="det_badge_estado" style="font-size: 0.75rem;"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- DIRECCIÓN --}}
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-2 fw-semibold text-muted" style="max-width: 20.833%;">Dirección:</div>
                                    <div class="col-sm-10 text-dark fw-medium" id="det_direccion"></div>
                                </div>
                            </div>

                            {{-- DESCRIPCIÓN --}}
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-2 fw-semibold text-muted" style="max-width: 20.833%;">Descripción:</div>
                                    <div class="col-sm-10 text-muted" id="det_descripcion"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CONTENIDO EN PANELES (TABS) --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 px-4">
        <ul class="nav nav-tabs card-header-tabs" id="projectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold border-0" 
                        id="documentos-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#documentos" 
                        type="button" 
                        role="tab" 
                        aria-controls="documentos" 
                        aria-selected="true">
                    Documentos del proyecto
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold border-0" 
                        id="mantenimientos-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#mantenimientos" 
                        type="button" 
                        role="tab" 
                        aria-controls="mantenimientos" 
                        aria-selected="false">
                    Historial de mantenimientos
                </button>
            </li>
        </ul>
    </div>

                    <div class="card-body p-4 bg-white rounded-bottom">
                        <div class="tab-content" id="projectTabsContent">

                            {{-- PANEL: DOCUMENTOS --}}
                            <div class="tab-pane fade show active" id="documentos" role="tabpanel">
                                <div class="d-flex justify-content-end mb-3">
                                    <button class="btn btn-success px-3 fw-medium" onclick="abrirModalSubirDocumento()">
                                        + Subir documento
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle" id="tablaDetalleDocumentos">
                                        <thead class="table-light text-secondary">
                                            <tr>
                                                <th class="py-3 px-3">Documento</th>
                                                <th class="py-3">Tipo</th>
                                                <th class="py-3">Fecha de subida</th>
                                                <th class="py-3 text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- PANEL: MANTENIMIENTOS --}}
<div class="tab-pane fade" id="mantenimientos" role="tabpanel">
    <div class="table-responsive">
        <table class="table align-middle" id="tablaDetalleMantenimientos">
            <thead class="table-light text-secondary">
                <tr>
                    <th class="py-3 px-3">Fecha</th>
                    <th class="py-3">Técnico</th>
                    <th class="py-3">Observaciones</th>
                    <th class="py-3">Estado</th>
                </tr>
            </thead>
            {{-- Le añadimos el ID para manipularlo desde JavaScript --}}
            <tbody id="tbodyDetalleMantenimientos">
                {{-- Aquí se inyectarán las filas dinámicamente vía AJAX --}}
            </tbody>
        </table>
    </div>
</div>

                        </div> {{-- Cierra tab-content --}}
                    </div> {{-- Cierra card-body de las pestañas --}}
                </div> {{-- Cierra card de las pestañas --}}

            </div> {{-- Cierra modal-body --}}
        </div> {{-- Cierra modal-content --}}
    </div> {{-- Cierra modal-dialog --}}
</div> {{-- Cierra modal fade --}}


{{-- ===================================================== --}}
{{-- MODAL SUBIR DOCUMENTO (GLOBAL)                        --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" id="formSubirDocumento">
                @csrf
                {{-- Campo oculto para asociar el documento al proyecto actual --}}
                <input type="hidden" name="proyecto_id" id="doc_proyecto_id">

                <div class="modal-header bg-light">
                    <h5 class="fw-bold text-dark m-0" id="modalDocumentoLabel">
                        <i class="bi bi-file-earmark-arrow-up text-success me-2"></i>Subir documento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">

                    {{-- BLOQUE DE MENSAJES DE ERROR DE LARAVEL --}}
    @if ($errors->any())
        <div class="alert alert-danger p-3 mb-4 rounded-3 border-0">
            <h6 class="fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Error al subir el archivo:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

                    {{-- TIPO DOCUMENTO (REACTIVADO DINÁMICAMENTE) --}}
                    <div class="mb-4">
                        <label for="doc_tipo" class="form-label fw-semibold text-secondary">Tipo documento *</label>
                        <select class="form-select" id="doc_tipo" name="tipo_documento_id" required>
                            <option value="" selected disabled>Seleccionar tipo</option>
                            {{-- Trae los tipos reales de tu catálogo de la base de datos --}}
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ARCHIVO (CORREGIDO EL NAME) --}}
                    <div class="mb-3">
                        <label for="doc_archivo" class="form-label fw-semibold text-secondary">Archivo *</label>
                        {{-- Se cambió name="archivo" por name="doc_archivo" para que coincida con el controlador --}}
                        <input type="file" class="form-control" id="doc_archivo" name="doc_archivo" required>
                        <div class="form-text text-muted small mt-1">
                            <i class="bi bi-info-circle me-1"></i> Formatos permitidos: PDF, JPG, PNG, DOC, XLSX. (Máx. 10MB)
                        </div>
                        
                        {{-- Div de error oculto para el control de los 10MB en JavaScript --}}
                        <div class="invalid-feedback d-none text-danger fw-bold mt-2" id="error_peso_documento">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> El archivo supera el límite permitido de 10 MB.
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top bg-light p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2 shadow-sm" id="btnGuardarDocumento">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Subir documento
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>




{{-- ===================================================== --}}
{{-- MODAL EDITAR PROYECTO --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalEditarProyecto" tabindex="-1" aria-labelledby="modalEditarProyectoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            
            @php
                $urlAction = '';
                if(session('error_en_editar')) {
                    $urlAction = '/proyectos/' . session('error_en_editar');
                }
            @endphp

            <form action="{{ $urlAction }}" method="POST" id="formEditarProyecto">
                @csrf
                @method('PUT')

                @if(session('error_en_editar'))
                    <input type="hidden" class="is-invalid d-none" id="marca_error_editar" value="{{ session('error_en_editar') }}">
                @endif

                <div class="modal-header">
                    <h4 class="fw-bold" id="modalEditarProyectoLabel">Editar proyecto</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row">

                        {{-- NOMBRE PROYECTO --}}
                        <div class="col-md-6 mb-3">
                            <label for="edit_nombre" class="form-label fw-semibold text-secondary">Nombre proyecto</label>
                            <input type="text" 
                                   class="form-control @error('nombre') @if(session('error_en_editar')) is-invalid @endif @enderror" 
                                   id="edit_nombre" 
                                   name="nombre" 
                                   value="{{ session('error_en_editar') ? old('nombre') : '' }}" 
                                   required maxlength="150">
                            @error('nombre')
                                @if(session('error_en_editar'))
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        {{-- CLIENTE --}}
                        <div class="col-md-6 mb-3">
                            <label for="edit_cliente" class="form-label fw-semibold text-secondary">Cliente</label>
                            <input type="text" 
                                   class="form-control @error('cliente') @if(session('error_en_editar')) is-invalid @endif @enderror" 
                                   id="edit_cliente" 
                                   name="cliente" 
                                   value="{{ session('error_en_editar') ? old('cliente') : '' }}" 
                                   required maxlength="150">
                            @error('cliente')
                                @if(session('error_en_editar'))
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        {{-- TIPO INSTALACIÓN --}}
                        <div class="col-md-6 mb-3">
                            <label for="edit_tipo_instalacion_id" class="form-label fw-semibold text-secondary">Tipo instalación</label>
                            <select class="form-select @error('tipo_instalacion_id') @if(session('error_en_editar')) is-invalid @endif @enderror" 
                                    id="edit_tipo_instalacion_id" 
                                    name="tipo_instalacion_id" 
                                    required>
                                <option value="" disabled>Seleccionar tipo</option>
                                @foreach($tiposInstalacion as $tipo)
                                    <option value="{{ $tipo->id }}" {{ (session('error_en_editar') && old('tipo_instalacion_id') == $tipo->id) ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_instalacion_id')
                                @if(session('error_en_editar'))
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        {{-- FECHA INSTALACIÓN --}}
                        <div class="col-md-6 mb-3">
                            <label for="edit_fecha_instalacion" class="form-label fw-semibold text-secondary">Fecha instalación</label>
                            <input type="date" 
                                   class="form-control @error('fecha_instalacion') @if(session('error_en_editar')) is-invalid @endif @enderror" 
                                   id="edit_fecha_instalacion" 
                                   name="fecha_instalacion" 
                                   value="{{ session('error_en_editar') ? old('fecha_instalacion') : '' }}" 
                                   required>
                            @error('fecha_instalacion')
                                @if(session('error_en_editar'))
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        {{-- DIRECCIÓN (Cambiado a col-md-12 para abarcar todo el ancho completo) --}}
                        <div class="col-md-12 mb-3">
                            <label for="edit_direccion" class="form-label fw-semibold text-secondary">Dirección</label>
                            <textarea class="form-control @error('direccion') @if(session('error_en_editar')) is-invalid @endif @enderror" 
                                      id="edit_direccion" 
                                      name="direccion" 
                                      rows="2" 
                                      style="resize: none;"
                                      required>{{ session('error_en_editar') ? old('direccion') : '' }}</textarea>
                            @error('direccion')
                                @if(session('error_en_editar'))
                                    <div class="invalid-feedback fw-bold d-block">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        {{-- DESCRIPCIÓN (Compactado al máximo para que no se desborde) --}}
                        <div class="col-md-12 mb-2">
                            <label for="edit_descripcion" class="form-label fw-semibold text-secondary mb-1">Descripción</label>
                            <textarea class="form-control @error('descripcion') @if(session('error_en_editar')) is-invalid @endif @enderror" 
                                      id="edit_descripcion" 
                                      name="descripcion" 
                                      style="resize: none;"
                                      rows="2">{{ session('error_en_editar') ? old('descripcion') : '' }}</textarea>
                            @error('descripcion')
                                @if(session('error_en_editar'))
                                    <div class="invalid-feedback fw-bold d-block" style="font-size: 0.8rem; margin-top: 2px;">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                    </div> {{-- Cierra row --}}
                </div> {{-- Cierra modal-body --}}

                <div class="modal-footer border-0 pt-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                        <i class="bi bi-floppy"></i> Guardar cambios
                    </button>
                </div>

            </form>
        </div> {{-- Cierra modal-content --}}
    </div> {{-- Cierra modal-dialog --}}
</div> {{-- Cierra modal fade --}}

@endsection

@push('scripts')
    @vite('resources/js/proyectos.js')

    {{-- Solo abrir el modal si el error viene del formulario de documentos --}}
    @if ($errors->has('doc_archivo') || $errors->has('tipo_documento_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalDocumento = new bootstrap.Modal(document.getElementById('modalDocumento'));
                modalDocumento.show();
            });
        </script>
    @endif
@endpush
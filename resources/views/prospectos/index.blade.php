@extends('layouts.app')

@section('contenido')

<div class="prospectos-container">

    <!-- Encabezado -->
    <div class="prospectos-header">

        <div>

            <h2 class="prospectos-title">
                Prospectos
            </h2>

            <p class="prospectos-subtitle">
                Gestiona y da seguimiento a los prospectos registrados.
            </p>

        </div>

        <!-- Botón Nuevo Prospecto -->
        <button
            type="button"
            class="btn btn-success btn-nuevo-prospecto"
            data-bs-toggle="modal"
            data-bs-target="#modalNuevoProspecto">

            <i class="bi bi-plus-lg"></i>

            Nuevo prospecto

        </button>

    </div>

    <!-- ALERTA DE ÉXITO -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Barra de búsqueda (Ahora está fuera de la tarjeta) -->
<!-- Barra de búsqueda -->
<div class="buscador-container mb-3">
    <i class="bi bi-search"></i>
    <input type="text" id="inputBuscarProspecto" class="form-control" placeholder="Buscar por nombre o teléfono">
</div>

    <!-- Tarjeta -->
    <div class="prospectos-card">
        <!-- Tabla -->
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-header-gray">

                    <tr>

                        <th width="22%">
                            Nombre
                        </th>

                        <th width="18%">
                            Número de teléfono
                        </th>

                        <th width="15%">
                            Dejó documento
                        </th>

                        <th width="18%">
                            Estado del prospecto
                        </th>

                        <th width="12%">
                            Estado
                        </th>

                        <th width="15%" class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody id="tablaProspectosBody">
    @forelse($prospectos as $prospecto)
        <tr>
            <td>{{ $prospecto->nombre }} {{ $prospecto->apellido_paterno }} {{ $prospecto->apellido_materno }}</td>
            <td>{{ $prospecto->telefono }}</td>
            <td>
                @if($prospecto->dejo_documento)
                    <span class="text-success">Sí: {{ $prospecto->detalle_documento }}</span>
                @else
                    <span class="text-secondary">No</span>
                @endif
            </td>
            <td>
                <span class="badge bg-info text-dark">
                    {{ $prospecto->estadoProspecto->nombre ?? 'N/A' }}
                </span>
            </td>
            <td>
                @if($prospecto->estado == 'activo')
                    <span class="badge bg-primary">Activo</span>
                @else
                    <span class="badge bg-danger">Inactivo</span>
                @endif
            </td>
            <td class="text-center">
                <!-- Editar -->
                @if($prospecto->estado === 'activo')
                <button 
                    class="btn btn-warning btn-sm btn-editar" 
                    title="Editar" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalEditarProspecto"
                    data-id="{{ $prospecto->id }}"
                    data-nombre="{{ $prospecto->nombre }}"
                    data-apellido-paterno="{{ $prospecto->apellido_paterno }}"
                    data-apellido-materno="{{ $prospecto->apellido_materno }}"
                    data-telefono="{{ $prospecto->telefono }}"
                    data-tipo-instalacion="{{ $prospecto->tipo_instalacion_id }}"
                    data-dejo-documento="{{ $prospecto->dejo_documento }}"
                    data-detalle-documento="{{ $prospecto->detalle_documento }}"
                    data-estado-prospecto="{{ $prospecto->estado_prospecto_id }}"
                    data-notas="{{ $prospecto->notas }}">
                    <i class="bi bi-pencil-fill"></i>
               </button>
               @else
               <!-- Botón deshabilitado si está inactivo -->
                <button type="button" class="btn btn-secondary btn-sm" disabled title="No se puede editar un prospecto inactivo">
                   <i class="bi bi-pencil-fill"></i>
               </button>
                @endif
                <!-- Botón Cambiar Estado (Activar/Desactivar) -->
               <form action="{{ route('prospectos.toggleStatus', $prospecto->id) }}" method="POST" style="display:inline;">
                     @csrf
                     @method('PATCH')
                     <button type="submit" class="btn btn-secondary btn-sm" title="Cambiar estado">
                          🔄
                     </button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center py-5">
                <i class="bi bi-person-x fs-1 text-secondary"></i>
                <br>No hay prospectos registrados.
            </td>
        </tr>
    @endforelse
</tbody>

            </table>

        </div>

    </div>

</div>

<!-- =======================================================
            MODAL NUEVO PROSPECTO
======================================================= -->

<div
    class="modal fade"
    id="modalNuevoProspecto"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-person-plus-fill text-success"></i>

                    Nuevo prospecto

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">

                </button>

            </div>

            <div class="modal-body">

                <form action="{{ route('prospectos.store') }}" method="POST">
                    @csrf 

                    <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label">Nombre <span class="text-danger">*</span></label>
                           <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required>

                           @error('nombre')
                          <div class="text-danger small">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" 
                              class="form-control @error('apellido_paterno') is-invalid @enderror" 
                              name="apellido_paterno" 
                              value="{{ old('apellido_paterno') }}" 
                              required>

                              @error('apellido_paterno')
                              <div class="text-danger small">{{ $message }}</div>
                              @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                           <label class="form-label">Apellido materno</label>
                           <input type="text" 
                            class="form-control @error('apellido_materno') is-invalid @enderror" 
                            name="apellido_materno" 
                            value="{{ old('apellido_materno') }}">

                            @error('apellido_materno')
                           <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                   </div>

                    <div class="row">
                       <div class="col-md-6 mb-3">
                            <label class="form-label">Número de teléfono <span class="text-danger">*</span></label>
                            <input type="text" 
                                    class="form-control @error('telefono') is-invalid @enderror" 
                                    name="telefono" 
                                    id="inputTelefono" 
                                    value="{{ old('telefono') }}"
                                    maxlength="10" 
                                    placeholder="Ej. 2381234567"
                                    required>
    
                            <!-- Este es el mensaje del JavaScript -->
                            <div id="feedbackTelefono" class="invalid-feedback">Debe ingresar exactamente 10 dígitos.</div>

                            <!-- Este es el mensaje del servidor (Laravel) -->
                            @error('telefono')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                       </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proyecto de interés <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_instalacion_id') is-invalid @enderror" 
                                    name="tipo_instalacion_id" 
                                    required>
                                <option selected disabled>Seleccione un proyecto</option>
        
                               @foreach($tiposInstalacion as $tipo)
                                  <option value="{{ $tipo->id }}" {{ old('tipo_instalacion_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                   </option>
                                  @endforeach
                                </select>

                             @error('tipo_instalacion_id')
                               <div class="text-danger small">{{ $message }}</div>
                             @enderror
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                              <label class="form-label">¿Dejó documento?</label>
                              <div class="mt-2">
                                   <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('dejo_documento') is-invalid @enderror" 
                                               type="radio" 
                                               name="dejo_documento" 
                                               id="nuevoSiDocumento" 
                                               value="1" 
                                               {{ old('dejo_documento') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="nuevoSiDocumento">Sí</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('dejo_documento') is-invalid @enderror" 
                                               type="radio" 
                                               name="dejo_documento" 
                                               id="nuevoNoDocumento" 
                                               value="0" 
                                               {{ old('dejo_documento', '0') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="nuevoNoDocumento">No</label>
                                    </div>
                                </div>
    
                               @error('dejo_documento')
                                 <div class="text-danger small mt-1">{{ $message }}</div>
                              @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                           <label class="form-label">Estado del prospecto <span class="text-danger">*</span></label>
                           <select class="form-select @error('estado_prospecto_id') is-invalid @enderror" 
                                   name="estado_prospecto_id" 
                                   required>
                                <option selected disabled>Seleccione una opción</option>
        
                                @foreach($estadosProspecto as $estado)
                                    <option value="{{ $estado->id }}" {{ old('estado_prospecto_id') == $estado->id ? 'selected' : '' }}>
                                          {{ $estado->nombre }}
                                    </option>
                              @endforeach
                          </select>

                          @error('estado_prospecto_id')
                            <div class="text-danger small">{{ $message }}</div>
                          @enderror
                       </div>
                    </div>

                    <!-- Documento que dejó -->
                    <div class="row" id="nuevoDocumentoContainer" style="{{ old('dejo_documento') == '1' ? 'display:block;' : 'display:none;' }}">
                          <div class="col-md-12 mb-3">
                               <label class="form-label">Documento que dejó</label>
                               <input type="text" 
                                      class="form-control @error('detalle_documento') is-invalid @enderror" 
                                      name="detalle_documento" 
                                      value="{{ old('detalle_documento') }}"
                                      placeholder="Ejemplo: INE, recibo de luz...">
        
                                @error('detalle_documento')
                                    <div class="text-danger small">{{ $message }}</div>
                               @enderror
                          </div>
                   </div>

                  <!-- Notas -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                         <label class="form-label">Notas</label>
                         <textarea class="form-control @error('notas') is-invalid @enderror" 
                                   name="notas" 
                                   rows="3">{{ old('notas') }}</textarea>
        
                          @error('notas')
                             <div class="text-danger small">{{ $message }}</div>
                         @enderror
                    </div>
              </div>

                  <!-- Mantenemos el botón dentro del form para que funcione el submit -->
                    <div class="modal-footer border-0 p-0 mt-3">
                        <button type="submit" class="btn btn-success">
                             <i class="bi bi-floppy-fill"></i> Guardar prospecto
                        </button>
                  </div>
                </form> 
            </div> <!-- Cierra modal-body -->
        </div> <!-- Cierra modal-content -->
    </div> <!-- Cierra modal-dialog -->
</div> <!-- Cierra modal-fade -->






<!-- =======================================================
                MODAL EDITAR PROSPECTO
======================================================= -->

<div
    class="modal fade"
    id="modalEditarProspecto"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-pencil-square text-warning"></i>

                    Editar prospecto

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">

                </button>

            </div>

            <div class="modal-body">

                <form id="formEditarProspecto" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Campo oculto para el ID (Vital para el UPDATE) -->
                    <input type="hidden" id="editId" name="id" value="{{ old('id') }}">

                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-4 mb-3">

                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="editNombre" name="nombre" value="{{ old('nombre') }}">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        </div>
                        <!-- Apellido paterno -->
                        <div class="col-md-4 mb-3">

                            <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('apellido_paterno') is-invalid @enderror" id="editPaterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}">
                            @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        </div>
                        <!-- Apellido materno -->
                        <div class="col-md-4 mb-3">

                            <label class="form-label">Apellido materno</label>
                            <input type="text" class="form-control @error('apellido_materno') is-invalid @enderror" id="editMaterno" name="apellido_materno" value="{{ old('apellido_materno') }}">
                            @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        </div>

                    </div>

                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Número de teléfono <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="editTelefono" name="telefono" value="{{ old('telefono') }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                            @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        </div>

                        <!-- Proyecto de interés -->
                       <div class="col-md-6 mb-3">
                            <label class="form-label">Proyecto de interés <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_instalacion_id') is-invalid @enderror" id="editProyecto" name="tipo_instalacion_id">
                                <option value="">Seleccione una opción</option>
                                @foreach($tiposInstalacion as $tipo)
                                       <option value="{{ $tipo->id }}" {{ old('tipo_instalacion_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_instalacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    <div class="row">
                        <!-- Dejó documento -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">¿Dejó documento?</label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                     <input class="form-check-input" type="radio" name="dejo_documento" id="editSiDoc" value="1" {{ old('dejo_documento') == '1' ? 'checked' : '' }}>
                                     <label class="form-check-label">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dejo_documento" id="editNoDoc" value="0" {{ old('dejo_documento') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label">No</label>

                                </div>

                            </div>

                        </div>

                        <!-- Estado del prospecto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado del prospecto</label>
                            <select class="form-select @error('estado_prospecto_id') is-invalid @enderror" id="editEstado" name="estado_prospecto_id">
                               <option value="">Seleccione un estado</option>
                               @foreach($estadosProspecto as $estado)
                                   <option value="{{ $estado->id }}" {{ old('estado_prospecto_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                              @endforeach
                          </select>
                            @error('estado_prospecto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                       </div>

                    </div>

                    <!-- Documento detalle -->

                    <div class="row" id="editDocumentoContainer">
                         <div class="col-md-12 mb-3">
                              <label class="form-label">Documento que dejó</label>
                               <input type="text" class="form-control @error('detalle_documento') is-invalid @enderror" id="editDetalle" name="detalle_documento" value="{{ old('detalle_documento') }}">
                                 @error('detalle_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                         </div>
                   </div>
                   <!-- Notas -->
                   <div class="row">
                       <div class="col-md-12 mb-3">
                           <label class="form-label">Notas</label>
                            <textarea class="form-control @error('notas') is-invalid @enderror" id="editNotas" name="notas" rows="3">{{ old('notas') }}</textarea>
                            @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                     </div>
                  </div>
               </form>
          </div>



            <div class="modal-footer">
                <button type="submit" form="formEditarProspecto" class="btn btn-warning text-white">
                      <i class="bi bi-pencil-fill"></i>
                 Actualizar prospecto
                </button>
          </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')
    @vite('resources/js/prospectos.js')

    {{-- Solo intentamos abrir un modal si el controlador nos envió el aviso --}}
    @if (session('modal_abierto'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Identificamos qué modal abrir según lo que el controlador nos dijo
                const modalId = "{{ session('modal_abierto') }}" === 'editar' ? 'modalEditarProspecto' : 'modalNuevoProspecto';
                const modalElement = document.getElementById(modalId);
                
                if (modalElement) {
                    var myModal = new bootstrap.Modal(modalElement);
                    myModal.show();
                }
            });
        </script>
    @endif
@endpush
@extends('layouts.app')

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Usuarios</h2>

    <button class="btn btn-success btn-add-user"
            data-bs-toggle="modal"
            data-bs-target="#modalNuevoUsuario">
        <i class="bi bi-plus-circle me-2"></i>
        Agregar usuario
    </button>
</div>

<div class="row mb-4">
    <div class="col-md-5">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="inputBuscarUsuario" class="form-control" placeholder="Buscar por nombre, rol o estado...">
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
    <tr>
        <td>
            {{ $usuario->nombre }}
            {{ $usuario->apellido_paterno }}
            {{ $usuario->apellido_materno }}
        </td>
        <td>{{ $usuario->email }}</td>
        <td>
            @if($usuario->rol == 'admin')
                <span class="badge badge-admin">Admin</span>
            @else
                <span class="badge badge-tecnico">Empleado</span>
            @endif
        </td>
        <td>
            <form action="{{ route('usuarios.estado', $usuario->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                
                @if($usuario->estado == 'activo')
                    <button type="submit" class="badge badge-activo" style="border: none; cursor: pointer;" title="Click para desactivar usuario">
                        Activo
                    </button>
                @else
                    <button type="submit" class="badge badge-inactivo" style="border: none; cursor: pointer;" title="Click para activar usuario">
                        Inactivo
                    </button>
                @endif
            </form>
        </td>
        <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
        <td>
            <button type="button"
                    class="btn btn-sm btn-light action-btn btn-edit-user"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditarUsuario"
                    data-id="{{ $usuario->id }}"
                    data-nombre="{{ $usuario->nombre }}"
                    data-paterno="{{ $usuario->apellido_paterno }}"
                    data-materno="{{ $usuario->apellido_materno }}"
                    data-email="{{ $usuario->email }}"
                    data-login="{{ $usuario->usuario_login }}"
                    data-rol="{{ $usuario->rol }}"
                    {{-- Si el usuario está inactivo, deshabilitamos el botón por completo --}}
                    @if($usuario->estado === 'inactivo') disabled @endif>
                <i class="bi bi-pencil text-primary"></i>
            </button>
        </td>
    </tr>
@endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL NUEVO USUARIO --}}
<div class="modal fade {{ session('modal') == 'nuevo' ? 'show' : '' }}" 
     id="modalNuevoUsuario" 
     tabindex="-1" 
     style="{{ session('modal') == 'nuevo' ? 'display: block;' : '' }}"
     {!! session('modal') == 'nuevo' ? 'aria-modal="true" role="dialog"' : '' !!}>
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h4 class="fw-bold">Agregar usuario</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ url('/usuarios') }}" method="POST" autocomplete="off" id="formNuevoUsuario">
                @csrf

                <div class="modal-body">
                    <p class="text-muted small mb-4">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text"
                               class="form-control {{ session('modal') == 'nuevo' && $errors->has('nombre') ? 'is-invalid' : '' }}"
                                name="nombre"
                                value="{{ session('modal') == 'nuevo' ? old('nombre') : '' }}"
                                required>
                             <div class="invalid-feedback">
                                {{ $errors->first('nombre') }}
                             </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text"
                            class="form-control {{ session('modal') == 'nuevo' && $errors->has('apellido_paterno') ? 'is-invalid' : '' }}"
                            name="apellido_paterno"
                            value="{{ session('modal') == 'nuevo' ? old('apellido_paterno') : '' }}"
                            required>
                            <div class="invalid-feedback">
                                {{ $errors->first('apellido_paterno') }}
                             </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Apellido materno</label>
                            <input type="text"
                            class="form-control {{ session('modal') == 'nuevo' && $errors->has('apellido_materno') ? 'is-invalid' : '' }}"
                            name="apellido_materno"
                            value="{{ session('modal') == 'nuevo' ? old('apellido_materno') : '' }}">
                            <div class="invalid-feedback">
                                {{ $errors->first('apellido_materno') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email"
                            class="form-control {{ session('modal') == 'nuevo' && $errors->has('email') ? 'is-invalid' : '' }}"
                            name="email"
                            value="{{ session('modal') == 'nuevo' ? old('email') : '' }}"
                            placeholder="ejemplo@correo.com"
                            required>
                            <div class="invalid-feedback" id="email-error-custom">
                                {{ $errors->first('email') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Usuario (login) <span class="text-danger">*</span></label>
                            <input type="text"
                            class="form-control {{ session('modal') == 'nuevo' && $errors->has('usuario_login') ? 'is-invalid' : '' }}"
                            name="usuario_login"
                            value="{{ session('modal') == 'nuevo' ? old('usuario_login') : '' }}"
                            placeholder="Ej. hola123"
                            required>
                           <div class="invalid-feedback">
                                {{ $errors->first('usuario_login') }}
                           </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password"
                            class="form-control {{ session('modal') == 'nuevo' && $errors->has('password') ? 'is-invalid' : '' }}"
                            name="password"
                            autocomplete="new-password"
                            maxlength="8"
                            minlength="8"
                            required>
                           <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                            <small class="text-muted">Debe tener exactamente 8 caracteres, una mayúscula, letras y números.</small>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                            <select
                            class="form-select {{ session('modal') == 'nuevo' && $errors->has('rol') ? 'is-invalid' : '' }}"
                            name="rol"
                            required>
                                <option value="">Seleccionar rol</option>
                                @if($totalAdmins < 3 || (session('modal') == 'nuevo' && old('rol') == 'admin'))
                                    <option value="admin" {{ (session('modal') == 'nuevo' && old('rol') == 'admin') ? 'selected' : '' }}>Administrador</option>
                                @endif
                                <option value="empleado" {{ (session('modal') == 'nuevo' && old('rol') == 'empleado') ? 'selected' : '' }}>Empleado</option>
                            </select>
                            <div class="invalid-feedback">
                                {{ $errors->first('rol') }}
                           </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success btn-save-user">
                        <i class="bi bi-floppy me-2"></i> Guardar usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDITAR USUARIO --}}
<div class="modal fade {{ session('modal') == 'editar' ? 'show' : '' }}" 
     id="modalEditarUsuario" 
     tabindex="-1" 
     style="{{ session('modal') == 'editar' ? 'display: block;' : '' }}"
     {!! session('modal') == 'editar' ? 'aria-modal="true" role="dialog"' : '' !!}>
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h4 class="fw-bold">Editar usuario</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ url('/usuarios/' . (session('usuario_id') ?? '0')) }}" method="POST" autocomplete="off" id="formEditarUsuario">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <p class="text-muted small mb-4">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>

                    <div class="row">
                        {{-- Dejamos los inputs limpios en Blade para que JavaScript los controle libremente --}}
                        <input type="hidden" name="usuario_id" id="edit_usuario_id">

                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text"
                            class="form-control {{ session('modal') == 'editar' && $errors->has('nombre') ? 'is-invalid' : '' }}"
                            name="nombre"
                            id="edit_nombre"
                            required>
                            <div class="invalid-feedback">
                                {{ $errors->first('nombre') }}
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                             <label class="form-label fw-semibold">Apellido paterno <span class="text-danger">*</span></label>
                             <input type="text"
                              class="form-control {{ session('modal') == 'editar' && $errors->has('apellido_paterno') ? 'is-invalid' : '' }}"
                              name="apellido_paterno"
                              id="edit_apellido_paterno"
                              required>
                            <div class="invalid-feedback">
                                {{ $errors->first('apellido_paterno') }}
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Apellido materno</label>
                            <input type="text"
                            class="form-control {{ session('modal') == 'editar' && $errors->has('apellido_materno') ? 'is-invalid' : '' }}"
                            name="apellido_materno"
                            id="edit_apellido_materno">
                           <div class="invalid-feedback">
                                {{ $errors->first('apellido_materno') }}
                           </div>
                         </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                             <input type="email"
                             class="form-control {{ session('modal') == 'editar' && $errors->has('email') ? 'is-invalid' : '' }}"
                             name="email"
                             id="edit_email"
                             placeholder="ejemplo@correo.com"
                             required>
                             <div class="invalid-feedback" id="email-edit-error-custom">
                                {{ $errors->first('email') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                           <label class="form-label fw-semibold">Usuario (login) <span class="text-danger">*</span></label>
                           <input type="text"
                            class="form-control {{ session('modal') == 'editar' && $errors->has('usuario_login') ? 'is-invalid' : '' }}"
                            name="usuario_login"
                            id="edit_usuario_login"
                            placeholder="Ej. hola123"
                            required>
                            <div class="invalid-feedback">
                                {{ $errors->first('usuario_login') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Contraseña</label>
                           <input type="password"
                           class="form-control {{ session('modal') == 'editar' && $errors->has('password') ? 'is-invalid' : '' }}"
                           name="password"
                           id="edit_password"
                           autocomplete="new-password"
                           maxlength="8"
                           minlength="8"
                           placeholder="Dejar en blanco para no cambiar">
                           <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                           <small class="text-muted">Solo escribe una nueva contraseña si deseas cambiarla (8 caracteres).</small>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Rol</label>
                            {{-- Fijo y bloqueado para edición como lo acordamos --}}
                            <input type="text" class="form-control bg-light text-muted" id="edit_rol_visual" readonly>
                            <input type="hidden" name="rol" id="edit_rol">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary btn-update-user">
                        <i class="bi bi-pencil-square me-2"></i> Actualizar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Backdrop oscuro manual en caso de errores --}}
@if(session('modal'))
    <div class="modal-backdrop fade show" id="backdrop-custom"></div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalNuevo = document.getElementById('modalNuevoUsuario');
    const modalEditarElement = document.getElementById('modalEditarUsuario');
    const formNuevo = document.getElementById('formNuevoUsuario');
    const formEditar = document.getElementById('formEditarUsuario');

    // Inicializaciones seguras protegiendo si la librería bootstrap no se ha cargado globalmente
    let bsModalNuevo = null;
    let bsModalEditar = null;

    try {
        if (typeof bootstrap !== 'undefined') {
            if (modalNuevo) bsModalNuevo = new bootstrap.Modal(modalNuevo);
            if (modalEditarElement) bsModalEditar = new bootstrap.Modal(modalEditarElement);
        }
    } catch (e) {
        console.warn("Bootstrap JS no está completamente cargado en este punto, usando fallback nativo.");
    }

    function removerBackdropManual() {
        const backdrop = document.getElementById('backdrop-custom');
        if(backdrop) backdrop.remove();
        document.body.style.overflow = '';
        document.body.classList.remove('modal-open');
    }

    // =====================================================
    // CAPTURA DE CLIC DIRECTO (INDEPENDIENTE DE BOOTSTRAP)
    // =====================================================
    document.addEventListener('click', function (e) {
        // Buscamos si el clic fue en el botón del lápiz o dentro de su icono
        const boton = e.target.closest('.btn-edit-user');
        
        // Si no es el botón de editar, salimos sin alterar nada
        if (!boton) return;

        console.log("¡Botón de edición detectado! Cargando datos para ID:", boton.dataset.id);

        // 1. Limpiar clases de errores anteriores
        removerBackdropManual();
        if (modalEditarElement) {
            modalEditarElement.querySelectorAll('input, select').forEach(input => {
                input.classList.remove('is-invalid');
            });
        }

        // 2. Extraer datos del dataset del botón
        const id = boton.dataset.id || '';
        const nombre = boton.dataset.nombre || '';
        const paterno = boton.dataset.paterno || '';
        const materno = boton.dataset.materno || '';
        const email = boton.dataset.email || '';
        const login = boton.dataset.login || '';
        const rol = boton.dataset.rol || '';

        // 3. Inyectar valores de forma directa y forzada en los inputs del HTML
        if (document.getElementById('edit_usuario_id')) document.getElementById('edit_usuario_id').value = id;
        if (document.getElementById('edit_nombre')) document.getElementById('edit_nombre').value = nombre;
        if (document.getElementById('edit_apellido_paterno')) document.getElementById('edit_apellido_paterno').value = paterno;
        if (document.getElementById('edit_apellido_materno')) document.getElementById('edit_apellido_materno').value = materno;
        if (document.getElementById('edit_email')) document.getElementById('edit_email').value = email;
        if (document.getElementById('edit_usuario_login')) document.getElementById('edit_usuario_login').value = login;
        
        // Asignación de Roles (Oculto y Visual)
        if (document.getElementById('edit_rol')) document.getElementById('edit_rol').value = rol;
        if (document.getElementById('edit_rol_visual')) {
            document.getElementById('edit_rol_visual').value = rol === 'admin' ? 'Administrador' : 'Empleado';
        }
        
        // Resetear campo contraseña para que sea opcional
        if (document.getElementById('edit_password')) document.getElementById('edit_password').value = '';

        // 4. Cambiar el ACTION del formulario dinámicamente
        if (formEditar) {
            formEditar.action = `/usuarios/${id}`;
        }

        // 5. Si los objetos de Bootstrap fallaron por la carga, dejamos que tus atributos data-bs-toggle abran el modal de todos modos
        if (bsModalEditar) {
            e.preventDefault();
            bsModalEditar.show();
        }
    });

    // =====================================================
    // REAPERTURA DE EDICIÓN CON ERRORES DESDE LARAVEL (OLD)
    // =====================================================
    const sesionModal = "{{ session('modal') ?? '' }}";
    if (sesionModal === 'editar') {
        const backId = "{{ session('usuario_id') ?? old('usuario_id') ?? '' }}";
        
        if (document.getElementById('edit_usuario_id')) document.getElementById('edit_usuario_id').value = backId;
        if (document.getElementById('edit_nombre')) document.getElementById('edit_nombre').value = "{{ old('nombre') ?? '' }}";
        if (document.getElementById('edit_apellido_paterno')) document.getElementById('edit_apellido_paterno').value = "{{ old('apellido_paterno') ?? '' }}";
        if (document.getElementById('edit_apellido_materno')) document.getElementById('edit_apellido_materno').value = "{{ old('apellido_materno') ?? '' }}";
        if (document.getElementById('edit_email')) document.getElementById('edit_email').value = "{{ old('email') ?? '' }}";
        if (document.getElementById('edit_usuario_login')) document.getElementById('edit_usuario_login').value = "{{ old('usuario_login') ?? '' }}";
        
        const oldRol = "{{ old('rol') ?? '' }}";
        if (document.getElementById('edit_rol')) document.getElementById('edit_rol').value = oldRol;
        if (document.getElementById('edit_rol_visual')) {
            document.getElementById('edit_rol_visual').value = oldRol === 'admin' ? 'Administrador' : (oldRol === 'empleado' ? 'Empleado' : '');
        }

        if (formEditar && backId) {
            formEditar.action = `/usuarios/${backId}`;
        }
        
        if (bsModalEditar) {
            bsModalEditar.show();
        } else if (modalEditarElement) {
            // Fallback manual si bootstrap no se cargó globalmente todavía
            modalEditarElement.classList.add('show');
            modalEditarElement.style.display = 'block';
        }
    }

    if (sesionModal === 'nuevo') {
        if (bsModalNuevo) {
            bsModalNuevo.show();
        } else if (modalNuevo) {
            modalNuevo.classList.add('show');
            modalNuevo.style.display = 'block';
        }
    }

    // =====================================================
    // VALIDACIÓN DE CORREOS EN TIEMPO REAL
    // =====================================================
    function validarCorreo(inputElement, feedbackElement) {
        if (!inputElement) return true;
        const emailValue = inputElement.value.toLowerCase().trim();
        if (emailValue === '') return true;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const typos = ['gamil.com', 'gamil.co', 'gmil.com', 'hotmial.com', 'hotmial.es', 'outlok.com'];

        if (!emailRegex.test(emailValue)) {
            inputElement.classList.remove('is-valid');
            inputElement.classList.add('is-invalid');
            if (feedbackElement) feedbackElement.innerHTML = 'Por favor, escribe un correo electrónico válido.';
            return false;
        }

        if (typos.some(typo => emailValue.endsWith(typo))) {
            inputElement.classList.remove('is-valid');
            inputElement.classList.add('is-invalid');
            if (feedbackElement) feedbackElement.innerHTML = 'Parece que escribiste mal el dominio del correo.';
            return false;
        }

        inputElement.classList.remove('is-invalid');
        return true;
    }

    const emailNuevoInput = document.querySelector('#modalNuevoUsuario input[name="email"]');
    if (emailNuevoInput) {
        emailNuevoInput.addEventListener('input', function() {
            validarCorreo(this, document.getElementById('email-error-custom'));
        });
    }

    const emailEditInput = document.getElementById('edit_email');
    if (emailEditInput) {
        emailEditInput.addEventListener('input', function() {
            validarCorreo(this, document.getElementById('email-edit-error-custom'));
        });
    }

    if (formNuevo) {
        formNuevo.addEventListener('submit', function (e) {
            const emailInput = modalNuevo.querySelector('input[name="email"]');
            const emailFeedback = document.getElementById('email-error-custom');
            if (!validarCorreo(emailInput, emailFeedback)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                emailInput.focus();
                return false;
            }
        });
    }

    if (formEditar) {
        formEditar.addEventListener('submit', function (e) {
            const emailEditFeedback = document.getElementById('email-edit-error-custom');
            if (!validarCorreo(emailEditInput, emailEditFeedback)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (emailEditInput) emailEditInput.focus();
                return false;
            }
        });
    }

    // =====================================================
// LIMPIAR MODAL DE NUEVO USUARIO AL CERRARSE
// =====================================================
if (modalNuevo) {
    modalNuevo.addEventListener('hidden.bs.modal', function () {
        // Busca el formulario de nuevo usuario y lo resetea por completo
        if (formNuevo) {
            formNuevo.reset();
        }
        
        // Quita las clases de error en rojo (is-invalid) si es que quedaron algunas
        modalNuevo.querySelectorAll('.is-invalid, .is-valid').forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
        });

        // Limpia el mensaje de error personalizado del correo si existía
        const emailFeedback = document.getElementById('email-error-custom');
        if (emailFeedback) {
            emailFeedback.innerHTML = '';
        }
    });
}


// =====================================================
    // FILTRADO / BUSCADOR EN TIEMPO REAL (PARCIAL)
    // =====================================================
    const inputBuscar = document.getElementById('inputBuscarUsuario');
    // Selecciona todas las filas (renglones) de tu tabla
    const filasTabla = document.querySelectorAll('table tbody tr');

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function () {
            // Convertimos lo que escribe el usuario a minúsculas para que no importen las mayúsculas
            const terminoBusqueda = this.value.toLowerCase().trim();

            filasTabla.forEach(fila => {
                // Obtenemos el texto interno de cada columna clave
                const columnaNombre = fila.children[0] ? fila.children[0].textContent.toLowerCase() : '';
                const columnaEmail  = fila.children[1] ? fila.children[1].textContent.toLowerCase() : '';
                const columnaRol    = fila.children[2] ? fila.children[2].textContent.toLowerCase() : '';
                const columnaEstado = fila.children[3] ? fila.children[3].textContent.toLowerCase() : '';

                // Búsqueda parcial: revisa si el término está metido en el nombre, correo, rol o estado
                if (
                    columnaNombre.includes(terminoBusqueda) ||
                    columnaEmail.includes(terminoBusqueda)  ||
                    columnaRol.includes(terminoBusqueda)    ||
                    columnaEstado.includes(terminoBusqueda)
                ) {
                    // Si coincide con algo, la fila se queda visible
                    fila.style.display = '';
                } else {
                    // Si no coincide con nada, la fila se esconde
                    fila.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection
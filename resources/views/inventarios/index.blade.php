@extends('layouts.app')

@section('contenido')


{{-- MENSAJE DE ÉXITO --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ===================================================== --}}
{{-- ENCABEZADO --}}
{{-- ===================================================== --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="fw-bold mb-0">
        Inventario
    </h2>

    <div class="d-flex gap-2">

        {{-- BOTÓN MOVIMIENTOS --}}
        <button class="btn btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#modalMovimientos">

            <i class="bi bi-arrow-left-right me-2"></i>
            Movimientos

        </button>

        {{-- BOTÓN NUEVO --}}
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
        <i class="bi bi-plus-circle me-2"></i> Agregar producto
    </button>
    </div>

</div>





{{-- ===================================================== --}}
{{-- FILTROS --}}
{{-- ===================================================== --}}
<div class="row mb-4">

    {{-- BUSCADOR --}}
    <div class="col-md-5">

        <div class="input-group">

            <span class="input-group-text bg-white border-end-0">

                <i class="bi bi-search text-muted"></i>

            </span>

            <input type="text"
       id="inputBuscarProducto"
       class="form-control border-start-0"
       placeholder="Buscar por nombre, categoría o estado..."
       onkeyup="filtrarProductos()">

        </div>

    </div>

</div>




{{-- TABLA INVENTARIO --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="tablaProductos">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock actual</th>
                    <th>Unidad</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                {{-- Si está inactivo, aplicamos la clase table-secondary para el fondo gris --}}
                <tr class="{{ $producto->estado_producto == 'inactivo' ? 'table-secondary' : '' }}">
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->categoria->nombre }}</td>
                    <td><span class="stock-badge">{{ $producto->stock }}</span></td>
                    <td>{{ $producto->unidad->nombre }}</td>
                    <td>${{ number_format($producto->precio, 2) }}</td>
                    <td>
                        <span class="badge {{ $producto->estado == 'disponible' ? 'bg-success' : ($producto->estado == 'stock_bajo' ? 'bg-warning' : 'bg-danger') }}">
                            {{ ucfirst($producto->estado) }}
                        </span>
                        {{-- También mostramos el estado de activo/inactivo --}}
                        <span class="badge {{ $producto->estado_producto == 'activo' ? 'bg-info' : 'bg-secondary' }}">
                            {{ ucfirst($producto->estado_producto) }}
                        </span>
                    </td>
                    <td>
                        {{-- EDITAR --}}
                        @if($producto->estado_producto === 'activo')
                        <button class="btn btn-sm btn-light" 
                            onclick="editarProducto({{ json_encode($producto) }})"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditarProducto"
                            title="Editar">
                            ✏️
                        </button>
                        @else
                       {{-- Botón deshabilitado si está inactivo --}}
                       <button type="button" class="btn btn-sm btn-secondary" disabled title="No se puede editar un producto inactivo">
                              ✏️
                       </button>
                        @endif

                        {{-- ACTIVAR / DESACTIVAR (En lugar de eliminar) --}}
                        <form action="{{ route('inventario.desactivar', $producto->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-light" 
                                title="{{ $producto->estado_producto == 'activo' ? 'Desactivar producto' : 'Reactivar producto' }}"
                                onclick="return confirm('¿Estás seguro de cambiar el estado de este producto?')">
                                {{ $producto->estado_producto == 'activo' ? '🚫' : '✅' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>















{{-- MODAL NUEVO PRODUCTO --}}
<div class="modal fade" id="modalNuevoProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('inventario.store') }}" method="POST" class="modal-content border-0">
            @csrf
            <div class="modal-header border-0">
                <h4 class="fw-bold">Agregar nuevo producto</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Categoría</label>
                        <select name="categoria_id" class="form-select" required>
                        <option value="" disabled selected>Seleccionar categoría...</option>
                               @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Unidad</label>
                        <select name="unidad_id" class="form-select" required>
                          <option value="" disabled selected>Seleccionar unidad...</option>
                            @foreach($unidades as $uni)
                           <option value="{{ $uni->id }}">{{ $uni->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Stock inicial</label>
                        <input type="number" name="stock_inicial" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Precio unitario</label>
                       <input type="number" name="precio" class="form-control" step="0.01" min="0.1" placeholder="Ej: 100" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success"><i class="bi bi-floppy me-2"></i> Guardar producto</button>
            </div>
        </form>
    </div>
</div>


















{{-- ===================================================== --}}
{{-- MODAL EDITAR PRODUCTO --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modalEditarProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        {{-- Agregamos el ID al form --}}
        <form id="formEditar" method="POST" class="modal-content border-0">
            @csrf
            @method('PUT')
            
            <div class="modal-header border-0">
                <h4 class="fw-bold">Editar producto</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Categoría</label>
                        <select name="categoria_id" id="edit_categoria" class="form-select" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Unidad</label>
                        <select name="unidad_id" id="edit_unidad" class="form-select" required>
                            @foreach($unidades as $uni)
                                <option value="{{ $uni->id }}">{{ $uni->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Precio unitario</label>
                        <input type="number" name="precio" id="edit_precio" class="form-control" step="0.01" min="0.1" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success"><i class="bi bi-floppy me-2"></i> Actualizar producto</button>
            </div>
        </form>
    </div>
</div>















{{-- ===================================================== --}}
{{-- MODAL MOVIMIENTOS --}}
{{-- ===================================================== --}}
<div class="modal fade"
     id="modalMovimientos"
     tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content border-0 modal-custom">

            <div class="modal-header border-0">

                <h4 class="fw-bold">
                    Movimientos de inventario
                </h4>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                {{-- FILTROS SIMPLIFICADOS --}}
<div class="row mb-4 align-items-center">
    
    <div class="col-md-9">
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" 
               id="inputBuscarMovimiento" 
               class="form-control border-start-0" 
               placeholder="Buscar movimiento por producto o tipo..."
               onkeyup="filtrarTabla()">
    </div>
</div>

    <div class="col-md-3 d-flex gap-2">
        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modalEntrada">
            <i class="bi bi-arrow-down-circle me-1"></i> Entrada
        </button>
        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalSalida">
            <i class="bi bi-arrow-up-circle me-1"></i> Salida
        </button>
    </div>
</div>

                {{-- TABLA --}}
                <div class="table-responsive">

                    <table class="table align-middle">
    <thead class="table-light">
        <tr>
            <th>Tipo</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Stock Resultante</th>
            <th>Fecha</th>
            <th>Responsable</th>
            <th>Observación</th>
        </tr>
    </thead>
    <tbody>
        @forelse($movimientos as $movimiento)
            <tr>
                <td>
                    <span class="text-{{ $movimiento->tipo_movimiento == 'entrada' ? 'success' : 'danger' }} fw-semibold">
                        {{ $movimiento->tipo_movimiento == 'entrada' ? '↑ Entrada' : '↓ Salida' }}
                    </span>
                </td>
                <td>{{ $movimiento->producto->nombre ?? 'Producto eliminado' }}</td>
                <td>{{ $movimiento->cantidad }}</td>
                <td>
                    {{-- Si quieres mostrar el stock final, esto depende de cómo lo calcules --}}
                    <span class="badge bg-light text-dark">{{ $movimiento->producto->stock ?? 'N/A' }}</span>
                </td>
                <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                <td>
    {{ $movimiento->usuario ? $movimiento->usuario->nombre . ' ' . $movimiento->usuario->apellido_paterno : 'Usuario no encontrado' }}
</td>
                <td>{{ $movimiento->observacion }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No hay movimientos registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

                </div>

            </div>

        </div>

    </div>

</div>

















{{-- MODAL ENTRADA --}}
<div class="modal fade" id="modalEntrada" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('movimientos.store') }}" method="POST" class="modal-content border-0 modal-custom">
            @csrf
            <input type="hidden" name="tipo_movimiento" value="entrada">

            <div class="modal-header border-0">
                <h4 class="fw-bold">Registrar entrada</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <option value="" disabled selected>Seleccionar producto</option>
                        {{-- Cambiamos a $productosActivos --}}
    @foreach($productosActivos as $producto)
        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
    @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" required min="1">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Observación</label>
                    <textarea name="observacion" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-floppy me-2"></i> Guardar entrada
                </button>
            </div>
        </form>
    </div>
</div>







{{-- MODAL SALIDA --}}
<div class="modal fade" id="modalSalida" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('movimientos.store') }}" method="POST" class="modal-content border-0 modal-custom">
            @csrf
            <input type="hidden" name="tipo_movimiento" value="salida">

            <div class="modal-header border-0">
                <h4 class="fw-bold">Registrar salida</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Producto</label>
                    <select name="producto_id" class="form-select" required>
    <option value="" disabled {{ old('producto_id') ? '' : 'selected' }}>Seleccionar producto</option>
    
    {{-- Cambiamos a $productosActivos --}}
    @foreach($productosActivos as $producto)
        <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
            {{ $producto->nombre }} (Stock: {{ $producto->stock }})
        </option>
    @endforeach
</select>
                </div>

                <div class="mb-4">
    <label class="form-label fw-semibold">Cantidad</label>
    {{-- Agregamos la clase 'is-invalid' si existe un error --}}
    <input type="number" 
       name="cantidad" 
       class="form-control @error('cantidad') is-invalid @enderror" 
       value="{{ old('cantidad') }}" 
       oninput="this.classList.remove('is-invalid'); document.getElementById('error-cantidad').style.display='none';"
       required 
       min="1">

@error('cantidad')
    <div id="error-cantidad" class="invalid-feedback">
        {{ $message }}
    </div>
@enderror
</div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Observación</label>
                    <textarea name="observacion" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-floppy me-2"></i> Guardar salida
                </button>
            </div>
        </form>
    </div>
</div>

@endsection


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Si hay una sesión con un modal que abrir (ya sea por error o por éxito)
        @if(session('modal_abierto'))
            var modalId = "{{ session('modal_abierto') }}";
            var myModalEl = document.getElementById(modalId);
            
            // Abrir el modal
            if(myModalEl) {
                var modal = new bootstrap.Modal(myModalEl, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }
        @endif
    });
</script>

<script>
    function filtrarTabla() {
        let input = document.getElementById("inputBuscarMovimiento").value.toLowerCase();
        let filas = document.querySelectorAll("#modalMovimientos tbody tr");

        filas.forEach(fila => {
            let textoFila = fila.textContent.toLowerCase();
            // Si el texto de la fila contiene lo que escribimos, la mostramos, sino la ocultamos
            if (textoFila.includes(input)) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    }
</script>

@push('scripts')
    @vite('resources/js/inventario.js')
@endpush
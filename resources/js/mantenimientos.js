

/**
 * 1. CONSULTA DE HISTORIAL (Para el botón del ojito 👁️)
 * Consulta vía AJAX los mantenimientos de un proyecto y abre el modal dinámico
 */
window.verHistorialProyecto = function(proyectoId, proyectoNombre) {
    // Cambiar el título del modal con el nombre del proyecto seleccionado
    $('#historialModalTitulo').text('Historial de mantenimientos: ' + proyectoNombre);
    
    // Poner el cargador temporal en la tabla
    const tbody = $('#tablaHistorialContenido');
    tbody.html(`
        <tr>
            <td colspan="4" class="text-center py-4 text-secondary">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Buscando bitácora de servicios...
            </td>
        </tr>
    `);
    
    // Desplegar el modal en la pantalla
    //$('#modalDetalleMantenimiento').modal('show');

    // NUEVA (la que sí funcionará):
new bootstrap.Modal(document.getElementById('modalDetalleMantenimiento')).show();
    
    // Petición AJAX al controlador
    $.ajax({
        url: '/proyectos/' + proyectoId + '/historial-mantenimientos',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            tbody.empty();
            
            if (data.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Este proyecto no cuenta con registros en la bitácora de mantenimiento.
                        </td>
                    </tr>
                `);
                return;
            }
            
            // Recorrer los registros formateados por el MantenimientoController
            data.forEach(function(mantenimiento) {
                tbody.append(`
                    <tr>
                        <td class="fw-semibold">${mantenimiento.fecha}</td>
                        <td>${mantenimiento.tecnico}</td>
                        <td class="text-secondary" style="max-width: 320px; white-space: pre-line;">${mantenimiento.observaciones}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success fw-bold p-2 border border-success-subtle rounded">
                                <i class="bi bi-check-circle-fill me-1"></i> ${mantenimiento.estado.charAt(0).toUpperCase() + mantenimiento.estado.slice(1)}
                            </span>
                        </td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error("Error al cargar la bitácora:", xhr);
            tbody.html(`
                <tr>
                    <td colspan="4" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Error al conectar con el servidor.
                    </td>
                </tr>
            `);
        }
    });
};

/**
 * 2. EDICIÓN DE MANTENIMIENTO (Para el botón del lápiz ✏️)
 * Inyecta los datos existentes en los inputs correspondientes y abre el modal
 */
window.abrirModalEditarMantenimiento = function(id, proyectoNombre, tecnicoId, fecha, observaciones) {
    // Configurar la ruta dinámicamente en el formulario para apuntar al método update
    $('#formEditarMantenimiento').attr('action', '/mantenimientos/' + id);
    
    // Rellenar cada uno de los campos del modal de edición
    $('#edit_proyecto_nombre').val(proyectoNombre);
    $('#edit_tecnico_id').val(tecnicoId);
    $('#edit_fecha_mantenimiento').val(fecha);
    $('#edit_observaciones').val(observaciones);
    
    // Mostrar el modal de edición en pantalla
    //$('#modalEditarMantenimiento').modal('show');

    new bootstrap.Modal(document.getElementById('modalEditarMantenimiento')).show();
};

/**
 * 3. BUSCADOR INTERACTIVO EN TIEMPO REAL
 * Filtra las filas de la tabla según lo que el usuario escriba
 */
$(document).ready(function() {
    $('#buscadorMantenimientos').on('keyup', function() {
        const valorBusqueda = $(this).val().toLowerCase();
        
        $('.fila-mantenimiento').each(function() {
            const nombreProyecto = $(this).data('proyecto').toLowerCase();
            const nombreTecnico = $(this).data('tecnico').toLowerCase();
            
            // Si coincide el proyecto o el técnico, mantiene la fila visible; si no, la oculta
            if (nombreProyecto.includes(valorBusqueda) || nombreTecnico.includes(valorBusqueda)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // --- NUEVA VALIDACIÓN: Impedir fechas futuras en el formulario ---
    const hoy = new Date().toISOString().split('T')[0];
    
    // Aplicamos a los inputs de fecha (ajusta el ID si es distinto en tu HTML)
    $('#fecha_mantenimiento').attr('max', hoy);
    $('#edit_fecha_mantenimiento').attr('max', hoy);

    // Validación extra al enviar el formulario (por si intentan saltarse el 'max')
    $('#formRegistrarMantenimiento, #formEditarMantenimiento').on('submit', function(e) {
        const fechaInput = $(this).find('input[type="date"]').val();
        if (new Date(fechaInput) > new Date()) {
            e.preventDefault(); // Detiene el envío
            alert("¡Error! La fecha no puede ser una fecha futura.");
        }
    });
});

/**
 * 4. LIMPIEZA AUTOMÁTICA MÁS ROBUSTA
 */
$(document).ready(function() {

    // Al cerrar el modal de NUEVO mantenimiento
    $('#modalNuevoMantenimiento').on('hidden.bs.modal', function () {
        // Buscamos cualquier formulario DENTRO de este modal y lo reseteamos
        $(this).find('form').trigger('reset');
    });

    // Al cerrar el modal de EDITAR mantenimiento
    $('#modalEditarMantenimiento').on('hidden.bs.modal', function () {
        // Buscamos cualquier formulario DENTRO de este modal y lo reseteamos
        $(this).find('form').trigger('reset');
    });
    
});
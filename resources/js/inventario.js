// resources/js/inventario.js

window.editarProducto = function(producto) {
    // 1. Asignamos la acción del form dinámicamente con el ID
    const form = document.querySelector('#formEditar');
    if (!form) return; // Validación por si no encuentra el form
    
    form.action = '/inventario/' + producto.id;
    
    // 2. Llenamos los campos del modal
    document.querySelector('#edit_nombre').value = producto.nombre;
    document.querySelector('#edit_categoria').value = producto.categoria_id;
    document.querySelector('#edit_unidad').value = producto.unidad_id;
    document.querySelector('#edit_precio').value = producto.precio;
}

// --- Lógica para limpiar el modal al cerrar ---
document.addEventListener('DOMContentLoaded', function() {
    const modalAgregar = document.getElementById('modalNuevoProducto');

    if (modalAgregar) {
        modalAgregar.addEventListener('hidden.bs.modal', function () {
            // Buscamos el formulario dentro del modal
            const form = modalAgregar.querySelector('form');
            if (form) {
                form.reset(); // Esto limpia todos los inputs y selects
            }
        });
    }
});


// --- Lógica para el buscador de productos ---
window.filtrarProductos = function() {
    // Obtenemos el texto del buscador y lo pasamos a minúsculas
    let input = document.getElementById("inputBuscarProducto").value.toLowerCase();
    
    // Obtenemos todas las filas del cuerpo de la tabla
    let filas = document.querySelectorAll("#tablaProductos tbody tr");

    filas.forEach(fila => {
        // Obtenemos todo el texto de la fila (Nombre + Categoría + Estado + etc.)
        let textoFila = fila.textContent.toLowerCase();
        
        // Si el texto de la fila incluye lo que escribimos en el buscador
        if (textoFila.includes(input)) {
            fila.style.display = ""; // La mostramos
        } else {
            fila.style.display = "none"; // La ocultamos
        }
    });
};



// --- Lógica para limpiar formularios al cerrar modales ---
document.addEventListener('DOMContentLoaded', function() {
    // Array con los IDs de los modales que quieres que se limpien
    const modalesParaLimpiar = ['modalEntrada', 'modalSalida'];

    modalesParaLimpiar.forEach(modalId => {
        const modalElement = document.getElementById(modalId);
        
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function () {
                // Buscamos el formulario dentro del modal
                const form = modalElement.querySelector('form');
                if (form) {
                    form.reset(); // Limpia los inputs y selects
                    
                    // Limpieza agresiva: forzamos que los valores sean vacíos
                    form.querySelectorAll('input, select, textarea').forEach(el => {
                        if (el.type !== 'hidden') el.value = '';
                    });
                    
                    // Quitamos clases de error
                    const inputs = form.querySelectorAll('.is-invalid');
                    inputs.forEach(input => input.classList.remove('is-invalid'));
                    
                    // Ocultamos mensajes de error
                    const feedbacks = form.querySelectorAll('.invalid-feedback');
                    feedbacks.forEach(fb => fb.style.display = 'none');
                }
            });
        }
    });
});

// --- Lógica mejorada para evitar doble clic ---
document.addEventListener('DOMContentLoaded', function() {
    // Buscamos todos los botones de tipo submit que estén dentro de un modal
    const botonesSubmit = document.querySelectorAll('.modal-footer button[type="submit"]');

    botonesSubmit.forEach(btn => {
        btn.addEventListener('click', function(event) {
            // Verificamos si el formulario es válido antes de bloquear
            // (Si falta un campo requerido, HTML5 detendrá el submit)
            const form = btn.closest('form');
            if (form && form.checkValidity()) {
                // Deshabilitamos el botón
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Procesando...';
                
                // Enviamos el formulario manualmente
                form.submit();
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. Lógica del radio button ---
    const radios = document.querySelectorAll('input[name="dejo_documento"]');
    const container = document.getElementById('nuevoDocumentoContainer');

    radios.forEach((radio) => {
        radio.addEventListener('change', function() {
            if (this.value === '1') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                container.querySelector('input').value = '';
            }
        });
    });

    // --- 2. Lógica del Teléfono ---
    const inputTel = document.getElementById('inputTelefono');
    const feedbackTel = document.getElementById('feedbackTelefono');

    if (inputTel) {
        inputTel.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 10) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                if (feedbackTel) feedbackTel.style.display = 'none';
            } else {
                this.classList.remove('is-valid');
                if (this.value.length > 0) {
                    this.classList.add('is-invalid');
                    if (feedbackTel) feedbackTel.style.display = 'block';
                }
            }
        });
    }

    // --- 3. Función para campos de solo texto ---
    function aplicarSoloLetras(selector) {
        const input = document.querySelector(selector);
        if (input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚ\s]/g, '');
            });
        }
    }

    aplicarSoloLetras('input[name="nombre"]');
    aplicarSoloLetras('input[name="apellido_paterno"]');
    aplicarSoloLetras('input[name="apellido_materno"]');

    

    // --- 4. NUEVA LÓGICA: Limpieza automática de errores ---
    const inputs = document.querySelectorAll('.form-control, .form-select');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            
            if (this.id !== 'inputTelefono' || this.value.length === 10) {
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }

            // CAMBIO: Buscamos el contenedor padre más cercano de forma genérica
            // Esto evita que falle si el contenedor no tiene esas clases exactas
            const parent = this.closest('.mb-3') || this.closest('.row'); 
            if (parent) {
                const mensajesError = parent.querySelectorAll('.text-danger, .invalid-feedback');
                mensajesError.forEach(msg => {
                    msg.style.display = 'none';
                });
            }
        });
    });

    // --- 5. Limpieza automática al cerrar CUALQUIER modal ---
    const todosLosModales = document.querySelectorAll('.modal');

    todosLosModales.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            
            // NUEVO: Si existen campos con la clase 'is-invalid', significa que hubo un error de validación.
            // En ese caso, NO reseteamos nada para permitir que el usuario vea qué falló.
            if (this.querySelectorAll('.is-invalid').length > 0) {
                return;
            }

            // 1. Resetear el formulario solo si NO hay errores
            const form = this.querySelector('form');
            if (form) {
                form.reset();
            }

            // 2. Quitar clases de validación (rojo/verde)
            const inputs = this.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });

            // 3. Ocultar todos los mensajes de error
            const errores = this.querySelectorAll('.invalid-feedback');
            errores.forEach(msg => {
                msg.style.display = 'none';
            });

            // 4. Resetear visibilidad de contenedores específicos
            const containerDocumento = this.querySelector('#nuevoDocumentoContainer, #editDocumentoContainer');
            if (containerDocumento) {
                containerDocumento.style.display = 'none';
            }
        });
    });
    // --- 6. Lógica para el Modal de Editar ---
const modalEditar = document.getElementById('modalEditarProspecto');

if (modalEditar) {
    modalEditar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id'); // Obtenemos el ID aquí
        const form = document.getElementById('formEditarProspecto');

        // --- NUEVA LÍNEA: Actualiza la ruta del formulario dinámicamente ---
        form.action = `/prospectos/${id}`;

        // Cargar datos
        document.getElementById('editId').value = id;
        document.getElementById('editNombre').value = button.getAttribute('data-nombre');
        document.getElementById('editPaterno').value = button.getAttribute('data-apellido-paterno');
        document.getElementById('editMaterno').value = button.getAttribute('data-apellido-materno');
        document.getElementById('editTelefono').value = button.getAttribute('data-telefono');
        document.getElementById('editProyecto').value = button.getAttribute('data-tipo-instalacion');
        document.getElementById('editEstado').value = button.getAttribute('data-estado-prospecto');
        document.getElementById('editDetalle').value = button.getAttribute('data-detalle-documento');
        document.getElementById('editNotas').value = button.getAttribute('data-notas');

        // Radio buttons
        const valDoc = button.getAttribute('data-dejo-documento');
        document.getElementById('editSiDoc').checked = (valDoc === '1');
        document.getElementById('editNoDoc').checked = (valDoc === '0');
        
        // Mostrar/Ocultar contenedor
        document.getElementById('editDocumentoContainer').style.display = (valDoc === '1') ? 'block' : 'none';
    });

    // IMPORTANTE: Agregar esto para que funcione el cambio mientras el modal está abierto
    const editRadios = modalEditar.querySelectorAll('input[name="dejo_documento"]');
    const editContainer = document.getElementById('editDocumentoContainer');
    
    editRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            editContainer.style.display = (this.value === '1') ? 'block' : 'none';
        });
    });
}

// --- NUEVA LÓGICA DE RECUPERACIÓN POST-ERROR ---
const editIdInput = document.getElementById('editId');
const formEditar = document.getElementById('formEditarProspecto');

if (editIdInput && editIdInput.value) {
    formEditar.action = `/prospectos/${editIdInput.value}`;
}

// --- ASEGURAR VISIBILIDAD DEL CONTENEDOR TRAS UN ERROR DE VALIDACIÓN ---
const editSiDoc = document.getElementById('editSiDoc');
const editDocumentoContainer = document.getElementById('editDocumentoContainer');

if (editSiDoc && editDocumentoContainer) {
    // Si tras el error el radio "Sí" está marcado, mostramos el contenedor. Si no, lo ocultamos.
    editDocumentoContainer.style.display = editSiDoc.checked ? 'block' : 'none';
}

const inputBuscar = document.getElementById('inputBuscarProspecto');

if (inputBuscar) {
    inputBuscar.addEventListener('keyup', function() {
        const textoBusqueda = inputBuscar.value.toLowerCase().trim();
        const filas = document.querySelectorAll('#tablaProspectosBody tr');

        filas.forEach(fila => {
            // Ignoramos la fila del mensaje de "No hay prospectos" si llega a estar sola
            if (fila.querySelector('td').getAttribute('colspan')) return;

            const textoFila = fila.textContent.toLowerCase();
            
            // Si el texto de la fila incluye lo que escribiste, se muestra; si no, se oculta
            if (textoFila.includes(textoBusqueda)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
}
});
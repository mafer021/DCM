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

    // --- 5. Limpieza automática al cerrar el modal ---
    const modalElement = document.getElementById('modalNuevoProspecto');

    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function () {
            // 1. Resetear el formulario (esto limpia todos los inputs, selects y textareas)
            const form = this.querySelector('form');
            if (form) {
                form.reset();
            }

            // 2. Ocultar el contenedor de documentos si estaba abierto
            const container = document.getElementById('nuevoDocumentoContainer');
            if (container) {
                container.style.display = 'none';
            }

            // 3. Quitar clases de validación (para que no se vean rojos o verdes al volver a abrir)
            const inputs = this.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });

            // 4. Ocultar todos los mensajes de error
            const errores = this.querySelectorAll('.text-danger, .invalid-feedback');
            errores.forEach(msg => {
                msg.style.display = 'none';
            });
        });
    }
});
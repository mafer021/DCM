document.addEventListener('DOMContentLoaded', function () {
    
    // ---------------------------------------------------------
    // 1. CÁLCULO AUTOMÁTICO DE PRÓXIMO MANTENIMIENTO
    // ---------------------------------------------------------
    const fechaInstalacionInput = document.getElementById('nuevo_fecha_instalacion');
    const proximoMantenimientoInput = document.getElementById('nuevo_proximo_mantenimiento');

    if (fechaInstalacionInput && proximoMantenimientoInput) {
        fechaInstalacionInput.addEventListener('change', function () {
            const fechaSeleccionada = this.value;
            if (fechaSeleccionada) {
                const fecha = new Date(fechaSeleccionada + 'T00:00:00');
                fecha.setFullYear(fecha.getFullYear() + 1);

                const year = fecha.getFullYear();
                const month = String(fecha.getMonth() + 1).padStart(2, '0');
                const day = String(fecha.getDate()).padStart(2, '0');

                proximoMantenimientoInput.value = `${year}-${month}-${day}`;
            } else {
                proximoMantenimientoInput.value = '';
            }
        });
    }

    // ---------------------------------------------------------
    // 2. DETECTOR DE ERRORES INTELIGENTE (NUEVO VS EDICIÓN)
    // ---------------------------------------------------------
    const modalNuevoElement = document.getElementById('modalNuevoProyecto');
    const modalEditarElement = document.getElementById('modalEditarProyecto');

    if (modalNuevoElement || modalEditarElement) {
        const tieneErroresEditar = modalEditarElement && modalEditarElement.querySelector('.is-invalid, .invalid-feedback') !== null;
        const tieneErroresNuevo = modalNuevoElement && modalNuevoElement.querySelector('.is-invalid, .invalid-feedback') !== null && !tieneErroresEditar;

        if (tieneErroresNuevo && modalNuevoElement) {
            const bModalNuevo = new bootstrap.Modal(modalNuevoElement, { backdrop: 'static', keyboard: false });
            bModalNuevo.show();

            const btnCloseNuevo = modalNuevoElement.querySelector('.btn-close');
            if (btnCloseNuevo) {
                btnCloseNuevo.removeAttribute('data-bs-dismiss');
                btnCloseNuevo.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (confirm('¿Estás seguro de que deseas salir? Se perderán los datos y se recargará la página.')) {
                        window.location.reload();
                    }
                });
            }
        }

        if (tieneErroresEditar && modalEditarElement) {
            const bModalEditar = new bootstrap.Modal(modalEditarElement, { backdrop: 'static', keyboard: false });
            bModalEditar.show();
            
            const btnCloseEditar = modalEditarElement.querySelector('.btn-close');
            if (btnCloseEditar) {
                btnCloseEditar.removeAttribute('data-bs-dismiss');
                btnCloseEditar.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (confirm('¿Estás seguro? Al cerrar se restablecerán los valores originales del proyecto.')) {
                        window.location.reload();
                    }
                });
            }
        }
    }

    // ---------------------------------------------------------
    // 3. LIMPIAR ROJO DE LOS CAMPOS CUANDO EL USUARIO ESCRIBE
    // ---------------------------------------------------------
    const inputsConError = document.querySelectorAll('.form-control, .form-select');
    inputsConError.forEach(campo => {
        const limpiarError = function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const errorFeedback = this.nextElementSibling;
                if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
                    errorFeedback.style.display = 'none';
                }
            }
        };
        campo.addEventListener('input', limpiarError);
        campo.addEventListener('change', limpiarError);
    });

    // ---------------------------------------------------------
// 4. CONTROL DE PESO Y FORMATO DEL ARCHIVO
// ---------------------------------------------------------
const inputArchivo = document.getElementById('doc_archivo');
const formSubirDoc = document.getElementById('formSubirDocumento');
const divErrorPeso = document.getElementById('error_peso_documento');
const btnGuardarDoc = document.getElementById('btnGuardarDocumento');

// Lista de extensiones permitidas que coinciden con tu controlador
const extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xlsx'];

if (inputArchivo) {
    inputArchivo.addEventListener('change', function () {
        const archivo = this.files[0];
        if (archivo) {
            const pesoEnBytes = archivo.size;
            const limiteBytes = 10 * 1024 * 1024; // 10 MB
            
            // Obtener la extensión del archivo actual
            const nombreArchivo = archivo.name;
            const extension = nombreArchivo.split('.').pop().toLowerCase();
            const esFormatoValido = extensionesPermitidas.includes(extension);

            // Validamos peso Y formato
            if (pesoEnBytes > limiteBytes || !esFormatoValido) {
                this.classList.add('is-invalid');
                if (divErrorPeso) {
                    divErrorPeso.classList.remove('d-none');
                    // Cambiamos el texto del error según sea el caso
                    divErrorPeso.innerHTML = !esFormatoValido 
                        ? '<i class="bi bi-exclamation-triangle-fill me-1"></i> Formato no permitido. Solo PDF, JPG, PNG, DOC, XLSX.'
                        : '<i class="bi bi-exclamation-triangle-fill me-1"></i> El archivo supera los 10 MB.';
                }
                if (btnGuardarDoc) btnGuardarDoc.disabled = true;
            } else {
                this.classList.remove('is-invalid');
                if (divErrorPeso) divErrorPeso.classList.add('d-none');
                if (btnGuardarDoc) btnGuardarDoc.disabled = false;
            }
        }
    });
}

if (formSubirDoc) {
    formSubirDoc.addEventListener('submit', function (e) {
        const archivo = inputArchivo ? inputArchivo.files[0] : null;
        if (archivo) {
            const extension = archivo.name.split('.').pop().toLowerCase();
            // Validación final antes de enviar
            if (archivo.size > 10 * 1024 * 1024 || !extensionesPermitidas.includes(extension)) {
                e.preventDefault();
                alert('El archivo no cumple con el peso o formato permitido.');
            }
        }
    });
}

    // ---------------------------------------------------------
    // 5. BUSCADOR EN TIEMPO REAL (PARCIAL: NOMBRE, CLIENTE, ESTADO)
    // ---------------------------------------------------------
    const inputBuscar = document.getElementById('inputBuscarProyecto');
    
    if (inputBuscar) {
        inputBuscar.addEventListener('input', function(e) {
            const terminoBusqueda = e.target.value.toLowerCase().trim();
            // Buscamos todas las filas que tengan la clase 'fila-proyecto' en la vista
            const filasProyectos = document.querySelectorAll('.fila-proyecto');

            filasProyectos.forEach(fila => {
                const nombre = fila.getAttribute('data-nombre') ? fila.getAttribute('data-nombre').toLowerCase() : '';
                const cliente = fila.getAttribute('data-cliente') ? fila.getAttribute('data-cliente').toLowerCase() : '';
                const estado = fila.getAttribute('data-estado') ? fila.getAttribute('data-estado').toLowerCase() : '';

                if (
                    nombre.includes(terminoBusqueda) || 
                    cliente.includes(terminoBusqueda) || 
                    estado.includes(terminoBusqueda)
                ) {
                    fila.style.display = ''; // Muestra la fila si coincide
                } else {
                    fila.style.display = 'none'; // Oculta la fila si no coincide
                }
            });
        });
    }
});

// =========================================================
// FUNCIÓN GLOBAL: LLENAR MODAL DE DETALLE (EL OJITO)
// =========================================================
window.verDetallesProyecto = function(id, nombre, cliente, tipo, fechaInstalacion, proximoMantenimiento, estado, direccion, descripcion, documentosJson) {
    try {
        const modalLabel = document.getElementById('det_modal_titulo');
        if (modalLabel) {
            modalLabel.innerHTML = `Proyecto: <span class="text-success">${nombre || 'Sin nombre'}</span>`;
        }

        const inputDocProyectoId = document.getElementById('doc_proyecto_id');
        if (inputDocProyectoId) inputDocProyectoId.value = id;

        const formatearFecha = (strFecha) => {
            if (!strFecha) return '';
            const partes = strFecha.split('-');
            if (partes.length !== 3) return strFecha;
            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        };

        if (document.getElementById('det_cliente')) document.getElementById('det_cliente').textContent = cliente || '';
        if (document.getElementById('det_tipo_instalacion')) document.getElementById('det_tipo_instalacion').textContent = tipo || 'No asignado';
        if (document.getElementById('det_fecha_instalacion')) document.getElementById('det_fecha_instalacion').textContent = formatearFecha(fechaInstalacion);
        if (document.getElementById('det_proximo_mantenimiento')) document.getElementById('det_proximo_mantenimiento').textContent = formatearFecha(proximoMantenimiento);
        if (document.getElementById('det_direccion')) document.getElementById('det_direccion').textContent = direccion || '';
        if (document.getElementById('det_descripcion')) document.getElementById('det_descripcion').textContent = descripcion || '';

        const metaCsrf = document.querySelector('meta[name="csrf-token"]');
        const tokenVal = metaCsrf ? metaCsrf.getAttribute('content') : '';

        const tablaDocsBody = document.querySelector('#tablaDetalleDocumentos tbody');
        if (tablaDocsBody) {
            tablaDocsBody.innerHTML = ''; 

            let documentos = Array.isArray(documentosJson) ? documentosJson : [];

            if (documentos.length > 0) {
                documentos.forEach(doc => {
                    const tipoNombre = doc.tipo_documento ? doc.tipo_documento.nombre : 'No especificado';
                    const fechaSubidaFormateada = formatearFecha(doc.fecha_subida);

                    let badgeColor = 'bg-light text-dark border'; 
                    const tipoNormalizado = tipoNombre.toLowerCase().trim();

                    if (tipoNormalizado.includes('recibo') || tipoNormalizado.includes('luz')) {
                        badgeColor = 'bg-info-subtle text-info-emphasis border border-info-subtle'; 
                    } else if (tipoNormalizado.includes('ine') || tipoNormalizado.includes('identificacion')) {
                        badgeColor = 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'; 
                    } else if (tipoNormalizado.includes('carta') || tipoNormalizado.includes('poder') || tipoNormalizado.includes('contrato')) {
                        badgeColor = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'; 
                    } else if (tipoNormalizado.includes('cotizacion') || tipoNormalizado.includes('factura')) {
                        badgeColor = 'bg-success-subtle text-success-emphasis border border-success-subtle'; 
                    } else if (tipoNormalizado.includes('reporte') || tipoNormalizado.includes('tecnico')) {
                        badgeColor = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'; 
                    }

                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td class="px-3 fw-medium text-dark">${doc.nombre_archivo}</td>
                        <td><span class="badge ${badgeColor}">${tipoNombre}</span></td>
                        <td>${fechaSubidaFormateada}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/proyectos/documentos/${doc.id}/ver" target="_blank" class="btn btn-sm btn-outline-success" title="Ver archivo">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="/proyectos/documentos/${doc.id}/descargar" class="btn btn-sm btn-outline-primary" title="Descargar">
                                    <i class="bi bi-download"></i>
                                </a>

                                <form action="/proyectos/documentos/${doc.id}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este documento?');">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="${tokenVal}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    `;
                    tablaDocsBody.appendChild(fila);
                });
            } else {
                tablaDocsBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">No hay documentos registrados en este proyecto.</td></tr>`;
            }
        }

        // ==================================================================
        // CARGAR HISTORIAL DE MANTENIMIENTOS EN LAS TABS DEL PROYECTO
        // ==================================================================
        const tbodyMantenimientos = document.getElementById('tbodyDetalleMantenimientos');

        if (tbodyMantenimientos) {
            tbodyMantenimientos.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Cargando historial de servicios...
                    </td>
                </tr>
            `;

            fetch(`/proyectos/${id}/historial-mantenimientos`)
                .then(response => response.json())
                .then(mantenimientos => {
                    tbodyMantenimientos.innerHTML = ''; 

                    if (mantenimientos.length === 0) {
                        tbodyMantenimientos.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i> Este proyecto no cuenta con registros en la bitácora de mantenimiento.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    mantenimientos.forEach(maint => {
    // 1. Ahora usamos 'maint.fecha' porque es lo que viene en el JSON
    let fechaFormateada = maint.fecha ? maint.fecha : 'Sin fecha';

    // 2. Usamos 'maint.tecnico' directamente porque el servidor envía el nombre como string
    let tecnicoNombre = maint.tecnico ? maint.tecnico : 'No asignado';
    
    let observaciones = maint.observaciones ? maint.observaciones : '<span class="text-muted italic">Sin observaciones</span>';

    // 3. Usamos 'maint.estado' para el texto del badge
    let estadoTexto = maint.estado ? maint.estado.charAt(0).toUpperCase() + maint.estado.slice(1) : 'Realizado';

    let fila = `
        <tr>
            <td class="px-3 fw-medium">${fechaFormateada}</td>
            <td>${tecnicoNombre}</td>
            <td class="text-secondary small" style="max-width: 300px;">${observaciones}</td>
            <td>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    ${estadoTexto}
                </span>
            </td>
        </tr>
    `;
    tbodyMantenimientos.innerHTML += fila;
});
                })
                .catch(error => {
                    console.error('Error al cargar la bitácora de mantenimientos:', error);
                    tbodyMantenimientos.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-danger py-3">
                                <i class="bi bi-exclamation-triangle me-1"></i> Error al conectar con el servidor.
                            </td>
                        </tr>
                    `;
                });
        }

        if (proximoMantenimiento) {
            const fMantenimiento = new Date(proximoMantenimiento + 'T00:00:00');
            const fHoy = new Date();
            fHoy.setHours(0,0,0,0);

            const diferenciaTiempo = fMantenimiento.getTime() - fHoy.getTime();
            const diferenciaDias = Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));

            const txtDiasRestantes = document.getElementById('det_dias_restantes');
            const badgeEstado = document.getElementById('det_badge_estado');

            if (badgeEstado && txtDiasRestantes) {
                badgeEstado.className = "badge ms-2 px-2 rounded";

                if (estado === 'vencido' || diferenciaDias < 0) {
                    const diasPasados = Math.abs(diferenciaDias);
                    txtDiasRestantes.textContent = `(Hace ${diasPasados} ${diasPasados === 1 ? 'día' : 'días'})`;
                    txtDiasRestantes.className = "text-danger small ms-1 fw-bold";
                    badgeEstado.textContent = "Vencido";
                    badgeEstado.className = "badge ms-2 px-2 rounded bg-danger-subtle text-danger border border-danger-subtle";
                } else if (estado === 'proximo' || (diferenciaDias <= 30 && diferenciaDias >= 0)) {
                    txtDiasRestantes.textContent = `(Faltan ${diferenciaDias} ${diferenciaDias === 1 ? 'día' : 'días'})`;
                    txtDiasRestantes.className = "text-warning-emphasis small ms-1 fw-bold";
                    badgeEstado.textContent = "Próximo";
                    badgeEstado.className = "badge ms-2 px-2 rounded bg-warning-subtle text-warning-emphasis border border-warning-subtle";
                } else {
                    txtDiasRestantes.textContent = `(Faltan ${diferenciaDias} dias)`;
                    txtDiasRestantes.className = "text-muted small ms-1";
                    badgeEstado.textContent = "Al día";
                    badgeEstado.className = "badge ms-2 px-2 rounded bg-success-subtle text-success border border-success-subtle";
                }
            }
        }

        const modalElement = document.getElementById('modalDetalleProyecto');
        if (modalElement) {
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            modalInstance.show();
        }

    } catch (error) {
        console.error("Error crítico al abrir los detalles del proyecto:", error);
    }
};

window.abrirModalEditar = function(id, nombre, cliente, tipoInstalacionId, fechaInstalacion, direccion, descripcion) {
    const formEditar = document.getElementById('formEditarProyecto');
    if (formEditar) {
        formEditar.action = `/proyectos/${id}`;
    }

    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_cliente').value = cliente;
    document.getElementById('edit_tipo_instalacion_id').value = tipoInstalacionId;
    document.getElementById('edit_fecha_instalacion').value = fechaInstalacion;
    document.getElementById('edit_direccion').value = direccion;
    document.getElementById('edit_descripcion').value = descripcion ? descripcion : '';
};

window.abrirModalSubirDocumento = function() {
    const inputArchivo = document.getElementById('doc_archivo');
    const divErrorPeso = document.getElementById('error_peso_documento');
    const btnGuardarDoc = document.getElementById('btnGuardarDocumento');
    const selectTipoDoc = document.getElementById('doc_tipo');
    
    if (inputArchivo) {
        inputArchivo.value = '';
        inputArchivo.classList.remove('is-invalid');
    }
    if (selectTipoDoc) selectTipoDoc.value = '';
    if (divErrorPeso) divErrorPeso.classList.add('d-none');
    if (btnGuardarDoc) btnGuardarDoc.disabled = false;

    const modalDoc = new bootstrap.Modal(document.getElementById('modalDocumento'));
    modalDoc.show();
};
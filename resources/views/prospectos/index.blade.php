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

    <!-- Barra de búsqueda (Ahora está fuera de la tarjeta) -->
<div class="buscador-container mb-3">
    <i class="bi bi-search"></i>
    <input type="text" class="form-control" placeholder="Buscar por nombre o teléfono">
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

                <tbody>

                    <!-- Registro de ejemplo -->

                    <tr>

                        <td>

                            Juan Pérez López

                        </td>

                        <td>

                            238 123 4567

                        </td>

                        <td>

                            Sí dejó

                        </td>

                        <td>

                            <span class="badge bg-success">

                                Interesado

                            </span>

                        </td>

                        <td>

                            <span class="badge bg-primary">

                                Activo

                            </span>

                        </td>

                        <td class="text-center">

                            <!-- Editar -->

                            <button
                                class="btn btn-warning btn-sm"
                                title="Editar"

                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarProspecto">

                                <i class="bi bi-pencil-fill"></i>

                            </button>

                            <!-- Cambiar Estado -->

                            <button
                                class="btn btn-secondary btn-sm"
                                title="Cambiar estado">

                                <i class="bi bi-arrow-repeat"></i>

                            </button>

                        </td>

                    </tr>

                                        <!-- Registro de ejemplo 2 -->

                    <tr>

                        <td>

                            María González Hernández

                        </td>

                        <td>

                            238 456 7890

                        </td>

                        <td>

                            No dejó

                        </td>

                        <td>

                            <span class="badge bg-warning text-dark">

                                Solo preguntó

                            </span>

                        </td>

                        <td>

                            <span class="badge bg-danger">

                                Inactivo

                            </span>

                        </td>

                        <td class="text-center">

                            <!-- Editar -->

                            <button
                                class="btn btn-warning btn-sm"
                                title="Editar"

                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarProspecto">

                                <i class="bi bi-pencil-fill"></i>

                            </button>

                            <!-- Cambiar Estado -->

                            <button
                                class="btn btn-secondary btn-sm"
                                title="Cambiar estado">

                                <i class="bi bi-arrow-repeat"></i>

                            </button>

                        </td>

                    </tr>

                    {{-- Más adelante irá el @forelse() --}}
                    {{--

                    <tr>

                        <td colspan="6" class="text-center py-5">

                            <i class="bi bi-person-x fs-1 text-secondary"></i>

                            <br><br>

                            No hay prospectos registrados.

                        </td>

                    </tr>

                    --}}

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

                <form>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Nombre <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                class="form-control">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Apellido paterno <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                class="form-control">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Apellido materno

                            </label>

                            <input
                                type="text"
                                class="form-control">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Número de teléfono <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                class="form-control">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Proyecto de interés <span class="text-danger">*</span>

                            </label>

                            <select class="form-select">

                                <option selected>

                                    Seleccione un proyecto

                                </option>

                                <option>Sistemas Fotovoltaicos</option>

                                <option>Calentadores Solares</option>

                                <option>Paneles Solares</option>

                                <option>Bombas Solares</option>

                                <option>Biodigestores</option>

                                <option>Instalaciones Eléctricas</option>

                            </select>

                        </div>

                    </div>
                                        <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                ¿Dejó documento?

                            </label>

                            <div class="mt-2">

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="dejo_documento"
                                        id="nuevoSiDocumento"
                                        value="1">

                                    <label
                                        class="form-check-label"
                                        for="nuevoSiDocumento">

                                        Sí

                                    </label>

                                </div>

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="dejo_documento"
                                        id="nuevoNoDocumento"
                                        value="0"
                                        checked>

                                    <label
                                        class="form-check-label"
                                        for="nuevoNoDocumento">

                                        No

                                    </label>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Estado del prospecto
                                <span class="text-danger">*</span>

                            </label>

                            <select class="form-select">

                                <option selected>

                                    Seleccione una opción

                                </option>

                                <option>

                                    Interesado

                                </option>

                                <option>

                                    Solo preguntó

                                </option>

                                <option>

                                    No interesado

                                </option>

                            </select>

                        </div>

                    </div>

                    <!-- Documento que dejó -->
<div
    class="row"
    id="nuevoDocumentoContainer"
    style="display:none;">

    <div class="col-md-12 mb-3">

        <label class="form-label">

            Documento que dejó

        </label>

        <input
            type="text"
            class="form-control"
            placeholder="Ejemplo: INE, recibo de luz, escritura, etc.">

    </div>

</div>

<!-- Notas -->
<div class="row">

    <div class="col-md-12 mb-3">

        <label class="form-label">

            Notas

        </label>

        <textarea
            class="form-control"
            rows="3"
            placeholder="Escriba aquí información adicional sobre el prospecto..."></textarea>

    </div>

</div>

</form>

</div>

<div class="modal-footer">

    

    <button
        type="button"
        class="btn btn-success">

        <i class="bi bi-floppy-fill"></i>

        Guardar prospecto

    </button>

</div>

        </div>

    </div>

</div>

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

                <form>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Nombre <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Juan">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Apellido paterno <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Pérez">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Apellido materno

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="López">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Número de teléfono <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="2381234567">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Proyecto de interés <span class="text-danger">*</span>

                            </label>

                            <select class="form-select">

                                <option>Sistemas Fotovoltaicos</option>
                                <option selected>Calentadores Solares</option>
                                <option>Paneles Solares</option>
                                <option>Bombas Solares</option>
                                <option>Biodigestores</option>
                                <option>Instalaciones Eléctricas</option>

                            </select>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                ¿Dejó documento?

                            </label>

                            <div class="mt-2">

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        checked>

                                    <label class="form-check-label">

                                        Sí

                                    </label>

                                </div>

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio">

                                    <label class="form-check-label">

                                        No

                                    </label>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Estado del prospecto

                            </label>

                            <select class="form-select">

                                <option selected>

                                    Interesado

                                </option>

                                <option>

                                    Solo preguntó

                                </option>

                                <option>

                                    No interesado

                                </option>

                            </select>

                        </div>

                    </div>

                    

                    <div class="row">

    <div class="col-md-12 mb-3">

        <label class="form-label">

            Documento que dejó

        </label>

        <input
            type="text"
            class="form-control"
            value="Cotización">

    </div>

</div>

<!-- NOTAS -->
<div class="row">

    <div class="col-md-12 mb-3">

        <label class="form-label">

            Notas

        </label>

        <textarea
            class="form-control"
            rows="3">El cliente solicitó una cotización para un sistema fotovoltaico de 6 paneles. Prefiere ser contactado por WhatsApp después de las 5:00 p.m.</textarea>

    </div>

</div>

</form>

</div>

<div class="modal-footer">

    

    <button
        type="button"
        class="btn btn-warning text-white">

        <i class="bi bi-pencil-fill"></i>

        Actualizar prospecto

    </button>

</div>

        </div>

    </div>

</div>

@endsection
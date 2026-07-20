<?php

namespace App\Http\Controllers;

use App\Models\Prospecto;
use App\Models\TipoInstalacion; // <--- Importamos este
use App\Models\EstadoProspecto; // <--- Importamos este también para el otro select
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProspectoController extends Controller
{
    public function index()
    {
        // 1. Traemos los prospectos para la tabla
        $prospectos = Prospecto::with(['tipoInstalacion', 'estadoProspecto'])->get();
        
        // 2. Traemos los catálogos para los selects del modal
        $tiposInstalacion = TipoInstalacion::all();
        $estadosProspecto = EstadoProspecto::all();

        // 3. Enviamos todo a la vista
        return view('prospectos.index', compact('prospectos', 'tiposInstalacion', 'estadosProspecto'));
    }

    public function store(Request $request)
{
    // 1. Creamos el validador manualmente
    $validator = Validator::make($request->all(), [
        'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'apellido_paterno' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'apellido_materno' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'telefono' => ['required', 'digits:10', 'unique:prospectos,telefono'],
        'tipo_instalacion_id' => ['required', 'exists:tipos_instalacion,id'],
        'estado_prospecto_id' => ['required', 'exists:estados_prospecto,id'],
        'dejo_documento' => ['required', 'boolean'],
        'detalle_documento' => ['required_if:dejo_documento,1', 'nullable', 'string', 'max:255'],
        'notas' => ['nullable', 'string', 'max:500', 'min:5'],
    ], [
        'telefono.unique' => 'Este número de teléfono ya está registrado en el sistema.',
        'telefono.digits' => 'El número de teléfono debe tener exactamente 10 dígitos.',
        'nombre.regex' => 'El nombre solo debe contener letras.',
        'detalle_documento.required_if' => 'El campo Documento que dejó es obligatorio.',
    ]);

    // 2. Si la validación falla, regresamos con los errores y el "aviso" para abrir el modal
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_abierto', 'nuevo'); // <--- Esto es la clave
    }

    // 3. Preparamos los datos si la validación pasó
    $datos = $request->all();

    if ($request->dejo_documento == '0') {
        $datos['detalle_documento'] = null;
    }

    // 4. Guardamos
    Prospecto::create($datos);

    // 5. Regresamos con éxito

    $nombreCompleto = $prospecto->nombre . ' ' . $prospecto->apellido_paterno . ($prospecto->apellido_materno ? ' ' . $prospecto->apellido_materno : '');

return redirect()->route('prospectos.index')->with('success', 'Prospecto ' . $nombreCompleto . ' registrado correctamente.');
}

public function update(Request $request, $id)
{
    // 1. Buscamos el prospecto
    $prospecto = Prospecto::findOrFail($id);

    // SEGURIDAD EXTRA: Si está inactivo, no permitir editarlo
    if ($prospecto->estado === 'inactivo') {
        return redirect()->route('prospectos.index')->with('error', 'No se puede editar un prospecto que se encuentra inactivo.');
    }

    // 2. Creamos el validador manualmente
    $validator = Validator::make($request->all(), [
        'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'apellido_paterno' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'apellido_materno' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'telefono' => ['required', 'digits:10', 'unique:prospectos,telefono,' . $id],
        'tipo_instalacion_id' => ['required', 'exists:tipos_instalacion,id'],
        'estado_prospecto_id' => ['required', 'exists:estados_prospecto,id'],
        'dejo_documento' => ['required', 'boolean'],
        'detalle_documento' => ['required_if:dejo_documento,1', 'nullable', 'string', 'max:255'],
        'notas' => ['nullable', 'string', 'max:500'],
    ], [
        // Mensajes personalizados
        'detalle_documento.required_if' => 'El campo documento que dejó es obligatorio cuando seleccionas que sí dejó documento.'
    ]);

    // 3. Si la validación falla, regresamos con errores y el aviso 'editar'
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_abierto', 'editar'); // <--- Esto abre el modal de edición
    }

    // 4. Preparamos los datos
    $datos = $request->all();

    // Si cambió a "No dejó documento", limpiamos el detalle anterior
    if ($request->dejo_documento == '0') {
        $datos['detalle_documento'] = null;
    }

    // 5. Actualizamos
    $prospecto->update($datos);

    // 6. Regresamos con éxito
    $nombreCompleto = $prospecto->nombre . ' ' . $prospecto->apellido_paterno . ($prospecto->apellido_materno ? ' ' . $prospecto->apellido_materno : '');

return redirect()->route('prospectos.index')->with('success', 'Prospecto ' . $nombreCompleto . ' actualizado correctamente.');
}

public function toggleStatus($id)
{
    $prospecto = Prospecto::findOrFail($id);
    
    // Cambiamos el estado dependiendo de lo que tenga actualmente
    if ($prospecto->estado === 'activo') {
        $prospecto->estado = 'inactivo';
    } else {
        $prospecto->estado = 'activo';
    }
    
    $prospecto->save();

    $nombreCompleto = $prospecto->nombre . ' ' . $prospecto->apellido_paterno . ($prospecto->apellido_materno ? ' ' . $prospecto->apellido_materno : '');

return redirect()->route('prospectos.index')->with('success', 'El estado del prospecto ' . $nombreCompleto . ' ha sido actualizado correctamente.');
}
}

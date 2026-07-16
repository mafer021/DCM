<?php

namespace App\Http\Controllers;

use App\Models\Prospecto;
use App\Models\TipoInstalacion; // <--- Importamos este
use App\Models\EstadoProspecto; // <--- Importamos este también para el otro select
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
    // 1. Validamos
    $request->validate([
        'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'apellido_paterno' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'apellido_materno' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
        'telefono' => ['required', 'digits:10', 'unique:prospectos,telefono'],
        'tipo_instalacion_id' => ['required', 'exists:tipos_instalacion,id'],
        'estado_prospecto_id' => ['required', 'exists:estados_prospecto,id'],
        'dejo_documento' => ['required', 'boolean'],
        'detalle_documento' => ['required_if:dejo_documento,1', 'nullable', 'string', 'max:255'],
        'notas' => ['nullable', 'string', 'max:500', 'min:5'], // Mínimo 5 caracteres si escribe algo
    ], [
        // Mensajes personalizados (opcional, para que el usuario entienda mejor)
        'telefono.unique' => 'Este número de teléfono ya está registrado en el sistema.',
        'telefono.digits' => 'El número de teléfono debe tener exactamente 10 dígitos.',
        'nombre.regex' => 'El nombre solo debe contener letras.',
        'detalle_documento.required_if' => 'El campo Documento que dejó es obligatorio.',
    ]);

    // 2. Preparamos los datos
    $datos = $request->all();

    // Si el usuario seleccionó "No" (0), forzamos a que el detalle sea null
    if ($request->dejo_documento == '0') {
        $datos['detalle_documento'] = null;
    }

    // 3. Guardamos
    Prospecto::create($datos);

    // 4. Regresamos
    return redirect()->route('prospectos.index')->with('success', 'Prospecto registrado correctamente.');
}
}

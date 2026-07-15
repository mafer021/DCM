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
    // 1. Validamos que los datos vengan bien
    $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'tipo_instalacion_id' => 'required|exists:tipos_instalacion,id',
        'estado_prospecto_id' => 'required|exists:estados_prospecto,id',
    ]);

    // 2. Guardamos el nuevo prospecto
    \App\Models\Prospecto::create($request->all());

    // 3. Regresamos a la lista con un mensaje de éxito
    return redirect()->route('prospectos.index')->with('success', 'Prospecto registrado correctamente.');
}
}

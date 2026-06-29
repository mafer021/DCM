<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mantenimiento;
use App\Models\Proyecto;
use App\Models\User; // Importamos el modelo User para buscar los técnicos

class MantenimientoController extends Controller
{
    /**
     * Muestra la lista de mantenimientos.
     */
    public function index()
    {
        // 1. Traer todos los mantenimientos con sus relaciones cargadas
        $mantenimientos = Mantenimiento::with(['proyecto', 'tecnico'])
            ->orderBy('fecha_mantenimiento', 'desc')
            ->get();

        // 2. Traer todos los proyectos para el select del formulario "Registrar"
        $proyectos = Proyecto::orderBy('nombre', 'asc')->get();

        // 3. Traer SOLO los usuarios con rol admin o empleado que estén ACTIVOS
        $tecnicos = User::whereIn('rol', ['admin', 'empleado'])
            ->where('estado', 'activo')
            ->orderBy('nombre', 'asc')
            ->get();

        // 4. Mandamos las 3 variables hacia la vista index
        return view('mantenimientos.index', compact('mantenimientos', 'proyectos', 'tecnicos'));
    }

    /**
     * Almacena un nuevo registro de mantenimiento en la base de datos.
     */
    /**
     * Almacena un nuevo registro de mantenimiento y actualiza el estado del proyecto.
     */
    public function store(Request $request)
    {
        $request->validate([
    'proyecto_id'         => 'required|exists:proyectos,id',
    'tecnico_id'          => 'required|exists:users,id',
    'fecha_mantenimiento' => [
        'required', 
        'date', 
        'before_or_equal:today', 
        'after:' . now()->subYears(3)->format('Y-m-d')
    ],
    'observaciones'       => 'nullable|string',
], [
    'fecha_mantenimiento.before_or_equal' => 'La fecha de mantenimiento no puede ser una fecha futura.',
    'fecha_mantenimiento.after'           => 'La fecha de mantenimiento no puede ser mayor a 3 años de antigüedad.'
]);
        

        // 2. Crear el registro de la bitácora
        Mantenimiento::create([
            'proyecto_id'         => $request->proyecto_id,
            'tecnico_id'          => $request->tecnico_id,
            'fecha_mantenimiento' => $request->fecha_mantenimiento,
            'observaciones'       => $request->observaciones,
            'estado'              => 'realizado',
        ]);

        // 3. ¡ACTUALIZAR EL PROYECTO RELACIONADO!
        $proyecto = Proyecto::findOrFail($request->proyecto_id);
        
        // Calculamos la siguiente fecha sumando 1 año a la fecha del mantenimiento realizado
        // Si DCM usa 6 meses, puedes cambiar '+1 year' por '+6 months'
        $nuevaFechaProximo = date('Y-m-d', strtotime($request->fecha_mantenimiento . ' +1 year'));

        $proyecto->update([
            'estado'               => 'al_dia', // Cambia de Vencido a Al día
            'proximo_mantenimiento'=> $nuevaFechaProximo, // Se recorre el calendario
        ]);

        // 4. Redireccionar con éxito
        return redirect()->back()->with('success', 'Mantenimiento registrado. El estado del proyecto ha sido actualizado a "Al día".');
    }

    /**
     * Actualiza un registro de mantenimiento existente (Solo técnico, fecha y observaciones).
     */
    public function update(Request $request, $id)

    {
        // Limpiamos los errores de la sesión antes de validar lo nuevo
    session()->forget('errors');
        // 1. Validar los campos permitidos para la edición
        // Solo validamos lo que el usuario SÍ puede modificar
    $request->validate([
        'tecnico_id'    => 'required|exists:users,id',
        'observaciones' => 'nullable|string',
    ]);
        

        // 2. Buscar el registro de mantenimiento por su ID
        $mantenimiento = Mantenimiento::findOrFail($id);

        // 3. Actualizar únicamente los datos correspondientes
        $mantenimiento->update([
            'tecnico_id'          => $request->tecnico_id,
            'fecha_mantenimiento' => $request->fecha_mantenimiento,
            'observaciones'       => $request->observaciones,
            // El proyecto_id y el estado se quedan exactamente como estaban
        ]);

        // 4. Redireccionar de vuelta con mensaje de éxito
        return redirect()->back()->with('success', 'Registro de mantenimiento actualizado correctamente.');
    }

    /**
 * Retorna el historial de mantenimientos de un proyecto específico en formato JSON.
 * (Método exclusivo para las peticiones AJAX desde el módulo de Proyectos o Mantenimientos)
 */
public function historialPorProyecto($id)
{
    // 1. Buscar los mantenimientos que pertenezcan al proyecto solicitado
    // Cargamos la relación 'tecnico' para saber el nombre de quién asistió
    $historial = Mantenimiento::with(['tecnico'])
        ->where('proyecto_id', $id)
        ->orderBy('fecha_mantenimiento', 'desc')
        ->get();

    // 2. Formateamos los campos para que JavaScript los pueda pintar directo sin romperse
    $data = $historial->map(function ($m) {
        return [
            // Convierte AAAA-MM-DD a DD/MM/AAAA para que se vea bien en la tabla
            'fecha'         => date('d/m/Y', strtotime($m->fecha_mantenimiento)),
            
            // Une el nombre y apellido del técnico. Si no tiene, pone 'No asignado'
            'tecnico'       => $m->tecnico 
                ? ($m->tecnico->nombre . ' ' . $m->tecnico->apellido_paterno) 
                : 'No asignado',
                
            // Si las observaciones están vacías, pone un texto por defecto
            'observaciones' => $m->observaciones ?? 'Sin observaciones.',
            'estado'        => $m->estado ?? 'realizado'
        ];
    }); // 👈 Aquí cierra correctamente el map

    // 3. Retornar la colección limpia en formato JSON
    return response()->json($data, 200);
}

public function destroy($id)
{
    $mantenimiento = Mantenimiento::findOrFail($id);
    $proyecto = $mantenimiento->proyecto;

    $mantenimiento->delete();

    // Buscamos el mantenimiento más reciente que haya quedado
    $ultimoMantenimiento = Mantenimiento::where('proyecto_id', $proyecto->id)
                                        ->orderBy('fecha_mantenimiento', 'desc')
                                        ->first();

    if ($ultimoMantenimiento) {
        // Si hay otro mantenimiento, calculamos la fecha a 1 año después de ese
        $nuevaFechaProximo = date('Y-m-d', strtotime($ultimoMantenimiento->fecha_mantenimiento . ' +1 year'));
        $proyecto->update([
            'proximo_mantenimiento' => $nuevaFechaProximo
        ]);
    } else {
        // SI YA NO HAY MANTENIMIENTOS:
        // Calculamos: fecha_instalacion (2025) + 1 año = (2026)
        $fechaInstalacionMasUno = date('Y-m-d', strtotime($proyecto->fecha_instalacion . ' +1 year'));
        
        $proyecto->update([
            'proximo_mantenimiento' => $fechaInstalacionMasUno 
        ]);
    }
    
    return redirect()->back()->with('success', 'Registro eliminado y fecha restablecida correctamente.');
}
}

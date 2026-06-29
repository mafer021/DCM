<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Documento;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\TipoInstalacion;
use App\Models\TipoDocumento;
use Carbon\Carbon; // Importante para calcular las fechas de forma sencilla
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ProyectoController extends Controller
{
    public function index()
    {
        // 1. Traemos los proyectos con sus documentos y el tipo de documento
        $proyectos = Proyecto::with(['tipoInstalacion', 'documentos.tipoDocumento'])->get();

        // 2. Traemos los tipos de instalación para el formulario de crear proyectos
        $tiposInstalacion = TipoInstalacion::all();

        // 3. Traemos el catálogo de documentos para el select de subir archivos
        $tiposDocumento = TipoDocumento::all();

        // 4. Mandamos solo lo que la base de datos real necesita
        return view('proyectos.index', compact('proyectos', 'tiposInstalacion', 'tiposDocumento'));
    }

    /**
     * Almacena un proyecto recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        // Calculamos la fecha exacta de hace 3 años a partir de hoy
        $haceTresAnios = \Carbon\Carbon::today()->subYears(3)->format('Y-m-d');

        $request->validate([
            'nombre'              => 'required|string|min:3|max:150|unique:proyectos,nombre', 
            'cliente'             => 'required|string|min:2|max:150',
            'tipo_instalacion_id' => 'required|exists:tipos_instalacion,id',
            
            // Modificamos esta línea: Máximo hoy, mínimo hace 3 años
            'fecha_instalacion'   => 'required|date|before_or_equal:today|after_or_equal:' . $haceTresAnios,
            
            'direccion'           => 'required|string|min:10|max:500',
            'descripcion'         => 'nullable|string|max:1000',
        ], [
            'nombre.required'              => 'El nombre del proyecto es obligatorio.',
            'nombre.unique'                => 'Este nombre de proyecto ya existe. Por favor, añade un diferenciador.',
            'cliente.required'             => 'El nombre del cliente es obligatorio.',
            'tipo_instalacion_id.required' => 'Debes seleccionar un tipo de instalación válido.',
            'fecha_instalacion.required'   => 'La fecha de instalación es obligatoria.',
            'fecha_instalacion.before_or_equal' => 'La fecha de instalación no puede ser una fecha futura.',
            
            // Mensaje personalizado para el límite de 3 años
            'fecha_instalacion.after_or_equal'  => 'No se pueden registrar proyectos con más de 3 años de antigüedad.',
            
            'direccion.required'           => 'La dirección es obligatoria.',
        ]);

        // Cálculo del próximo mantenimiento (1 año después)
        $fechaInstalacion = \Carbon\Carbon::parse($request->fecha_instalacion);
        $proximoMantenimiento = $fechaInstalacion->addYear()->startOfDay();
        $hoy = \Carbon\Carbon::today();

        // CALCULAMOS EL ESTADO REAL EN EL BACKEND
        if ($proximoMantenimiento->isPast() && !$proximoMantenimiento->isToday()) {
            // Si el mantenimiento calculado ya pasó con respecto a hoy
            $estadoReal = 'vencido';
        } elseif ($hoy->diffInDays($proximoMantenimiento, false) <= 30 && $hoy->diffInDays($proximoMantenimiento, false) >= 0) {
            // Si falta un mes o menos
            $estadoReal = 'proximo';
        } else {
            // Si es un proyecto nuevo o falta más de un mes
            $estadoReal = 'al_dia';
        }

        // Guardamos el modelo creado dentro de la variable $proyecto
        $proyecto = Proyecto::create([
            'nombre'              => $request->nombre,
            'cliente'             => $request->cliente,
            'tipo_instalacion_id' => $request->tipo_instalacion_id,
            'fecha_instalacion'   => $request->fecha_instalacion,
            'proximo_mantenimiento'=> $proximoMantenimiento->format('Y-m-d'),
            'direccion'           => $request->direccion,
            'descripcion'         => $request->descripcion,
            'estado'              => $estadoReal,
        ]);

        // REDIRECCIÓN CON EL NOMBRE DINÁMICO
        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto "' . $proyecto->nombre . '" creado con éxito.');
    }

    public function update(Request $request, $id)
    {
        $proyecto = \App\Models\Proyecto::findOrFail($id);
        $haceTresAnios = \Carbon\Carbon::today()->subYears(3)->format('Y-m-d');

        try {
            // Ejecutamos la validación normal
            $request->validate([
                'nombre'              => 'required|string|min:3|max:150|unique:proyectos,nombre,' . $id,
                'cliente'             => 'required|string|min:2|max:150',
                'tipo_instalacion_id' => 'required|exists:tipos_instalacion,id',
                'fecha_instalacion'   => 'required|date|before_or_equal:today|after_or_equal:' . $haceTresAnios,
                'direccion'           => 'required|string|min:10|max:500',
                'descripcion'         => 'nullable|string|max:1000',
            ], [
                'nombre.required'              => 'El nombre del proyecto es obligatorio.',
                'nombre.unique'                => 'Este nombre de proyecto ya existe. Por favor, añade un diferenciador.',
                'cliente.required'             => 'El nombre del cliente es obligatorio.',
                'tipo_instalacion_id.required' => 'Debes seleccionar un tipo de instalación válido.',
                'fecha_instalacion.required'   => 'La fecha de instalación es obligatoria.',
                'fecha_instalacion.before_or_equal' => 'La fecha de instalación no puede ser una fecha futura.',
                'fecha_instalacion.after_or_equal'  => 'No se pueden registrar proyectos con más de 3 años de antigüedad.',
                'direccion.required'           => 'La dirección es obligatoria.',
            ]);

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error_en_editar', $id); 
        }

        // Si la validación pasa, guardamos normalmente
        $fechaInstalacion = \Carbon\Carbon::parse($request->fecha_instalacion);
        $proximoMantenimiento = $fechaInstalacion->addYear()->startOfDay();
        $hoy = \Carbon\Carbon::today();

        if ($proximoMantenimiento->isPast() && !$proximoMantenimiento->isToday()) {
            $estadoReal = 'vencido';
        } elseif ($hoy->diffInDays($proximoMantenimiento, false) <= 30 && $hoy->diffInDays($proximoMantenimiento, false) >= 0) {
            $estadoReal = 'proximo';
        } else {
            $estadoReal = 'al_dia';
        }

        $proyecto->update([
            'nombre'              => $request->nombre,
            'cliente'             => $request->cliente,
            'tipo_instalacion_id' => $request->tipo_instalacion_id,
            'fecha_instalacion'   => $request->fecha_instalacion,
            'proximo_mantenimiento'=> $proximoMantenimiento->format('Y-m-d'),
            'direccion'           => $request->direccion,
            'descripcion'         => $request->descripcion,
            'estado'              => $estadoReal,
        ]);

        // REDIRECCIÓN CON EL NOMBRE DINÁMICO
        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto "' . $proyecto->nombre . '" actualizado con éxito.');
    }

    /**
     * DESCARGAR DOCUMENTO
     */
   public function descargarDocumento($id)
{
    // 1. Verificar si el usuario está logueado
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
$documento = Documento::findOrFail($id);

    // Si tu carpeta real es: C:\...\dcm-sistema\storage\app\private\documentos_privados\
    // Entonces la ruta física es 'private/' + lo que diga la base de datos
    $rutaReal = storage_path('app/private/' . $documento->ruta_archivo);

    // Verificamos si existe físicamente
    if (!file_exists($rutaReal)) {
        return back()->with('error', 'El archivo no se encuentra en: ' . $rutaReal);
    }

    // Retornamos la descarga
    return response()->download($rutaReal, $documento->nombre_archivo);
}

    /**
     * ELIMINAR DOCUMENTO
     */
    public function destroyDocumento($id)
    {
        $documento = Documento::findOrFail($id);
        
        // Buscamos el proyecto asociado antes de eliminar el documento para obtener su nombre
        $proyecto = Proyecto::find($documento->proyecto_id);
        $nombreProyecto = $proyecto ? $proyecto->nombre : 'Proyecto no especificado';
        $nombreDocumento = $documento->nombre_archivo;

        if (Storage::disk('public')->exists($documento->ruta_archivo)) {
            Storage::disk('public')->delete($documento->ruta_archivo);
        }

        $documento->delete();

        // MENSAJE PERSONALIZADO CON NOMBRE DEL ARCHIVO Y DEL PROYECTO
        return back()->with('success', 'Documento "' . $nombreDocumento . '" eliminado del proyecto "' . $nombreProyecto . '" correctamente.');
    }

    /**
     * ALMACENAR DOCUMENTO ASOCIADO A UN PROYECTO
     */
    public function storeDocumento(Request $request)
    {
        // 1. Validaciones del Backend (10240 KB = 10 MB máximo)
        $request->validate([
            'proyecto_id'       => 'required|exists:proyectos,id',
            'tipo_documento_id' => 'required|exists:tipos_documento,id',
            'doc_archivo'       => 'required|file|mimes:pdf,jpg,png,doc,docx,xlsx|max:10240',
            'descripcion'       => 'nullable|string|max:500',
        ], [
            'doc_archivo.required' => 'Debes seleccionar un archivo para subir.',
            'doc_archivo.max'      => 'El archivo excede el límite permitido de 10 MB.',
            'doc_archivo.mimes'    => 'El formato del archivo no está permitido (Formatos válidos: PDF, JPG, PNG, DOC, XLSX).',
            'tipo_documento_id.required' => 'El tipo de documento es obligatorio.'
        ]);

        if ($request->hasFile('doc_archivo')) {
            $archivo = $request->file('doc_archivo');

            // 2. Extraemos las propiedades físicas reales usando la instancia del archivo
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $tamanoBytes = $archivo->getSize(); // Peso físico real

            // 1. Cambiamos el almacenamiento:
            // Usamos 'local' para ir a storage/app/
            // La ruta final será 'private/documentos_privados'
            // En lugar de 'private/documentos_privados'
$rutaAlmacenamiento = 'documentos_privados'; 
$ruta = $archivo->store($rutaAlmacenamiento, 'local');

// Esto guardará los archivos dentro de storage/app/documentos_privados/

            // 4. Registramos en la base de datos usando exactamente los campos de tu $fillable
            Documento::create([
                'proyecto_id'       => $request->proyecto_id,
                'tipo_documento_id' => $request->tipo_documento_id,
                'nombre_archivo'    => $nombreOriginal, 
                'descripcion'       => $request->descripcion,
                'ruta_archivo'      => $ruta,
                'extension'         => $extension,
                'tamano'            => $tamanoBytes,
                'fecha_subida'      => \Carbon\Carbon::now()->format('Y-m-d'), // Fecha de hoy estructurada
                'usuario_id'        => auth()->id() ?? null, // Registra el ID de quien subió el archivo si está logueado
            ]);

            // Buscamos el proyecto para traer su nombre real
            $proyecto = Proyecto::findOrFail($request->proyecto_id);

            // MENSAJE PERSONALIZADO CON EL NOMBRE DEL PROYECTO
            return redirect()->back()->with('success', '¡Documento "' . $nombreOriginal . '" subido y asociado al proyecto "' . $proyecto->nombre . '" con éxito!');
        }

        return redirect()->back()->with('error', 'No se pudo procesar la subida del archivo.');
    }

    public function verDocumento($id)
{
    // 1. Verificar si el usuario está logueado
    if (!auth()->check()) {
        return redirect()->route('login');
    }

$documento = Documento::findOrFail($id);
    
    // Usamos la misma lógica que en descargar para ser consistentes
    $rutaReal = (strpos($documento->ruta_archivo, 'private/') === 0) 
                ? storage_path('app/' . $documento->ruta_archivo) 
                : storage_path('app/private/' . $documento->ruta_archivo);

    if (!file_exists($rutaReal)) {
        abort(404, 'Archivo no encontrado.');
    }

    return response()->file($rutaReal);
}
}
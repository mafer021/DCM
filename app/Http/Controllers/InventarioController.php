<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\UnidadMedida;
use App\Models\MovimientoInventario; // Importamos este
use Illuminate\Support\Facades\Auth;  // Para saber quién hizo el movimiento
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        // Traemos todos los productos con su categoría y unidad relacionada
        $productos = Producto::with(['categoria', 'unidad'])->get();

        // AGREGA ESTA LÍNEA: Filtramos solo los activos para los selects de los modales
    $productosActivos = Producto::where('estado_producto', 'activo')->get();
        
        // Traemos los catálogos para los formularios (el select de categorías y unidades)
        $categorias = CategoriaProducto::all();
        $unidades = UnidadMedida::all();

        // 2. AGREGA ESTA LÍNEA para traer los movimientos
    // 'with('producto')' es clave para poder mostrar el nombre del producto en la tabla
    // Cambia tu línea actual en el InventarioController por esta:
$movimientos = MovimientoInventario::with(['producto', 'usuario'])->latest()->get();

        return view('inventarios.index', compact('productos', 'productosActivos', 'categorias', 'unidades', 'movimientos'));
    }

    public function store(Request $request)
{
    // 1. Validamos los datos
    $request->validate([
        'nombre'       => 'required|string|max:150',
        'categoria_id' => 'required|exists:categorias_productos,id', // Verifica que exista en la BD
        'unidad_id'    => 'required|exists:unidades_medida,id',
        'precio'       => 'required|numeric|min:0.01', // Mínimo 0.01, no acepta negativos
        'stock_inicial'=> 'required|integer|min:0',    // Mínimo 0, no acepta negativos
    ]);

    // 2. Definimos la lógica del estado (Umbral: 5 productos)
    $stock = $request->stock_inicial;
    $estado = 'disponible'; // Por defecto

    if ($stock == 0) {
        $estado = 'agotado';
    } elseif ($stock <= 5) {
        $estado = 'stock_bajo';
    }

    // 3. Creamos el producto
    $producto = Producto::create([
        'nombre' => $request->nombre,
        'categoria_id' => $request->categoria_id,
        'unidad_id' => $request->unidad_id,
        'precio' => $request->precio,
        'stock' => $stock,
        'estado' => $estado,
    ]);

    // 4. Registramos el movimiento inicial
    MovimientoInventario::create([
        'producto_id' => $producto->id,
        'tipo_movimiento' => 'entrada',
        'cantidad' => $stock,
        'observacion' => 'Registro inicial de producto',
        'usuario_id' => Auth::id(),
    ]);

    return redirect()->back()->with('success', 'El producto "' . $producto->nombre . '" se registró correctamente.');
}

public function desactivar($id)
{
    $producto = \App\Models\Producto::findOrFail($id);
    
    // Si está 'activo', lo pasa a 'inactivo'. Si es cualquier otra cosa (inactivo), lo vuelve 'activo'.
    $nuevoEstado = ($producto->estado_producto === 'activo') ? 'inactivo' : 'activo';
    
    $producto->update(['estado_producto' => $nuevoEstado]);

    $mensaje = ($nuevoEstado === 'activo') ? 'reactivado' : 'desactivado';
    return redirect()->back()->with('success', 'El producto "' . $producto->nombre . '" ha sido ' . $mensaje . '.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|max:150',
        'categoria_id' => 'required',
        'unidad_id' => 'required',
        'precio' => 'required|numeric|min:0.01',
    ]);

    $producto = \App\Models\Producto::findOrFail($id);
    $producto->update([
        'nombre' => $request->nombre,
        'categoria_id' => $request->categoria_id,
        'unidad_id' => $request->unidad_id,
        'precio' => $request->precio,
        // El stock no se edita desde aquí, eso se hace en Movimientos
    ]);

    return redirect()->back()->with('success', 'El producto "' . $producto->nombre . '" se actualizó correctamente.');
}
}

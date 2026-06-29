<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos que los datos vengan correctos
        $request->validate([
            'producto_id'     => 'required|exists:productos,id',
            'tipo_movimiento' => 'required|in:entrada,salida',
            'cantidad'        => 'required|integer|min:1',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        // --- NUEVA VALIDACIÓN DE SEGURIDAD ---
    if ($producto->estado_producto === 'inactivo') {
        return redirect()->route('inventario.index')
                         ->withErrors(['producto_id' => 'No puedes realizar movimientos en un producto inactivo.']);
    }
    // -------------------------------------

        // 2. Lógica: Si es salida, verificamos que haya suficiente stock
        // MovimientoController.php

if ($request->tipo_movimiento === 'salida' && $producto->stock < $request->cantidad) {
    // 1. Redirigimos a la ruta index, NO hacia atrás
    // 2. Enviamos el mensaje de error
    // 3. Enviamos un "flag" para que el script sepa qué modal abrir
    return redirect()->route('inventario.index')
                    ->withInput() // <--- ¡ESTO ES LO MÁS IMPORTANTE!
                     ->withErrors(['cantidad' => 'No hay suficiente stock disponible.'])
                     ->with('modal_abierto', 'modalSalida'); 
}

        // 3. Actualizamos el stock en el producto
        if ($request->tipo_movimiento === 'entrada') {
            $producto->stock += $request->cantidad;
        } else {
            $producto->stock -= $request->cantidad;
        }

        // 4. Recalculamos el estado del producto automáticamente
        if ($producto->stock <= 0) {
            $producto->estado = 'agotado';
        } elseif ($producto->stock <= 5) {
            $producto->estado = 'stock_bajo';
        } else {
            $producto->estado = 'disponible';
        }
        
        $producto->save();

        // 5. Registramos el movimiento en la base de datos
        MovimientoInventario::create([
            'producto_id'     => $producto->id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'cantidad'        => $request->cantidad,
            'observacion'     => $request->observacion,
            'usuario_id'      => Auth::id(), // Guardamos quién realizó el movimiento
        ]);

        // En tu MovimientoController.php

// En MovimientoController.php, en el método store:
// ... después de crear el registro ...

return redirect()->route('inventario.index')
                 ->with('success', 'Movimiento registrado correctamente.')
                 ->with('modal_abierto', 'modalMovimientos');
    }
}

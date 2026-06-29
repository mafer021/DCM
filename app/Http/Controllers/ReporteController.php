<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Producto;
use App\Models\Mantenimiento;

class ReporteController extends Controller
{
    public function index()
    {
        // 1. KPIs (Contadores)
        $totalProyectos = Proyecto::count();
        $totalProductos = Producto::count();
        $totalMantenimientos = Mantenimiento::count();

        // 2. Últimos mantenimientos (ordenados por fecha, los 5 más recientes)
        // Asegúrate de incluir with('proyecto')
    $ultimosMantenimientos = Mantenimiento::with('proyecto')
                                ->latest()
                                ->take(5)
                                ->get();

        // 3. Productos con stock bajo (activos)
        $productosBajos = Producto::whereIn('estado', ['stock_bajo', 'agotado'])
                                  ->where('estado_producto', 'activo')
                                  ->limit(5)
                                  ->get();

        // Obtenemos los últimos 5 movimientos de cada tabla
    $movimientosInventario = \App\Models\MovimientoInventario::latest()->take(3)->get()->map(function($item) {
        return [
            'titulo' => 'Movimiento de Inventario',
            'detalle' => $item->producto->nombre . ' - ' . $item->tipo_movimiento,
            'fecha' => $item->created_at
        ];
    });

    $nuevosProyectos = \App\Models\Proyecto::latest()->take(3)->get()->map(function($item) {
        return [
            'titulo' => 'Nuevo Proyecto registrado',
            'detalle' => $item->nombre,
            'fecha' => $item->created_at
        ];
    });

    // Combinamos todo y lo ordenamos por fecha (el más reciente primero)
    $actividadReciente = $movimientosInventario->merge($nuevosProyectos)
                            ->sortByDesc('fecha')
                            ->take(5);

    return view('reportes.index', compact(
        'totalProyectos', 'totalProductos', 'totalMantenimientos', 
        'ultimosMantenimientos', 'productosBajos', 'actividadReciente'
    ));
    }
}

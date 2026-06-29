<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Producto;
use App\Models\Mantenimiento;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Contadores
        $totalProyectos = Proyecto::count();
        $totalProductos = Producto::count();
        $totalMantenimientos = Mantenimiento::count(); // Los que ya se hicieron

        // 2. Alertas de inventario (productos con stock bajo o agotado)
        // AHORA agrégale el filtro 'activo':
$productosBajos = Producto::whereIn('estado', ['stock_bajo', 'agotado'])
                            ->where('estado_producto', 'activo') // <--- AGREGA ESTA LÍNEA
                            ->limit(5)
                            ->get();

        // 1. Traemos todos los proyectos (sin filtrar por estado en la DB)
    $proyectos = Proyecto::all();

    // 2. Filtramos en memoria (usando la misma lógica que en tu vista)
    $proximosMantenimientos = $proyectos->filter(function ($proyecto) {
        $fechaMantenimiento = \Carbon\Carbon::parse($proyecto->proximo_mantenimiento);
        $hoy = \Carbon\Carbon::today();
        $en30Dias = \Carbon\Carbon::today()->addDays(30);

        // Queremos mostrar si está VENCIDO o PRÓXIMO
        return $fechaMantenimiento->lt($hoy) || $fechaMantenimiento->between($hoy, $en30Dias);
    })->sortBy('proximo_mantenimiento'); // Los ordenamos por fecha

    return view('dashboard.index', compact(
        'totalProyectos', 
        'totalProductos', 
        'totalMantenimientos', 
        'productosBajos', 
        'proximosMantenimientos'
    ));
}
}


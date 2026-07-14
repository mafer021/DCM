<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProspectoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| PÁGINA PRINCIPAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Proyectos
    |--------------------------------------------------------------------------
    |*/

    // RUTAS COMPLETAS PARA PROYECTOS (Con sus nombres correspondientes)
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
    Route::put('/proyectos/{id}', [ProyectoController::class, 'update'])->name('proyectos.update');
    Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

    // LAS NUEVAS RUTAS PARA TUS DOCUMENTOS APUNTANDO A TU PROYECTOCONTROLLER:
    Route::post('/proyectos/documentos', [ProyectoController::class, 'storeDocumento'])->name('documentos.store');
    Route::get('/proyectos/documentos/{id}/descargar', [ProyectoController::class, 'descargarDocumento'])->name('documentos.descargar');
    Route::delete('/proyectos/documentos/{id}', [ProyectoController::class, 'destroyDocumento'])->name('documentos.destroy');

    Route::get('/proyectos/documentos/{id}/ver', [ProyectoController::class, 'verDocumento'])->name('documentos.ver');
    /*
    |--------------------------------------------------------------------------
    | Mantenimientos
    |--------------------------------------------------------------------------
    */

    Route::get('/mantenimientos', [MantenimientoController::class, 'index'])->name('mantenimientos.index');
    Route::post('/mantenimientos', [MantenimientoController::class, 'store'])->name('mantenimientos.store');
    Route::put('/mantenimientos/{id}', [MantenimientoController::class, 'update'])->name('mantenimientos.update');
    Route::delete('/mantenimientos/{id}', [MantenimientoController::class, 'destroy'])->name('mantenimientos.destroy');

    // Esta ruta nos servirá más adelante para que el JavaScript pida el historial completo de un proyecto mediante AJAX
    Route::get('/proyectos/{id}/historial-mantenimientos', [MantenimientoController::class, 'historialPorProyecto'])->name('proyectos.mantenimientos.historial');

    /*
    |--------------------------------------------------------------------------
    | Inventario
    |--------------------------------------------------------------------------
    */

    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
//Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
// Por esto:
Route::patch('/inventario/{id}/desactivar', [InventarioController::class, 'desactivar'])->name('inventario.desactivar');
Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');

Route::post('/movimientos/store', [MovimientoController::class, 'store'])->name('movimientos.store');


/*
    |--------------------------------------------------------------------------
    | Prospectos
    |--------------------------------------------------------------------------
    |*/

    Route::get('/prospectos', [ProspectoController::class, 'index'])->name('prospectos.index');



    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */

    Route::get('/reportes', [ReporteController::class, 'index']);

});


/*
|--------------------------------------------------------------------------
| USUARIOS SOLO ADMIN GET mostrar modulo usuarios POST guardar usuarios
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
/*
    |----------------------------------------------------------------------
    | Vista principal usuarios
    |----------------------------------------------------------------------
    */
    Route::get('/usuarios', [UsuarioController::class, 'index']);

    /*
    |----------------------------------------------------------------------
    | Guardar nuevo usuario, para el formulario /usuarios es la URL donde enviara el formulario
    |----------------------------------------------------------------------
    */

    Route::post('/usuarios', [UsuarioController::class, 'store'])
        ->name('usuarios.store');

    /*
    |--------------------------------------------------------------------------
    | Actualizar usuario
    |--------------------------------------------------------------------------
    */

    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])
        ->name('usuarios.update');

        /*
    |--------------------------------------------------------------------------
    | Actualizar el estado de activo y inactivo 
    |--------------------------------------------------------------------------
    */

    Route::patch('/usuarios/{id}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');

});
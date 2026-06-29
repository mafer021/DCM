<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $fillable = ['producto_id', 'tipo_movimiento', 'cantidad', 'fecha', 'observacion', 'usuario_id'];

    // Relación con Producto
    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    // Relación con Usuario (el responsable)
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
}

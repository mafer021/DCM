<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'categoria_id', 'unidad_id', 'stock', 'precio', 'estado', 'estado_producto'];

    // Relación con Categoría
    public function categoria() {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    // Relación con Unidad
    public function unidad() {
        return $this->belongsTo(UnidadMedida::class, 'unidad_id');
    }

    // Relación con Movimientos
    public function movimientos() {
        return $this->hasMany(MovimientoInventario::class);
    }
}

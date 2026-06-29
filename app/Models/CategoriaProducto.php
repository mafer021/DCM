<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categorias_productos'; // Porque tu tabla no se llama 'categoria_productos'
    protected $fillable = ['nombre'];
}

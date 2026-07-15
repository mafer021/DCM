<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoProspecto extends Model
{
    use HasFactory;

    // Indicamos la tabla correcta
    protected $table = 'estados_prospecto';

    // Campos permitidos
    protected $fillable = ['nombre'];

    /**
     * Relación: Un estado puede estar asignado a muchos prospectos
     */
    public function prospectos()
    {
        return $this->hasMany(Prospecto::class, 'estado_prospecto_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInstalacion extends Model
{
    use HasFactory;

    // Le indicamos explícitamente el nombre de la tabla en la base de datos
    protected $table = 'tipos_instalacion';

    // Campos que se pueden llenar en masa
    protected $fillable = ['nombre'];

    /**
     * Relación: Un tipo de instalación puede tener muchos proyectos asociados.
     */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'tipo_instalacion_id');
    }

    /**
     * Relación: tiene muchos proyectos y muchos prospectos..
     */

    public function prospectos()
    {
        return $this->hasMany(Prospecto::class, 'tipo_instalacion_id');
    }
}

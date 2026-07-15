<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospecto extends Model
{
    use HasFactory;

    // Campos que permitimos llenar mediante formularios
    protected $fillable = [
        'nombre', 
        'apellido_paterno', 
        'apellido_materno', 
        'telefono', 
        'tipo_instalacion_id', 
        'estado_prospecto_id', 
        'dejo_documento', 
        'detalle_documento', 
        'notas', 
        'estado'
    ];

    /**
     * Relación: Un prospecto pertenece a un tipo de instalación
     */
    public function tipoInstalacion()
    {
        return $this->belongsTo(TipoInstalacion::class, 'tipo_instalacion_id');
    }

    /**
     * Relación: Un prospecto pertenece a un estado (Interesado, etc.)
     */
    public function estadoProspecto()
    {
        return $this->belongsTo(EstadoProspecto::class, 'estado_prospecto_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'proyectos';

    // Campos EXACTOS que tienes en tu migración create_proyectos_table
    protected $fillable = [
        'nombre',
        'cliente',
        'tipo_instalacion_id',
        'fecha_instalacion',
        'proximo_mantenimiento',
        'direccion',
        'descripcion',
        'estado',
    ];

    /**
     * Relación: Un proyecto pertenece a un Tipo de Instalación.
     */
    public function tipoInstalacion()
    {
        return $this->belongsTo(TipoInstalacion::class, 'tipo_instalacion_id');
    }

    

    /**
     * Relación: Un proyecto puede tener muchos Documentos guardados.
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'proyecto_id');
    }

    /**
     * Relación: Un proyecto puede tener muchos Mantenimientos programados (Ordenados por fecha reciente).
     */
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'proyecto_id')->orderBy('fecha_mantenimiento', 'desc');
    }

    
}

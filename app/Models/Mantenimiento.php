<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    // Nombre de la tabla exacto
    protected $table = 'mantenimientos';

    // Campos EXACTOS que dejamos en tu migración corregida
    protected $fillable = [
        'proyecto_id',
        'tecnico_id',         // <-- Tu campo real que apunta a users
        'fecha_mantenimiento',
        'observaciones',       // <-- Tu campo real para las notas
        'estado',
    ];

    /**
     * Relación: El mantenimiento pertenece a un Proyecto.
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    /**
     * Relación: El mantenimiento pertenece a un Usuario (Técnico de la tabla users).
     */
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}
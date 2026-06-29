<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla
    protected $table = 'tipos_documento';

    // Permiso para llenar el nombre (para el seeder y futuros cambios)
    protected $fillable = ['nombre'];

    /**
     * Relación: Un tipo de documento puede estar en muchos documentos de clientes.
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'tipo_documento_id');
    }
}

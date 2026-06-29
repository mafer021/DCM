<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'documentos';

    // Campos que el sistema permitirá registrar
    protected $fillable = [
        'proyecto_id',
    'tipo_documento_id',
    'nombre_archivo', // <-- Tu campo real
    'descripcion',
    'ruta_archivo',   // <-- Tu campo real
    'extension',
    'tamano',
    'fecha_subida',
    'usuario_id',
    ];

    /**
     * Relación: Un documento pertenece a un Proyecto.
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    /**
     * Relación: Un documento pertenece a un Tipo de Documento específico (catálogo).
     */
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    // NUEVO/CORREGIDO: Aquí sabemos qué usuario registró/subió el documento
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

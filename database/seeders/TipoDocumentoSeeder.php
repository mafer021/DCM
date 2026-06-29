<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            ['nombre' => 'Cotización'],
            ['nombre' => 'Recibo de luz'],
            ['nombre' => 'Documento de propiedad'],
            ['nombre' => 'INE'],
            ['nombre' => 'Carta poder'],
            ['nombre' => 'Contrato firmado'],
            ['nombre' => 'Órdenes de CFE'],
            ['nombre' => 'Evidencia fotográfica: Antes de la instalación'],
            ['nombre' => 'Evidencia fotográfica: Después de la instalación'],
        ];

        DB::table('tipos_documento')->insert($documentos);
    }
}

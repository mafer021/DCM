<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoInstalacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Sistemas fotovoltaicos'],
            ['nombre' => 'Calentadores solares'],
            ['nombre' => 'Paneles solares'],
            ['nombre' => 'Bombas solares'],
            ['nombre' => 'Biodigestores'],
            ['nombre' => 'Instalaciones eléctricas'],
        ];

        DB::table('tipos_instalacion')->insert($tipos);
    }
}

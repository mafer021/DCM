<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoProspectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Interesado'],
            ['nombre' => 'Solo pregunto'],
            ['nombre' => 'No interesado'],
        ];

        DB::table('estados_prospecto')->insert($estados);
    }
}

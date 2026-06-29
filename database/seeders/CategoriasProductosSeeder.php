<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categorias_productos')->insert([
        ['nombre' => 'Paneles solares'],
        ['nombre' => 'Calentadores solares'],
        ['nombre' => 'Inversores'],
        ['nombre' => 'Controladores de carga'],
        ['nombre' => 'Baterías'],
        ['nombre' => 'Estructuras'],
        ['nombre' => 'Sistema de bombeo'],
        ['nombre' => 'Variadores'],
        ['nombre' => 'Accesorios'],
        ['nombre' => 'Arrancadores'],
        ['nombre' => 'Tableros'],
        ['nombre' => 'Tanques precargados'],
        ['nombre' => 'Bombas sumergibles'],
        ['nombre' => 'Motobombas sumergibles'],
        ['nombre' => 'Microinversores'],
        ['nombre' => 'Presurizadores sin variador'],
        ['nombre' => 'Presurizadores con variador'],
    ]);
    }
}

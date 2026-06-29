<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([

            'nombre' => 'Admin',

            'apellido_paterno' => 'Soluciones',

            'apellido_materno' => 'Energeticas',

            'usuario_login' => 'admin',

            'email' => 'admin@soluciones.com',

            'password' => 'admin123',

            'rol' => 'admin',

            'estado' => 'activo'

        ]);
    }
}

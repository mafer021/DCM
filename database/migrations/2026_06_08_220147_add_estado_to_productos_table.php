<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Usamos enum para mantener la consistencia con tu módulo de usuarios
        // Por defecto, al crear el producto, estará 'activo'
        $table->enum('estado_producto', ['activo', 'inactivo'])->default('activo')->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('estado_producto');
        });
    }
};

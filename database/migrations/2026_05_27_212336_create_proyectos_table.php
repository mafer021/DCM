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
    Schema::create('proyectos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 150);
        $table->string('cliente', 150);
        // Esto crea la llave foránea que se conecta con la tabla tipos_instalacion
        $table->foreignId('tipo_instalacion_id')->constrained('tipos_instalacion');
        $table->date('fecha_instalacion');
        $table->date('proximo_mantenimiento');
        $table->text('direccion');
        $table->text('descripcion')->nullable(); // Con nullable permite que vaya vacío
        $table->enum('estado', ['al_dia', 'proximo', 'vencido'])->default('al_dia');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};

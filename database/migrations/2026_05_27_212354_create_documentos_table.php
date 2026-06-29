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
    Schema::create('documentos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
        $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
        $table->string('nombre_archivo', 255);
        $table->text('descripcion')->nullable();
        $table->string('ruta_archivo', 255);
        $table->string('extension', 20);
        $table->bigInteger('tamano'); // Guardará los bytes puros para validar los 10MB
        $table->date('fecha_subida');
        $table->foreignId('usuario_id')->constrained('users'); // Apunta directo a la tabla users de Laravel
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};

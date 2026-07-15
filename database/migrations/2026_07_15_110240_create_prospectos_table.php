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
        Schema::create('prospectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        $table->string('apellido_paterno');
        $table->string('apellido_materno')->nullable();
        $table->string('telefono');
        
        $table->foreignId('tipo_instalacion_id')->constrained('tipos_instalacion');
        $table->foreignId('estado_prospecto_id')->constrained('estados_prospecto');
        
        $table->boolean('dejo_documento')->default(false);
        $table->string('detalle_documento')->nullable();
        $table->text('notas')->nullable(); 
        
        // Ahora todo en minúsculas
        $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospectos');
    }
};

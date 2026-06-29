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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            
            // CORREGIDO: Un solo técnico, apuntando directo a la tabla 'users' como acordamos
            $table->foreignId('tecnico_id')->constrained('users');
            
            $table->date('fecha_mantenimiento');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['realizado', 'pendiente'])->default('realizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('cascade');
        $table->enum('tipo_movimiento', ['entrada', 'salida']);
        $table->integer('cantidad');
        $table->dateTime('fecha')->useCurrent();
        $table->text('observacion')->nullable();
        $table->foreignId('usuario_id')->constrained('users'); // Relación con tu tabla de usuarios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};

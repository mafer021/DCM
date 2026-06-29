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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        $table->foreignId('categoria_id')->constrained('categorias_productos');
        $table->foreignId('unidad_id')->constrained('unidades_medida');
        $table->integer('stock')->default(0);
        $table->decimal('precio', 10, 2);
        $table->enum('estado', ['disponible', 'stock_bajo', 'agotado'])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

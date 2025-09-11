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
        Schema::create('cat_etiquetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->string('color', 20)->default('gray'); // Para diferentes colores de etiquetas
            $table->boolean('activo')->default(true);
            $table->integer('veces_usada')->default(0); // Contador de uso
            $table->timestamps();
            
            $table->index(['activo', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_etiquetas');
    }
}; 
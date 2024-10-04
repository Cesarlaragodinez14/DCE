<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartadosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('apartados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // Referencia para el nodo padre
            $table->integer('nivel')->default(1); // Para gestionar los niveles del árbol
            $table->timestamps();

            // Relación de clave externa para apuntar al nodo padre
            $table->foreign('parent_id')->references('id')->on('apartados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('apartados');
    }
}

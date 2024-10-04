<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistApartadosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('checklist_apartados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apartado_id'); // Referencia al apartado
            $table->unsignedBigInteger('auditoria_id'); // Referencia a la auditoría
            $table->boolean('se_aplica')->nullable();
            $table->boolean('es_obligatorio')->default(false);
            $table->boolean('se_integra')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('comentarios_uaa')->nullable();
            $table->timestamps();

            // Relación con la tabla de apartados y auditorías
            $table->foreign('apartado_id')->references('id')->on('apartados')->onDelete('cascade');
            $table->foreign('auditoria_id')->references('id')->on('aditorias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('checklist_apartados');
    }
}

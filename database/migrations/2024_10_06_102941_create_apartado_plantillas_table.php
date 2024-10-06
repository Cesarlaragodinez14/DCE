<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartadoPlantillasTable extends Migration
{
    public function up()
    {
        Schema::create('apartado_plantillas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartado_id')->constrained('apartados')->onDelete('cascade');
            $table->string('plantilla'); // Por ejemplo, 01, 03, 06, 07
            $table->boolean('es_aplicable')->default(false);
            $table->boolean('es_obligatorio')->default(false);
            $table->boolean('se_integra')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartado_plantillas');
    }
}

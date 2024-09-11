<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecepcionEntregasTable extends Migration
{
    public function up()
    {
        Schema::create('recepcion_entregas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrega_id');
            $table->string('nombre_servidor_uaa'); // Campo para el servidor UAA que entrega
            $table->string('puesto_servidor_uaa'); // Campo para el puesto del servidor UAA
            $table->string('firma_servidor_uaa');  // Campo para la firma del servidor UAA
            $table->string('nombre_servidor_dce'); // Campo para el servidor DCE que recibe
            $table->string('puesto_servidor_dce'); // Campo para el puesto del servidor DCE
            $table->string('firma_servidor_dce');  // Campo para la firma del servidor DCE
            $table->timestamps();

            // Relaciones
            $table->foreign('entrega_id')->references('id')->on('entregas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('recepcion_entregas');
    }
}

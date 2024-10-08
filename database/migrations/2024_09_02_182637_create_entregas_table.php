<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntregasTable extends Migration
{
    public function up()
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auditoria_id');
            $table->string('clave_accion'); // Clave de acción
            $table->string('tipo_accion');  // Tipo de acción
            $table->string('CP');           // Cuenta pública
            $table->string('entrega');      // Entrega
            $table->date('fecha_entrega');
            $table->string('responsable');
            $table->integer('numero_legajos');
            $table->unsignedBigInteger('confirmado_por'); // ID del usuario que confirmó la entrega
            $table->timestamps();

            // Relaciones
            $table->foreign('auditoria_id')->references('id')->on('aditorias')->onDelete('cascade');
            $table->foreign('confirmado_por')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entregas');
    }
}

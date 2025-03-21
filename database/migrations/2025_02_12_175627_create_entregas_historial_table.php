<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('entregas_historial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entrega_id');
            $table->string('estado', 255);
            $table->timestamp('fecha_estado')->default(now());
            $table->string('pdf_path')->nullable(); // Guarda la ruta del PDF generado
            $table->timestamps();

            // Claves foráneas
            $table->foreign('entrega_id')->references('id')->on('entregas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entregas_historial');
    }
};

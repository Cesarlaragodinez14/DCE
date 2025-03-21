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
        Schema::create('entregas_pdf_histories', function (Blueprint $table) {
            $table->id();
            // Referencia a la tabla 'entregas'
            $table->unsignedBigInteger('entrega_id');
            
            // Si aún necesitas la clave de acción u otro campo, lo dejas
            $table->string('hash')->nullable();

            // Ruta del PDF
            $table->string('pdf_path');

            // ID del usuario que generó el PDF
            $table->unsignedBigInteger('generated_by'); 
            
            $table->timestamps();

            // Clave foránea a la tabla 'entregas'
            $table->foreign('entrega_id')
                  ->references('id')->on('entregas')
                  ->onDelete('cascade');

            // Clave foránea a la tabla 'users'
            $table->foreign('generated_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas_pdf_histories');
    }
};

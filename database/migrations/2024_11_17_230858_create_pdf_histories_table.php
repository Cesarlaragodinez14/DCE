<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auditoria_id');
            $table->string('clave_de_accion');
            $table->string('pdf_path');
            $table->unsignedBigInteger('generated_by'); // ID del usuario que generó el PDF
            $table->timestamps();

            // Clave foránea a la tabla 'auditorias'
            $table->foreign('auditoria_id')->references('id')->on('aditorias')->onDelete('cascade');

            // Clave foránea a la tabla 'users'
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_histories');
    }
}

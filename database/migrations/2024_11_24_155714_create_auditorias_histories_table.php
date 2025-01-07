<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditoriasHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auditorias_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auditoria_id');
            $table->unsignedBigInteger('changed_by'); // ID del usuario que realizó el cambio
            $table->json('changes'); // JSON con los cambios realizados
            $table->timestamps();

            // Clave foránea a la tabla 'auditorias'
            $table->foreign('auditoria_id')->references('id')->on('aditorias')->onDelete('cascade');

            // Clave foránea a la tabla 'users'
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias_histories');
    }
}

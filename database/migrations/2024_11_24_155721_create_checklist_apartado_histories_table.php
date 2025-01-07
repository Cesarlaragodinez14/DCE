<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistApartadoHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checklist_apartado_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_apartado_id');
            $table->unsignedBigInteger('changed_by'); // ID del usuario que realizó el cambio
            $table->json('changes'); // JSON con los cambios realizados
            $table->timestamps();

            // Clave foránea a la tabla 'checklist_apartados'
            $table->foreign('checklist_apartado_id')->references('id')->on('checklist_apartados')->onDelete('cascade');

            // Clave foránea a la tabla 'users'
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_apartado_histories');
    }
}

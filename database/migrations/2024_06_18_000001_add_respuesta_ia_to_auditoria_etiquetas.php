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
        Schema::table('auditoria_etiquetas', function (Blueprint $table) {
            // Agregar campo para almacenar la respuesta completa de la IA
            $table->text('respuesta_ia')->nullable()->after('comentario_fuente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_etiquetas', function (Blueprint $table) {
            $table->dropColumn('respuesta_ia');
        });
    }
}; 
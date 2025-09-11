<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar registros existentes que tengan checklist_apartado_id NULL
        // (Este es un caso especial si hay registros existentes)
        DB::statement('UPDATE auditoria_etiquetas SET checklist_apartado_id = 1 WHERE checklist_apartado_id IS NULL');
        
        Schema::table('auditoria_etiquetas', function (Blueprint $table) {
            // Hacer checklist_apartado_id obligatorio
            $table->unsignedBigInteger('checklist_apartado_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_etiquetas', function (Blueprint $table) {
            // Revertir a nullable
            $table->unsignedBigInteger('checklist_apartado_id')->nullable()->change();
        });
    }
};

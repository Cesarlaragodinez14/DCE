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
        Schema::table('auditoria_etiquetas', function (Blueprint $table) {
            // Agregar nueva columna apartado_id
            $table->foreignId('apartado_id')->nullable()->after('etiqueta_id')
                  ->constrained('apartados')->onDelete('cascade');
            
            // Hacer checklist_apartado_id nullable para transición
            $table->foreignId('checklist_apartado_id')->nullable()->change();
            
            // Actualizar el índice único
            $table->dropIndex('unique_auditoria_etiqueta_apartado');
            $table->unique(['auditoria_id', 'etiqueta_id', 'apartado_id'], 'unique_auditoria_etiqueta_apartado_nuevo');
        });
        
        // Migrar datos existentes de checklist_apartado_id a apartado_id
        DB::statement("
            UPDATE auditoria_etiquetas ae 
            JOIN checklist_apartados ca ON ae.checklist_apartado_id = ca.id 
            SET ae.apartado_id = ca.apartado_id 
            WHERE ae.apartado_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_etiquetas', function (Blueprint $table) {
            // Remover nuevo índice
            $table->dropIndex('unique_auditoria_etiqueta_apartado_nuevo');
            
            // Restaurar índice original
            $table->unique(['auditoria_id', 'etiqueta_id', 'checklist_apartado_id'], 'unique_auditoria_etiqueta_apartado');
            
            // Remover nueva columna
            $table->dropForeign(['apartado_id']);
            $table->dropColumn('apartado_id');
            
            // Restaurar checklist_apartado_id como no nullable
            $table->foreignId('checklist_apartado_id')->nullable(false)->change();
        });
    }
}; 
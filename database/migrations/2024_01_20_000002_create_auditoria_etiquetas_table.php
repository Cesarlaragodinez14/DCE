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
        Schema::create('auditoria_etiquetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditoria_id')->constrained('aditorias')->onDelete('cascade');
            $table->foreignId('etiqueta_id')->constrained('cat_etiquetas')->onDelete('cascade');
            $table->foreignId('checklist_apartado_id')->nullable()->constrained('checklist_apartados')->onDelete('cascade');
            $table->text('razon_asignacion'); // Razón por la que se asignó esta etiqueta
            $table->text('comentario_fuente')->nullable(); // El comentario que generó la etiqueta
            $table->decimal('confianza_ia', 3, 2)->default(0.00); // Score de confianza de la IA (0.00-1.00)
            $table->boolean('validado_manualmente')->default(false);
            $table->foreignId('procesado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('procesado_en');
            $table->timestamps();
            
            // Evitar duplicados de la misma etiqueta para la misma auditoría y apartado
            $table->unique(['auditoria_id', 'etiqueta_id', 'checklist_apartado_id'], 'unique_auditoria_etiqueta_apartado');
            
            $table->index(['auditoria_id', 'etiqueta_id']);
            $table->index(['procesado_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_etiquetas');
    }
}; 
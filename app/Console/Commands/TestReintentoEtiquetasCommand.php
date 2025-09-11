<?php

namespace App\Console\Commands;

use App\Jobs\ProcesarApartadoIndividualJob;
use App\Models\Auditorias;
use App\Models\Apartado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestReintentoEtiquetasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etiquetas:test-reintento 
                            {--auditoria-id= : ID de auditoría para probar}
                            {--apartado-id= : ID de apartado para probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el sistema de reintento de etiquetas con manejo de duplicados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auditoriaId = $this->option('auditoria-id');
        $apartadoId = $this->option('apartado-id');

        if (!$auditoriaId || !$apartadoId) {
            $this->error('❌ Debes proporcionar tanto --auditoria-id como --apartado-id');
            return 1;
        }

        $this->info("🧪 Iniciando prueba de reintento para auditoría {$auditoriaId}, apartado {$apartadoId}");

        // Verificar que existen
        $auditoria = Auditorias::find($auditoriaId);
        if (!$auditoria) {
            $this->error("❌ Auditoría {$auditoriaId} no encontrada");
            return 1;
        }

        $apartado = Apartado::find($apartadoId);
        if (!$apartado) {
            $this->error("❌ Apartado {$apartadoId} no encontrado");
            return 1;
        }

        $this->info("✅ Auditoría encontrada: {$auditoria->clave_de_accion}");
        $this->info("✅ Apartado encontrado: {$apartado->nombre}");

        // Crear un job de prueba
        $this->info("🚀 Despachando job de prueba...");
        
        ProcesarApartadoIndividualJob::dispatch(
            $auditoriaId,
            $apartadoId,
            'Documentación faltante', // Etiqueta de prueba
            'Respuesta de prueba de la IA',
            'Razón de prueba: Test de sistema de reintento',
            'Comentario de prueba: Sistema funcionando correctamente',
            1, // Usuario ID 1
            0  // Sin intentos previos
        );

        $this->info("✅ Job despachado exitosamente");
        $this->info("💡 Monitorear logs con: tail -f storage/logs/laravel.log");
        $this->info("🔄 Procesar jobs con: php artisan queue:work --once");

        return 0;
    }
} 
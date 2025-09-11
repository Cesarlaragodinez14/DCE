<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\AIController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use ReflectionClass;

class ProbarContextoIA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:probar-contexto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el contexto específico que se envía a la IA sin filtros';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Autenticar temporalmente un usuario para las pruebas
        $testUser = User::first();
        if (!$testUser) {
            $this->error('❌ No se encontró ningún usuario en el sistema para las pruebas');
            return Command::FAILURE;
        }
        
        Auth::login($testUser);
        
        $this->info('🤖 PRUEBA DIRECTA DEL CONTEXTO ENVIADO A LA IA');
        $this->line(str_repeat('=', 50));
        $this->line("🔑 Usuario: {$testUser->name}");
        $this->line("📨 Pregunta simulada: '¿Cuántos expedientes tienes?'");
        $this->line("🎯 Filtros: NINGUNO (array vacío)");
        $this->newLine();

        try {
            // Crear instancia del controlador AI
            $aiController = new AIController();
            
            // Simular exactamente los filtros que llegan sin filtros aplicados
            $filters = []; // Array vacío, como viene desde el frontend
            
            // Usar reflexión para acceder al método privado
            $reflection = new ReflectionClass($aiController);
            $method = $reflection->getMethod('getSystemPrompt');
            $method->setAccessible(true);
            
            $this->line('🧠 Generando contexto (includeContext=true, filters=[])...');
            $systemPrompt = $method->invoke($aiController, true, $filters);
            
            $this->line('📏 Longitud del contexto: ' . strlen($systemPrompt) . ' caracteres');
            $this->newLine();
            
            // Análisis del contexto
            $this->info('📋 ANÁLISIS DEL CONTEXTO GENERADO:');
            $this->line(str_repeat('-', 40));
            
            // Verificar si es contexto dinámico o estático
            if (strpos($systemPrompt, 'CONTEXTO ACTUAL DEL SISTEMA') !== false) {
                $this->line('✅ CONTEXTO DINÁMICO detectado');
            } else {
                $this->error('❌ CONTEXTO ESTÁTICO (problema persiste)');
            }
            
            // Buscar datos de expedientes
            if (preg_match('/Total de expedientes:\s*(\d+)/', $systemPrompt, $matches)) {
                $totalExpedientes = $matches[1];
                $this->line("✅ EXPEDIENTES en contexto: {$totalExpedientes}");
                
                if ($totalExpedientes > 3000) {
                    $this->line('✅ Cantidad correcta (>3000)');
                } else {
                    $this->error('❌ Cantidad parece incorrecta (<3000)');
                }
            } else {
                $this->error('❌ NO se encontraron datos de expedientes');
            }
            
            // Verificar fecha actualizada
            $fechaHoy = now()->format('d/m/Y');
            if (strpos($systemPrompt, $fechaHoy) !== false) {
                $this->line("✅ FECHA ACTUALIZADA: {$fechaHoy}");
            } else {
                $this->error('❌ Fecha no actualizada');
            }
            
            // Verificar que contenga distribución por estatus
            if (strpos($systemPrompt, 'EXPEDIENTES POR ESTATUS') !== false) {
                $this->line('✅ DISTRIBUCIÓN POR ESTATUS incluida');
            } else {
                $this->error('❌ Distribución por estatus no incluida');
            }
            
            $this->newLine();
            $this->info('👀 PREVIEW DEL CONTEXTO COMPLETO:');
            $this->line(str_repeat('-', 60));
            
            // Mostrar el contexto completo
            $lines = explode("\n", $systemPrompt);
            foreach ($lines as $line) {
                $this->line($line);
            }
            
            $this->line(str_repeat('-', 60));
            
        } catch (\Exception $e) {
            $this->error('❌ ERROR: ' . $e->getMessage());
            $this->line('📍 Archivo: ' . $e->getFile() . ':' . $e->getLine());
        }
        
        // Cerrar sesión de prueba
        Auth::logout();
        
        $this->newLine();
        $this->info('🎯 CONCLUSIÓN:');
        $this->line('   Si el contexto muestra "CONTEXTO ACTUAL DEL SISTEMA"');
        $this->line('   y contiene >3000 expedientes, la corrección funcionó.');
        $this->line('   La IA ahora debería responder con datos correctos.');
        
        return Command::SUCCESS;
    }
} 
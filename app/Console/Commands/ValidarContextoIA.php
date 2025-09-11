<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\AIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardEntregasController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use ReflectionClass;

class ValidarContextoIA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:validar-contexto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida que el contexto dinámico de la IA esté funcionando correctamente';

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
        $this->line("🔑 Usuario de prueba autenticado: {$testUser->name}");
        
        $this->info('🔍 VALIDACIÓN DEL CONTEXTO DINÁMICO - SISTEMA SAES-AI');
        $this->line(str_repeat('=', 60));
        $this->newLine();

        // Configurar diferentes escenarios de filtros para probar
        $testCases = [
            'sin_filtros' => [],
            'solo_entrega' => ['entrega' => 18],
            'entrega_y_cuenta' => ['entrega' => 18, 'cuenta_publica' => 1],
            'todos_los_filtros' => ['entrega' => 18, 'cuenta_publica' => 1, 'uaa_id' => 1, 'dg_id' => 1]
        ];

        foreach ($testCases as $caseName => $filters) {
            $this->info('📋 CASO DE PRUEBA: ' . strtoupper(str_replace('_', ' ', $caseName)));
            $this->line(str_repeat('-', 40));
            
            // Mostrar filtros aplicados
            if (empty($filters)) {
                $this->line('   Filtros: Ninguno');
            } else {
                $this->line('   Filtros aplicados:');
                foreach ($filters as $key => $value) {
                    $this->line("   - $key: $value");
                }
            }
            $this->newLine();
            
            try {
                // Crear request con filtros
                $request = new Request();
                if (!empty($filters)) {
                    $request->merge($filters);
                }
                
                // 1. Probar datos de Dashboard
                $this->line('   🔸 Probando datos de Dashboard...');
                $dashboardController = new DashboardController();
                $dashboardData = $dashboardController->getDashboardData($request);
                
                if ($dashboardData && isset($dashboardData['success']) && $dashboardData['success']) {
                    $this->line('   ✅ Dashboard: Datos obtenidos correctamente');
                    
                    // Verificar estructura de datos
                    $data = $dashboardData['data'];
                    
                    if (isset($data['expedientes_por_estatus'])) {
                        $totalExpedientes = collect($data['expedientes_por_estatus'])->sum('total');
                        $this->line("   📊 Total expedientes encontrados: $totalExpedientes");
                        
                        // Mostrar distribución por estatus
                        foreach ($data['expedientes_por_estatus'] as $estatus) {
                            $estatusArray = $this->objectToArray($estatus);
                            $nombre = $estatusArray['estatus_checklist'] ?? 'Sin nombre';
                            $total = $estatusArray['total'] ?? 0;
                            $this->line("      - $nombre: $total expedientes");
                        }
                    }
                    
                    if (isset($data['expedientes_por_ente_fiscalizado'])) {
                        $totalEntes = count($data['expedientes_por_ente_fiscalizado']);
                        $this->line("   🏢 Entes fiscalizados encontrados: $totalEntes");
                        
                        // Mostrar top 3 entes
                        $topEntes = collect($data['expedientes_por_ente_fiscalizado'])->take(3);
                        foreach ($topEntes as $index => $ente) {
                            $enteArray = $this->objectToArray($ente);
                            $nombre = $enteArray['cat_ente_fiscalizado']['valor'] ?? 'Sin nombre';
                            $total = $enteArray['total'] ?? 0;
                            $this->line('      ' . ($index + 1) . ". $nombre: $total expedientes");
                        }
                    }
                    
                } else {
                    $this->error('   ❌ Dashboard: Error obteniendo datos');
                    if (isset($dashboardData['error'])) {
                        $this->line('      Error: ' . $dashboardData['error']);
                    }
                }
                
                // 2. Probar datos de Entregas
                $this->line('   🔸 Probando datos de Entregas...');
                $entregasController = new DashboardEntregasController();
                $entregasData = $entregasController->getDashboardData($request);
                
                if ($entregasData && isset($entregasData['success']) && $entregasData['success']) {
                    $this->line('   ✅ Entregas: Datos obtenidos correctamente');
                    
                    $data = $entregasData['data'];
                    if (isset($data['delivery_status'])) {
                        foreach ($data['delivery_status'] as $status) {
                            $statusArray = $this->objectToArray($status);
                            $entregados = $statusArray['delivered'] ?? 0;
                            $enProceso = $statusArray['in_process'] ?? 0;
                            $sinProgramar = $statusArray['unscheduled'] ?? 0;
                            $total = $entregados + $enProceso + $sinProgramar;
                            
                            $this->line("   📦 Estado de entregas (Total: $total):");
                            $this->line("      - Entregados: $entregados");
                            $this->line("      - En proceso: $enProceso");
                            $this->line("      - Sin programar: $sinProgramar");
                            break; // Solo mostrar el primer resumen
                        }
                    }
                } else {
                    $this->error('   ❌ Entregas: Error obteniendo datos');
                    if (isset($entregasData['error'])) {
                        $this->line('      Error: ' . $entregasData['error']);
                    }
                }
                
                // 3. Probar generación de contexto completo
                $this->line('   🔸 Probando generación de contexto...');
                
                // Usar reflexión para acceder al método privado
                $aiController = new AIController();
                $reflection = new ReflectionClass($aiController);
                $method = $reflection->getMethod('generateDynamicContext');
                $method->setAccessible(true);
                
                $contexto = $method->invoke($aiController, $filters);
                
                if ($contexto && strlen($contexto) > 50) {
                    $this->line('   ✅ Contexto: Generado correctamente');
                    $this->line('   📏 Longitud del contexto: ' . strlen($contexto) . ' caracteres');
                    
                    // Mostrar preview del contexto (primeras 200 caracteres)
                    $preview = substr($contexto, 0, 200);
                    $this->line('   👀 Preview del contexto:');
                    $this->line('      ' . str_replace("\n", "\n      ", $preview) . '...');
                } else {
                    $this->error('   ❌ Contexto: Error generando o contexto vacío');
                }
                
            } catch (\Exception $e) {
                $this->error('   ❌ ERROR: ' . $e->getMessage());
                $this->line('   📍 Archivo: ' . $e->getFile() . ':' . $e->getLine());
            }
            
            $this->newLine();
            $this->line(str_repeat('-', 50));
            $this->newLine();
        }

        // Prueba adicional: Verificar catálogos
        $this->info('📚 VERIFICACIÓN DE CATÁLOGOS');
        $this->line(str_repeat('=', 30));

        try {
            $entregasController = new DashboardEntregasController();
            $catalogos = $entregasController->getCatalogos();
            
            $catalogosTypes = ['entregas', 'cuentasPublicas', 'uaas', 'dgsegs'];
            
            foreach ($catalogosTypes as $tipo) {
                if (isset($catalogos[$tipo])) {
                    $count = count($catalogos[$tipo]);
                    $this->line("✅ $tipo: $count elementos");
                    
                    // Mostrar algunos ejemplos
                    if ($count > 0) {
                        $ejemplos = collect($catalogos[$tipo])->take(3);
                        foreach ($ejemplos as $item) {
                            $nombre = $item->nombre ?? $item->valor ?? 'Sin nombre';
                            $this->line("   - ID {$item->id}: $nombre");
                        }
                    }
                } else {
                    $this->error("❌ $tipo: No encontrado");
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Error obteniendo catálogos: ' . $e->getMessage());
        }

        $this->newLine();
        $this->line(str_repeat('=', 60));
        $this->info('🎯 RESUMEN DE VALIDACIÓN COMPLETADO');
        $this->line('   Si todos los elementos muestran ✅, el contexto dinámico funciona correctamente.');
        $this->line('   Si hay ❌, revisa los errores específicos mostrados arriba.');
        $this->line(str_repeat('=', 60));

        // Cerrar sesión de prueba
        Auth::logout();

        return Command::SUCCESS;
    }

    /**
     * Convierte un objeto a array de forma segura
     * 
     * @param mixed $item El item a convertir
     * @return array
     */
    private function objectToArray($item)
    {
        if (is_array($item)) {
            return $item;
        }
        
        if (is_object($item)) {
            // Si es un modelo de Eloquent, usar toArray()
            if (method_exists($item, 'toArray')) {
                return $item->toArray();
            }
            
            // Si es stdClass u otro objeto, convertir a array
            return json_decode(json_encode($item), true);
        }
        
        // Si no es objeto ni array, devolver como array con el valor
        return ['value' => $item];
    }
} 
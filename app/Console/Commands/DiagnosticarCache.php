<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DiagnosticarCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:diagnosticar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnosticar la configuración y funcionamiento del sistema de caché';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Iniciando diagnóstico del sistema de caché...');
        $this->line('');

        // 1. Verificar configuración básica
        $this->checkBasicConfig();
        
        // 2. Verificar driver específico
        $this->checkCacheDriver();
        
        // 3. Probar funcionalidad básica
        $this->testBasicFunctionality();
        
        // 4. Verificar estado específico de ResumenAuditorias
        $this->checkResumenAuditoriasCache();
        
        $this->line('');
        $this->info('✅ Diagnóstico completado. Revisa los logs para más detalles.');
        
        return 0;
    }
    
    private function checkBasicConfig()
    {
        $this->info('📋 1. Verificando configuración básica...');
        
        $driver = config('cache.default');
        $this->line("   Driver por defecto: {$driver}");
        
        $prefix = config('cache.prefix');
        $this->line("   Prefijo de caché: {$prefix}");
        
        // Verificar stores disponibles
        $stores = config('cache.stores');
        $this->line("   Stores configurados: " . implode(', ', array_keys($stores)));
        
        $this->line('');
    }
    
    private function checkCacheDriver()
    {
        $this->info('🔧 2. Verificando driver específico...');
        
        $driver = config('cache.default');
        
        switch ($driver) {
            case 'database':
                $this->checkDatabaseCache();
                break;
            case 'redis':
                $this->checkRedisCache();
                break;
            case 'file':
                $this->checkFileCache();
                break;
            case 'array':
                $this->warn("   ⚠️  Driver 'array' no persiste entre requests!");
                break;
            default:
                $this->line("   Driver: {$driver}");
                break;
        }
        
        $this->line('');
    }
    
    private function checkDatabaseCache()
    {
        $this->line('   🗄️ Verificando caché de base de datos...');
        
        $table = config('cache.stores.database.table', 'cache');
        $connection = config('cache.stores.database.connection') ?: config('database.default');
        
        $this->line("   Tabla: {$table}");
        $this->line("   Conexión: {$connection}");
        
        try {
            // Verificar si la tabla existe
            if (Schema::connection($connection)->hasTable($table)) {
                $this->line("   ✅ Tabla '{$table}' existe");
                
                // Contar registros
                $count = DB::connection($connection)->table($table)->count();
                $this->line("   📊 Registros en caché: {$count}");
                
                // Verificar registros de ResumenAuditorias
                $resumenCount = DB::connection($connection)
                    ->table($table)
                    ->where('key', 'like', '%resumen_auditorias_stats_%')
                    ->count();
                $this->line("   📈 Registros de ResumenAuditorias: {$resumenCount}");
                
            } else {
                $this->error("   ❌ Tabla '{$table}' NO existe!");
                $this->warn("   💡 Ejecuta: php artisan cache:table && php artisan migrate");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error verificando tabla: {$e->getMessage()}");
        }
    }
    
    private function checkRedisCache()
    {
        $this->line('   🔴 Verificando caché de Redis...');
        
        try {
            $redis = Cache::getRedis();
            $this->line('   ✅ Conexión a Redis exitosa');
            
            // Obtener info básica
            $info = $redis->info();
            if (isset($info['redis_version'])) {
                $this->line("   📊 Versión Redis: {$info['redis_version']}");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error conectando a Redis: {$e->getMessage()}");
        }
    }
    
    private function checkFileCache()
    {
        $this->line('   📁 Verificando caché de archivos...');
        
        $path = config('cache.stores.file.path');
        $this->line("   Ruta: {$path}");
        
        if (is_dir($path) && is_writable($path)) {
            $this->line('   ✅ Directorio existe y es escribible');
            
            // Contar archivos
            $files = glob($path . '/*');
            $count = count($files);
            $this->line("   📊 Archivos de caché: {$count}");
            
        } else {
            $this->error('   ❌ Directorio no existe o no es escribible');
        }
    }
    
    private function testBasicFunctionality()
    {
        $this->info('🧪 3. Probando funcionalidad básica...');
        
        $testKey = 'test_cache_' . time();
        $testValue = ['timestamp' => time(), 'random' => rand(1000, 9999)];
        
        try {
            // Guardar en caché
            Cache::put($testKey, $testValue, 60);
            $this->line('   ✅ Cache::put() exitoso');
            
            // Verificar si existe
            if (Cache::has($testKey)) {
                $this->line('   ✅ Cache::has() exitoso');
                
                // Recuperar valor
                $retrieved = Cache::get($testKey);
                if ($retrieved === $testValue) {
                    $this->line('   ✅ Cache::get() exitoso - datos íntegros');
                } else {
                    $this->error('   ❌ Cache::get() - datos corruptos');
                    $this->line('   Esperado: ' . json_encode($testValue));
                    $this->line('   Obtenido: ' . json_encode($retrieved));
                }
                
                // Limpiar prueba
                Cache::forget($testKey);
                $this->line('   ✅ Cache::forget() exitoso');
                
            } else {
                $this->error('   ❌ Cache::has() falló - el valor no se guardó');
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error en prueba básica: {$e->getMessage()}");
        }
        
        $this->line('');
    }
    
    private function checkResumenAuditoriasCache()
    {
        $this->info('📊 4. Verificando caché específico de ResumenAuditorias...');
        
        try {
            // Buscar claves de ResumenAuditorias existentes
            $cacheKeys = Cache::get('resumen_auditorias_cache_keys', []);
            $this->line("   📝 Claves registradas: " . count($cacheKeys));
            
            if (!empty($cacheKeys)) {
                foreach ($cacheKeys as $index => $key) {
                    $exists = Cache::has($key);
                    $status = $exists ? '✅' : '❌';
                    $this->line("   {$status} Clave #{$index}: {$key}");
                    
                    if ($exists) {
                        $data = Cache::get($key);
                        if (is_array($data) && isset($data['totalAuditorias'])) {
                            $this->line("       📊 Total auditorías en caché: {$data['totalAuditorias']}");
                        } else {
                            $this->warn("       ⚠️  Datos de caché corruptos");
                        }
                    }
                }
            } else {
                $this->line('   📝 No hay claves de ResumenAuditorias registradas');
            }
            
            // Verificar patrón general
            if (config('cache.default') === 'database') {
                $table = config('cache.stores.database.table', 'cache');
                $connection = config('cache.stores.database.connection') ?: config('database.default');
                
                $patternCount = DB::connection($connection)
                    ->table($table)
                    ->where('key', 'like', '%resumen_auditorias_stats_%')
                    ->count();
                    
                $this->line("   🔍 Claves por patrón en DB: {$patternCount}");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error verificando ResumenAuditorias: {$e->getMessage()}");
        }
        
        $this->line('');
    }
} 
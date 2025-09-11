<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConfigurarCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:configurar {--force : Forzar reconfiguración}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurar automáticamente el sistema de caché para el proyecto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('⚙️ Configurando sistema de caché...');
        $this->line('');

        $driver = config('cache.default');
        $this->line("Driver actual: {$driver}");
        
        if ($driver === 'database') {
            $this->configurarCacheBaseDatos();
        } elseif ($driver === 'array') {
            $this->warn('⚠️ El driver "array" no persiste entre requests!');
            if ($this->confirm('¿Quieres cambiar a caché de base de datos?')) {
                $this->configurarCacheBaseDatos();
            }
        } else {
            $this->line("✅ Driver {$driver} no requiere configuración adicional.");
        }
        
        $this->line('');
        $this->info('🧪 Probando configuración...');
        Artisan::call('cache:diagnosticar');
        $this->line(Artisan::output());
        
        $this->info('✅ Configuración completada!');
        
        return 0;
    }
    
    private function configurarCacheBaseDatos()
    {
        $this->info('🗄️ Configurando caché de base de datos...');
        
        $table = config('cache.stores.database.table', 'cache');
        $connection = config('cache.stores.database.connection') ?: config('database.default');
        
        try {
            // Verificar si la tabla existe
            if (!Schema::connection($connection)->hasTable($table)) {
                $this->line("📝 Creando tabla '{$table}'...");
                
                // Crear la migración de caché
                Artisan::call('cache:table');
                $this->line('✅ Migración de caché creada');
                
                // Ejecutar la migración
                Artisan::call('migrate', ['--force' => true]);
                $this->line('✅ Migración ejecutada');
                
            } else {
                $this->line("✅ Tabla '{$table}' ya existe");
            }
            
            // Verificar que la tabla funcione
            $this->verificarTablaCache($table, $connection);
            
        } catch (\Exception $e) {
            $this->error("❌ Error configurando caché de BD: {$e->getMessage()}");
            return false;
        }
        
        return true;
    }
    
    private function verificarTablaCache($table, $connection)
    {
        try {
            // Probar insertar un registro de prueba
            DB::connection($connection)->table($table)->updateOrInsert(
                ['key' => 'test_config_cache'],
                [
                    'value' => base64_encode(serialize(['test' => true, 'timestamp' => time()])),
                    'expiration' => time() + 3600
                ]
            );
            
            $this->line('✅ Tabla de caché funcional');
            
            // Limpiar registro de prueba
            DB::connection($connection)->table($table)->where('key', 'test_config_cache')->delete();
            
        } catch (\Exception $e) {
            $this->error("❌ Error probando tabla de caché: {$e->getMessage()}");
            throw $e;
        }
    }
} 
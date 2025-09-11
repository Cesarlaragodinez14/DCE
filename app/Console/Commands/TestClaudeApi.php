<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestClaudeApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:claude-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la configuración de la API de Claude';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Probando configuración de Claude API...');
        
        // Verificar variables de entorno
        $apiKey = trim(env('CLAUDE_API'));
        $model = env('CLAUDE_MODEL', 'claude-3-5-haiku-20241022');
        
        // Clean API key of any whitespace/line breaks
        $apiKey = preg_replace('/\s+/', '', $apiKey);
        
        $this->info("API Key configurada: " . (!empty($apiKey) ? '✅ SÍ' : '❌ NO'));
        $this->info("Longitud de API Key: " . strlen($apiKey ?? ''));
        $this->info("Modelo configurado: " . $model);
        $this->info("API Key (primeros 10 chars): " . substr($apiKey ?? '', 0, 10) . '...');
        $this->info("API Key (últimos 5 chars): ..." . substr($apiKey ?? '', -5));
        
        // Verificaciones adicionales
        $this->info("API Key contiene espacios: " . (preg_match('/\s/', $apiKey ?? '') ? '❌ SÍ' : '✅ NO'));
        $this->info("API Key empieza con sk-ant-: " . (str_starts_with($apiKey ?? '', 'sk-ant-') ? '✅ SÍ' : '❌ NO'));
        
        // Verificar variables específicas
        $this->info("CLAUDE_API desde env(): " . (!empty(env('CLAUDE_API')) ? '✅ SET' : '❌ NO SET'));
        $this->info("CLAUDE_MODEL desde env(): " . (env('CLAUDE_MODEL') ?? 'NO SET'));
        
        if (empty($apiKey)) {
            $this->error('❌ La variable CLAUDE_API no está configurada en el archivo .env');
            return 1;
        }
        
        // Probar conexión con Claude
        $this->info('🔄 Enviando request de prueba a Claude...');
        
        $endpoint = 'https://api.anthropic.com/v1/messages';
        
        $requestBody = [
            'model' => $model,
            'max_tokens' => 100,
            'temperature' => 0.7,
            'system' => 'Eres un asistente útil.',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Di "Hola, conexión exitosa" en una línea'
                ]
            ]
        ];
        
        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->timeout(60)->post($endpoint, $requestBody);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['content'][0]['text'])) {
                    $this->info('✅ Conexión exitosa con Claude!');
                    $this->info('📝 Respuesta: ' . $responseData['content'][0]['text']);
                    return 0;
                } else {
                    $this->error('❌ Formato de respuesta inesperado');
                    $this->error('Respuesta completa: ' . json_encode($responseData, JSON_PRETTY_PRINT));
                    return 1;
                }
            } else {
                $statusCode = $response->status();
                $errorBody = $response->body();
                
                $this->error("❌ Error en la API de Claude (código $statusCode):");
                $this->error($errorBody);
                
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Excepción al conectar con Claude: ' . $e->getMessage());
            return 1;
        }
    }
} 
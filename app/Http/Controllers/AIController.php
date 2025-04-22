<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    /**
     * Muestra la vista principal del Asistente IA.
     */
    public function index()
    {
        return view('dashboard.ai.index');
    }

    /**
     * Recibe el mensaje del usuario y responde usando la API de Anthropic.
     */
    public function sendMessage(Request $request)
    {
        // Validar que se reciba un texto
        $data = $request->validate([
            'message' => 'required|string|min:1',
        ]);

        $userMessage = $data['message'];

        try {
            // Verificar que existen las variables de entorno necesarias
            if (empty(env('CLAUDE_API'))) {
                throw new \Exception('La clave API de Claude no está configurada en el archivo .env');
            }

            // Obtener la respuesta de Anthropic Claude
            $assistantResponse = $this->getClaudeResponse($userMessage);

            // Retornar JSON para que el front lo muestre
            return response()->json([
                'userMessage'       => $userMessage,
                'assistantMessage'  => $assistantResponse,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'userMessage'       => $userMessage,
                'assistantMessage'  => 'Ha ocurrido un error al procesar tu mensaje. Por favor, intenta nuevamente.',
                'error'             => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ejemplo: limpiar el chat (opcional).
     */
    public function clearChat()
    {
        return redirect()->route('ai.index')
                         ->with('info','Chat limpio.');
    }

    /**
     * Método para obtener la respuesta de Anthropic Claude
     */
    private function getClaudeResponse($userMessage)
    {
        $apiKey = env('CLAUDE_API');
        $model = env('CLAUDE_MODEL', 'claude-3-haiku-20240307');
        
        // Endpoint de la API de Anthropic Messages
        $endpoint = 'https://api.anthropic.com/v1/messages';
        
        // Definir el mensaje del sistema
        $systemMessage = '
        Eres un asistente de SAES tu nombre es SAES-AI. Si el usuario te pregunta algo sobre la plataforma guialo por medio de las siguientes url:
        URL BASE: https://saes-asf.icu
        /dashboard/distribucion - Ver la distribución de las acciones
        /dashboard/expedientes/entrega - Programar entrega de expedientes
        /dashboard/expedientes/recepcion - Recepción de expedientes programados
        /dashboard/expedientes/historial-programacion - Historial de movimiento de expedientes (rastreo de expedientes se puede saber en donde se encuentran)
        /dashboard/all-auditorias - Listado de todos los expedientes y acceso a la lista de verificación
        
        ';
        
        // Preparar el cuerpo de la solicitud con el parámetro system
        $requestBody = [
            'model' => $model,
            'max_tokens' => 1024,
            'system' => $systemMessage,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage]
            ]
        ];
        
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post($endpoint, $requestBody);
        
        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $responseData = $response->json();
            
            // Verificar que la estructura de respuesta es como esperamos
            if (!isset($responseData['content']) || !is_array($responseData['content']) || empty($responseData['content'])) {
                throw new \Exception('Formato de respuesta inesperado de la API de Anthropic');
            }
            
            return $responseData['content'][0]['text']; // Extraer el texto de la respuesta
        } else {
            // Obtener detalles del error para diagnóstico
            $statusCode = $response->status();
            $errorBody = $response->body();
            
            // Registrar detalles del error
            Log::error("Error API Anthropic (código $statusCode): $errorBody");
            
            // Si hay un error, lanzar una excepción con el mensaje
            $errorData = $response->json();
            $errorMessage = isset($errorData['error']) ? ($errorData['error']['message'] ?? 'Error sin mensaje') : 'Error desconocido';
            throw new \Exception("API error ($statusCode): " . $errorMessage);
        }
    }
}
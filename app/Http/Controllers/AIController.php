<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AIController extends Controller
{
    /**
     * Muestra la vista principal del Asistente IA.
     */
    public function index()
    {
        // Obtener proveedores disponibles para la vista
        $providers = [
            'anthropic' => 'Anthropic Claude',
            'openai' => 'OpenAI GPT',
            'gemini' => 'Google Gemini',
        ];
        
        // Obtener modelos disponibles por proveedor
        $models = [
            'anthropic' => [
                'claude-3-haiku-20240307' => 'Claude 3 Haiku',
                'claude-3-sonnet-20240229' => 'Claude 3 Sonnet',
                'claude-3-opus-20240229' => 'Claude 3 Opus',
            ],
            'openai' => [
                'gpt-4-1106-preview' => 'GPT-4 Turbo',
                'gpt-4-0613' => 'GPT-4',
                'gpt-4.1-nano' => 'GPT-4.1 Nano',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ],
            'gemini' => [
                'gemini-pro' => 'Gemini Pro',
                'gemini-ultra' => 'Gemini Ultra',
            ],
        ];

        // Obtener catálogos para los filtros
        $catalogos = $this->getCatalogos();
        
        $defaultProvider = env('DEFAULT_AI_PROVIDER', 'anthropic');
        $defaultModel = env('DEFAULT_AI_MODEL', 'claude-3-haiku-20240307');
        
        // Inicializar la sesión para la conversación si no existe
        if (!Session::has('ai_conversation_history')) {
            Session::put('ai_conversation_history', []);
        }
        
        return view('dashboard.ai.index', [
            'providers' => $providers,
            'models' => $models,
            'defaultProvider' => $defaultProvider,
            'defaultModel' => $defaultModel,
            'catalogos' => $catalogos
        ]);
    }

    /**
     * Recibe el mensaje del usuario y responde usando la API seleccionada.
     */
    public function sendMessage(Request $request)
    {
        try {
            // Validar que se reciba un texto y opcionalmente el proveedor y modelo
            $data = $request->validate([
                'message' => 'required|string|min:1',
                'provider' => 'sometimes|string|in:anthropic,openai,gemini',
                'model' => 'sometimes|string',
                'includeContext' => 'sometimes|boolean',
                'conversation_id' => 'nullable|string',
                'filters' => 'sometimes|array',
                'filters.entrega' => 'nullable|string',
                'filters.cuenta_publica' => 'nullable|string',
                'filters.uaa_id' => 'nullable|string',
                'filters.dg_id' => 'nullable|string'
            ]);

            $userMessage = $data['message'];
            $provider = $data['provider'] ?? env('DEFAULT_AI_PROVIDER', 'anthropic');
            $model = $data['model'] ?? null;
            $includeContext = $data['includeContext'] ?? true;
            $conversationId = isset($data['conversation_id']) && !empty($data['conversation_id']) ? 
                              $data['conversation_id'] : null;
            $filters = $data['filters'] ?? null;
            
            // Obtener el historial de la conversación actual
            $conversationHistory = Session::get('ai_conversation_history', []);
            
            // Inicializar nueva conversación si es necesario
            if ($conversationId && isset($conversationHistory[$conversationId])) {
                $currentConversation = &$conversationHistory[$conversationId];
            } else {
                $conversationId = uniqid('conv_');
                $conversationHistory[$conversationId] = [
                    'id' => $conversationId,
                    'created_at' => now()->toDateTimeString(),
                    'title' => $this->generateConversationTitle($userMessage),
                    'messages' => []
                ];
                $currentConversation = &$conversationHistory[$conversationId];
            }
            
            // Añadir el mensaje del usuario al historial
            $currentConversation['messages'][] = [
                'role' => 'user',
                'content' => $userMessage,
                'timestamp' => now()->toDateTimeString()
            ];

            try {
                // Obtener la respuesta del proveedor seleccionado con el historial de conversación
                $assistantResponse = $this->getAIResponse(
                    $userMessage, 
                    $provider, 
                    $model, 
                    $includeContext, 
                    $currentConversation['messages'],
                    $filters
                );
                
                // Añadir la respuesta del asistente al historial
                $currentConversation['messages'][] = [
                    'role' => 'assistant',
                    'content' => $assistantResponse,
                    'timestamp' => now()->toDateTimeString()
                ];
                
                // Actualizar la sesión con el historial actualizado
                Session::put('ai_conversation_history', $conversationHistory);

                // Retornar JSON para que el front lo muestre
                return response()->json([
                    'userMessage' => $userMessage,
                    'assistantMessage' => $assistantResponse,
                    'provider' => $provider,
                    'conversation_id' => $conversationId,
                    'conversation_title' => $currentConversation['title']
                ]);
            } catch (\Exception $e) {
                // Error específico de la API de IA
                Log::error("Error en API de IA: " . $e->getMessage());
                return response()->json([
                    'userMessage' => $userMessage,
                    'assistantMessage' => 'Ha ocurrido un error al procesar tu mensaje con el proveedor de IA: ' . $e->getMessage(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        } catch (\Exception $e) {
            // Error general del controlador
            Log::error("Error general en el asistente de IA: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());
            return response()->json([
                'assistantMessage' => 'Ha ocurrido un error general: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Genera un título para la conversación basado en el primer mensaje
     */
    private function generateConversationTitle($message)
    {
        // Limitar a 50 caracteres
        $title = substr($message, 0, 50);
        if (strlen($message) > 50) {
            $title .= '...';
        }
        return $title;
    }

    /**
     * Obtiene el historial de conversaciones
     */
    public function getConversations()
    {
        $conversations = Session::get('ai_conversation_history', []);
        
        // Ordenar conversaciones por fecha de creación, más reciente primero
        uasort($conversations, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return response()->json($conversations);
    }

    /**
     * Obtiene una conversación específica por ID
     */
    public function getConversation($id)
    {
        $conversations = Session::get('ai_conversation_history', []);
        
        if (isset($conversations[$id])) {
            return response()->json($conversations[$id]);
        }
        
        return response()->json(['error' => 'Conversación no encontrada'], 404);
    }

    /**
     * Elimina una conversación
     */
    public function deleteConversation($id)
    {
        $conversations = Session::get('ai_conversation_history', []);
        
        if (isset($conversations[$id])) {
            unset($conversations[$id]);
            Session::put('ai_conversation_history', $conversations);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'Conversación no encontrada'], 404);
    }

    /**
     * Elimina todas las conversaciones
     */
    public function clearChat()
    {
        Session::forget('ai_conversation_history');
        return redirect()->route('ai.index')
                        ->with('info','Chat limpio.');
    }

    /**
     * Método para obtener la respuesta del proveedor seleccionado
     */
    private function getAIResponse($userMessage, $provider = null, $model = null, $includeContext = true, $conversationHistory = [], $filters = null)
    {
        $provider = $provider ?? env('DEFAULT_AI_PROVIDER', 'anthropic');
        
        // Obtener datos actualizados del sistema antes de cada respuesta
        $systemMessage = $this->getSystemPrompt($includeContext, $filters);
        
        switch ($provider) {
            case 'anthropic':
                return $this->getAnthropicResponse($userMessage, $model, $systemMessage, $conversationHistory);
            case 'openai':
                return $this->getOpenAIResponse($userMessage, $model, $systemMessage, $conversationHistory);
            case 'gemini':
                return $this->getGeminiResponse($userMessage, $model, $systemMessage, $conversationHistory);
            default:
                throw new \Exception("Proveedor de IA no soportado: $provider");
        }
    }

    /**
     * Método para obtener la respuesta de Anthropic Claude
     */
    private function getAnthropicResponse($userMessage, $model = null, $systemMessage = null, $conversationHistory = [])
    {
        $apiKey = env('CLAUDE_API');
        if (empty($apiKey)) {
            throw new \Exception('La clave API de Claude no está configurada en el archivo .env');
        }
        
        $model = $model ?? env('CLAUDE_MODEL', 'claude-3-haiku-20240307');
        
        // Endpoint de la API de Anthropic Messages
        $endpoint = 'https://api.anthropic.com/v1/messages';
        
        // Convertir el historial de conversación al formato esperado por Claude
        $messages = $this->formatConversationForAnthropic($conversationHistory);
        
        // Si no hay mensajes previos o solo existe el mensaje actual del usuario,
        // usar formato simple
        if (empty($messages)) {
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        } else {
            // El último mensaje debe ser del usuario, así que aseguramos que sea el actual
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        }
        
        // Preparar el cuerpo de la solicitud con el parámetro system
        $requestBody = [
            'model' => $model,
            'max_tokens' => 4024,
            'system' => $systemMessage,
            'messages' => $messages
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
    
    /**
     * Método para obtener la respuesta de OpenAI
     */
    private function getOpenAIResponse($userMessage, $model = null, $systemMessage = null, $conversationHistory = [])
    {
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            throw new \Exception('La clave API de OpenAI no está configurada en el archivo .env');
        }
        
        $model = $model ?? env('OPENAI_MODEL', 'gpt-4.1');
        
        // Endpoint de la API de OpenAI
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        
        // Convertir el historial de conversación al formato esperado por OpenAI
        $messages = $this->formatConversationForOpenAI($conversationHistory, $systemMessage);
        
        // Asegurar que el último mensaje es el del usuario
        $lastMessageIsUser = false;
        if (!empty($messages)) {
            $lastMessage = end($messages);
            $lastMessageIsUser = ($lastMessage['role'] === 'user');
        }
        
        if (!$lastMessageIsUser) {
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        }
        
        // Preparar el cuerpo de la solicitud
        $requestBody = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 4024
        ];
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $requestBody);
        
        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $responseData = $response->json();
            
            // Verificar que la estructura de respuesta es como esperamos
            if (!isset($responseData['choices'][0]['message']['content'])) {
                throw new \Exception('Formato de respuesta inesperado de la API de OpenAI');
            }
            
            return $responseData['choices'][0]['message']['content'];
        } else {
            // Obtener detalles del error para diagnóstico
            $statusCode = $response->status();
            $errorBody = $response->body();
            
            // Registrar detalles del error
            Log::error("Error API OpenAI (código $statusCode): $errorBody");
            
            // Si hay un error, lanzar una excepción con el mensaje
            $errorData = $response->json();
            $errorMessage = isset($errorData['error']) ? ($errorData['error']['message'] ?? 'Error sin mensaje') : 'Error desconocido';
            throw new \Exception("API error ($statusCode): " . $errorMessage);
        }
    }
    
    /**
     * Método para obtener la respuesta de Google Gemini
     */
    private function getGeminiResponse($userMessage, $model = null, $systemMessage = null, $conversationHistory = [])
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            throw new \Exception('La clave API de Gemini no está configurada en el archivo .env');
        }
        
        $model = $model ?? env('GEMINI_MODEL', 'gemini-pro');
        
        // Endpoint de la API de Gemini
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        
        // Convertir historial de conversación al formato de Gemini
        $contents = $this->formatConversationForGemini($conversationHistory, $systemMessage, $userMessage);
        
        // Preparar el cuerpo de la solicitud
        $requestBody = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => 4024,
            ]
        ];
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withQueryParameters([
            'key' => $apiKey
        ])->post($endpoint, $requestBody);
        
        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $responseData = $response->json();
            
            // Verificar que la estructura de respuesta es como esperamos
            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                throw new \Exception('Formato de respuesta inesperado de la API de Gemini');
            }
            
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        } else {
            // Obtener detalles del error para diagnóstico
            $statusCode = $response->status();
            $errorBody = $response->body();
            
            // Registrar detalles del error
            Log::error("Error API Gemini (código $statusCode): $errorBody");
            
            // Si hay un error, lanzar una excepción con el mensaje
            $errorData = $response->json();
            $errorMessage = isset($errorData['error']) ? ($errorData['error']['message'] ?? 'Error sin mensaje') : 'Error desconocido';
            throw new \Exception("API error ($statusCode): " . $errorMessage);
        }
    }

    /**
     * Formatea el historial de conversación para Anthropic Claude
     */
    private function formatConversationForAnthropic($history)
    {
        $formatted = [];
        
        foreach ($history as $message) {
            if ($message['role'] !== 'system') {
                $formatted[] = [
                    'role' => $message['role'],
                    'content' => $message['content']
                ];
            }
        }
        
        return $formatted;
    }
    
    /**
     * Formatea el historial de conversación para OpenAI
     */
    private function formatConversationForOpenAI($history, $systemMessage)
    {
        $formatted = [
            ['role' => 'system', 'content' => $systemMessage]
        ];
        
        foreach ($history as $message) {
            if ($message['role'] !== 'system') {
                $formatted[] = [
                    'role' => $message['role'],
                    'content' => $message['content']
                ];
            }
        }
        
        return $formatted;
    }
    
    /**
     * Formatea el historial de conversación para Gemini
     */
    private function formatConversationForGemini($history, $systemMessage, $currentUserMessage)
    {
        $contents = [];
        
        // Gemini no tiene un rol de sistema, así que lo añadimos como parte del primer mensaje del usuario
        $systemPromptApplied = false;
        
        foreach ($history as $index => $message) {
            if ($message['role'] === 'user') {
                // Al primer mensaje del usuario, añadirle el prompt del sistema
                $content = $message['content'];
                if (!$systemPromptApplied) {
                    $content = $systemMessage . "\n\n" . $content;
                    $systemPromptApplied = true;
                }
                
                $contents[] = [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $content]
                    ]
                ];
            } elseif ($message['role'] === 'assistant') {
                $contents[] = [
                    'role' => 'model',
                    'parts' => [
                        ['text' => $message['content']]
                    ]
                ];
            }
        }
        
        // Si no hay historial o no se aplicó el sistema, añadir el mensaje actual con el prompt
        if (!$systemPromptApplied) {
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $systemMessage . "\n\n" . $currentUserMessage]
                ]
            ];
        } else {
            // Añadir el mensaje actual del usuario si no es el último
            $lastMessage = end($history);
            if (!$lastMessage || $lastMessage['role'] !== 'user') {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $currentUserMessage]
                    ]
                ];
            }
        }
        
        return $contents;
    }
    
    /**
     * Obtiene información del sistema SAES directamente de los controladores
     */
    private function getSaesData($type, Request $request = null)
    {
        try {
            if (!$request) {
                $request = new Request();
            }

            // Obtener los parámetros de la URL actual si existen
            $params = [
                'entrega' => $request->query('entrega', 18),
                'cuenta_publica' => $request->query('cuenta_publica', 1),
                'uaa_id' => $request->query('uaa_id'),
                'dg_id' => $request->query('dg_id')
            ];

            // Aplicar los parámetros a la nueva request
            $request->merge($params);

            switch ($type) {
                case 'charts':
                    $controller = new \App\Http\Controllers\DashboardController();
                    $data = $controller->getDashboardData($request);
                    return $data;

                case 'entregas':
                    $controller = new \App\Http\Controllers\DashboardEntregasController();
                    $data = $controller->getDashboardData($request);
                    return $data;

                default:
                    return null;
            }
        } catch (\Exception $e) {
            \Log::error("Error obteniendo datos del sistema: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el mensaje del sistema (prompt) común para todos los proveedores
     * 
     * @param bool $includeContext Si se debe incluir el contexto de datos en el prompt
     * @return string
     */
    private function getSystemPrompt($includeContext = true, $filters = null)
    {
        $basePrompt = '
        Eres un asistente de SAES tu nombre es SAES-AI.
        Tienes acceso a la información de SAES y puedes responder preguntas sobre la información que tengas a la mano.
        Última actualización: 06/05/2025 11:13
        
        Contexto de la entrega actual cuenta publica 2023 entrega 3
        Contexto de los graficos de lista de verificación de expedientes de acción:
            Expedientes por Estatus
                2860 expedientes

                Aceptado
                1045
                (36.5%)
                Sin Revisar (No entregados + Entregados sin revisar)
                991
                (34.7%)
                Devuelto
                510
                (17.8%)
                En Proceso de Revisión del Checklist
                314
                (11.0%)
                Desglose por Estatus
                Buscar estatus...

                Exportar CSV
                Estatus	Expedientes	Porcentaje	Gráfico
                Aceptado
                1045	36.5%	
                Sin Revisar (No entregados + Entregados sin revisar)
                991	34.7%	
                Devuelto
                510	17.8%	
                En Proceso de Revisión del Checklist
                314	11.0%	
                Total	2860	100%	
                Expedientes por Dirección General de Seguimiento
                DGSEG EF	Estatus	Total	Porcentaje
                Dirección General de Seguimiento "B"	Total por DGSEG EF	453	100%
                Aceptado	327	72.19%
                En Proceso de Revisión del Checklist	42	9.27%
                Devuelto	66	14.57%
                Sin Revisar (No entregados + Entregados sin revisar)	18	3.97%
                Dirección General de Seguimiento "D"	Total por DGSEG EF	776	100%
                Aceptado	268	34.54%
                En Proceso de Revisión del Checklist	130	16.75%
                Devuelto	120	15.46%
                Sin Revisar (No entregados + Entregados sin revisar)	258	33.25%
                Dirección General de Seguimiento "A"	Total por DGSEG EF	1072	100%
                Aceptado	278	25.93%
                En Proceso de Revisión del Checklist	101	9.42%
                Devuelto	189	17.63%
                Sin Revisar (No entregados + Entregados sin revisar)	504	47.01%
                Dirección General de Seguimiento "C"	Total por DGSEG EF	559	100%
                Aceptado	172	30.77%
                En Proceso de Revisión del Checklist	41	7.33%
                Devuelto	135	24.15%
                Sin Revisar (No entregados + Entregados sin revisar)	211	37.75%
                Expedientes por Siglas Tipo Acción
                Siglas Tipo Acción	Estatus	Total	Porcentaje
                PO	Total por Sigla	1738	100%
                Sin Revisar (No entregados + Entregados sin revisar)	627	36.08%
                Aceptado	461	26.52%
                Devuelto	397	22.84%
                En Proceso de Revisión del Checklist	253	14.56%
                R	Total por Sigla	665	100%
                Aceptado	485	72.93%
                Devuelto	101	15.19%
                Sin Revisar (No entregados + Entregados sin revisar)	29	4.36%
                En Proceso de Revisión del Checklist	50	7.52%
                SA	Total por Sigla	376	100%
                Sin Revisar (No entregados + Entregados sin revisar)	335	89.10%
                Aceptado	18	4.79%
                Devuelto	12	3.19%
                En Proceso de Revisión del Checklist	11	2.93%
                RD	Total por Sigla	81	100%
                Aceptado	81	100.00%
                Gran Total		2860	100%
                * Cada color representa un Estatus, cada barra corresponde a una Sigla de Tipo Acción.

                Expedientes por Siglas de Auditoría Especial (Apilado por Estatus)
                Siglas Auditoría Especial	Estatus	Total	Porcentaje
                AEGF	Total por Sigla	2117	100%
                Sin Revisar (No entregados + Entregados sin revisar)	957	45.21%
                Aceptado	558	26.36%
                Devuelto	368	17.38%
                En Proceso de Revisión del Checklist	234	11.05%
                AECF	Total por Sigla	605	100%
                Aceptado	349	57.69%
                Devuelto	142	23.47%
                En Proceso de Revisión del Checklist	80	13.22%
                Sin Revisar (No entregados + Entregados sin revisar)	34	5.62%
                AED	Total por Sigla	138	100%
                Aceptado	138	100.00%
                Gran Total		2860	100%
                * Cada color representa un estatus, cada barra una Sigla.

                Expedientes por Auditoría Especial, UAA y Estatus
                Auditoría Especial: AECF (Total: 605)
                UAA	Estatus	Total	Porcentaje
                Dirección General de Auditoría de Inversiones Físicas Federales	Total por UAA	172	100%
                Aceptado	81	47.09%
                En Proceso de Revisión del Checklist	38	22.09%
                Devuelto	46	26.74%
                Sin Revisar (No entregados + Entregados sin revisar)	7	4.07%
                Dirección General de Auditoría Forense de Cumplimiento Financiero	Total por UAA	157	100%
                Aceptado	108	68.79%
                En Proceso de Revisión del Checklist	8	5.10%
                Devuelto	18	11.46%
                Sin Revisar (No entregados + Entregados sin revisar)	23	14.65%
                Dirección General de Auditoría Financiera Federal "C"	Total por UAA	141	100%
                Aceptado	71	50.35%
                En Proceso de Revisión del Checklist	10	7.09%
                Devuelto	59	41.84%
                Sin Revisar (No entregados + Entregados sin revisar)	1	0.71%
                Dirección General de Auditoría Financiera Federal "B"	Total por UAA	62	100%
                Aceptado	51	82.26%
                Devuelto	7	11.29%
                En Proceso de Revisión del Checklist	3	4.84%
                Sin Revisar (No entregados + Entregados sin revisar)	1	1.61%
                Dirección General de Auditoría de Tecnologías de Información y Comunicaciones	Total por UAA	45	100%
                Aceptado	21	46.67%
                En Proceso de Revisión del Checklist	12	26.67%
                Devuelto	10	22.22%
                Sin Revisar (No entregados + Entregados sin revisar)	2	4.44%
                Dirección General de Auditoría Financiera Federal "A"	Total por UAA	28	100%
                Aceptado	17	60.71%
                En Proceso de Revisión del Checklist	9	32.14%
                Devuelto	2	7.14%
                Gran Total		605	100%
                Auditoría Especial: AED (Total: 138)
                UAA	Estatus	Total	Porcentaje
                Dirección General de Auditoría y Evaluación a los Sistemas de Control Interno	Total por UAA	57	100%
                Aceptado	57	100.00%
                Dirección General de Auditoría de Desempeño al Desarrollo Económico	Total por UAA	42	100%
                Aceptado	42	100.00%
                Dirección General de Auditoría de Desempeño al Desarrollo Social	Total por UAA	26	100%
                Aceptado	26	100.00%
                Dirección General de Auditoría de Desempeño a Programas Presupuestarios	Total por UAA	8	100%
                Aceptado	8	100.00%
                Dirección General de Auditoría de Desempeño a Gobierno y Finanzas	Total por UAA	5	100%
                Aceptado	5	100.00%
                Gran Total		138	100%
                Auditoría Especial: AEGF (Total: 2117)
                UAA	Estatus	Total	Porcentaje
                Dirección General de Auditoría del Gasto Federalizado "D"	Total por UAA	1061	100%
                Aceptado	53	5.00%
                En Proceso de Revisión del Checklist	63	5.94%
                Devuelto	110	10.37%
                Sin Revisar (No entregados + Entregados sin revisar)	835	78.70%
                Dirección General de Auditoría del Gasto Federalizado "C"	Total por UAA	430	100%
                Aceptado	200	46.51%
                En Proceso de Revisión del Checklist	64	14.88%
                Devuelto	124	28.84%
                Sin Revisar (No entregados + Entregados sin revisar)	42	9.77%
                Dirección General de Auditoría del Gasto Federalizado "A"	Total por UAA	288	100%
                Aceptado	139	48.26%
                En Proceso de Revisión del Checklist	36	12.50%
                Devuelto	70	24.31%
                Sin Revisar (No entregados + Entregados sin revisar)	43	14.93%
                Dirección General de Auditoría del Gasto Federalizado "B"	Total por UAA	170	100%
                Aceptado	94	55.29%
                En Proceso de Revisión del Checklist	30	17.65%
                Devuelto	25	14.71%
                Sin Revisar (No entregados + Entregados sin revisar)	21	12.35%
                Dirección General de Auditoría Forense del Gasto Federalizado	Total por UAA	162	100%
                Aceptado	70	43.21%
                En Proceso de Revisión del Checklist	38	23.46%
                Devuelto	38	23.46%
                Sin Revisar (No entregados + Entregados sin revisar)	16	9.88%
                Dirección General de Evaluación del Gasto Federalizado	Total por UAA	6	100%
                Aceptado	2	33.33%
                En Proceso de Revisión del Checklist	3	50.00%
                Devuelto	1	16.67%
                Gran Total		2117	100%
                Expedientes por UAA y Estatus
                UAA	Estatus	Total	Porcentaje
                Dirección General de Auditoría del Gasto Federalizado "D"	Total por UAA	1061	100%
                Sin Revisar (No entregados + Entregados sin revisar)	835	78.70%
                Devuelto	110	10.37%
                Aceptado	53	5.00%
                En Proceso de Revisión del Checklist	63	5.94%
                Dirección General de Auditoría del Gasto Federalizado "C"	Total por UAA	430	100%
                Aceptado	200	46.51%
                Devuelto	124	28.84%
                Sin Revisar (No entregados + Entregados sin revisar)	42	9.77%
                En Proceso de Revisión del Checklist	64	14.88%
                Dirección General de Auditoría del Gasto Federalizado "A"	Total por UAA	288	100%
                Aceptado	139	48.26%
                Devuelto	70	24.31%
                Sin Revisar (No entregados + Entregados sin revisar)	43	14.93%
                En Proceso de Revisión del Checklist	36	12.50%
                Dirección General de Auditoría de Inversiones Físicas Federales	Total por UAA	172	100%
                Aceptado	81	47.09%
                Devuelto	46	26.74%
                En Proceso de Revisión del Checklist	38	22.09%
                Sin Revisar (No entregados + Entregados sin revisar)	7	4.07%
                Dirección General de Auditoría del Gasto Federalizado "B"	Total por UAA	170	100%
                Aceptado	94	55.29%
                Devuelto	25	14.71%
                Sin Revisar (No entregados + Entregados sin revisar)	21	12.35%
                En Proceso de Revisión del Checklist	30	17.65%
                Dirección General de Auditoría Forense del Gasto Federalizado	Total por UAA	162	100%
                Aceptado	70	43.21%
                Devuelto	38	23.46%
                En Proceso de Revisión del Checklist	38	23.46%
                Sin Revisar (No entregados + Entregados sin revisar)	16	9.88%
                Dirección General de Auditoría Forense de Cumplimiento Financiero	Total por UAA	157	100%
                Aceptado	108	68.79%
                Sin Revisar (No entregados + Entregados sin revisar)	23	14.65%
                Devuelto	18	11.46%
                En Proceso de Revisión del Checklist	8	5.10%
                Dirección General de Auditoría Financiera Federal "C"	Total por UAA	141	100%
                Aceptado	71	50.35%
                Devuelto	59	41.84%
                En Proceso de Revisión del Checklist	10	7.09%
                Sin Revisar (No entregados + Entregados sin revisar)	1	0.71%
                Dirección General de Auditoría Financiera Federal "B"	Total por UAA	62	100%
                Aceptado	51	82.26%
                Devuelto	7	11.29%
                En Proceso de Revisión del Checklist	3	4.84%
                Sin Revisar (No entregados + Entregados sin revisar)	1	1.61%
                Dirección General de Auditoría y Evaluación a los Sistemas de Control Interno	Total por UAA	57	100%
                Aceptado	57	100.00%
                Dirección General de Auditoría de Tecnologías de Información y Comunicaciones	Total por UAA	45	100%
                Aceptado	21	46.67%
                Devuelto	10	22.22%
                En Proceso de Revisión del Checklist	12	26.67%
                Sin Revisar (No entregados + Entregados sin revisar)	2	4.44%
                Dirección General de Auditoría de Desempeño al Desarrollo Económico	Total por UAA	42	100%
                Aceptado	42	100.00%
                Dirección General de Auditoría Financiera Federal "A"	Total por UAA	28	100%
                Aceptado	17	60.71%
                En Proceso de Revisión del Checklist	9	32.14%
                Devuelto	2	7.14%
                Dirección General de Auditoría de Desempeño al Desarrollo Social	Total por UAA	26	100%
                Aceptado	26	100.00%
                Dirección General de Auditoría de Desempeño a Programas Presupuestarios	Total por UAA	8	100%
                Aceptado	8	100.00%
                Dirección General de Evaluación del Gasto Federalizado	Total por UAA	6	100%
                Aceptado	2	33.33%
                En Proceso de Revisión del Checklist	3	50.00%
                Devuelto	1	16.67%
                Dirección General de Auditoría de Desempeño a Gobierno y Finanzas	Total por UAA	5	100%
                Aceptado	5	100.00%
                Gran Total		2860	100%
                * Cada color representa un estatus, cada barra una UAA.

                Expedientes por Ente Fiscalizado
                Ente Fiscalizado	Total
                Comisión Nacional del Agua	67
                Secretaría de Infraestructura, Comunicaciones y Transportes	50
                Pemex Corporativo	47
                Instituto Mexicano del Seguro Social	40
                Fondo Nacional de Fomento al Turismo	30
                Gobierno del Estado de Chiapas	26
                Instituto Nacional de Transparencia, Acceso a la Información y Protección de Datos Personales	23
                Gobierno del Estado de Baja California Sur	23
                Gobierno del Estado de Durango	23
                Gobierno del Estado de Morelos	22
                Secretaría de Educación Pública	21
                Secretaría de Bienestar	21
                CFE Distribución	20
                Servicios de Salud de Morelos	20
                Municipio de San Luis Potosí, San Luis Potosí	20
                Municipio de San Pedro Garza García, Nuevo León	19
                Banco Nacional de Obras y Servicios Públicos, S.N.C.	18
                Gobierno del Estado de Aguascalientes	17
                Gobierno del Estado de Veracruz de Ignacio de la Llave	17
                Pemex Transformación Industrial	17
                Instituto Nacional de Antropología e Historia	17
                Gobierno del Estado de Baja California	16
                Gobierno del Estado de Coahuila de Zaragoza	16
                Gobierno del Estado de Nuevo León	16
                Gobierno del Estado de Tabasco	16
                Gobierno del Estado de Tlaxcala	16
                Servicios de Salud del Instituto Mexicano del Seguro Social para el Bienestar (IMSS-BIENESTAR)	16
                Seguridad Alimentaria Mexicana	15
                Diconsa, S.A. de C.V.	15
                Administración del Sistema Portuario Nacional Veracruz, S.A. de C.V.	15
                Gobierno del Estado de Guerrero	15
                Municipio de Aguascalientes, Aguascalientes	15
                Secretaría de Marina	14
                Gobierno del Estado de Oaxaca	14
                Municipio de Axtla de Terrazas, San Luis Potosí	14
                Gobierno del Estado de Colima	13
                Casa de Moneda de México	13
                Laboratorios de Biológicos y Reactivos de México, S.A. de C.V.	13
                Servicios de Salud de Veracruz	13
                Fideicomiso de Fomento Minero	12
                Gobierno del Estado de Jalisco	12
                Gobierno del Estado de Michoacán de Ocampo	12
                Gobierno del Estado de Nayarit	12
                CFE Suministrador de Servicios Básicos	12
                Comisión Nacional de Áreas Naturales Protegidas	12
                Tribunal Electoral del Poder Judicial de la Federación	11
                Consejo Nacional de Fomento Educativo	11
                Instituto Nacional de Migración	11
                Pemex Exploración y Producción	11
                Fiscalía General del Estado de Morelos	11
                CFE Telecomunicaciones e Internet para Todos	10
                Gobierno del Estado de Zacatecas	10
                Municipio de Huimanguillo, Tabasco	10
                Municipio de Sain Alto, Zacatecas	10
                Universidad Autónoma de Nayarit	10
                Caminos y Puentes Federales de Ingresos y Servicios Conexos	9
                Instituto de Seguridad y Servicios Sociales de los Trabajadores del Estado	9
                Fondo de Capitalización e Inversión del Sector Rural	9
                Gobierno del Estado de Sonora	9
                Gobierno del Estado de Yucatán	9
                Municipio de Toluca, Estado de México	9
                Municipio de Xalapa, Veracruz de Ignacio de la Llave	9
                Instituto Nacional de Investigaciones Nucleares	9
                Liconsa, S.A. de C.V.	9
                Universidad Michoacana de San Nicolás de Hidalgo	9
                Instituto Tecnológico José Mario Molina Pasquel y Henríquez	9
                Municipio de Guadalajara, Jalisco	9
                Universidad Autónoma Metropolitana	8
                Gobierno del Estado de Chihuahua	8
                Gobierno del Estado de México	8
                Alcaldía Milpa Alta, Ciudad de México	8
                Municipio de Ciudad del Maíz, San Luis Potosí	8
                Secretaría de Relaciones Exteriores	8
                Instituto Politécnico Nacional	8
                Consejo Nacional de Humanidades, Ciencias y Tecnologías	8
                Comisión Federal de Competencia Económica	8
                Gobierno del Estado de Hidalgo	7
                Municipio de Juárez, Chihuahua	7
                Alcaldía Gustavo A. Madero, Ciudad de México	7
                Municipio de Contla de Juan Cuamatzi, Tlaxcala	7
                Universidad Nacional Autónoma de México	7
                Fiscalía General de la República	7
                Municipio de San Pedro Tlaquepaque, Jalisco	7
                Gobierno del Estado de Campeche	6
                Municipio de Campeche, Campeche	6
                Municipio de Madero, Michoacán de Ocampo	6
                Municipio de Heroica Ciudad de Juchitán de Zaragoza, Oaxaca	6
                Municipio de San Juan Bautista Tuxtepec, Oaxaca	6
                Municipio de Bacalar, Quintana Roo	6
                Municipio de Huatabampo, Sonora	6
                Comisión Reguladora de Energía	6
                Centro de Investigación y Docencia Económicas, A.C.	6
                Universidad Autónoma del Carmen	6
                Instituto Tecnológico Superior de Los Ríos	6
                Instituto Tecnológico Superior de Zongolica	6
                Municipio de Las Margaritas, Chiapas	6
                Municipio de Camargo, Chihuahua	6
                Municipio de Matlapa, San Luis Potosí	6
                Municipio de Puerto Peñasco, Sonora	6
                Secretaría de la Defensa Nacional	5
                Secretaría de Desarrollo Agrario, Territorial y Urbano	5
                Comisión Nacional de Hidrocarburos	5
                Coordinación Nacional de Becas para el Bienestar Benito Juárez	5
                Autoridad Educativa Federal en la Ciudad de México	5
                Gobierno del Estado de Puebla	5
                Municipio de Mulegé, Baja California Sur	5
                Municipio de Calakmul, Campeche	5
                Municipio de León, Guanajuato	5
                Municipio de Pénjamo, Guanajuato	5
                Municipio de Coyuca de Benítez, Guerrero	5
                Municipio de Yahualica, Hidalgo	5
                Municipio de Puerto Vallarta, Jalisco	5
                Municipio de Maravatío, Michoacán de Ocampo	5
                Municipio de Pátzcuaro, Michoacán de Ocampo	5
                Municipio de Puebla, Puebla	5
                Municipio de Landa de Matamoros, Querétaro	5
                Municipio de San Pablo del Monte, Tlaxcala	5
                Municipio de Ixhuatlán de Madero, Veracruz de Ignacio de la Llave	5
                Municipio de Sayula de Alemán, Veracruz de Ignacio de la Llave	5
                Municipio de Chemax, Yucatán	5
                Municipio de Jerez, Zacatecas	5
                Municipio de Zacatecas, Zacatecas	5
                Municipio de Tijuana, Baja California	5
                Municipio de Ocosingo, Chiapas	5
                Secretaría de Salud	5
                Procuraduría Federal del Consumidor	5
                Universidad Autónoma de Chiapas	5
                Universidad Politécnica de Pachuca	5
                Instituto Tecnológico Superior de Comalcalco	5
                Universidad Politécnica Metropolitana de Hidalgo	5
                Instituto Tecnológico Superior de Acayucan	5
                Instituto Tecnológico Superior de Las Choapas	5
                Instituto Tecnológico Superior de Pánuco	5
                Municipio de Chenalhó, Chiapas	5
                Municipio de Venustiano Carranza, Chiapas	5
                Municipio de Lázaro Cárdenas, Michoacán de Ocampo	5
                Municipio de Jaumave, Tamaulipas	5
                Pemex Logística	4
                Municipio de Matamoros, Coahuila de Zaragoza	4
                Municipio de Torreón, Coahuila de Zaragoza	4
                Municipio de San Luis Acatlán, Guerrero	4
                Municipio de Lolotla, Hidalgo	4
                Municipio de Chalco, Estado de México	4
                Municipio de Texcoco, Estado de México	4
                Municipio de Jojutla, Morelos	4
                Municipio de Amozoc, Puebla	4
                Municipio de Tehuacán, Puebla	4
                Municipio de Solidaridad, Quintana Roo	4
                Municipio de Agua Prieta, Sonora	4
                Municipio de Huamantla, Tlaxcala	4
                Municipio de Valladolid, Yucatán	4
                Patronato de Obras e Instalaciones del Instituto Politécnico Nacional	4
                Hospital Regional de Alta Especialidad de la Península de Yucatán	4
                Universidad Juárez del Estado de Durango	4
                Universidad Autónoma de Campeche	4
                Universidad de Guadalajara	4
                Universidad Tecnológica Santa Catarina	4
                Universidad Tecnológica de Tijuana	4
                Universidad Tecnológica del Estado de Zacatecas	4
                Universidad Intercultural del Estado de Puebla	4
                Instituto Tecnológico Superior de Apatzingán	4
                Instituto Tecnológico Superior de Cananea	4
                Instituto Tecnológico Superior de Huatusco	4
                Instituto Tecnológico Superior de la Montaña	4
                Instituto Tecnológico Superior de Misantla	4
                Instituto Tecnológico Superior de San Andrés Tuxtla	4
                Tecnológico de Estudios Superiores de Ecatepec	4
                Tecnológico de Estudios Superiores del Oriente del Estado de México	4
                Municipio de Candelaria, Campeche	4
                Municipio de Cuatro Ciénegas, Coahuila de Zaragoza	4
                Municipio de Juárez, Coahuila de Zaragoza	4
                Alcaldía Iztapalapa, Ciudad de México	4
                Municipio de Hidalgo, Michoacán de Ocampo	4
                Municipio de Xoxocotla, Morelos	4
                Municipio de Felipe Carrillo Puerto, Quintana Roo	4
                Municipio de Calpulalpan, Tlaxcala	4
                Poder Judicial del Estado de Nuevo León	4
                Instituto Nacional Electoral	3
                Sistema Nacional para el Desarrollo Integral de la Familia	3
                Ferrocarril del Istmo de Tehuantepec, S.A. de C.V.	3
                Gobierno del Estado de Guanajuato	3
                Municipio de El Llano, Aguascalientes	3
                Municipio de San Felipe, Baja California	3
                Municipio de Comondú, Baja California Sur	3
                Municipio de Los Cabos, Baja California Sur	3
                Municipio de Castaños, Coahuila de Zaragoza	3
                Municipio de Múzquiz, Coahuila de Zaragoza	3
                Municipio de Ramos Arizpe, Coahuila de Zaragoza	3
                Municipio de Manzanillo, Colima	3
                Municipio de Salto de Agua, Chiapas	3
                Alcaldía Magdalena Contreras, Ciudad de México	3
                Alcaldía Tláhuac, Ciudad de México	3
                Alcaldía Tlalpan, Ciudad de México	3
                Alcaldía Xochimilco, Ciudad de México	3
                Municipio de Irapuato, Guanajuato	3
                Municipio de San Felipe, Guanajuato	3
                Municipio de Santa Cruz de Juventino Rosas, Guanajuato	3
                Municipio de Silao de la Victoria, Guanajuato	3
                Municipio de Chilpancingo de los Bravo, Guerrero	3
                Municipio de San Marcos, Guerrero	3
                Municipio de Tlapa de Comonfort, Guerrero	3
                Municipio de Tlanchinol, Hidalgo	3
                Municipio de Almoloya de Juárez, Estado de México	3
                Municipio de Chicoloapan, Estado de México	3
                Municipio de Naucalpan de Juárez, Estado de México	3
                Municipio de Nezahualcóyotl, Estado de México	3
                Municipio de Tecámac, Estado de México	3
                Municipio de Tlalnepantla de Baz, Estado de México	3
                Municipio de Huetamo, Michoacán de Ocampo	3
                Municipio de Morelia, Michoacán de Ocampo	3
                Municipio de Axochiapan, Morelos	3
                Municipio de Santa María Huatulco, Oaxaca	3
                Municipio de Tlacolula de Matamoros, Oaxaca	3
                Municipio de Atempan, Puebla	3
                Municipio de Rioverde, San Luis Potosí	3
                Municipio de Tamazunchale, San Luis Potosí	3
                Municipio de Badiraguato, Sinaloa	3
                Municipio de El Fuerte, Sinaloa	3
                Municipio de Chiautempan, Tlaxcala	3
                Municipio de Altotonga, Veracruz de Ignacio de la Llave	3
                Municipio de Nochistlán de Mejía, Zacatecas	3
                Municipio de Sombrerete, Zacatecas	3
                Municipio de Valparaíso, Zacatecas	3
                Gobierno del Estado de San Luis Potosí	3
                Secretaría de Gobernación	3
                Tribunales Agrarios	3
                Centro Nacional de Control de Energía	3
                Benemérita Universidad Autónoma de Puebla	3
                Universidad Politécnica de Tulancingo	3
                Universidad Autónoma "Benito Juárez" de Oaxaca	3
                Universidad Politécnica de Tlaxcala	3
                Universidad Intercultural Indígena de Michoacán	3
                Instituto Tecnológico Superior de Álamo-Temapache	3
                Instituto Tecnológico Superior de Alvarado	3
                Tecnológico de Estudios Superiores de Coacalco	3
                Instituto Tecnológico Superior de Fresnillo	3
                Instituto Tecnológico Superior de Guasave	3
                Instituto Tecnológico de Estudios Superiores de Los Cabos	3
                Instituto Tecnológico Superior de Teposcolula	3
                Tecnológico de Estudios Superiores de Chimalhuacán	3
                Servicio de Información Agroalimentaria y Pesquera	3
                Municipio de San Quintín, Baja California	3
                Municipio de Arteaga, Coahuila de Zaragoza	3
                Municipio de Chilón, Chiapas	3
                Municipio de Riva Palacio, Chihuahua	3
                Municipio de Apaseo el Alto, Guanajuato	3
                Municipio de Taxco de Alarcón, Guerrero	3
                Municipio de Huautla, Hidalgo	3
                Municipio de Tepehuacán de Guerrero, Hidalgo	3
                Municipio de Coacalco de Berriozábal, Estado de México	3
                Municipio de Coatepec Harinas, Estado de México	3
                Municipio de Metepec, Estado de México	3
                Municipio de Tultepec, Estado de México	3
                Municipio de San Pedro Mixtepec, Distrito de Juquila, Oaxaca	3
                Municipio de Villa de Zaachila, Oaxaca	3
                Municipio de Chilchotla, Puebla	3
                Municipio de San Martín Texmelucan, Puebla	3
                Municipio de Tampacán, San Luis Potosí	3
                Municipio de Nanacamilpa de Mariano Arista, Tlaxcala	3
                Municipio de Jalacingo, Veracruz de Ignacio de la Llave	3
                Municipio de Nautla, Veracruz de Ignacio de la Llave	3
                Municipio de Santiago Tuxtla, Veracruz de Ignacio de la Llave	3
                Municipio de Uxpanapa, Veracruz de Ignacio de la Llave	3
                Municipio de Espita, Yucatán	3
                Municipio de Halachó, Yucatán	3
                Municipio de Progreso, Yucatán	3
                Comisión del Agua del Estado de México	3
                Secretaría de Hacienda y Crédito Público	2
                Secretaría de Economía	2
                Secretaría de Cultura	2
                Universidad Autónoma de Sinaloa	2
                Universidad Autónoma de Querétaro	2
                Universidad Autónoma de Tamaulipas	2
                Gobierno del Estado de Sinaloa	2
                Municipio de Dzitbalché, Campeche	2
                Municipio de Francisco I. Madero, Coahuila de Zaragoza	2
                Municipio de Frontera, Coahuila de Zaragoza	2
                Municipio de Lamadrid, Coahuila de Zaragoza	2
                Municipio de Monclova, Coahuila de Zaragoza	2
                Municipio de Armería, Colima	2
                Municipio de Chamula, Chiapas	2
                Municipio de Tuxtla Gutiérrez, Chiapas	2
                Municipio de Villa Corzo, Chiapas	2
                Municipio de Batopilas de Manuel Gómez Morín, Chihuahua	2
                Municipio de Guachochi, Chihuahua	2
                Municipio de Guerrero, Chihuahua	2
                Alcaldía Azcapotzalco, Ciudad de México	2
                Alcaldía Coyoacán, Ciudad de México	2
                Alcaldía Cuajimalpa de Morelos, Ciudad de México	2
                Alcaldía Iztacalco, Ciudad de México	2
                Alcaldía Álvaro Obregón, Ciudad de México	2
                Alcaldía Benito Juárez, Ciudad de México	2
                Alcaldía Miguel Hidalgo, Ciudad de México	2
                Municipio de Durango, Durango	2
                Municipio de San Dimas, Durango	2
                Municipio de Guanajuato, Guanajuato	2
                Municipio de San Francisco del Rincón, Guanajuato	2
                Municipio de San Luis de la Paz, Guanajuato	2
                Municipio de Zihuatanejo de Azueta, Guerrero	2
                Municipio de Olinalá, Guerrero	2
                Municipio de Tixtla de Guerrero, Guerrero	2
                Municipio de Ixmiquilpan, Hidalgo	2
                Municipio de Tonalá, Jalisco	2
                Municipio de Atlacomulco, Estado de México	2
                Municipio de Tepotzotlán, Estado de México	2
                Municipio de Los Reyes, Michoacán de Ocampo	2
                Municipio de Tarímbaro, Michoacán de Ocampo	2
                Municipio de Emiliano Zapata, Morelos	2
                Municipio de Jonacatepec de Leandro Valle, Morelos	2
                Municipio de Tepoztlán, Morelos	2
                Municipio de Totolapan, Morelos	2
                Municipio de Yautepec, Morelos	2
                Municipio de Yecapixtla, Morelos	2
                Municipio de Huajicori, Nayarit	2
                Municipio de Ixtlán del Río, Nayarit	2
                Municipio de Santa María del Oro, Nayarit	2
                Municipio de Tuxpan, Nayarit	2
                Municipio de Galeana, Nuevo León	2
                Municipio de Hualahuises, Nuevo León	2
                Municipio de Linares, Nuevo León	2
                Municipio de Monterrey, Nuevo León	2
                Municipio de Los Ramones, Nuevo León	2
                Municipio de Sabinas Hidalgo, Nuevo León	2
                Municipio de Acatlán de Pérez Figueroa, Oaxaca	2
                Municipio de Santo Domingo Tehuantepec, Oaxaca	2
                Municipio de Acatzingo, Puebla	2
                Municipio de Amealco de Bonfil, Querétaro	2
                Municipio de Ezequiel Montes, Querétaro	2
                Municipio de Huimilpan, Querétaro	2
                Municipio de Pedro Escobedo, Querétaro	2
                Municipio de San Joaquín, Querétaro	2
                Municipio de Tequisquiapan, Querétaro	2
                Municipio de Lázaro Cárdenas, Quintana Roo	2
                Municipio de Matehuala, San Luis Potosí	2
                Municipio de Tamuín, San Luis Potosí	2
                Municipio de Elota, Sinaloa	2
                Municipio de Mazatlán, Sinaloa	2
                Municipio de Centla, Tabasco	2
                Municipio de Comalcalco, Tabasco	2
                Municipio de Jalpa de Méndez, Tabasco	2
                Municipio de Nacajuca, Tabasco	2
                Municipio de Matamoros, Tamaulipas	2
                Municipio de Tetla de la Solidaridad, Tlaxcala	2
                Municipio de Camerino Z. Mendoza, Veracruz de Ignacio de la Llave	2
                Municipio de Catemaco, Veracruz de Ignacio de la Llave	2
                Municipio de Cosoleacaque, Veracruz de Ignacio de la Llave	2
                Municipio de Emiliano Zapata, Veracruz de Ignacio de la Llave	2
                Municipio de Isla, Veracruz de Ignacio de la Llave	2
                Municipio de Pánuco, Veracruz de Ignacio de la Llave	2
                Municipio de San Andrés Tuxtla, Veracruz de Ignacio de la Llave	2
                Municipio de Tantoyuca, Veracruz de Ignacio de la Llave	2
                Municipio de Tezonapa, Veracruz de Ignacio de la Llave	2
                Municipio de San Rafael, Veracruz de Ignacio de la Llave	2
                Secretaría Ejecutiva del Sistema Nacional de Protección Integral de Niñas, Niños y Adolescentes	2
                Consejo Nacional para Prevenir la Discriminación	2
                Hospital General "Dr. Manuel Gea González"	2
                Universidad Autónoma de Baja California	2
                Universidad Veracruzana	2
                Universidad Autónoma de Baja California Sur	2
                Universidad Autónoma de Tlaxcala	2
                Universidad Tecnológica de Aguascalientes	2
                Universidad Tecnológica de Huejotzingo	2
                Universidad Tecnológica de Jalisco	2
                Universidad Tecnológica de la Selva	2
                Universidad Tecnológica de Tecamachalco	2
                Universidad Tecnológica del Valle de Toluca	2
                Universidad Tecnológica Gral. Mariano Escobedo	2
                Universidad Tecnológica Metropolitana	2
                Universidad Intercultural del Estado de Guerrero	2
                Instituto Tecnológico Superior de Centla	2
                Instituto Tecnológico Superior de Cintalapa	2
                Instituto Tecnológico Superior de Ciudad Constitución	2
                Instituto Tecnológico Superior de Huichapan	2
                Instituto Tecnológico Superior de Nuevo Casas Grandes	2
                Instituto Tecnológico Superior del Sur del Estado de Yucatán	2
                Instituto Tecnológico Superior de Tantoyuca	2
                Instituto Tecnológico Superior de Teziutlán	2
                Instituto Tecnológico Superior de Zacapoaxtla	2
                Instituto Tecnológico Superior de Zamora Michoacán	2
                Tecnológico de Estudios Superiores de Ixtapaluca	2
                Universidad Autónoma de la Ciudad de México	2
                Universidad Autónoma de Nuevo León	2
                Instituto de Administración y Avalúos de Bienes Nacionales	2
                Municipio de Ensenada, Baja California	2
                Municipio de Playas de Rosarito, Baja California	2
                Municipio de Hopelchén, Campeche	2
                Municipio de Tenabo, Campeche	2
                Municipio de Allende, Coahuila de Zaragoza	2
                Municipio de Escobedo, Coahuila de Zaragoza	2
                Municipio de Morelos, Coahuila de Zaragoza	2
                Municipio de Viesca, Coahuila de Zaragoza	2
                Municipio de Villa Unión, Coahuila de Zaragoza	2
                Municipio de Zaragoza, Coahuila de Zaragoza	2
                Municipio de Tecomán, Colima	2
                Municipio de Altamirano, Chiapas	2
                Municipio de Frontera Comalapa, Chiapas	2
                Municipio de Ocozocoautla de Espinosa, Chiapas	2
                Municipio de Aquiles Serdán, Chihuahua	2
                Municipio de Buenaventura, Chihuahua	2
                Municipio de Janos, Chihuahua	2
                Municipio de Celaya, Guanajuato	2
                Municipio de Manuel Doblado, Guanajuato	2
                Municipio de Comonfort, Guanajuato	2
                Municipio de Dolores Hidalgo Cuna de la Independencia Nacional, Guanajuato	2
                Municipio de Jerécuaro, Guanajuato	2
                Municipio de Moroleón, Guanajuato	2
                Municipio de San José Iturbide, Guanajuato	2
                Municipio de Tarandacuao, Guanajuato	2
                Municipio de Valle de Santiago, Guanajuato	2
                Municipio de Victoria, Guanajuato	2
                Municipio de Villagrán, Guanajuato	2
                Municipio de Tecoanapa, Guerrero	2
                Municipio de Santiago Tulantepec de Lugo Guerrero, Hidalgo	2
                Municipio de Zimapán, Hidalgo	2
                Municipio de Hostotipaquillo, Jalisco	2
                Municipio de Juchitlán, Jalisco	2
                Municipio de Lagos de Moreno, Jalisco	2
                Municipio de Mascota, Jalisco	2
                Municipio de Ocotlán, Jalisco	2
                Municipio de Poncitlán, Jalisco	2
                Municipio de Tala, Jalisco	2
                Municipio de Atizapán, Estado de México	2
                Municipio de Chimalhuacán, Estado de México	2
                Municipio de Temascalcingo, Estado de México	2
                Municipio de Teoloyucan, Estado de México	2
                Municipio de Tultitlán, Estado de México	2
                Municipio de Villa de Allende, Estado de México	2
                Municipio de Villa Guerrero, Estado de México	2
                Municipio de Zinacantepec, Estado de México	2
                Municipio de Valle de Chalco Solidaridad, Estado de México	2
                Municipio de Apatzingán, Michoacán de Ocampo	2
                Municipio de Ocampo, Michoacán de Ocampo	2
                Municipio de Tancítaro, Michoacán de Ocampo	2
                Municipio de Turicato, Michoacán de Ocampo	2
                Municipio de Zamora, Michoacán de Ocampo	2
                Municipio de Zitácuaro, Michoacán de Ocampo	2
                Municipio de Atlatlahucan, Morelos	2
                Municipio de Huitzilac, Morelos	2
                Municipio de Ocuituco, Morelos	2
                Municipio de Tetela del Volcán, Morelos	2
                Municipio de Tlalnepantla, Morelos	2
                Municipio de Tlaquiltenango, Morelos	2
                Municipio de Tlayacapan, Morelos	2
                Municipio de Zacatepec, Morelos	2
                Municipio de Zacualpan de Amilpas, Morelos	2
                Municipio de Guadalupe, Nuevo León	2
                Municipio de Heroica Ciudad de Huajuapan de León, Oaxaca	2
                Municipio de San Jacinto Amilpas, Oaxaca	2
                Municipio de San Pablo Etla, Oaxaca	2
                Municipio de San Pedro Ixtlahuaca, Oaxaca	2
                Municipio de Santa María Zacatepec, Oaxaca	2
                Municipio de Acajete, Puebla	2
                Municipio de Altepexi, Puebla	2
                Municipio de Cuautlancingo, Puebla	2
                Municipio de Chiautzingo, Puebla	2
                Municipio de Huaquechula, Puebla	2
                Municipio de Nopalucan, Puebla	2
                Municipio de Jalpan de Serra, Querétaro	2
                Municipio de El Marqués, Querétaro	2
                Municipio de Benito Juárez, Quintana Roo	2
                Municipio de Ciudad Valles, San Luis Potosí	2
                Municipio de Tanquián de Escobedo, San Luis Potosí	2
                Municipio de Guasave, Sinaloa	2
                Municipio de Navojoa, Sonora	2
                Municipio de Cárdenas, Tabasco	2
                Municipio de Tula, Tamaulipas	2
                Municipio de Apizaco, Tlaxcala	2
                Municipio de Panotla, Tlaxcala	2
                Municipio de Actopan, Veracruz de Ignacio de la Llave	2
                Municipio de Alpatláhuac, Veracruz de Ignacio de la Llave	2
                Municipio de Coatzintla, Veracruz de Ignacio de la Llave	2
                Municipio de Coscomatepec, Veracruz de Ignacio de la Llave	2
                Municipio de Chicontepec, Veracruz de Ignacio de la Llave	2
                Municipio de Jáltipan, Veracruz de Ignacio de la Llave	2
                Municipio de Nogales, Veracruz de Ignacio de la Llave	2
                Municipio de Paso de Ovejas, Veracruz de Ignacio de la Llave	2
                Municipio de Playa Vicente, Veracruz de Ignacio de la Llave	2
                Municipio de Tempoal, Veracruz de Ignacio de la Llave	2
                Municipio de Tlapacoyan, Veracruz de Ignacio de la Llave	2
                Municipio de Tuxpan, Veracruz de Ignacio de la Llave	2
                Municipio de Zongolica, Veracruz de Ignacio de la Llave	2
                Municipio de Kanasín, Yucatán	2
                Municipio de Tixcacalcupul, Yucatán	2
                Municipio de Guadalupe, Zacatecas	2
                Municipio de Jalpa, Zacatecas	2
                Poder Judicial del Estado de Coahuila de Zaragoza	2
                Consejo de la Judicatura Federal	1
                Secretaría de Agricultura y Desarrollo Rural	1
                Secretaría de Medio Ambiente y Recursos Naturales	1
                Instituto Nacional de Ciencias Médicas y Nutrición Salvador Zubirán	1
                Comisión Ejecutiva de Atención a Víctimas	1
                Gobierno del Estado de Querétaro	1
                Gobierno del Estado de Quintana Roo	1
                Municipio de Calvillo, Aguascalientes	1
                Municipio de Cosío, Aguascalientes	1
                Municipio de Pabellón de Arteaga, Aguascalientes	1
                Municipio de Tepezalá, Aguascalientes	1
                Municipio de La Paz, Baja California Sur	1
                Municipio de Calkiní, Campeche	1
                Municipio de Carmen, Campeche	1
                Municipio de Champotón, Campeche	1
                Municipio de Hecelchakán, Campeche	1
                Municipio de Palizada, Campeche	1
                Municipio de Escárcega, Campeche	1
                Municipio de Seybaplaya, Campeche	1
                Municipio de Acuña, Coahuila de Zaragoza	1
                Municipio de Candela, Coahuila de Zaragoza	1
                Municipio de Nava, Coahuila de Zaragoza	1
                Municipio de Parras, Coahuila de Zaragoza	1
                Municipio de Sabinas, Coahuila de Zaragoza	1
                Municipio de Saltillo, Coahuila de Zaragoza	1
                Municipio de San Pedro, Coahuila de Zaragoza	1
                Municipio de Coquimatlán, Colima	1
                Municipio de Ixtlahuacán, Colima	1
                Municipio de Minatitlán, Colima	1
                Municipio de Villa de Álvarez, Colima	1
                Municipio de Comitán de Domínguez, Chiapas	1
                Municipio de Palenque, Chiapas	1
                Municipio de Cuauhtémoc, Chihuahua	1
                Municipio de Chihuahua, Chihuahua	1
                Municipio de Hidalgo del Parral, Chihuahua	1
                Municipio de Madera, Chihuahua	1
                Municipio de Meoqui, Chihuahua	1
                Alcaldía Cuauhtémoc, Ciudad de México	1
                Alcaldía Venustiano Carranza, Ciudad de México	1
                Municipio de Cuencamé, Durango	1
                Municipio de Mapimí, Durango	1
                Municipio de Mezquital, Durango	1
                Municipio de Pueblo Nuevo, Durango	1
                Municipio de Santiago Papasquiaro, Durango	1
                Municipio de Tamazula, Durango	1
                Municipio de Vicente Guerrero, Durango	1
                Municipio de Acámbaro, Guanajuato	1
                Municipio de San Miguel de Allende, Guanajuato	1
                Municipio de Apaseo el Grande, Guanajuato	1
                Municipio de Romita, Guanajuato	1
                Municipio de Salamanca, Guanajuato	1
                Municipio de Salvatierra, Guanajuato	1
                Municipio de Uriangato, Guanajuato	1
                Municipio de Acapulco de Juárez, Guerrero	1
                Municipio de Arcelia, Guerrero	1
                Municipio de Atlixtac, Guerrero	1
                Municipio de Chilapa de Álvarez, Guerrero	1
                Municipio de General Heliodoro Castillo, Guerrero	1
                Municipio de Iguala de la Independencia, Guerrero	1
                Municipio de Ometepec, Guerrero	1
                Municipio de Petatlán, Guerrero	1
                Municipio de Tecpan de Galeana, Guerrero	1
                Municipio de Teloloapan, Guerrero	1
                Municipio de Actopan, Hidalgo	1
                Municipio de Nopala de Villagrán, Hidalgo	1
                Municipio de Tepeji del Río de Ocampo, Hidalgo	1
                Municipio de Tezontepec de Aldama, Hidalgo	1
                Municipio de Tizayuca, Hidalgo	1
                Municipio de Tula de Allende, Hidalgo	1
                Municipio de Ixtlahuacán de los Membrillos, Jalisco	1
                Municipio de Tapalpa, Jalisco	1
                Municipio de Tepatitlán de Morelos, Jalisco	1
                Municipio de Tequila, Jalisco	1
                Municipio de Tomatlán, Jalisco	1
                Municipio de Acolman, Estado de México	1
                Municipio de Atizapán de Zaragoza, Estado de México	1
                Municipio de Cuautitlán, Estado de México	1
                Municipio de Ecatepec de Morelos, Estado de México	1
                Municipio de Huixquilucan, Estado de México	1
                Municipio de Nicolás Romero, Estado de México	1
                Municipio de Otzolotepec, Estado de México	1
                Municipio de La Paz, Estado de México	1
                Municipio de San Felipe del Progreso, Estado de México	1
                Municipio de Tenango del Valle, Estado de México	1
                Municipio de Teotihuacán, Estado de México	1
                Municipio de Tianguistenco, Estado de México	1
                Municipio de Zumpango, Estado de México	1
                Municipio de San José del Rincón, Estado de México	1
                Municipio de Nahuatzen, Michoacán de Ocampo	1
                Municipio de Puruándiro, Michoacán de Ocampo	1
                Municipio de Sahuayo, Michoacán de Ocampo	1
                Municipio de Uruapan, Michoacán de Ocampo	1
                Municipio de Ayala, Morelos	1
                Municipio de Cuernavaca, Morelos	1
                Municipio de Puente de Ixtla, Morelos	1
                Municipio de Temixco, Morelos	1
                Municipio de Tepalcingo, Morelos	1
                Municipio de Tlaltizapán de Zapata, Morelos	1
                Municipio de Acaponeta, Nayarit	1
                Municipio de Amatlán de Cañas, Nayarit	1
                Municipio de Xalisco, Nayarit	1
                Municipio de Rosamorada, Nayarit	1
                Municipio de Ruiz, Nayarit	1
                Municipio de San Blas, Nayarit	1
                Municipio de Santiago Ixcuintla, Nayarit	1
                Municipio de Tecuala, Nayarit	1
                Municipio de Tepic, Nayarit	1
                Municipio de La Yesca, Nayarit	1
                Municipio de Abasolo, Nuevo León	1
                Municipio de Agualeguas, Nuevo León	1
                Municipio de Los Aldamas, Nuevo León	1
                Municipio de Allende, Nuevo León	1
                Municipio de Aramberri, Nuevo León	1
                Municipio de Bustamante, Nuevo León	1
                Municipio de Cadereyta Jiménez, Nuevo León	1
                Municipio de El Carmen, Nuevo León	1
                Municipio de Cerralvo, Nuevo León	1
                Municipio de Ciénega de Flores, Nuevo León	1
                Municipio de China, Nuevo León	1
                Municipio de Doctor Coss, Nuevo León	1
                Municipio de Doctor González, Nuevo León	1
                Municipio de García, Nuevo León	1
                Municipio de General Bravo, Nuevo León	1
                Municipio de General Terán, Nuevo León	1
                Municipio de General Treviño, Nuevo León	1
                Municipio de General Zaragoza, Nuevo León	1
                Municipio de General Zuazua, Nuevo León	1
                Municipio de Los Herreras, Nuevo León	1
                Municipio de Higueras, Nuevo León	1
                Municipio de Iturbide, Nuevo León	1
                Municipio de Lampazos de Naranjo, Nuevo León	1
                Municipio de Marín, Nuevo León	1
                Municipio de Melchor Ocampo, Nuevo León	1
                Municipio de Mier y Noriega, Nuevo León	1
                Municipio de Mina, Nuevo León	1
                Municipio de Montemorelos, Nuevo León	1
                Municipio de Pesquería, Nuevo León	1
                Municipio de Rayones, Nuevo León	1
                Municipio de Salinas Victoria, Nuevo León	1
                Municipio de San Nicolás de los Garza, Nuevo León	1
                Municipio de Hidalgo, Nuevo León	1
                Municipio de Santa Catarina, Nuevo León	1
                Municipio de Villaldama, Nuevo León	1
                Municipio de Asunción Nochixtlán, Oaxaca	1
                Municipio de Huautla de Jiménez, Oaxaca	1
                Municipio de Miahuatlán de Porfirio Díaz, Oaxaca	1
                Municipio de San Felipe Jalapa de Díaz, Oaxaca	1
                Municipio de San Juan Guichicovi, Oaxaca	1
                Municipio de Santa Cruz Xoxocotlán, Oaxaca	1
                Municipio de Heroica Ciudad de Tlaxiaco, Oaxaca	1
                Municipio de Santa María Colotepec, Oaxaca	1
                Municipio de Santa María Jalapa del Marqués, Oaxaca	1
                Municipio de Santa María Tonameca, Oaxaca	1
                Municipio de Santiago Pinotepa Nacional, Oaxaca	1
                Municipio de Cuetzalan del Progreso, Puebla	1
                Municipio de Chichiquila, Puebla	1
                Municipio de Chignautla, Puebla	1
                Municipio de Huauchinango, Puebla	1
                Municipio de Huehuetla, Puebla	1
                Municipio de Libres, Puebla	1
                Municipio de Ocoyucan, Puebla	1
                Municipio de Quecholac, Puebla	1
                Municipio de San Pedro Cholula, Puebla	1
                Municipio de Tecamachalco, Puebla	1
                Municipio de Tlatlauquitepec, Puebla	1
                Municipio de Vicente Guerrero, Puebla	1
                Municipio de Xiutetelco, Puebla	1
                Municipio de Pinal de Amoles, Querétaro	1
                Municipio de Arroyo Seco, Querétaro	1
                Municipio de Cadereyta de Montes, Querétaro	1
                Municipio de Peñamiller, Querétaro	1
                Municipio de San Juan del Río, Querétaro	1
                Municipio de Tolimán, Querétaro	1
                Municipio de Othón P. Blanco, Quintana Roo	1
                Municipio de José María Morelos, Quintana Roo	1
                Municipio de Ebano, San Luis Potosí	1
                Municipio de Tamasopo, San Luis Potosí	1
                Municipio de Ahome, Sinaloa	1
                Municipio de Angostura, Sinaloa	1
                Municipio de Culiacán, Sinaloa	1
                Municipio de Choix, Sinaloa	1
                Municipio de Mocorito, Sinaloa	1
                Municipio de Salvador Alvarado, Sinaloa	1
                Municipio de Sinaloa, Sinaloa	1
                Municipio de Navolato, Sinaloa	1
                Municipio de Hermosillo, Sonora	1
                Municipio de Balancán, Tabasco	1
                Municipio de Centro, Tabasco	1
                Municipio de Emiliano Zapata, Tabasco	1
                Municipio de Jalapa, Tabasco	1
                Municipio de Jonuta, Tabasco	1
                Municipio de Paraíso, Tabasco	1
                Municipio de Tacotalpa, Tabasco	1
                Municipio de Teapa, Tabasco	1
                Municipio de Tenosique, Tabasco	1
                Municipio de Aldama, Tamaulipas	1
                Municipio de El Mante, Tamaulipas	1
                Municipio de Reynosa, Tamaulipas	1
                Municipio de Victoria, Tamaulipas	1
                Municipio de Natívitas, Tlaxcala	1
                Municipio de Tlaxcala, Tlaxcala	1
                Municipio de Tlaxco, Tlaxcala	1
                Municipio de Zacatelco, Tlaxcala	1
                Municipio de Amatlán de los Reyes, Veracruz de Ignacio de la Llave	1
                Municipio de Banderilla, Veracruz de Ignacio de la Llave	1
                Municipio de Cazones de Herrera, Veracruz de Ignacio de la Llave	1
                Municipio de Ixtaczoquitlán, Veracruz de Ignacio de la Llave	1
                Municipio de Medellín de Bravo, Veracruz de Ignacio de la Llave	1
                Municipio de Minatitlán, Veracruz de Ignacio de la Llave	1
                Municipio de Papantla, Veracruz de Ignacio de la Llave	1
                Municipio de Poza Rica de Hidalgo, Veracruz de Ignacio de la Llave	1
                Municipio de Río Blanco, Veracruz de Ignacio de la Llave	1
                Municipio de Tenochtitlán, Veracruz de Ignacio de la Llave	1
                Municipio de Agua Dulce, Veracruz de Ignacio de la Llave	1
                Municipio de Mérida, Yucatán	1
                Municipio de Temozón, Yucatán	1
                Municipio de General Francisco R. Murguía, Zacatecas	1
                Municipio de Miguel Auza, Zacatecas	1
                Municipio de Pinos, Zacatecas	1
                Municipio de Río Grande, Zacatecas	1
                Municipio de Tapachula, Chiapas	1
                Centro Regional de Alta Especialidad de Chiapas	1
                Instituto Tecnológico de Sonora	1
                Universidad Autónoma de Zacatecas "Francisco García Salinas"	1
                Universidad de Guanajuato	1
                Universidad Autónoma del Estado de México	1
                Universidad Autónoma de Coahuila	1
                Universidad de Colima	1
                Universidad Autónoma del Estado de Morelos	1
                Universidad Politécnica de San Luis Potosí	1
                Universidad Autónoma del Estado de Quintana Roo	1
                Universidad Politécnica de Sinaloa	1
                Universidad Juárez Autónoma de Tabasco	1
                Universidad Autónoma de Chihuahua	1
                Universidad Tecnológica de Cancún	1
                Universidad Tecnológica de Hermosillo, Sonora	1
                Universidad Tecnológica de Nayarit	1
                Universidad Tecnológica de Nezahualcóyotl	1
                Universidad Tecnológica del Centro de Veracruz	1
                Instituto Tecnológico Superior de Cajeme	1
                Instituto Tecnológico Superior de Coatzacoalcos	1
                Instituto Tecnológico Superior de Felipe Carrillo Puerto	1
                Instituto Tecnológico Superior de Huauchinango	1
                Instituto Tecnológico Superior de la Región Sierra	1
                Instituto Tecnológico Superior de Macuspana	1
                Tecnológico de Estudios Superiores de Tianguistenco	1
                Instituto Tecnológico Superior de Tlaxco	1
                Instituto Tecnológico Superior de Valladolid	1
                Instituto Tecnológico Superior de Villa La Venta	1
                Tecnológico de Estudios Superiores de Cuautitlán Izcalli	1
                Tecnológico de Estudios Superiores de Jocotitlán	1
                Comisión Nacional para el Uso Eficiente de la Energía	1
                Municipio de Asientos, Aguascalientes	1
                Municipio de San José de Gracia, Aguascalientes	1
                Municipio de San Francisco de los Romo, Aguascalientes	1
                Municipio de Mexicali, Baja California	1
                Municipio de Tecate, Baja California	1
                Municipio de Loreto, Baja California Sur	1
                Municipio de Abasolo, Coahuila de Zaragoza	1
                Municipio de General Cepeda, Coahuila de Zaragoza	1
                Municipio de Guerrero, Coahuila de Zaragoza	1
                Municipio de Hidalgo, Coahuila de Zaragoza	1
                Municipio de Jiménez, Coahuila de Zaragoza	1
                Municipio de Nadadores, Coahuila de Zaragoza	1
                Municipio de Ocampo, Coahuila de Zaragoza	1
                Municipio de Piedras Negras, Coahuila de Zaragoza	1
                Municipio de Progreso, Coahuila de Zaragoza	1
                Municipio de Sacramento, Coahuila de Zaragoza	1
                Municipio de San Buenaventura, Coahuila de Zaragoza	1
                Municipio de San Juan de Sabinas, Coahuila de Zaragoza	1
                Municipio de Sierra Mojada, Coahuila de Zaragoza	1
                Municipio de Colima, Colima	1
                Municipio de Comala, Colima	1
                Municipio de Cuauhtémoc, Colima	1
                Municipio de Arriaga, Chiapas	1
                Municipio de Chiapa de Corzo, Chiapas	1
                Municipio de Mapastepec, Chiapas	1
                Municipio de Tonalá, Chiapas	1
                Municipio de San Juan Cancuc, Chiapas	1
                Municipio de Aldama, Chihuahua	1
                Municipio de Ascensión, Chihuahua	1
                Municipio de Delicias, Chihuahua	1
                Municipio de Jiménez, Chihuahua	1
                Municipio de Nuevo Casas Grandes, Chihuahua	1
                Municipio de Ojinaga, Chihuahua	1
                Municipio de Saucillo, Chihuahua	1
                Municipio de Guadalupe Victoria, Durango	1
                Municipio de Nombre de Dios, Durango	1
                Municipio de Pánuco de Coronado, Durango	1
                Municipio de Tlahualilo, Durango	1
                Municipio de Topia, Durango	1
                Municipio de Atarjea, Guanajuato	1
                Municipio de Coroneo, Guanajuato	1
                Municipio de Cortazar, Guanajuato	1
                Municipio de Cuerámaro, Guanajuato	1
                Municipio de Doctor Mora, Guanajuato	1
                Municipio de Huanímaro, Guanajuato	1
                Municipio de Jaral del Progreso, Guanajuato	1
                Municipio de Ocampo, Guanajuato	1
                Municipio de Pueblo Nuevo, Guanajuato	1
                Municipio de Purísima del Rincón, Guanajuato	1
                Municipio de San Diego de la Unión, Guanajuato	1
                Municipio de Santa Catarina, Guanajuato	1
                Municipio de Santiago Maravatío, Guanajuato	1
                Municipio de Tarimoro, Guanajuato	1
                Municipio de Tierra Blanca, Guanajuato	1
                Municipio de Xichú, Guanajuato	1
                Municipio de Alcozauca de Guerrero, Guerrero	1
                Municipio de Cuajinicuilapa, Guerrero	1
                Municipio de Florencio Villarreal, Guerrero	1
                Municipio de Huitzuco de los Figueroa, Guerrero	1
                Municipio de Leonardo Bravo, Guerrero	1
                Municipio de Mártir de Cuilapan, Guerrero	1
                Municipio de Pungarabato, Guerrero	1
                Municipio de Quechultenango, Guerrero	1
                Municipio de San Miguel Totolapan, Guerrero	1
                Municipio de Apan, Hidalgo	1
                Municipio de Atitalaquia, Hidalgo	1
                Municipio de Atotonilco de Tula, Hidalgo	1
                Municipio de Francisco I. Madero, Hidalgo	1
                Municipio de Huichapan, Hidalgo	1
                Municipio de Pachuca de Soto, Hidalgo	1
                Municipio de Progreso de Obregón, Hidalgo	1
                Municipio de Mineral de la Reforma, Hidalgo	1
                Municipio de Tepeapulco, Hidalgo	1
                Municipio de Tlaxcoapan, Hidalgo	1
                Municipio de Tulancingo de Bravo, Hidalgo	1
                Municipio de Zacualtipán de Ángeles, Hidalgo	1
                Municipio de Zempoala, Hidalgo	1
                Municipio de Acatlán de Juárez, Jalisco	1
                Municipio de Ameca, Jalisco	1
                Municipio de Arandas, Jalisco	1
                Municipio de El Arenal, Jalisco	1
                Municipio de La Barca, Jalisco	1
                Municipio de Cihuatlán, Jalisco	1
                Municipio de Zapotlán el Grande, Jalisco	1
                Municipio de Cocula, Jalisco	1
                Municipio de Cuautitlán de García Barragán, Jalisco	1
                Municipio de Jalostotitlán, Jalisco	1
                Municipio de Quitupan, Jalisco	1
                Municipio de San Juan de los Lagos, Jalisco	1
                Municipio de Gómez Farías, Jalisco	1
                Municipio de Sayula, Jalisco	1
                Municipio de Zapopan, Jalisco	1
                Municipio de Zapotiltic, Jalisco	1
                Municipio de Zapotlán del Rey, Jalisco	1
                Municipio de Amatepec, Estado de México	1
                Municipio de Amecameca, Estado de México	1
                Municipio de Chiconcuac, Estado de México	1
                Municipio de Ixtlahuaca, Estado de México	1
                Municipio de Jilotepec, Estado de México	1
                Municipio de Lerma, Estado de México	1
                Municipio de Ocuilan, Estado de México	1
                Municipio de Temoaya, Estado de México	1
                Municipio de Tenancingo, Estado de México	1
                Municipio de Cuautitlán Izcalli, Estado de México	1
                Municipio de Ario, Michoacán de Ocampo	1
                Municipio de Buenavista, Michoacán de Ocampo	1
                Municipio de Contepec, Michoacán de Ocampo	1
                Municipio de Cuitzeo, Michoacán de Ocampo	1
                Municipio de Jiquilpan, Michoacán de Ocampo	1
                Municipio de Nuevo Parangaricutiro, Michoacán de Ocampo	1
                Municipio de Paracho, Michoacán de Ocampo	1
                Municipio de Salvador Escalante, Michoacán de Ocampo	1
                Municipio de Senguio, Michoacán de Ocampo	1
                Municipio de Tepalcatepec, Michoacán de Ocampo	1
                Municipio de Tuxpan, Michoacán de Ocampo	1
                Municipio de Yurécuaro, Michoacán de Ocampo	1
                Municipio de Zinapécuaro, Michoacán de Ocampo	1
                Municipio de Amacuzac, Morelos	1
                Municipio de Coatlán del Río, Morelos	1
                Municipio de Mazatepec, Morelos	1
                Municipio de Miacatlán, Morelos	1
                Municipio de Tetecala, Morelos	1
                Municipio de Temoac, Morelos	1
                Municipio de Coatetelco, Morelos	1
                Municipio de Compostela, Nayarit	1
                Municipio de Bahía de Banderas, Nayarit	1
                Municipio de General Escobedo, Nuevo León	1
                Municipio de Cosolapa, Oaxaca	1
                Municipio de Putla Villa de Guerrero, Oaxaca	1
                Municipio de Salina Cruz, Oaxaca	1
                Municipio de San Agustín Loxicha, Oaxaca	1
                Municipio de San Felipe Usila, Oaxaca	1
                Municipio de San Martín Peras, Oaxaca	1
                Municipio de San Mateo del Mar, Oaxaca	1
                Municipio de Santa Cruz Amilpas, Oaxaca	1
                Municipio de Santa Lucía del Camino, Oaxaca	1
                Municipio de Santa María Petapa, Oaxaca	1
                Municipio de Santiago Jamiltepec, Oaxaca	1
                Municipio de Santiago Jocotepec, Oaxaca	1
                Municipio de Santos Reyes Nopala, Oaxaca	1
                Municipio de Huejotzingo, Puebla	1
                Municipio de Izúcar de Matamoros, Puebla	1
                Municipio de Juan C. Bonilla, Puebla	1
                Municipio de San Matías Tlalancaleca, Puebla	1
                Municipio de San Salvador El Verde, Puebla	1
                Municipio de Tepeaca, Puebla	1
                Municipio de Zautla, Puebla	1
                Municipio de Colón, Querétaro	1
                Municipio de Corregidora, Querétaro	1
                Municipio de Querétaro, Querétaro	1
                Municipio de Cozumel, Quintana Roo	1
                Municipio de Puerto Morelos, Quintana Roo	1
                Municipio de Tancanhuitz, San Luis Potosí	1
                Municipio de Villa de la Paz, San Luis Potosí	1
                Municipio de Villa de Reyes, San Luis Potosí	1
                Municipio de Cosalá, Sinaloa	1
                Municipio de Escuinapa, Sinaloa	1
                Municipio de Altar, Sonora	1
                Municipio de Arizpe, Sonora	1
                Municipio de Bácum, Sonora	1
                Municipio de Cananea, Sonora	1
                Municipio de Empalme, Sonora	1
                Municipio de Etchojoa, Sonora	1
                Municipio de Magdalena, Sonora	1
                Municipio de Nogales, Sonora	1
                Municipio de San Luis Río Colorado, Sonora	1
                Municipio de San Miguel de Horcasitas, Sonora	1
                Municipio de Santa Ana, Sonora	1
                Municipio de General Plutarco Elías Calles, Sonora	1
                Municipio de San Ignacio Río Muerto, Sonora	1
                Municipio de Cunduacán, Tabasco	1
                Municipio de Macuspana, Tabasco	1
                Municipio de Bustamante, Tamaulipas	1
                Municipio de Ocampo, Tamaulipas	1
                Municipio de Río Bravo, Tamaulipas	1
                Municipio de San Fernando, Tamaulipas	1
                Municipio de Ixtacuixtla de Mariano Matamoros, Tlaxcala	1
                Municipio de Tepetitla de Lardizábal, Tlaxcala	1
                Municipio de Acayucan, Veracruz de Ignacio de la Llave	1
                Municipio de Alto Lucero de Gutiérrez Barrios, Veracruz de Ignacio de la Llave	1
                Municipio de Ángel R. Cabada, Veracruz de Ignacio de la Llave	1
                Municipio de Atzalan, Veracruz de Ignacio de la Llave	1
                Municipio de Boca del Río, Veracruz de Ignacio de la Llave	1
                Municipio de Coatepec, Veracruz de Ignacio de la Llave	1
                Municipio de Coatzacoalcos, Veracruz de Ignacio de la Llave	1
                Municipio de Chiconquiaco, Veracruz de Ignacio de la Llave	1
                Municipio de Chontla, Veracruz de Ignacio de la Llave	1
                Municipio de Gutiérrez Zamora, Veracruz de Ignacio de la Llave	1
                Municipio de Huatusco, Veracruz de Ignacio de la Llave	1
                Municipio de Hueyapan de Ocampo, Veracruz de Ignacio de la Llave	1
                Municipio de Ixhuacán de los Reyes, Veracruz de Ignacio de la Llave	1
                Municipio de Xico, Veracruz de Ignacio de la Llave	1
                Municipio de Juan Rodríguez Clara, Veracruz de Ignacio de la Llave	1
                Municipio de Mariano Escobedo, Veracruz de Ignacio de la Llave	1
                Municipio de Martínez de la Torre, Veracruz de Ignacio de la Llave	1
                Municipio de Orizaba, Veracruz de Ignacio de la Llave	1
                Municipio de Ozuluama de Mascareñas, Veracruz de Ignacio de la Llave	1
                Municipio de Perote, Veracruz de Ignacio de la Llave	1
                Municipio de Pueblo Viejo, Veracruz de Ignacio de la Llave	1
                Municipio de Soteapan, Veracruz de Ignacio de la Llave	1
                Municipio de Tantima, Veracruz de Ignacio de la Llave	1
                Municipio de Tlalixcoyan, Veracruz de Ignacio de la Llave	1
                Municipio de Tlilapan, Veracruz de Ignacio de la Llave	1
                Municipio de Tonayán, Veracruz de Ignacio de la Llave	1
                Municipio de Tres Valles, Veracruz de Ignacio de la Llave	1
                Municipio de Cacalchén, Yucatán	1
                Municipio de Chichimilá, Yucatán	1
                Municipio de Hunucmá, Yucatán	1
                Municipio de Izamal, Yucatán	1
                Municipio de Kinchil, Yucatán	1
                Municipio de Motul, Yucatán	1
                Municipio de Oxkutzcab, Yucatán	1
                Municipio de Peto, Yucatán	1
                Municipio de Seyé, Yucatán	1
                Municipio de Sotuta, Yucatán	1
                Municipio de Tecoh, Yucatán	1
                Municipio de Tekax, Yucatán	1
                Municipio de Ticul, Yucatán	1
                Municipio de Tixkokob, Yucatán	1
                Municipio de Umán, Yucatán	1
                Municipio de Loreto, Zacatecas	1
                Municipio de Ojocaliente, Zacatecas	1
                Municipio de Tlaltenango de Sánchez Román, Zacatecas	1
                Municipio de Villanueva, Zacatecas	1
                Municipio de Trancoso, Zacatecas	1
                Apartados con mayores observaciones presentadas.
                Gráfico de Cambios en Apartados (Observaciones)
                Apartado	Observaciones
                Documentación soporte con la que se acredita la observación o irregularidad y, en su caso, donde se señale el importe del daño o perjuicio.	554
                Comprobante de domicilio particular.	449
                Carátula del Expediente	431
                Analíticas	385
                Certificación del Expediente	363
                Acuse del Oficio de Solicitud de Información o Documentación Preliminar con anexos.	360
                RFC con homoclave.	360
                Índice del Expediente Técnico	340
                Oficio(s) de respuesta por parte de la EF el cual deberá de tener la fecha de recepción, sello o firma de quien recibe por parte de la ASF.	331
                Documento(s) con el que se acredite el inicio (nombramiento) y, en su caso, conclusión del encargo en la EF, según corresponda (altas o bajas, contrato de prestación de servicios, oficios de designación -ejemplo: Residentes y Supervisores de Obra)	330
                Sumarias	320
                Acuse del Oficio de Notificación de Conclusión de los Trabajos de Auditoría	306
                Presupuesto de Egresos de la Federación, así como Acuerdos, Decretos, Convenios y Cuentas por Liquidar Certificadas, en los que conste que los recursos federales se asignaron y ministraron a la EF, a efecto de identificar la federalidad y asignación de los recursos y que los mismos fueron ministrados, transferidos o asignados a la EF o, en su caso recaudados por esta (de acuerdo con el tipo de auditoría).	266
                Oficio(s) de respuesta por parte de la EF (en su caso) el cual deberá de tener la fecha de recepción, sello o firma de quien recibe por parte de la ASF.	256
                Subanalíticas	253
                Documentación que acredite la erogación de los recursos: Contrato de apertura de la cuenta bancaria, estados de cuenta bancarios de todo el ejercicio fiscal, contratos, convenios, facturas (CFDI), oficios de solicitud de pago o autorizaciones de pago, estimaciones, generadores, bitácoras, finiquitos, actas de entrega-recepción y actas circunstanciadas.	249
                Acuse del Oficio de Solicitud de Información o Documentación Complementaria.	240
                Cédula de análisis de la Información o documentación aclaratoria presentada por la Entidad Fiscalizada.	223
                De emitirse, se agregarán las identificaciones correspondientes	216
                Anexos del Oficio de la Solicitud de Información o Documentación Complementaria.	216
                Otras (como pueden ser la constancia de situación fiscal, CURP).	212
                Cédula Resumen	197
                Documentos que acrediten la forma en que la EF asignó, destinó, ejerció o aplicó los recursos públicos federales de forma irregular o a fines distintos a los establecidos en la normatividad aplicable, según corresponda.	196
                Identificación oficial (INE, pasaporte, licencia).	186
                Acuse del Oficio para dar Aviso a la Entidad Fiscalizada del Aumento, Disminución o Sustitución del Personal Actuante	178
                Acuse del Oficio de Orden de Auditoría con anexos	174
                Oficio por el que se presenta la información o documentación aclaratoria y anexos, el cual deberá tener la fecha de recepción, sello o firma de quien recibe por parte de la ASF.	173
                Oficio(s) de respuesta por parte de la EF (en su caso) el cual deberá de tener la fecha de recepción, sello o firma de quien recibe por parte de la ASF.	160
                Oficio de designación del enlace de la EF el cual deberá de tener la fecha de recepción, sello o firma de quien recibe por parte de la ASF.	158
                Anexo de la solicitud de Información o Documentación (en su caso).	154
                Reportes fotográficos (en su caso)	154
                ARFOP	152
                Actas circunstanciadas, actas de visita de verificación física y, reportes fotográficos o cualquier otra evidencia, de emitirse.	152
                Informe de Auditoría publicado (Carátula y hojas del alcance, del resultado y de la irregularidad completa)	141
                Acuse del oficio por el que se presenta la información o documentación aclaratoria y anexos.	141
                Normativa infringida vigente en la fecha de la comisión de los hechos que motivaron la irregularidad atendiendo a la jerarquía de las disposiciones legales.	123
                Cédula de Resultados Finales (primera hoja de la cédula, las hojas del resultado correspondiente completo y la hoja de las firmas)	118
                Cédulas de trabajo para soportar el incumplimiento normativo del servidor público de la Entidad Fiscalizada que ejerció el recurso, considerando el periodo de gestión	110
                Cédulas de trabajo para soportar el importe del daño o perjuicio del servidor público de la EF que ejerció el gasto, considerando el periodo de gestión.	108
                Oficio(s) de respuesta por parte de la EF (en su caso) el cual deberá de tener la fecha de recepción, sello o firma de quien recibe por parte de la ASF.	88
                Anexo de la solicitud de información que se adjunta a la Orden de Auditoría (en su caso)	81
                AFITA	80
                Anexos del acta integrando las identificaciones del personal designado por parte de la ASF y de la Entidad Fiscalizada	54
                Anexos del acta con las identificaciones del personal designado por parte de la ASF y de la EF.	48
                <span style="color:red">Oficio Justificatorio en caso de no haber sido acordadas las recomendaciones<br><b>*Ya no Aplica</b></span>	48
                Anexo de los mecanismos de atención de la recomendación de que se trate (en su caso).	45
                Expedientes por DG y Usuario (Comparativa)
                DG	Usuario	Total	Porcentaje
                Dirección General de Seguimiento "A"	Total por DG	568	100%
                Daniel Rivas Gallardo	65	11.44%
                Jaime Alberto Saavedra Ibañez	62	10.92%
                Karla González Vargas	49	8.63%
                Miriam Guadalupe García Cervantes	47	8.27%
                Luis Franco López	46	8.10%
                Elisa Oropeza Díaz	45	7.92%
                Anayeli Hernández Valdez	41	7.22%
                Adriana Brigit Ramírez Florentino	34	5.99%
                Jesús Díaz Mojica	30	5.28%
                GERMAN VALDIVIA GARCÍA	18	3.17%
                German Valdivia García	17	2.99%
                Germán Valdivia García	14	2.46%
                Juan Carlos Rendón Cruz	11	1.94%
                Lizbeth Fuentes López	11	1.94%
                Jaqueline Álvarez Chávez	10	1.76%
                Michelle Reyes Sánchez	10	1.76%
                Adriana Brigit Ramirez Florentino	9	1.58%
                Axel David Espadín Pérez	9	1.58%
                José Ángel Magaña de la Cruz	8	1.41%
                Luisa Fernanda Galicia Ortiz	8	1.41%
                Hector Hugo Delgado Garduño	4	0.70%
                Diana Guadalupe Carrera Gutierrez	3	0.53%
                Diana Osmara Mejía Hernández	3	0.53%
                Fernanda Gabriela Olvera Madrid	3	0.53%
                Angelica Murcio Juárez	2	0.35%
                Elisa Oropeza Diaz	2	0.35%
                GERMAN VALDIVIA GARCIA	2	0.35%
                Samuel Velázquez Moreno	2	0.35%
                Cesar Lara Godinez	1	0.18%
                Karla Gonzalez Vargas	1	0.18%
                Valeria Lazaro Carmona	1	0.18%
                Dirección General de Seguimiento "D"	Total por DG	518	100%
                Ricardo Rodríguez Rebollo	68	13.13%
                Jesús Ronquillo Arias	55	10.62%
                Juan Carlos Parral Aricéaga	49	9.46%
                Noemí Escobar de la Rosa	49	9.46%
                Nubia Paola Pérez Durán	43	8.30%
                Romeo de Jesús Roblero González	34	6.56%
                Bárbara Karina Gutiérrez Rodríguez	29	5.60%
                Saúl Nava Arana	19	3.67%
                Estefanía Berenice Silva Hernández	14	2.70%
                José Carlos Lule Bañuelos	12	2.32%
                Brenda Lucero Ibarra Olvera	9	1.74%
                Griselda Flores Ovando	9	1.74%
                Victor Eduardo Corona Cano	9	1.74%
                Mariana Virginia Vazquez Martinez	8	1.54%
                Georgina Torres Oviedo	7	1.35%
                Graciela Guadalupe Silva Amezcua	7	1.35%
                Laura Yesica Camacho Tello	7	1.35%
                Horacio Omar Jiménez Sandoval	6	1.16%
                Javier Rivero Roldán	6	1.16%
                Jesus Ivan Puga Mendez	6	1.16%
                Verónica Raquel Velasco López	6	1.16%
                Barbara Karina Gutiérrez Rodríguez	5	0.97%
                Carmen Haydeé González Delgado	5	0.97%
                Gustavo Rodrigo Martínez Martínez	5	0.97%
                Jesus Ronquillo Arias	5	0.97%
                José Antonio Ceja Murillo	5	0.97%
                Sofia Julieta Villagomez Alcantara	5	0.97%
                Víctor Hugo Fuentes Sánchez	5	0.97%
                Barbara Karina Gutierrez Rodriguez	4	0.77%
                Israel Tenorio de la Cruz	4	0.77%
                Zaira Abigail Santillán Vargas	4	0.77%
                Agnir Sacbe Poot Velez	3	0.58%
                Alfredo Velasco Mendoza	3	0.58%
                Itzae Denisse Frias Mondragon	3	0.58%
                Mariana Virginia Vázquez Martínez	3	0.58%
                Adrián Agustín Velázquez Gómez	2	0.39%
                Alan Alejandro Cabanas Bautista	2	0.39%
                Gustavo Rodrigo Martinez Martinez	1	0.19%
                Jesús Iván Puga Méndez	1	0.19%
                Ramón Flores Fernandez	1	0.19%
                Dirección General de Seguimiento "B"	Total por DG	435	100%
                Claudia Ana Lilian Cruz Sanabria	40	9.20%
                Monserrat Hernández Alarcón	39	8.97%
                Jesé Daniel Victoria Ramírez	37	8.51%
                Magda Karina Morales Bucio	36	8.28%
                Nayeli Tovar Torres	34	7.82%
                Citlalli Aupart Hernández	32	7.36%
                Valeria Valle Pérez	29	6.67%
                Edwin Emmanuel Escorcia Carranza	28	6.44%
                Jesús Cortés Sánchez	28	6.44%
                Martín Yahir Moya Montaño	27	6.21%
                Amairany de Jesús Rodríguez Castorena	19	4.37%
                Amairany de Jesus Rodriguez Castorena	18	4.14%
                Omar Lizcano Gómez	18	4.14%
                Ilse Abigail Méndez Morales	9	2.07%
                Omar Lizcano Gomez	9	2.07%
                Rosaura Hernández Colorado	4	0.92%
                Diana Hernández Aguirre	3	0.69%
                Laurentino Juárez Vargas	3	0.69%
                Luis Enrique López González	3	0.69%
                Olga Lidia López Ramírez	3	0.69%
                Carlos Alberto Gonzalez Santa Cruz	2	0.46%
                Julio Cesar Muñoz Santana	2	0.46%
                Liliana Castillo Islas	2	0.46%
                Paola Rodriguez Galarza	2	0.46%
                Sthefany Aviles Ramírez	2	0.46%
                Sthefany Aviles Ramirez	2	0.46%
                Alma Delia Mendoza Chavero	1	0.23%
                Karen Atabeira Martinez Tellez	1	0.23%
                Mario Alberto Moreno Reza	1	0.23%
                Yuriria Ayala León	1	0.23%
                Dirección General de Seguimiento "C"	Total por DG	348	100%
                David Aguirre Romero	72	20.69%
                Daniela Jazmin Morales Cruz	59	16.95%
                Dina Maria Rubio Rojas	53	15.23%
                Stephany Fernanda Zea Ibarra	38	10.92%
                Luis Enrique Morán Pizaña	37	10.63%
                Alejandro De Anda Rosas	18	5.17%
                Luis Enrique Moran Pizaña	14	4.02%
                María de los Angeles Sancha Peña	13	3.74%
                Perla Diana Báez Caldiño	10	2.87%
                Luis Martin Cervantes Chavez	8	2.30%
                Axl Ayax Yoyakin Conde Cano	7	2.01%
                Luis Ernesto Ramos García	7	2.01%
                Alexis Canelo Muñoz	6	1.72%
                Cristina Lopez Sanchez	4	1.15%
                alejandro de anda rosas	1	0.29%
                Jorge Arenas Osnaya	1	0.29%
                Gran Total		1869	100%
                * Cada color representa un Usuario, cada grupo de barras corresponde a una DG.

                Cambios en Expedientes (Últimos 30 días)
                Fecha	Total Cambios
                2025-04-09	294
                2025-04-24	290
                2025-04-08	275
                2025-04-07	271
                2025-04-23	239
                2025-04-30	208
                2025-04-22	205
                2025-04-10	199
                2025-04-29	197
                2025-04-28	194
                2025-04-25	174
                2025-04-21	169
                2025-05-02	156
                2025-04-11	150
                2025-05-01	36
                2025-05-06	19
                2025-04-27	1
        Contexto de los graficos de recepción y entrega de expedientes de acción:
            Estado de Entregas a la Fecha
            Gráfico
            Tabla
            Aceptados
            1,044
            36.5%
            En Proceso de Aceptación
            1,300
            45.5%
            No Entregados
            516
            18.0%
            Estado	Cantidad	Porcentaje
            Aceptados
            1,044	36.5%
            En Proceso de Aceptación
            1,300	45.5%
            No Entregados
            516	18.0%
            Total	2,860	100%
            * Muestra la proporción de expedientes según su estado actual en el sistema.

            Entregas de Expedientes por Siglas de Auditoría Especial
            Gráfica
            Tabla
            CSV
            Aceptados
            En Proceso de Aceptación
            No Entregados
            * Haga clic en las leyendas para filtrar
            Sigla	Estado	Cantidad	Porcentaje
            AECF	Total	605	100%
            Aceptados
            349	
            57.7%
            En Proceso
            256	
            42.3%
            No Entregados
            0	
            0.0%
            AED	Total	138	100%
            Aceptados
            138	
            100.0%
            En Proceso
            0	
            0.0%
            No Entregados
            0	
            0.0%
            AEGF	Total	2117	100%
            Aceptados
            557	
            26.3%
            En Proceso
            1044	
            49.3%
            No Entregados
            516	
            24.4%
            Total: 2860
            Aceptados: 1044 (36.5%)
            En Proceso: 1300 (45.5%)
            No Entregados: 516 (18.0%)
            Expedientes por Auditoría Especial, UAA y Estado de Entrega

            Vista de Tabla

            Barras Apiladas
            Exportar
            Auditoría Especial: AECF (Total: 605)
            Aceptados: 349 (57.7%)
            En Proceso: 256 (42.3%)
            No Entregados: 0 (0.0%)


            UAA	Aceptados	En Proceso	No Entregados	Total
            Dirección General de Auditoría de Inversiones Físicas Federales	81 (47.1%)	91 (52.9%)	0 (0.0%)	172
            Dirección General de Auditoría Forense de Cumplimiento Financiero	108 (68.8%)	49 (31.2%)	0 (0.0%)	157
            Dirección General de Auditoría Financiera Federal "C"	71 (50.4%)	70 (49.6%)	0 (0.0%)	141
            Dirección General de Auditoría Financiera Federal "B"	51 (82.3%)	11 (17.7%)	0 (0.0%)	62
            Dirección General de Auditoría de Tecnologías de Información y Comunicaciones	21 (46.7%)	24 (53.3%)	0 (0.0%)	45
            Dirección General de Auditoría Financiera Federal "A"	17 (60.7%)	11 (39.3%)	0 (0.0%)	28
            Auditoría Especial: AED (Total: 138)
            Aceptados: 138 (100.0%)
            En Proceso: 0 (0.0%)
            No Entregados: 0 (0.0%)


            UAA	Aceptados	En Proceso	No Entregados	Total
            Dirección General de Auditoría y Evaluación a los Sistemas de Control Interno	57 (100.0%)	0 (0.0%)	0 (0.0%)	57
            Dirección General de Auditoría de Desempeño al Desarrollo Económico	42 (100.0%)	0 (0.0%)	0 (0.0%)	42
            Dirección General de Auditoría de Desempeño al Desarrollo Social	26 (100.0%)	0 (0.0%)	0 (0.0%)	26
            Dirección General de Auditoría de Desempeño a Programas Presupuestarios	8 (100.0%)	0 (0.0%)	0 (0.0%)	8
            Dirección General de Auditoría de Desempeño a Gobierno y Finanzas	5 (100.0%)	0 (0.0%)	0 (0.0%)	5
            Auditoría Especial: AEGF (Total: 2117)
            Aceptados: 557 (26.3%)
            En Proceso: 1044 (49.3%)
            No Entregados: 516 (24.4%)


            UAA	Aceptados	En Proceso	No Entregados	Total
            Dirección General de Auditoría del Gasto Federalizado "D"	52 (4.9%)	493 (46.5%)	516 (48.6%)	1061
            Dirección General de Auditoría del Gasto Federalizado "C"	200 (46.5%)	230 (53.5%)	0 (0.0%)	430
            Dirección General de Auditoría del Gasto Federalizado "A"	139 (48.3%)	149 (51.7%)	0 (0.0%)	288
            Dirección General de Auditoría del Gasto Federalizado "B"	94 (55.3%)	76 (44.7%)	0 (0.0%)	170
            Dirección General de Auditoría Forense del Gasto Federalizado	70 (43.2%)	92 (56.8%)	0 (0.0%)	162
            Dirección General de Evaluación del Gasto Federalizado	2 (33.3%)	4 (66.7%)	0 (0.0%)	6

            
            ';
        return $basePrompt;
    }

    /**
     * Obtiene los datos procesados de las gráficas
     */
    private function getChartsData(Request $request)
    {
        // Usamos getSaesData para obtener los datos del controlador DashboardController
        return $this->getSaesData('charts', $request);
    }

    /**
     * Obtiene los datos procesados de las entregas
     */
    private function getEntregasData(Request $request)
    {
        // Usamos getSaesData para obtener los datos del controlador DashboardEntregasController
        return $this->getSaesData('entregas', $request);
    }

    private function getCatalogos()
    {
        try {
            $dashboardController = new DashboardEntregasController();
            return $dashboardController->getCatalogos();
        } catch (\Exception $e) {
            \Log::error("Error al obtener catálogos: " . $e->getMessage());
            return [
                'entregas' => [],
                'cuentas_publicas' => [],
                'uaas' => [],
                'dgs' => []
            ];
        }
    }

    /**
     * Genera un resumen de las descripciones para las tarjetas informativas
     */
    public function generateSummary(Request $request)
    {
        try {
            $data = $request->validate([
                'descriptions' => 'required|array',
                'entity' => 'required|string',
                'context' => 'nullable|string'
            ]);
            
            // Filtrar descripciones vacías
            $descriptions = array_filter($data['descriptions'], function($desc) {
                return !empty(trim($desc));
            });
            
            if (empty($descriptions)) {
                return response()->json([
                    'summary' => 'No hay descripciones disponibles para generar un resumen.'
                ]);
            }
            
            // Limitar el número de descripciones para evitar exceder límites de tokens
            $descriptions = array_slice($descriptions, 0, 10);
            
            // Construir el mensaje para la IA
            $userMessage = "Por favor, genera un resumen ejecutivo conciso de las siguientes descripciones de acciones para la entidad '{$data['entity']}':\n\n";
            
            foreach ($descriptions as $index => $description) {
                $userMessage .= ($index + 1) . ". " . $description . "\n\n";
            }
            
            $userMessage .= "\nEl resumen debe ser profesional, claro y destacar los puntos más importantes en un párrafo de no más de 10 líneas.";
            
            if (!empty($data['context'])) {
                $userMessage .= "\n\nContexto adicional: " . $data['context'];
            }
            
            // Sistema de prompt específico para resúmenes
            $systemPrompt = "Eres un asistente especializado en generar resúmenes ejecutivos concisos y profesionales para tarjetas informativas de auditoría. 
            Debes extraer los puntos clave de las descripciones proporcionadas y presentarlos de manera clara y estructurada.
            Usa un lenguaje profesional y técnico apropiado para documentos de auditoría gubernamental.";
            
            // Usar el proveedor de IA por defecto
            $provider = env('DEFAULT_AI_PROVIDER', 'anthropic');
            $model = env('DEFAULT_AI_MODEL', 'claude-3-haiku-20240307');
            
            // Obtener la respuesta de la IA con el systemPrompt personalizado
            $conversationHistory = []; // Sin historial para este caso
            
            switch ($provider) {
                case 'anthropic':
                    $summary = $this->getAnthropicResponse($userMessage, $model, $systemPrompt, $conversationHistory);
                    break;
                case 'openai':
                    $summary = $this->getOpenAIResponse($userMessage, $model, $systemPrompt, $conversationHistory);
                    break;
                case 'gemini':
                    $summary = $this->getGeminiResponse($userMessage, $model, $systemPrompt, $conversationHistory);
                    break;
                default:
                    throw new \Exception("Proveedor de IA no soportado: $provider");
            }
            
            return response()->json([
                'summary' => $summary,
                'entity' => $data['entity']
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error generando resumen: " . $e->getMessage());
            return response()->json([
                'error' => 'Error al generar el resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera resúmenes individuales para múltiples descripciones
     */
    public function summarizeDescriptions(Request $request)
    {
        try {
            $data = $request->validate([
                'descriptions' => 'required|array',
                'maxLength' => 'nullable|integer|min:50|max:500'
            ]);
            
            $descriptions = $data['descriptions'];
            $maxLength = $data['maxLength'] ?? 1450; // Longitud máxima por defecto de 150 caracteres
            
            // Filtrar descripciones vacías y muy cortas
            $descriptionsToSummarize = [];
            $summaries = [];
            
            foreach ($descriptions as $index => $description) {
                if (empty(trim($description))) {
                    $summaries[$index] = '';
                } elseif (strlen($description) <= $maxLength) {
                    // Si la descripción ya es corta, no resumir
                    $summaries[$index] = $description;
                } else {
                    $descriptionsToSummarize[$index] = $description;
                }
            }
            
            if (empty($descriptionsToSummarize)) {
                return response()->json(['summaries' => $summaries]);
            }
            
            // Construir el mensaje para la IA
            $userMessage = "Por favor, resume cada una de las siguientes descripciones, manteniendo la información más importante:\n\n";
            
            foreach ($descriptionsToSummarize as $index => $description) {
                $userMessage .= "Descripción {$index}:\n{$description}\n\n";
            }
            
            $userMessage .= "Proporciona los resúmenes en el siguiente formato:\n";
            $userMessage .= "Resumen {índice}: [texto resumido]\n";
            $userMessage .= "\nCada resumen debe ser claro, conciso y capturar la esencia de la descripción original, ignora los montos pues ya se visualizan en la tabla y las leyes.";
            
            // Sistema de prompt específico para resúmenes
            $systemPrompt = "
                Eres un modelo especializado en resumir observaciones de auditoría pública.  
                Al recibir uno o varios bloques identificados como **Descripción**, devuelve para cada uno
                únicamente el texto denominado **Resumen**, aplicando todas las reglas siguientes:

                1. **Construcción inicial.**  
                Conserva la misma fórmula con la que inicia la Descripción  
                (p. ej. “Se presume…”, “Para que…”) y redacta siempre en tercera persona impersonal.

                2. **Contenido mínimo.**  
                Incluye solamente los elementos indispensables para comprender la irregularidad:  
                • sujeto responsable o programa/contrato involucrado  
                • acción u omisión relevante (falta de documentos, pago sin evidencia, etc.)  
                • monto numérico total (con símbolo $ y separador de miles)  
                • referencia genérica al periodo, pólizas o contratos si aportan contexto.

                3. **Montos.**  
                ─ Mantén todas las cantidades en formato numérico ($999,999,999.99).  
                ─ Elimina las cantidades escritas con palabras.

                4. **Qué eliminar.**  
                • Citas de leyes, artículos, fracciones, numerales, lineamientos o normas.  
                • Frases sobre intereses o rendimientos financieros.  
                • Listados extensos de conceptos de obra o pólizas: si son muchos, usa “entre otros”.  
                • Detalles operativos o argumentos jurídicos ajenos al núcleo de la observación.

                5. **Compresión de enumeraciones.**  
                Cuando haya varios elementos homogéneos (pólizas, transferencias, contratos, etc.)  
                reemplázalos por “en X pólizas/transferencias/contratos”, salvo que un número de contrato
                sea clave para la comprensión.

                6. **Extensión y estilo.**  
                • Máximo 3 frases y ≤ 100 palabras.  
                • Estilo claro y directo, sin encabezados ni etiquetas.  
                • Devuelve sólo el resumen, sin tabulaciones ni comillas.

                Ejemplo de transformación  
                Descripción:
                Se presume un daño a la Hacienda Pública Federal por un monto de $2,854,656,841.17 (DOS MIL OCHOCIENTOS CINCUENTA Y CUATRO MILLONES SEISCIENTOS CINCUENTA Y SEIS MIL OCHOCIENTOS CUARENTA Y UN PESOS 17/100 M.N.), por no proporcionar las boletas de recepción y liquidación de los productores, en las que se acredite la entrada de grano a los centros de acopio ni evidencia de las transferencias bancarias que acreditaran el pago, en 29 pólizas de registro contable por $531,481,216.24 (QUINIENTOS TREINTA Y UN MILLONES CUATROCIENTOS OCHENTA Y UN MIL DOSCIENTOS DIECISÉIS PESOS 24/100 M.N) y 29 pólizas de registro contable, por $190,966,772.07 (CIENTO NOVENTA MILLONES NOVECIENTOS SESENTA Y SEIS MIL SETECIENTOS SETENTA Y DOS PESOS 07/100 M.N.), correspondientes a maíz y frijol de pequeños productores, respectivamente. Asimismo, no se acreditó con la documentación soporte el cálculo aplicado entre el precio de referencia y el precio de garantía, para determinar el diferencial de los subsidios otorgados por $820,476,144.10 (OCHOCIENTOS VEINTE MILLONES CUATROCIENTOS SETENTA Y SEIS MIL CIENTO CUARENTA Y CUATRO PESOS 10/100 M.N.) de maíz a medianos productores, en las pólizas contables 69737, 69747, 69932, todas del 31 de diciembre de 2020; por $104,490,898.30 (CIENTO CUATRO MILLONES CUATROCIENTOS NOVENTA MIL OCHOCIENTOS NOVENTA Y OCHO PESOS 30/100 M.N.), de productores de arroz, en las pólizas contables 68189, 68224, 68623, 68674, 68938 y 69793 del 14 y 19 de mayo, 10 y 17 de junio, 25 de agosto y 31 de diciembre de 2020; y por $428,037,398.90 (CUATROCIENTOS VEINTIOCHO MILLONES TREINTA Y SIETE MIL TRESCIENTOS NOVENTA Y OCHO PESOS 90/100 M.N.), de productores de trigo, en las pólizas contables 69840, 69957, 69791, 69898, todas del 31 de diciembre de 2020; y por no proporcionar la documentación soporte que acredite el pago del subsidio otorgado bajo el concepto 'COMPLEMENTO' a maíz de medianos productores, trigo y arroz por $779,204,411.56 (SETECIENTOS SETENTA Y NUEVE MILLONES DOSCIENTOS CUATRO MIL CUATROCIENTOS ONCE PESOS 56/100 M.N.); que se integran por $427,612,796.39 (CUATROCIENTOS VEINTISIETE MILLONES SEISCIENTOS DOCE MIL SETECIENTOS NOVENTA Y SEIS PESOS 39/100 M.N) de maíz de medianos productores; $350,500,115.17 (TRESCIENTOS CINCUENTA MILLONES QUINIENTOS MIL CIENTO QUINCE PESOS 17/100 M.N.) de trigo y $1,091,500.0 (UN MILLÓN NOVENTA Y UN MIL QUINIENTOS PESOS 00/100 M.N.) de arroz

                Resumen:
                Se presume un daño a la Hacienda Pública Federal por no proporcionar las boletas de recepción y liquidación de los productores, en las que se acredite la entrada de grano a los centros de acopio ni evidencia de las transferencias bancarias que acreditaran el pago, en 29 pólizas de registro contable por $531,481,216.24 y 29 pólizas de registro contable, por $190,966,772.07, correspondientes a maíz y frijol de pequeños productores, respectivamente. Asimismo, no se acreditó con la documentación soporte el cálculo aplicado entre el precio de referencia y el precio de garantía, para determinar el diferencial de los subsidios otorgados por $820,476,144.10 de maíz a medianos productores, en las pólizas contables 69737, 69747, 69932, todas del 31 de diciembre de 2020; por $104,490,898.30, de productores de arroz, en las pólizas contables 68189, 68224, 68623, 68674, 68938 y 69793 del 14 y 19 de mayo, 10 y 17 de junio, 25 de agosto y 31 de diciembre de 2020; y por $428,037,398.90, de productores de trigo, en las pólizas contables 69840, 69957, 69791, 69898, todas del 31 de diciembre de 2020; y por no proporcionar la documentación soporte que acredite el pago del subsidio otorgado bajo el concepto 'COMPLEMENTO' a maíz de medianos productores, trigo y arroz por $779,204,411.56.
            ";
            
            // Usar el proveedor de IA por defecto
            $provider = 'anthropic';
            $model = 'claude-3-7-sonnet-20250219';
            
            // Obtener la respuesta de la IA
            $conversationHistory = [];
            
            switch ($provider) {
                case 'anthropic':
                    $response = $this->getAnthropicResponse($userMessage, $model, $systemPrompt, $conversationHistory);
                    break;
                default:
                    throw new \Exception("Proveedor de IA no soportado: $provider");
            }
            
            // Parsear la respuesta para extraer los resúmenes individuales
            $lines = explode("\n", $response);
            foreach ($lines as $line) {
                if (preg_match('/Resumen\s+(\d+):\s*(.+)/', $line, $matches)) {
                    $index = intval($matches[1]);
                    $summary = trim($matches[2]);
                    if (isset($descriptionsToSummarize[$index])) {
                        $summaries[$index] = $summary;
                    }
                }
            }
            
            // Verificar que tengamos todos los resúmenes
            foreach ($descriptionsToSummarize as $index => $description) {
                if (!isset($summaries[$index])) {
                    // Si falta algún resumen, usar una versión truncada
                    $summaries[$index] = substr($description, 0, $maxLength - 3) . '...';
                }
            }
            
            return response()->json(['summaries' => $summaries]);
            
        } catch (\Exception $e) {
            Log::error("Error generando resúmenes individuales: " . $e->getMessage());
            
            // En caso de error, devolver las descripciones truncadas
            $summaries = [];
            foreach ($request->input('descriptions', []) as $index => $description) {
                $maxLength = $request->input('maxLength', 4450);
                if (strlen($description) > $maxLength) {
                    $summaries[$index] = substr($description, 0, $maxLength - 3) . '...';
                } else {
                    $summaries[$index] = $description;
                }
            }
            
            return response()->json([
                'summaries' => $summaries,
                'error' => 'Se usó truncamiento simple debido a un error en el servicio de IA'
            ], 200); // Devolver 200 para no bloquear el proceso
        }
    }
}
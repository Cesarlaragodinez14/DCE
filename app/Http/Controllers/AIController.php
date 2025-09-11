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
            'groq' => 'Groq',
            'anthropic' => 'Anthropic',
        ];
        
        // Obtener modelos disponibles por proveedor
        $models = [
            'groq' => [
                'llama-3.1-8b-instant' => 'Llama 3 8B',
            ],
            'anthropic' => [
                'claude-3-haiku-20240307' => 'Claude 3 Haiku',
            ],
        ];

        // Obtener catálogos para los filtros
        $catalogos = $this->getCatalogos();
        
        $defaultProvider = 'anthropic';
        $defaultModel = 'claude-3-haiku-20240307';
        
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
                'provider' => 'sometimes|string|in:groq,anthropic',
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
            $provider = $data['provider'] ?? 'groq';
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
                // FALLBACK DESHABILITADO: Comentar las siguientes líneas para evitar el cambio automático a Groq
                /*
                // Si el proveedor principal falla y es Anthropic, intentar con Groq como respaldo
                if ($provider === 'anthropic' && str_contains($e->getMessage(), '401')) {
                    Log::warning("Claude API falló, intentando con Groq como respaldo: " . $e->getMessage());
                    
                    try {
                        $assistantResponse = $this->getAIResponse(
                            $userMessage, 
                            'groq', 
                            null, 
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

                        return response()->json([
                            'userMessage' => $userMessage,
                            'assistantMessage' => $assistantResponse,
                            'provider' => 'groq',
                            'conversation_id' => $conversationId,
                            'conversation_title' => $currentConversation['title'],
                            'fallback_used' => true
                        ]);
                    } catch (\Exception $fallbackError) {
                        Log::error("También falló el respaldo con Groq: " . $fallbackError->getMessage());
                    }
                }
                */
                
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
        $provider = $provider ?? 'groq';
        
        // Obtener datos actualizados del sistema antes de cada respuesta
        $systemMessage = $this->getSystemPrompt($includeContext, $filters);
        
        switch ($provider) {
            case 'groq':
                return $this->getGroqResponse($userMessage, $model, $systemMessage, $conversationHistory);
            case 'anthropic':
                return $this->getAnthropicResponse($userMessage, $model, $systemMessage, $conversationHistory);
            default:
                throw new \Exception("Proveedor de IA no soportado: $provider");
        }
    }

    /**
     * Método para obtener la respuesta de Groq
     */
    private function getGroqResponse($userMessage, $model = null, $systemMessage = null, $conversationHistory = [])
    {
        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            throw new \Exception('La clave API de Groq no está configurada en el archivo .env');
        }
        
        $model = $model ?? env('GROQ_DEF_MODEL', 'llama-3.1-8b-instant');
        
        // Endpoint de la API de Groq (compatible con OpenAI)
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
        
        // Convertir el historial de conversación al formato esperado por Groq
        $messages = $this->formatConversationForGroq($conversationHistory, $systemMessage);
        
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
            'max_tokens' => 4024,
            'temperature' => 0.7,
            'top_p' => 1,
            'stream' => false
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
                throw new \Exception('Formato de respuesta inesperado de la API de Groq');
            }
            
            return $responseData['choices'][0]['message']['content'];
        } else {
            // Obtener detalles del error para diagnóstico
            $statusCode = $response->status();
            $errorBody = $response->body();
            
            // Registrar detalles del error
            Log::error("Error API Groq (código $statusCode): $errorBody");
            
            // Si hay un error, lanzar una excepción con el mensaje
            $errorData = $response->json();
            $errorMessage = isset($errorData['error']) ? ($errorData['error']['message'] ?? 'Error sin mensaje') : 'Error desconocido';
            throw new \Exception("API error ($statusCode): " . $errorMessage);
        }
    }

    /**
     * Formatea el historial de conversación para Groq
     */
    private function formatConversationForGroq($history, $systemMessage)
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

    private function getAnthropicResponse($userMessage, $model = null, $systemMessage = null, $conversationHistory = [])
    {
        $apiKey = trim(env('CLAUDE_API'));
        if (empty($apiKey)) {
            throw new \Exception('La clave API de Claude no está configurada en el archivo .env (CLAUDE_API)');
        }
        
        // Clean API key of any whitespace/line breaks
        $apiKey = preg_replace('/\s+/', '', $apiKey);
        
        // Use model from environment or fallback
        $model = $model ?? env('CLAUDE_MODEL', 'claude-3-5-haiku-20241022');
        
        // Endpoint de la API de Anthropic
        $endpoint = 'https://api.anthropic.com/v1/messages';
        
        // Convertir el historial de conversación al formato esperado por Anthropic
        $messages = $this->formatConversationForAnthropic($conversationHistory);
        
        // Ensure we have a user message
        $lastMessageIsUser = false;
        if (!empty($messages)) {
            $lastMessage = end($messages);
            $lastMessageIsUser = ($lastMessage['role'] === 'user');
        }
        
        if (!$lastMessageIsUser) {
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        }
        
        // Prepare request body
        $requestBody = [
            'model' => $model,
            'max_tokens' => 4000,
            'temperature' => 0.7,
            'messages' => $messages
        ];
        
        // Only add system message if provided
        if (!empty($systemMessage)) {
            $requestBody['system'] = $systemMessage;
        }
        
        // Make request with correct headers
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->timeout(60)->post($endpoint, $requestBody);
        
        if ($response->successful()) {
            $responseData = $response->json();
            
            if (!isset($responseData['content'][0]['text'])) {
                throw new \Exception('Formato de respuesta inesperado de la API de Anthropic');
            }
            
            return $responseData['content'][0]['text'];
        } else {
            $statusCode = $response->status();
            $errorBody = $response->body();
            
            Log::error('Claude API Error', [
                'status_code' => $statusCode,
                'error_body' => $errorBody
            ]);
            
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Error desconocido';
            throw new \Exception("Claude API error ($statusCode): " . $errorMessage);
        }
    }

    /**
     * Formatea el historial de conversación para Anthropic
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
     * Obtiene información del sistema SAES directamente de los controladores
     */
    private function getSaesData($type, Request $request = null)
    {
        try {
            if (!$request) {
                $request = new Request();
            }

            // Para el contexto de IA, solo aplicar filtros si existen explícitamente
            // No usar valores por defecto para mostrar vista completa del sistema
            $params = [];
            
            // Solo aplicar filtros si están presentes y no son vacíos
            if ($request->query('entrega')) {
                $params['entrega'] = $request->query('entrega');
            }
            if ($request->query('cuenta_publica')) {
                $params['cuenta_publica'] = $request->query('cuenta_publica');
            }
            if ($request->query('uaa_id')) {
                $params['uaa_id'] = $request->query('uaa_id');
            }
            if ($request->query('dg_id')) {
                $params['dg_id'] = $request->query('dg_id');
            }

            // Aplicar solo los filtros específicos a la nueva request
            if (!empty($params)) {
                $request->merge($params);
            }

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
     * @param array|null $filters Filtros aplicados para obtener contexto dinámico
     * @return string
     */
    private function getSystemPrompt($includeContext = true, $filters = null)
    {
        $basePrompt = '
Eres SAES-AI, el asistente inteligente del Sistema de Auditorías de Expedientes SAES.

Tu función principal es ayudar a los usuarios con consultas sobre expedientes, auditorías, procesos y datos del sistema.

INSTRUCCIONES:
- Responde siempre en español de manera clara y profesional
- Proporciona datos precisos basados ÚNICAMENTE en el contexto actual del sistema proporcionado
- CRÍTICO: "auditorías", "expedientes", "expedientes de auditoría" y "expedientes del sistema" son EXACTAMENTE LO MISMO
- Cuando el contexto menciona "Total de expedientes: X", responde directamente: "El sistema tiene X auditorías registradas"
- NUNCA hagas distinción entre expedientes y auditorías - son el mismo concepto
- NUNCA inventes números - usa SOLO los datos del contexto proporcionado
- Responde de forma directa y clara, sin explicaciones innecesarias sobre la diferencia de términos
';

        // Si no se incluye contexto, usar mensaje genérico
        if (!$includeContext) {
            $basePrompt .= '
CONTEXTO GENERAL:
Sistema activo con datos de expedientes de auditoría.
Para obtener información específica, aplica filtros o proporciona más detalles en tu consulta.
';
            return $basePrompt;
        }

        // Generar contexto dinámico (con o sin filtros)
        $dynamicContext = $this->generateDynamicContext($filters);
        
        if ($dynamicContext) {
            $basePrompt .= "\n" . $dynamicContext;
        }

        return $basePrompt;
    }

    /**
     * Genera contexto dinámico basado en los filtros aplicados
     * 
     * @param array $filters Filtros aplicados
     * @return string Contexto generado
     */
    private function generateDynamicContext($filters)
    {
        try {
            // Crear request con filtros
            $request = new Request();
            if ($filters) {
                foreach ($filters as $key => $value) {
                    if ($value) {
                        $request->merge([$key => $value]);
                    }
                }
            }

            $context = "\nCONTEXTO ACTUAL DEL SISTEMA:\n";
            $context .= "Última actualización: " . now()->format('d/m/Y H:i') . "\n\n";

            // Obtener datos de dashboard principal
            try {
                $dashboardData = $this->getSaesData('charts', $request);
                
                if ($dashboardData && isset($dashboardData['success']) && $dashboardData['success']) {
                    $context .= $this->formatDashboardData($dashboardData['data'], $filters);
                }
            } catch (\Exception $e) {
                \Log::error("Error en datos de dashboard: " . $e->getMessage());
            }

            // Obtener datos de entregas
            try {
                $entregasData = $this->getSaesData('entregas', $request);
                
                if ($entregasData && isset($entregasData['success']) && $entregasData['success']) {
                    $context .= $this->formatEntregasData($entregasData['data'], $filters);
                }
            } catch (\Exception $e) {
                \Log::error("Error en datos de entregas: " . $e->getMessage());
            }

            return $context;

        } catch (\Exception $e) {
            \Log::error("Error general generando contexto dinámico: " . $e->getMessage());
            return "\nCONTEXTO: Datos del sistema disponibles (error al cargar contexto específico)\n";
        }
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

    /**
     * Formatea los datos del dashboard para el contexto de IA
     * 
     * @param array $data Datos del dashboard
     * @param array $filters Filtros aplicados
     * @return string Contexto formateado
     */
    private function formatDashboardData($data, $filters)
    {
        $context = "";

        // Información de filtros aplicados
        if ($filters && array_filter($filters)) {
            $context .= "FILTROS APLICADOS:\n";
            if (isset($filters['entrega']) && $filters['entrega']) {
                $context .= "- Entrega: " . $this->getCatalogName('entregas', $filters['entrega']) . "\n";
            }
            if (isset($filters['cuenta_publica']) && $filters['cuenta_publica']) {
                $context .= "- Cuenta Pública: " . $this->getCatalogName('cuentas_publicas', $filters['cuenta_publica']) . "\n";
            }
            if (isset($filters['uaa_id']) && $filters['uaa_id']) {
                $context .= "- UAA tambien conocidas como DG (Direcciones generales): " . $this->getCatalogName('uaas', $filters['uaa_id']) . "\n";
            }
            if (isset($filters['dg_id']) && $filters['dg_id']) {
                $context .= "- Direcciones generales de seguimiento (DGSEGA,DGSEGB,DGSEGC,DGSEGD): " . $this->getCatalogName('dgsegs', $filters['dg_id']) . "\n";
            }
            $context .= "\n";
        }

        // Expedientes por estatus
        if (isset($data['expedientes_por_estatus'])) {
            $estatusData = collect($data['expedientes_por_estatus']);
            $total = $estatusData->sum('total');
            
            $context .= "EXPEDIENTES POR ESTATUS:\n";
            $context .= "Total de expedientes: {$total}\n\n";

            foreach ($estatusData as $item) {
                $itemArray = $this->objectToArray($item);
                $itemTotal = $itemArray['total'] ?? 0;
                $itemEstatus = $itemArray['estatus_checklist'] ?? 'Sin datos';
                $percentage = $total > 0 ? round(($itemTotal / $total) * 100, 1) : 0;
                $context .= "- {$itemEstatus}: {$itemTotal} ({$percentage}%)\n";
            }
            $context .= "\n";
        }

        // Expedientes por ente fiscalizado (top 10)
        if (isset($data['expedientes_por_ente_fiscalizado'])) {
            $entesCollection = collect($data['expedientes_por_ente_fiscalizado']);
            $entesData = $entesCollection->take(10);
            $context .= "TOP 10 ENTES FISCALIZADOS:\n";
            
            foreach ($entesData as $index => $item) {
                $itemArray = $this->objectToArray($item);
                $ente = $itemArray['cat_ente_fiscalizado']['valor'] ?? 'Sin datos';
                $total = $itemArray['total'] ?? 0;
                $context .= ($index + 1) . ". {$ente}: {$total} expedientes\n";
            }
            $context .= "\n";
        }

        // Expedientes por siglas de auditoría especial
        if (isset($data['expedientes_por_siglas'])) {
            $siglasData = collect($data['expedientes_por_siglas']);
            $context .= "EXPEDIENTES POR AUDITORÍA ESPECIAL:\n";
            
            foreach ($siglasData as $item) {
                $itemArray = $this->objectToArray($item);
                $sigla = $itemArray['catSiglasAuditoriaEspecial']['valor'] ?? 'Sin datos';
                $total = $itemArray['total'] ?? 0;
                $context .= "- {$sigla}: {$total} expedientes\n";
            }
            $context .= "\n";
        }

        // NUEVO: Expedientes por UAA
        if (isset($data['expedientes_por_uaa'])) {
            $uaaData = collect($data['expedientes_por_uaa']);
            $context .= "EXPEDIENTES POR UAA Todas las categorias que aparecen en el contexto son UAA no DGS (direcciones generales de seguimiento no confundir estas son DGSEGA,DGSEGB,DGSEGC,DGSEGD):\n";
            
            // Agrupar por UAA y sumar totales por estatus
            $uaaGrouped = $uaaData->groupBy(function($item) {
                $itemArray = $this->objectToArray($item);
                return $itemArray['catUaa']['valor'] ?? $itemArray['catUaa']['nombre'] ?? 'Sin datos';
            });
            
            foreach ($uaaGrouped as $uaaName => $expedientes) {
                $totalExpedientes = $expedientes->sum(function($item) {
                    $itemArray = $this->objectToArray($item);
                    return $itemArray['total'] ?? 0;
                });
                
                $context .= "\n{$uaaName}: {$totalExpedientes} expedientes\n";
                
                foreach ($expedientes as $item) {
                    $itemArray = $this->objectToArray($item);
                    $total = $itemArray['total'] ?? 0;
                    $estatus = $itemArray['estatus_checklist'] ?? 'Sin datos';
                    $percentage = $totalExpedientes > 0 ? round(($total / $totalExpedientes) * 100, 1) : 0;
                    $context .= "  - {$estatus}: {$total} ({$percentage}%)\n";
                }
            }
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Formatea los datos de entregas para el contexto de IA
     * 
     * @param array $data Datos de entregas
     * @param array $filters Filtros aplicados
     * @return string Contexto formateado
     */
    private function formatEntregasData($data, $filters)
    {
        $context = "ESTADO DE ENTREGAS:\n";

        if (isset($data['delivery_status'])) {
            $deliveryData = collect($data['delivery_status']);
            
            foreach ($deliveryData as $item) {
                $itemArray = $this->objectToArray($item);
                $delivered = $itemArray['delivered'] ?? 0;
                $inProcess = $itemArray['in_process'] ?? 0;
                $unscheduled = $itemArray['unscheduled'] ?? 0;
                $total = $delivered + $inProcess + $unscheduled;
                
                if ($total > 0) {
                    $deliveredPct = round(($delivered / $total) * 100, 1);
                    $inProcessPct = round(($inProcess / $total) * 100, 1);
                    $unscheduledPct = round(($unscheduled / $total) * 100, 1);
                    
                    $context .= "- Entregados: {$delivered} ({$deliveredPct}%)\n";
                    $context .= "- En proceso: {$inProcess} ({$inProcessPct}%)\n";
                    $context .= "- Sin programar: {$unscheduled} ({$unscheduledPct}%)\n";
                }
                break; // Solo mostrar el primer resumen general
            }
        }

        $context .= "\n";
        return $context;
    }

    /**
     * Obtiene el nombre de un elemento del catálogo por su ID
     * 
     * @param string $catalog Tipo de catálogo
     * @param mixed $id ID del elemento
     * @return string Nombre del elemento
     */
    private function getCatalogName($catalog, $id)
    {
        try {
            $catalogos = $this->getCatalogos();
            
            $catalogMap = [
                'entregas' => 'entregas',
                'cuentas_publicas' => 'cuentasPublicas', 
                'uaas' => 'uaas',
                'dgsegs' => 'dgsegs'
            ];

            if (isset($catalogMap[$catalog]) && isset($catalogos[$catalogMap[$catalog]])) {
                foreach ($catalogos[$catalogMap[$catalog]] as $item) {
                    if ($item->id == $id) {
                        return $item->valor ?? $item->nombre ?? "ID: {$id}";
                    }
                }
            }

            return "ID: {$id}";
        } catch (\Exception $e) {
            return "ID: {$id}";
        }
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
            if (count($descriptions) > 15) {
                Log::info("⚠️ Limitando descripciones para resumen ejecutivo: " . count($descriptions) . " -> 15");
                $descriptions = array_slice($descriptions, 0, 15);
            }
            
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
            $provider = 'groq';
            $model = env('GROQ_DEF_MODEL', 'llama-3.1-8b-instant');
            
            // Obtener la respuesta de la IA con el systemPrompt personalizado
            $conversationHistory = []; // Sin historial para este caso
            
            switch ($provider) {
                case 'groq':
                    $summary = $this->getGroqResponse($userMessage, $model, $systemPrompt, $conversationHistory);
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
     * Genera resúmenes individuales para múltiples descripciones de forma optimizada
     */
    public function summarizeDescriptions(Request $request)
    {
        try {
            $data = $request->validate([
                'descriptions' => 'required|array',
                'maxLength' => 'nullable|integer|min:50|max:500',
                'batch_size' => 'nullable|integer|min:5|max:50'
            ]);
            
            $descriptions = $data['descriptions'];
            $maxLength = $data['maxLength'] ?? 200; // Longitud máxima por defecto
            $batchSize = $data['batch_size'] ?? 10; // Tamaño de lote por defecto más pequeño
            
            Log::info("📋 Iniciando resúmenes individuales optimizados", [
                'total_descriptions' => count($descriptions),
                'max_length' => $maxLength,
                'batch_size' => $batchSize
            ]);
            
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
                Log::info("✅ No hay descripciones para resumir");
                return response()->json(['summaries' => $summaries]);
            }
            
            Log::info("🤖 Descripciones a procesar con IA: " . count($descriptionsToSummarize));
            
            // Sistema de prompt optimizado para procesamiento individual
            $systemPrompt = "
                Eres un especialista en resumir observaciones de auditoría pública.
                
                INSTRUCCIONES:
                1. Resume la descripción manteniendo la fórmula inicial (\"Se presume...\", \"Para que...\")
                2. Conserva elementos esenciales: sujeto responsable, acción/omisión, monto principal
                3. Elimina: citas legales, montos escritos con palabras, detalles jurídicos extensos
                4. Máximo 2 oraciones, ≤ 80 palabras
                5. Mantén formato profesional y claro
                6. Devuelve SOLO el texto resumido, sin etiquetas ni formato adicional

                Sigue más el formato siguiente:
                Descripción (En caso de intereses/cargos moratorios/rendimientos o situación a fin, manten la estructura explicativa en el resumen):
                Se presume un daño a la Hacienda Pública Federal por un monto de $2,854,656,841.17 (DOS MIL OCHOCIENTOS CINCUENTA Y CUATRO MILLONES SEISCIENTOS CINCUENTA Y SEIS MIL OCHOCIENTOS CUARENTA Y UN PESOS 17/100 M.N.), por no proporcionar las boletas de recepción y liquidación de los productores, en las que se acredite la entrada de grano a los centros de acopio ni evidencia de las transferencias bancarias que acreditaran el pago, en 29 pólizas de registro contable por $531,481,216.24 (QUINIENTOS TREINTA Y UN MILLONES CUATROCIENTOS OCHENTA Y UN MIL DOSCIENTOS DIECISÉIS PESOS 24/100 M.N) y 29 pólizas de registro contable, por $190,966,772.07 (CIENTO NOVENTA MILLONES NOVECIENTOS SESENTA Y SEIS MIL SETECIENTOS SETENTA Y DOS PESOS 07/100 M.N.), correspondientes a maíz y frijol de pequeños productores, respectivamente. Asimismo, no se acreditó con la documentación soporte el cálculo aplicado entre el precio de referencia y el precio de garantía, para determinar el diferencial de los subsidios otorgados por $820,476,144.10 (OCHOCIENTOS VEINTE MILLONES CUATROCIENTOS SETENTA Y SEIS MIL CIENTO CUARENTA Y CUATRO PESOS 10/100 M.N.) de maíz a medianos productores, en las pólizas contables 69737, 69747, 69932, todas del 31 de diciembre de 2020; por $104,490,898.30 (CIENTO CUATRO MILLONES CUATROCIENTOS NOVENTA MIL OCHOCIENTOS NOVENTA Y OCHO PESOS 30/100 M.N.), de productores de arroz, en las pólizas contables 68189, 68224, 68623, 68674, 68938 y 69793 del 14 y 19 de mayo, 10 y 17 de junio, 25 de agosto y 31 de diciembre de 2020; y por $428,037,398.90 (CUATROCIENTOS VEINTIOCHO MILLONES TREINTA Y SIETE MIL TRESCIENTOS NOVENTA Y OCHO PESOS 90/100 M.N.), de productores de trigo, en las pólizas contables 69840, 69957, 69791, 69898, todas del 31 de diciembre de 2020; y por no proporcionar la documentación soporte que acredite el pago del subsidio otorgado bajo el concepto 'COMPLEMENTO' a maíz de medianos productores, trigo y arroz por $779,204,411.56 (SETECIENTOS SETENTA Y NUEVE MILLONES DOSCIENTOS CUATRO MIL CUATROCIENTOS ONCE PESOS 56/100 M.N.); que se integran por $427,612,796.39 (CUATROCIENTOS VEINTISIETE MILLONES SEISCIENTOS DOCE MIL SETECIENTOS NOVENTA Y SEIS PESOS 39/100 M.N) de maíz de medianos productores; $350,500,115.17 (TRESCIENTOS CINCUENTA MILLONES QUINIENTOS MIL CIENTO QUINCE PESOS 17/100 M.N.) de trigo y $1,091,500.0 (UN MILLÓN NOVENTA Y UN MIL QUINIENTOS PESOS 00/100 M.N.) de arroz
                Resumen:
                Se presume un daño a la Hacienda Pública Federal por no proporcionar las boletas de recepción y liquidación de los productores, en las que se acredite la entrada de grano a los centros de acopio ni evidencia de las transferencias bancarias que acreditaran el pago, en 29 pólizas de registro contable por $531,481,216.24 y 29 pólizas de registro contable, por $190,966,772.07, correspondientes a maíz y frijol de pequeños productores, respectivamente. Asimismo, no se acreditó con la documentación soporte el cálculo aplicado entre el precio de referencia y el precio de garantía, para determinar el diferencial de los subsidios otorgados por $820,476,144.10 de maíz a medianos productores, en las pólizas contables 69737, 69747, 69932, todas del 31 de diciembre de 2020; por $104,490,898.30, de productores de arroz, en las pólizas contables 68189, 68224, 68623, 68674, 68938 y 69793 del 14 y 19 de mayo, 10 y 17 de junio, 25 de agosto y 31 de diciembre de 2020; y por $428,037,398.90, de productores de trigo, en las pólizas contables 69840, 69957, 69791, 69898, todas del 31 de diciembre de 2020; y por no proporcionar la documentación soporte que acredite el pago del subsidio otorgado bajo el concepto 'COMPLEMENTO' a maíz de medianos productores, trigo y arroz por $779,204,411.56.
                ";
            
            // Procesar descripciones por lotes para optimizar tiempo y costos
            $processedCount = 0;
            $errorCount = 0;
            $provider = 'groq';
            $model = env('GROQ_DEF_MODEL', 'llama-3.1-8b-instant'); // Usar modelo por defecto de Groq
            $startTime = time();
            $maxExecutionTime = 240; // Máximo 4 minutos para evitar timeouts
            
            // Dividir descripciones en lotes
            $chunks = array_chunk($descriptionsToSummarize, $batchSize, true);
            $totalChunks = count($chunks);
            
            Log::info("🔀 Procesando en {$totalChunks} lotes de máximo {$batchSize} descripciones");
            
            foreach ($chunks as $chunkIndex => $chunk) {
                // Verificar si el cliente todavía está conectado
                if (connection_aborted()) {
                    Log::warning("🔌 Cliente desconectado, deteniendo procesamiento para ahorrar créditos");
                    break;
                }
                
                // Verificar tiempo límite
                if (time() - $startTime > $maxExecutionTime) {
                    Log::warning("⏱️ Tiempo límite alcanzado, procesando restantes con truncamiento");
                    foreach ($chunk as $index => $description) {
                        if (!isset($summaries[$index])) {
                            $summaries[$index] = $this->intelligentTruncate($description, $maxLength);
                            $errorCount++;
                        }
                    }
                    break;
                }
                
                Log::debug("📦 Procesando lote " . ($chunkIndex + 1) . "/{$totalChunks} con " . count($chunk) . " descripciones");
                
                // Procesar este lote
                foreach ($chunk as $index => $description) {
                    // Verificar conexión antes de cada proceso costoso
                    if (connection_aborted()) {
                        Log::warning("🔌 Cliente desconectado durante procesamiento del lote");
                        break 2; // Salir de ambos loops
                    }
                    
                    try {
                        // Verificar si la descripción es demasiado larga para procesar eficientemente
                        if (strlen($description) > 3000) {
                            Log::warning("⚠️ Descripción muy larga (>{$index}), aplicando pre-truncamiento");
                            $description = substr($description, 0, 2500) . '...';
                        }
                        
                        // Construir prompt simple para esta descripción individual
                        $userMessage = "Resume la siguiente observación de auditoría:\n\n{$description}";
                        
                        // Obtener resumen individual
                        $conversationHistory = [];
                        $summary = $this->getSingleSummary($userMessage, $provider, $model, $systemPrompt, $conversationHistory);
                        
                        if (!empty(trim($summary))) {
                            $summaries[$index] = trim($summary);
                            $processedCount++;
                            Log::debug("✅ Resumen generado para índice {$index}");
                        } else {
                            throw new \Exception("Respuesta vacía de la IA");
                        }
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::warning("❌ Error al resumir descripción {$index}: " . $e->getMessage());
                        
                        // Fallback: usar truncamiento inteligente
                        $summaries[$index] = $this->intelligentTruncate($description, $maxLength);
                    }
                    
                    // Verificar tiempo límite dentro del lote
                    if (time() - $startTime > $maxExecutionTime) {
                        Log::warning("⏱️ Tiempo límite alcanzado dentro del lote");
                        break 2; // Salir de ambos loops
                    }
                }
                
                // Pausa entre lotes para evitar rate limiting
                if ($chunkIndex < $totalChunks - 1) {
                    usleep(200000); // 0.2 segundos entre lotes
                }
            }
            
            Log::info("📊 Resúmenes completados", [
                'processed_with_ai' => $processedCount,
                'errors' => $errorCount,
                'total' => count($descriptionsToSummarize)
            ]);
            
            return response()->json([
                'summaries' => $summaries,
                'stats' => [
                    'total_descriptions' => count($descriptions),
                    'processed_with_ai' => $processedCount,
                    'errors' => $errorCount,
                    'skipped_short' => count($descriptions) - count($descriptionsToSummarize)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error("💥 Error general en resúmenes individuales: " . $e->getMessage());
            
            // Fallback completo: devolver descripciones truncadas
            $summaries = [];
            foreach ($request->input('descriptions', []) as $index => $description) {
                $maxLength = $request->input('maxLength', 200);
                $summaries[$index] = $this->intelligentTruncate($description, $maxLength);
            }
            
            return response()->json([
                'summaries' => $summaries,
                'error' => 'Se usó truncamiento automático debido a un error en el servicio de IA',
                'fallback' => true
            ], 200); // 200 para no bloquear el proceso
        }
    }
    
    /**
     * Obtiene un resumen individual de una descripción
     */
    private function getSingleSummary($userMessage, $provider, $model, $systemPrompt, $conversationHistory)
    {
        switch ($provider) {
            case 'groq':
                return $this->getGroqResponse($userMessage, $model, $systemPrompt, $conversationHistory);
            default:
                throw new \Exception("Proveedor de IA no soportado: $provider");
        }
    }
    
    /**
     * Truncamiento inteligente que preserva el inicio y contexto importante
     */
    private function intelligentTruncate($text, $maxLength)
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        // Buscar puntos de corte inteligentes (puntos, comas después de montos)
        $truncated = substr($text, 0, $maxLength - 3);
        
        // Intentar cortar en una oración completa
        $lastPeriod = strrpos($truncated, '.');
        if ($lastPeriod !== false && $lastPeriod > $maxLength * 0.7) {
            return substr($text, 0, $lastPeriod + 1);
        }
        
        // Intentar cortar después de un monto
        if (preg_match('/\$[\d,]+\.?\d*/', $truncated, $matches, PREG_OFFSET_CAPTURE)) {
            $lastMontoEnd = $matches[0][1] + strlen($matches[0][0]);
            if ($lastMontoEnd > $maxLength * 0.6) {
                $afterMonto = strpos($text, ',', $lastMontoEnd);
                if ($afterMonto !== false && $afterMonto < $maxLength) {
                    return substr($text, 0, $afterMonto) . '...';
                }
            }
        }
        
        // Fallback: corte simple con puntos suspensivos
        return $truncated . '...';
    }
}
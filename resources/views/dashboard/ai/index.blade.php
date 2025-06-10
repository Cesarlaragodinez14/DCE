<x-app-layout>
    <style>
        /* Clases de padding generales */
        .p-1 {
            padding: 0.25rem;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-3 {
            padding: 0.75rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        /* Padding horizontal y vertical */
        .py-1 {
            padding-top: 0.25rem; 
            padding-bottom: 0.25rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .py-6 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .px-1 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .px-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        /* Padding en direcciones específicas */
        .pt-1 {
            padding-top: 0.25rem;
        }

        .pt-2 {
            padding-top: 0.5rem;
        }

        .pt-4 {
            padding-top: 1rem;
        }

        .pr-1 {
            padding-right: 0.25rem;
        }

        .pr-2 {
            padding-right: 0.5rem;
        }

        .pb-1 {
            padding-bottom: 0.25rem;
        }

        .pb-2 {
            padding-bottom: 0.5rem;
        }

        .pl-1 {
            padding-left: 0.25rem;
        }

        .pl-2 {
            padding-left: 0.5rem;
        }

        .pl-5 {
            padding-left: 1.25rem;
        }

        /* Valores adicionales */
        .py-2\.5 {
            padding-top: 0.625rem;
            padding-bottom: 0.625rem;
        }

        .px-1\.5 {
            padding-left: 0.375rem;
            padding-right: 0.375rem;
        }

        .py-0\.5 {
            padding-top: 0.125rem;
            padding-bottom: 0.125rem;
        }

        /* Clases específicas de padding que se usan en tu código */
        .p-1\.5 {
            padding: 0.375rem;
        }

        .px-1\.5 {
            padding-left: 0.375rem;
            padding-right: 0.375rem;
        }

        .py-0\.5 {
            padding-top: 0.125rem;
            padding-bottom: 0.125rem;
        }

        .p-2\.5 {
            padding: 0.625rem;
        }

        .px-3\.5 {
            padding-left: 0.875rem;
            padding-right: 0.875rem;
        }

        .py-1\.5 {
            padding-top: 0.375rem;
            padding-bottom: 0.375rem;
        }

        .p-0\.5 {
            padding: 0.125rem;
        }
        /* Variables CSS mejoradas */
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --primary-gradient: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            --success-color: #10b981;
            --success-light: #34d399;
            --success-dark: #059669;
            --success-gradient: linear-gradient(135deg, var(--success-color), var(--success-light));
            --border-color: #e5e7eb;
            --text-color: #1f2937;
            --text-muted: #6b7280;
            --white: #ffffff;
            --bg-light: #f9fafb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --transition-normal: all 0.3s ease;
            --transition-fast: all 0.15s ease;
        }

        /* Estilos de layout para el chat */
        .chat-layout {
            display: grid;
            grid-template-columns: 250px 300px 1fr;
            height: calc(100vh - 140px);
            min-height: 600px;
            gap: 0;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        @media (max-width: 1200px) {
            .chat-layout {
                grid-template-columns: 250px 1fr;
            }
            
            .chat-sidebar {
                grid-column: 2;
            }
        }

        @media (max-width: 768px) {
            .chat-layout {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
            }
            
            .filters-sidebar, .chat-sidebar {
                display: none;
            }

            .mobile-sidebar-toggle, .mobile-filters-toggle {
                display: flex;
            }
        }

        /* Estilo para el panel de filtros */
        .filters-sidebar {
            background-color: white;
            border-right: 1px solid var(--border-color);
            padding: 1.25rem;
            overflow-y: auto;
        }

        .filters-heading {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        /* Mejoras en los mensajes */
        .chat-message {
            max-width: 85%;
            position: relative;
            line-height: 1.5;
        }

        .user-message {
            background: var(--primary-gradient);
            border: none;
            border-radius: var(--radius-lg) var(--radius-lg) 0 var(--radius-lg);
            margin-left: auto;
            color: white;
            box-shadow: var(--shadow-md);
        }

        .assistant-message {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg) var(--radius-lg) var(--radius-lg) 0;
            color: var(--text-color);
            box-shadow: var(--shadow-sm);
        }

        .message-container {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 40px;
            padding-right: 40px;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            position: absolute;
            bottom: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.75rem;
            color: white;
            box-shadow: var(--shadow-md);
        }

        .user-avatar {
            background: var(--primary-gradient);
            right: 0;
        }

        .assistant-avatar {
            background: var(--success-gradient);
            left: 0;
        }

        /* Indicador de escritura mejorado */
        .typing-indicator {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background-color: var(--success-color);
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
            opacity: 0.8;
        }
        
        .typing-indicator span:nth-child(1) {
            animation: typing 1.2s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation: typing 1.2s infinite 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation: typing 1.2s infinite 0.4s;
        }

        @keyframes typing {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
                background-color: var(--success-light);
            }
        }

        /* Mejoras en la barra lateral */
        .chat-sidebar {
            background-color: white;
            border-right: 1px solid var(--border-color);
            transition: var(--transition-normal);
        }

        .sidebar-section {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-heading {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .sidebar-heading svg {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        /* Mejoras en el input y botones */
        .chat-input-container {
            position: relative;
            background-color: white;
            border-top: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .chat-input-wrapper {
            position: relative;
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .chat-input {
            flex-grow: 1;
            background-color: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 0.75rem 3rem 0.75rem 1rem;
            font-size: 0.95rem;
            color: var(--text-color);
            transition: var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }

        .chat-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .input-actions {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 0.5rem;
        }

        .input-action-button {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 50%;
            transition: var(--transition-fast);
        }

        .input-action-button:hover {
            color: var(--primary-color);
            background-color: rgba(59, 130, 246, 0.1);
        }

        .send-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            transition: var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }

        .send-button:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-md);
        }

        .send-button svg {
            margin-left: 0.5rem;
            width: 1rem;
            height: 1rem;
        }

        /* Mejoras en el header del chat */
        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1.5rem;
            background-color: white;
            border-bottom: 1px solid var(--border-color);
        }

        .chat-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--success-color);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        /* Mejoras en el área de mensajes */
        .chat-messages-container {
            padding: 1.5rem;
            background-color: var(--bg-light);
            overflow-y: auto;
            height: calc(100vh - 300px); /* Altura fija restando header y footer */
            scrollbar-width: thin;
            scrollbar-color: var(--primary-light) var(--bg-light);
        }

        .chat-messages-container::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages-container::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        .chat-messages-container::-webkit-scrollbar-thumb {
            background-color: var(--primary-light);
            border-radius: 4px;
        }

        /* Estilos para el historial de conversaciones */
        .conversation-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 0.75rem;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: var(--transition-fast);
            border: 1px solid var(--border-color);
        }

        .conversation-item:hover {
            background-color: var(--bg-light);
        }

        .conversation-item.active {
            background-color: var(--primary-light);
            color: white;
        }

        /* Atajos de comandos */
        .command-badge {
            display: inline-flex;
            align-items: center;
            background-color: var(--bg-light);
            color: var(--text-color);
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            margin-right: 0.5rem;
            font-family: monospace;
            border: 1px solid var(--border-color);
        }

        /* Tooltip para los comandos */
        .command-tooltip {
            position: relative;
            cursor: pointer;
        }

        .command-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--text-color);
            color: white;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 0.5rem;
        }

        /* Botón de toggle para móvil */
        .mobile-sidebar-toggle, .mobile-filters-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-muted);
            margin-right: 0.5rem;
            cursor: pointer;
            padding: 0.375rem;
            border-radius: 0.375rem;
        }

        .mobile-sidebar-toggle:hover, .mobile-filters-toggle:hover {
            background-color: var(--bg-light);
            color: var(--primary-color);
        }

        @media (max-width: 640px) {
            .chat-message {
                max-width: 90%;
            }
            
            .chat-input-wrapper {
                flex-direction: column;
            }
            
            .send-button {
                width: 100%;
                margin-top: 0.5rem;
            }
        }
    </style>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg">
                <div class="chat-layout">
                    <!-- Panel lateral de filtros -->
                    <div class="filters-sidebar">
                        <h3 class="filters-heading">Filtros</h3>
                        
                        <!-- Filtro de Entrega -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Entrega</label>
                            <select id="filter-entrega" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar entrega</option>
                                @if(isset($catalogos['entregas']))
                                    @foreach($catalogos['entregas'] as $entrega)
                                        <option value="{{ $entrega->id }}">{{ $entrega->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Filtro de Cuenta Pública -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta Pública</label>
                            <select id="filter-cuenta-publica" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar cuenta pública</option>
                                @if(isset($catalogos['cuentasPublicas']))
                                    @foreach($catalogos['cuentasPublicas'] as $cuenta)
                                        <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Filtro de UAA -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">UAA</label>
                            <select id="filter-uaa" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar UAA</option>
                                @if(isset($catalogos['uaas']))
                                    @foreach($catalogos['uaas'] as $uaa)
                                        <option value="{{ $uaa->id }}">{{ $uaa->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Filtro de DG -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">DG</label>
                            <select id="filter-dg" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar DG</option>
                                @if(isset($catalogos['dgsegs']))
                                    @foreach($catalogos['dgsegs'] as $dg)
                                        <option value="{{ $dg->id }}">{{ $dg->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <button id="apply-filters" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Aplicar Filtros
                        </button>
                    </div>

                    <!-- Sidebar del chat mejorado -->
                    <div class="chat-sidebar">
                        <div class="sidebar-section">
                            <div class="sidebar-heading">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                                Asistente IA
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Este asistente inteligente te ayuda a resolver dudas sobre el sistema de expedientes.</p>
                            <button id="newChatBtn" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary-light text-white rounded-lg hover:bg-primary-dark transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Nueva conversación
                            </button>
                        </div>

                        <div class="sidebar-section">
                            <div class="sidebar-heading">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Historial
                            </div>
                            <div class="conversation-list mt-2">
                                <!-- Las conversaciones se cargarán aquí dinámicamente -->
                                <div class="text-sm text-gray-500 text-center py-4">
                                    No hay conversaciones guardadas.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Área principal del chat mejorada -->
                    <div class="chat-main flex flex-col">
                        <!-- Header del área de chat mejorado -->
                        <div class="chat-header">
                            <div class="flex items-center">
                                <button class="mobile-sidebar-toggle mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <button class="mobile-filters-toggle mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </button>
                                <div class="chat-status">
                                    <div class="status-indicator"></div>
                                    <span class="text-sm font-medium">Asistente conectado</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mensajes del chat mejorados -->
                        <div id="chatMessages" class="chat-messages-container flex-grow">
                            <!-- Mensaje de bienvenida del asistente -->
                            <div class="message-container">
                                <div class="message-avatar assistant-avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                </div>
                                <div class="chat-message assistant-message p-4">
                                    <p class="text-sm">¡Hola! Soy tu asistente virtual para expedientes. Puedo ayudarte con consultas sobre los procesos, estados y acciones del sistema.</p>
                                    <p class="text-sm mt-2">Puedes preguntarme sobre:</p>
                                    <ul class="list-disc pl-5 text-sm mt-1 space-y-1">
                                        <li>Información de expedientes específicos</li>
                                        <li>Explicación de estados y procedimientos</li>
                                        <li>Búsqueda de documentos por criterios</li>
                                        <li>Resúmenes de actividad reciente</li>
                                    </ul>
                                    <p class="text-sm mt-2">¿En qué puedo ayudarte hoy?</p>
                                </div>
                            </div> 
                            
                            <!-- Mensajes dinámicos se agregarán aquí mediante JavaScript -->
                        </div>
                        
                        <!-- Input de mensajes mejorado -->
                        <div class="chat-input-container">
                            <form id="chatForm" class="chat-input-wrapper">
                                <div class="relative w-full">
                                    <input 
                                        type="text" 
                                        id="userInput" 
                                        class="chat-input w-full" 
                                        placeholder="Escribe tu pregunta o usa un comando (ej: /buscar)..."
                                    >
                                    <div class="input-actions">
                                        <button type="button" class="input-action-button command-tooltip" data-tooltip="Ver comandos disponibles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Selector de proveedor y modelo de IA -->
                                <div class="flex flex-wrap items-center gap-2 mt-2 mb-2">
                                    <div class="relative">
                                        <select id="ai-provider" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                            @foreach($providers as $key => $name)
                                                <option value="{{ $key }}" {{ $key == $defaultProvider ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="relative">
                                        <select id="ai-model" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                            <!-- Se llenará con JavaScript -->
                                        </select>
                                    </div>
                                </div>
                                
                                <button type="submit" class="send-button">
                                    Enviar
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chatForm');
            const userInput = document.getElementById('userInput');
            const chatMessages = document.getElementById('chatMessages');
            const mobileSidebarToggle = document.querySelector('.mobile-sidebar-toggle');
            const mobileFiltersToggle = document.querySelector('.mobile-filters-toggle');
            const chatSidebar = document.querySelector('.chat-sidebar');
            const filtersSidebar = document.querySelector('.filters-sidebar');
            
            // Toggle sidebar en móvil
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    if (chatSidebar.style.display === 'none' || getComputedStyle(chatSidebar).display === 'none') {
                        // Cerrar el panel de filtros si está abierto
                        if (filtersSidebar.style.display === 'block') {
                            filtersSidebar.style.display = 'none';
                        }
                        
                        // Mostrar el sidebar
                        chatSidebar.style.display = 'block';
                        chatSidebar.style.position = 'absolute';
                        chatSidebar.style.zIndex = '50';
                        chatSidebar.style.top = '0';
                        chatSidebar.style.left = '0';
                        chatSidebar.style.height = '100%';
                        chatSidebar.style.width = '80%';
                        chatSidebar.style.maxWidth = '300px';
                    } else {
                        chatSidebar.style.display = 'none';
                    }
                });
            }
            
            // Toggle panel de filtros en móvil
            if (mobileFiltersToggle) {
                mobileFiltersToggle.addEventListener('click', function() {
                    if (filtersSidebar.style.display === 'none' || getComputedStyle(filtersSidebar).display === 'none') {
                        // Cerrar el sidebar si está abierto
                        if (chatSidebar.style.display === 'block') {
                            chatSidebar.style.display = 'none';
                        }
                        
                        // Mostrar el panel de filtros
                        filtersSidebar.style.display = 'block';
                        filtersSidebar.style.position = 'absolute';
                        filtersSidebar.style.zIndex = '50';
                        filtersSidebar.style.top = '0';
                        filtersSidebar.style.left = '0';
                        filtersSidebar.style.height = '100%';
                        filtersSidebar.style.width = '80%';
                        filtersSidebar.style.backgroundColor = 'white';
                    } else {
                        filtersSidebar.style.display = 'none';
                    }
                });
            }
            
            // Cerrar paneles cuando se toca fuera en móvil
            document.addEventListener('click', function(event) {
                const isMobile = window.matchMedia('(max-width: 768px)').matches;
                
                if (isMobile) {
                    // Cerrar sidebar si está abierto
                    if (chatSidebar.style.display === 'block') {
                        if (!chatSidebar.contains(event.target) && event.target !== mobileSidebarToggle) {
                            chatSidebar.style.display = 'none';
                        }
                    }
                    
                    // Cerrar panel de filtros si está abierto
                    if (filtersSidebar.style.display === 'block') {
                        if (!filtersSidebar.contains(event.target) && event.target !== mobileFiltersToggle) {
                            filtersSidebar.style.display = 'none';
                        }
                    }
                }
            });
            
            // Configuración de modelos de IA
            const models = @json($models);
            const defaultModel = '{{ $defaultModel }}';
            const providerSelect = document.getElementById('ai-provider');
            const modelSelect = document.getElementById('ai-model');
            
            // Función para actualizar la lista de modelos según el proveedor seleccionado
            function updateModels() {
                const provider = providerSelect.value;
                const providerModels = models[provider] || {};
                
                // Limpiar select de modelos
                modelSelect.innerHTML = '';
                
                // Agregar opciones de modelos
                Object.entries(providerModels).forEach(([modelId, modelName]) => {
                    const option = document.createElement('option');
                    option.value = modelId;
                    option.textContent = modelName;
                    
                    // Seleccionar modelo por defecto si coincide
                    if (modelId === defaultModel) {
                        option.selected = true;
                    }
                    
                    modelSelect.appendChild(option);
                });
            }
            
            // Actualizar modelos al cambiar de proveedor
            providerSelect.addEventListener('change', updateModels);
            
            // Inicializar lista de modelos
            updateModels();
            
            /**
             * Función para agregar mensajes a la conversación con animación mejorada
             * @param {string} text - El texto del mensaje
             * @param {'user'|'assistant'} sender - Quien envía el mensaje
             */
            function addMessage(text, sender) {
                const messageContainer = document.createElement('div');
                messageContainer.classList.add('message-container');
                messageContainer.style.opacity = '0';
                messageContainer.style.transform = 'translateY(20px)';
                messageContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
                if (sender === 'user') {
                    // Mensaje del usuario (alineado a la derecha)
                    messageContainer.innerHTML = `
                        <div class="message-avatar user-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="chat-message user-message p-4">
                            <p class="text-sm">${formatMessageText(text)}</p>
                        </div>
                    `;
                } else {
                    // Mensaje del asistente (alineado a la izquierda)
                    messageContainer.innerHTML = `
                        <div class="message-avatar assistant-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                      stroke-width="2" 
                                      d="M9 3v2m6-2v2M9 19v2m6-2v2
                                         M5 9H3m2 6H3m18-6h-2m2 6h-2
                                         M7 19h10a2 2 0 002-2V7
                                         a2 2 0 00-2-2H7a2 2 0 00-2 2v10
                                         a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>
                        <div class="chat-message assistant-message p-4">
                            <p class="text-sm">${formatMessageText(text)}</p>
                        </div>
                    `;
                }
        
                chatMessages.appendChild(messageContainer);
                
                // Animar entrada del mensaje
                setTimeout(() => {
                    messageContainer.style.opacity = '1';
                    messageContainer.style.transform = 'translateY(0)';
                }, 10);
                
                scrollToBottom();
            }
            
            /**
             * Formatea el texto del mensaje para manejar comandos, URLs, etc.
             * @param {string} text - El texto a formatear
             * @return {string} - El texto formateado con HTML
             */
            function formatMessageText(text) {
                // Detectar comandos y formatearlos
                text = text.replace(/\/([a-zA-Z0-9_]+)/g, '<span class="bg-gray-100 text-primary-color rounded px-1 font-mono">/$1</span>');
                
                // Convertir URLs en links
                const urlRegex = /(https?:\/\/[^\s]+)/g;
                text = text.replace(urlRegex, '<a href="$1" target="_blank" class="text-primary-color underline">$1</a>');
                
                // Manejar saltos de línea
                text = text.replace(/\n/g, '<br>');
                
                return text;
            }
        
            /**
             * Muestra el indicador de escritura (typing) mejorado
             */
            function showTypingIndicator() {
                const typingContainer = document.createElement('div');
                typingContainer.id = 'typingIndicator';
                typingContainer.classList.add('message-container');
                typingContainer.style.opacity = '0';
                typingContainer.style.transform = 'translateY(10px)';
                typingContainer.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                
                typingContainer.innerHTML = `
                    <div class="message-avatar assistant-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                  stroke-width="2" 
                                  d="M9 3v2m6-2v2M9 19v2m6-2v2
                                     M5 9H3m2 6H3m18-6h-2m2 6h-2
                                     M7 19h10a2 2 0 002-2V7
                                     a2 2 0 00-2-2H7a2 2 0 00-2 2v10
                                     a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <div class="chat-message assistant-message py-3 px-4">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                `;
                
                chatMessages.appendChild(typingContainer);
                
                // Animar entrada del indicador
                setTimeout(() => {
                    typingContainer.style.opacity = '1';
                    typingContainer.style.transform = 'translateY(0)';
                }, 10);
                
                scrollToBottom();
            }
        
            /**
             * Elimina el indicador de escritura (typing)
             */
            function removeTypingIndicator() {
                const typingIndicator = document.getElementById('typingIndicator');
                if (typingIndicator) {
                    // Animar salida
                    typingIndicator.style.opacity = '0';
                    typingIndicator.style.transform = 'translateY(10px)';
                    
                    // Eliminar después de la animación
                    setTimeout(() => {
                        typingIndicator.remove();
                    }, 200);
                }
            }
        
            /**
             * Desplaza la vista al final de la conversación con animación suave
             */
            function scrollToBottom() {
                const lastMessage = chatMessages.lastElementChild;
                if (lastMessage) {
                    lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            }
            
            /**
             * Maneja comandos especiales en el input
             * @param {string} message - El mensaje a procesar
             * @returns {boolean} - true si se manejó un comando, false de lo contrario
             */
            function handleCommands(message) {
                // Comando para limpiar
                if (message.trim().toLowerCase() === '/limpiar' || message.trim().toLowerCase() === '/clear') {
                    chatMessages.innerHTML = '';
                    
                    // Mensaje de sistema limpio
                    let systemMessage = document.createElement('div');
                    systemMessage.className = 'bg-blue-100 text-blue-800 p-4 rounded-lg mb-4 text-sm';
                    systemMessage.textContent = 'Chat limpiado. ¿En qué puedo ayudarte?';
                    chatMessages.appendChild(systemMessage);
                    
                    // Limpiar el historial local
                    currentConversationId = null;
                    
                    return true;
                }
                
                // Comando para ayuda
                if (message.trim().toLowerCase() === '/ayuda' || message.trim().toLowerCase() === '/help') {
                    addMessage('Comandos disponibles:\n- /limpiar o /clear - Limpiar este chat\n- /ayuda o /help - Mostrar esta ayuda\n- /buscar [término] - Buscar expedientes\n- /estatus - Ver estados generales', 'assistant');
                    return true;
                }
                
                return false;
            }
        
            // Variables para el manejo de conversaciones
            let currentConversationId = null;

            // Función para cargar la lista de conversaciones desde el servidor
            function fetchConversations() {
                fetch("{{ route('ai.getConversations') }}")
                .then(response => response.json())
                .then(data => {
                    updateConversationList(data);
                })
                .catch(error => {
                    console.error('Error al cargar conversaciones:', error);
                });
            }

            // Actualizar la lista de conversaciones en la UI
            function updateConversationList(data) {
                const conversationList = document.querySelector('.conversation-list');
                if (!conversationList) return;
                
                conversationList.innerHTML = '';
                
                // Convertir objeto a array para poder iterarlo
                const conversations = Object.values(data);
                
                conversations.forEach(conv => {
                    const item = document.createElement('div');
                    item.className = `conversation-item ${conv.id === currentConversationId ? 'active' : ''}`;
                    item.textContent = conv.title;
                    item.onclick = () => loadConversation(conv.id);
                    conversationList.appendChild(item);
                });
            }

            // Cargar una conversación específica
            function loadConversation(conversationId) {
                fetch(`{{ route('ai.getConversation', ['id' => '_ID_']) }}`.replace('_ID_', conversationId))
                .then(response => response.json())
                .then(conversation => {
                    if (!conversation || conversation.error) {
                        console.error('Error al cargar conversación:', conversation.error || 'Conversación no encontrada');
                        return;
                    }
                    
                    // Actualizar el ID de conversación actual
                    currentConversationId = conversation.id;
                    
                    // Limpiar los mensajes actuales
                    chatMessages.innerHTML = '';
                    
                    // Mostrar los mensajes de la conversación
                    conversation.messages.forEach(msg => {
                        addMessage(msg.content, msg.role);
                    });
                    
                    // Actualizar el estado de las conversaciones en el sidebar
                    fetchConversations();
                })
                .catch(error => {
                    console.error('Error al cargar conversación:', error);
                });
            }

            // Botón para nueva conversación
            const newChatBtn = document.getElementById('newChatBtn');
            if (newChatBtn) {
                newChatBtn.addEventListener('click', function() {
                    // Reiniciar la conversación actual
                    currentConversationId = null;
                    
                    // Limpiar la ventana de chat
                    chatMessages.innerHTML = '';
                    
                    // Mostrar mensaje de bienvenida
                    addMessage('¡Hola! Soy SAES-AI, tu asistente. ¿En qué puedo ayudarte?', 'assistant');
                });
            }

            // Inicializar: cargar las conversaciones al iniciar
            fetchConversations();

            // Evento de envío del formulario (mensaje del usuario)
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
        
                const message = userInput.value.trim();
                if (message) {
                    if (handleCommands(message)) {
                        userInput.value = '';
                        return;
                    }
                    
                    addMessage(message, 'user');
                    userInput.value = '';
                    showTypingIndicator();
                    
                    fetch("{{ route('ai.sendMessage') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message,
                            provider: document.getElementById('ai-provider').value,
                            model: document.getElementById('ai-model').value,
                            includeContext: contextEnabled,
                            conversation_id: currentConversationId || null,
                            filters: {
                                entrega: entregaSelect.value || null,
                                cuenta_publica: cuentaPublicaSelect.value || null,
                                uaa_id: uaaSelect.value || null,
                                dg_id: dgSelect.value || null
                            }
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Extraer texto del error para mejor diagnóstico
                            return response.text().then(text => {
                                throw new Error(`Error de servidor (${response.status}): ${text.substring(0, 150)}...`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        removeTypingIndicator();
                        addMessage(data.assistantMessage, 'assistant');
                        
                        // Guardar el ID de conversación recibido del servidor
                        if (data.conversation_id && typeof data.conversation_id === 'string' && !currentConversationId) {
                            currentConversationId = data.conversation_id;
                            
                            // Si es una nueva conversación, actualizamos la lista
                            fetchConversations();
                        }
                    })
                    .catch(error => {
                        removeTypingIndicator();
                        console.error('Error fetch IA:', error);
                        addMessage('Lo siento, ocurrió un error con la IA. Por favor, intenta nuevamente en unos momentos. Error: ' + error.message, 'assistant');
                    });
                }
            });
            
            // Autocompletar comandos al escribir /
            userInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value === '/') {
                    // Mostrar una pequeña ayuda visual
                    const helpText = document.createElement('div');
                    helpText.id = 'commandHelp';
                    helpText.className = 'absolute bottom-full left-0 bg-white p-2 rounded-md shadow-md border border-gray-200 text-xs w-64';
                    helpText.innerHTML = `
                        <p class="font-medium text-gray-700 mb-1">Comandos disponibles:</p>
                        <div class="grid grid-cols-1 gap-1">
                            <div class="flex items-center">
                                <span class="command-badge">/buscar</span>
                                <span class="text-gray-600 ml-1">Buscar expedientes</span>
                            </div>
                            <div class="flex items-center">
                                <span class="command-badge">/estatus</span>
                                <span class="text-gray-600 ml-1">Ver estados</span>
                            </div>
                            <div class="flex items-center">
                                <span class="command-badge">/ayuda</span>
                                <span class="text-gray-600 ml-1">Ver todos los comandos</span>
                            </div>
                        </div>
                    `;
                    
                    // Eliminar si ya existe
                    const oldHelp = document.getElementById('commandHelp');
                    if (oldHelp) oldHelp.remove();
                    
                    const inputContainer = userInput.parentElement;
                    inputContainer.appendChild(helpText);
                    
                    // Eliminar después de 5 segundos o cuando se pulse Enter
                    setTimeout(() => {
                        const help = document.getElementById('commandHelp');
                        if (help) {
                            help.style.opacity = '0';
                            setTimeout(() => help.remove(), 300);
                        }
                    }, 5000);
                } else if (value.indexOf('/') === -1) {
                    // Si ya no hay /, quitar la ayuda
                    const help = document.getElementById('commandHelp');
                    if (help) help.remove();
                }
            });
            
            // Enfocar la caja de texto al cargar
            userInput.focus();

            // Variable global para seguir si hay filtros activos
            let contextEnabled = true;

            const applyFiltersBtn = document.getElementById('apply-filters');
            const entregaSelect = document.getElementById('filter-entrega');
            const cuentaPublicaSelect = document.getElementById('filter-cuenta-publica');
            const uaaSelect = document.getElementById('filter-uaa');
            const dgSelect = document.getElementById('filter-dg');

            // Función para actualizar el indicador de contexto
            function updateContextIndicator(enabled) {
                const indicator = document.getElementById('context-indicator');
                if (enabled) {
                    indicator.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                    indicator.textContent = 'Contexto: Activado';
                } else {
                    indicator.className = 'px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                    indicator.textContent = 'Contexto: Desactivado';
                }
            }

            applyFiltersBtn.addEventListener('click', function() {
                const filters = {
                    entrega: entregaSelect.value,
                    cuenta_publica: cuentaPublicaSelect.value,
                    uaa_id: uaaSelect.value,
                    dg_id: dgSelect.value
                };

                // Determinar si algún filtro está activo
                const hasActiveFilters = Object.values(filters).some(value => value !== '');
                
                // INVERTIR LA LÓGICA: Si hay filtros activos, activar el contexto
                contextEnabled = hasActiveFilters;

                // Actualizar el indicador visual
                updateContextIndicator(contextEnabled);

                // Actualizar la URL con los filtros
                const url = new URL(window.location.href);
                Object.entries(filters).forEach(([key, value]) => {
                    if (value) {
                        url.searchParams.set(key, value);
                    } else {
                        url.searchParams.delete(key);
                    }
                });
                window.history.pushState({}, '', url);

                // Limpiar el chat actual
                chatMessages.innerHTML = '';
                
                // Crear el mensaje de filtros aplicados
                const filterMessage = document.createElement('div');
                filterMessage.className = 'bg-blue-100 text-blue-800 p-4 rounded-lg mb-4';
                let filterText = 'Filtros aplicados: ';
                
                if (filters.entrega && entregaSelect.selectedIndex > 0) {
                    filterText += 'Entrega ' + entregaSelect.options[entregaSelect.selectedIndex].text + ', ';
                }
                if (filters.cuenta_publica && cuentaPublicaSelect.selectedIndex > 0) {
                    filterText += 'Cuenta Pública ' + cuentaPublicaSelect.options[cuentaPublicaSelect.selectedIndex].text + ', ';
                }
                if (filters.uaa_id && uaaSelect.selectedIndex > 0) {
                    filterText += 'UAA ' + uaaSelect.options[uaaSelect.selectedIndex].text + ', ';
                }
                if (filters.dg_id && dgSelect.selectedIndex > 0) {
                    filterText += 'DG ' + dgSelect.options[dgSelect.selectedIndex].text;
                }
                
                // Eliminar la coma final si existe
                filterText = filterText.replace(/,\s*$/, '');
                
                // Si no se seleccionó ningún filtro
                if (filterText === 'Filtros aplicados: ') {
                    filterText += 'Ninguno';
                    contextEnabled = false;
                } else {
                    // Añadir mensaje sobre el contexto
                    filterText += '<br>El asistente utilizará el contexto de datos filtrados.';
                }
                
                filterMessage.innerHTML = filterText;
                chatMessages.appendChild(filterMessage);
                
                // Mostrar un mensaje de bienvenida nuevamente
                addMessage('¡Hola! Los filtros han sido aplicados. ¿En qué puedo ayudarte?', 'assistant');
            });

            // Cargar filtros de la URL al iniciar
            const urlParams = new URLSearchParams(window.location.search);
            entregaSelect.value = urlParams.get('entrega') || '';
            cuentaPublicaSelect.value = urlParams.get('cuenta_publica') || '';
            uaaSelect.value = urlParams.get('uaa_id') || '';
            dgSelect.value = urlParams.get('dg_id') || '';
            
            // Comprobar si hay filtros activos al cargar la página
            const hasActiveFiltersOnLoad = [
                urlParams.get('entrega'),
                urlParams.get('cuenta_publica'),
                urlParams.get('uaa_id'),
                urlParams.get('dg_id')
            ].some(param => param !== null && param !== '');
            
            // INVERTIR LA LÓGICA: Si hay filtros activos, activar el contexto
            contextEnabled = hasActiveFiltersOnLoad;
            updateContextIndicator(contextEnabled);
            
            // Si hay filtros activos, mostrar un mensaje
            if (hasActiveFiltersOnLoad) {
                // Crear el mensaje de filtros aplicados
                const filterMessage = document.createElement('div');
                filterMessage.className = 'bg-blue-100 text-blue-800 p-4 rounded-lg mb-4';
                let filterText = 'Filtros activos: ';
                
                if (urlParams.get('entrega') && entregaSelect.selectedIndex > 0) {
                    filterText += 'Entrega ' + entregaSelect.options[entregaSelect.selectedIndex].text + ', ';
                }
                if (urlParams.get('cuenta_publica') && cuentaPublicaSelect.selectedIndex > 0) {
                    filterText += 'Cuenta Pública ' + cuentaPublicaSelect.options[cuentaPublicaSelect.selectedIndex].text + ', ';
                }
                if (urlParams.get('uaa_id') && uaaSelect.selectedIndex > 0) {
                    filterText += 'UAA ' + uaaSelect.options[uaaSelect.selectedIndex].text + ', ';
                }
                if (urlParams.get('dg_id') && dgSelect.selectedIndex > 0) {
                    filterText += 'DG ' + dgSelect.options[dgSelect.selectedIndex].text;
                }
                
                // Eliminar la coma final si existe
                filterText = filterText.replace(/,\s*$/, '');
                
                // Añadir mensaje sobre el contexto
                filterText += '<br>El asistente utilizará el contexto de datos filtrados.';
                
                filterMessage.innerHTML = filterText;
                chatMessages.insertBefore(filterMessage, chatMessages.firstChild);
            }
        });
    </script>
</x-app-layout>
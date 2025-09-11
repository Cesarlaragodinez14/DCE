<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asistente de IA') }}
        </h2>
    </x-slot>

    <!-- Metadatos para JavaScript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Archivos CSS y JS -->
    <link rel="stylesheet" href="{{ asset('css/ai-chat.css') }}">
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal del chat -->
            <div class="ai-chat-container">
                
                <!-- Sidebar de Filtros -->
                <aside id="filtersSidebar" class="filters-sidebar">
                    <div class="filters-header">
                        <h3 class="filters-title">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtros
                        </h3>
                    </div>
                    
                    <div class="filters-content">
                        <form id="filtersForm">
                        <!-- Filtro de Entrega -->
                            <div class="filter-group">
                                <label for="filter-entrega" class="filter-label">Entrega</label>
                                <select id="filter-entrega" class="filter-select">
                                    <option value="">Todas las entregas</option>
                                @if(isset($catalogos['entregas']))
                                    @foreach($catalogos['entregas'] as $entrega)
                                        <option value="{{ $entrega->id }}">{{ $entrega->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Filtro de Cuenta Pública -->
                            <div class="filter-group">
                                <label for="filter-cuenta-publica" class="filter-label">Cuenta Pública</label>
                                <select id="filter-cuenta-publica" class="filter-select">
                                    <option value="">Todas las cuentas públicas</option>
                                @if(isset($catalogos['cuentasPublicas']))
                                    @foreach($catalogos['cuentasPublicas'] as $cuenta)
                                        <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Filtro de UAA -->
                            <div class="filter-group">
                                <label for="filter-uaa" class="filter-label">UAA</label>
                                <select id="filter-uaa" class="filter-select">
                                    <option value="">Todas las UAA</option>
                                @if(isset($catalogos['uaas']))
                                    @foreach($catalogos['uaas'] as $uaa)
                                        <option value="{{ $uaa->id }}">{{ $uaa->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Filtro de DG -->
                            <div class="filter-group">
                                <label for="filter-dg" class="filter-label">DG</label>
                                <select id="filter-dg" class="filter-select">
                                    <option value="">Todas las DG</option>
                                @if(isset($catalogos['dgsegs']))
                                    @foreach($catalogos['dgsegs'] as $dg)
                                        <option value="{{ $dg->id }}">{{ $dg->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                            </div>
                        </form>
                        </div>

                    <div class="filters-actions">
                        <button type="submit" form="filtersForm" class="btn-apply-filters">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Aplicar Filtros
                        </button>
                    </div>
                </aside>

                <!-- Sidebar de Conversaciones -->
                <aside id="chatSidebar" class="chat-sidebar">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            SAES-AI
                        </h3>
                        <p class="sidebar-subtitle">
                            Tu asistente inteligente para el sistema de expedientes
                        </p>
                        <button id="newChatBtn" class="btn-new-chat">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            Nueva Conversación
                            </button>
                        </div>

                    <div class="conversations-section">
                        <h4 class="conversations-header">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Historial
                        </h4>
                        <div id="conversationList" class="conversation-list">
                            <div class="empty-state">
                                <p>No hay conversaciones guardadas</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Área Principal del Chat -->
                <main class="chat-main">
                    <!-- Header del Chat -->
                    <header class="chat-header">
                        <div class="mobile-toggles">
                            <button id="filtersToggle" class="mobile-toggle" title="Filtros">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </button>
                            <button id="sidebarToggle" class="mobile-toggle" title="Conversaciones">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                        </div>
                        
                        <div class="chat-status">
                            <div class="status-indicator"></div>
                            <span class="chat-title">Asistente SAES-AI</span>
                        </div>
                        
                        <div class="ai-settings">
                            <select id="ai-provider" class="ai-select">
                                            @foreach($providers as $key => $name)
                                    <option value="{{ $key }}" {{ $key == $defaultProvider ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                            @endforeach
                                        </select>
                            <select id="ai-model" class="ai-select">
                                <!-- Se llena con JavaScript -->
                                        </select>
                                    </div>
                    </header>

                    <!-- Mensajes del Chat -->
                    <div id="chatMessages" class="chat-messages">
                        <!-- Los mensajes se agregan dinámicamente aquí -->
                                </div>
                                
                    <!-- Área de Input -->
                    <div class="chat-input-area">
                        <form id="chatForm" class="chat-form">
                            <div class="input-wrapper">
                                <textarea 
                                    id="userInput" 
                                    class="chat-input" 
                                    placeholder="Escribe tu pregunta... (Presiona Enter para enviar, Shift+Enter para nueva línea)"
                                    rows="1"
                                ></textarea>
                                <button type="submit" id="sendButton" class="btn-send">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span class="visually-hidden">Enviar mensaje</span>
                                </button>
                            </div>
                            </form>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Overlay para móvil -->
    <div id="mobileOverlay" class="mobile-overlay"></div>

    <!-- Scripts -->
    <script>
        // Configuración global para JavaScript
        window.aiConfig = {
            models: @json($models),
            defaultModel: '{{ $defaultModel }}',
            defaultProvider: '{{ $defaultProvider }}',
            routes: {
                sendMessage: '{{ route("ai.sendMessage") }}',
                getConversations: '{{ route("ai.getConversations") }}',
                getConversation: '{{ url("/dashboard/ai/conversation") }}/__ID__'
            }
        };
    </script>
    <script src="{{ asset('js/ai-chat.js') }}"></script>
</x-app-layout>
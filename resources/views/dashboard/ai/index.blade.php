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
            grid-template-columns: 300px 1fr;
            height: calc(100vh - 140px);
            min-height: 600px;
            gap: 0;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .chat-layout {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
            }
            
            .chat-sidebar {
                display: none;
            }

            .mobile-sidebar-toggle {
                display: flex;
            }
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
        .mobile-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-muted);
            margin-right: 0.5rem;
        }

        /* Media queries adicionales */
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
                        </div>
 
                        <div class="sidebar-section p-4">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                                <p class="text-xs text-gray-500 font-medium">HISTORIAL DE CONVERSACIONES</p>
                                <p class="text-sm text-gray-700 mt-2">No hay conversaciones guardadas.</p>
                                <button class="mt-2 text-xs text-primary-color font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Nueva conversación
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Área principal del chat mejorada -->
                    <div class="chat-main flex flex-col">
                        <!-- Header del área de chat mejorado -->
                        <div class="chat-header">
                            <div class="flex items-center">
                                <button class="mobile-sidebar-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div class="chat-status">
                                    <div class="status-indicator"></div>
                                    <span class="text-sm font-medium">Asistente conectado</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
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
            const chatSidebar = document.querySelector('.chat-sidebar');
            
            // Toggle sidebar en móvil
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    if (chatSidebar.style.display === 'none' || getComputedStyle(chatSidebar).display === 'none') {
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
            
            // Cerrar sidebar cuando se toca fuera en móvil
            document.addEventListener('click', function(event) {
                const isMobile = window.matchMedia('(max-width: 768px)').matches;
                if (isMobile && chatSidebar.style.display === 'block') {
                    if (!chatSidebar.contains(event.target) && event.target !== mobileSidebarToggle) {
                        chatSidebar.style.display = 'none';
                    }
                }
            });
            
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
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            }
            
            /**
             * Maneja comandos especiales en el input
             * @param {string} message - El mensaje a procesar
             * @returns {boolean} - true si se manejó un comando, false de lo contrario
             */
            function handleCommands(message) {
                // Verificar si es un comando
                if (message.startsWith('/')) {
                    const command = message.split(' ')[0].toLowerCase();
                    
                    switch(command) {
                        case '/ayuda':
                            addMessage(message, 'user');
                            addMessage("Estos son los comandos disponibles:\n\n/buscar - Buscar expedientes por número o palabra clave\n/estatus - Ver todos los estados disponibles para expedientes\n/ayuda - Mostrar esta lista de comandos", 'assistant');
                            return true;
                            
                        case '/buscar':
                            addMessage(message, 'user');
                            addMessage("Para buscar un expediente, utiliza:\n\n/buscar [número o palabra clave]\n\nPor ejemplo: /buscar 123456 o /buscar contrato", 'assistant');
                            return true;
                            
                        case '/estatus':
                            addMessage(message, 'user');
                            addMessage("Estados disponibles para expedientes:\n\n• Pendiente\n• En revisión\n• Aprobado\n• Rechazado\n• En proceso\n• Finalizado\n• Archivado", 'assistant');
                            return true;
                    }
                }
                
                return false;
            }
        
            // Evento de envío del formulario (mensaje del usuario)
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
        
                const message = userInput.value.trim();
                if (message) {
                    // Manejar comandos especiales
                    if (handleCommands(message)) {
                        userInput.value = '';
                        return;
                    }
                    
                    // 1) Agregar mensaje del usuario en la interfaz
                    addMessage(message, 'user');
                    userInput.value = '';
        
                    // 2) Mostrar indicador de escritura
                    showTypingIndicator();
        
                    // 3) Llamar al backend con fetch
                    
                    fetch("{{ route('ai.sendMessage') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message })
                    })
                    .then(response => {
                        // En caso de error de status
                        if (!response.ok) {
                            throw new Error('Error de servidor, status ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // 4) Quitar indicador de escritura
                        removeTypingIndicator();
        
                        // data.assistantMessage => respuesta del asistente
                        addMessage(data.assistantMessage, 'assistant');
                    })
                    .catch(error => {
                        removeTypingIndicator();
                        console.error('Error fetch IA:', error);
                        addMessage('Lo siento, ocurrió un error con la IA. Por favor, intenta nuevamente en unos momentos.', 'assistant');
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
        });
    </script>
</x-app-layout>
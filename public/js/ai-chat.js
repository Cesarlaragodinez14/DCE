/**
 * SAES AI Chat Interface
 * Módulo JavaScript para la interfaz de chat con IA
 */

class AIChat {
    constructor() {
        this.currentConversationId = null;
        this.contextEnabled = true;
        this.isTyping = false;
        
        this.init();
    }

    init() {
        this.initializeElements();
        this.setupEventListeners();
        this.loadInitialData();
    }

    initializeElements() {
        // Elementos del DOM
        this.chatForm = document.getElementById('chatForm');
        this.userInput = document.getElementById('userInput');
        this.chatMessages = document.getElementById('chatMessages');
        this.providerSelect = document.getElementById('ai-provider');
        this.modelSelect = document.getElementById('ai-model');
        this.sendButton = document.getElementById('sendButton');
        
        // Elementos de filtros
        this.filtersForm = document.getElementById('filtersForm');
        this.entregaSelect = document.getElementById('filter-entrega');
        this.cuentaPublicaSelect = document.getElementById('filter-cuenta-publica');
        this.uaaSelect = document.getElementById('filter-uaa');
        this.dgSelect = document.getElementById('filter-dg');
        
        // Elementos de sidebar
        this.conversationList = document.getElementById('conversationList');
        this.newChatBtn = document.getElementById('newChatBtn');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        this.filtersToggle = document.getElementById('filtersToggle');
    }

    setupEventListeners() {
        // Chat form
        if (this.chatForm) {
            this.chatForm.addEventListener('submit', (e) => this.handleSendMessage(e));
        }

        // Provider change
        if (this.providerSelect) {
            this.providerSelect.addEventListener('change', () => this.updateModels());
        }

        // Nueva conversación
        if (this.newChatBtn) {
            this.newChatBtn.addEventListener('click', () => this.startNewConversation());
        }

        // Filtros
        if (this.filtersForm) {
            this.filtersForm.addEventListener('submit', (e) => this.applyFilters(e));
        }

        // Sidebar toggles
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => this.toggleSidebar());
        }

        if (this.filtersToggle) {
            this.filtersToggle.addEventListener('click', () => this.toggleFilters());
        }

        // Auto-resize textarea
        if (this.userInput) {
            this.userInput.addEventListener('input', () => this.autoResizeInput());
            this.userInput.addEventListener('keydown', (e) => this.handleKeydown(e));
        }
    }

    loadInitialData() {
        this.updateModels();
        this.loadConversations();
        this.loadFiltersFromURL();
        this.showWelcomeMessage();
    }

    // Manejo de mensajes
    async handleSendMessage(e) {
        e.preventDefault();
        
        const message = this.userInput.value.trim();
        if (!message || this.isTyping) return;

        // Manejar comandos especiales
        if (this.handleCommands(message)) {
            this.userInput.value = '';
            return;
        }

        this.addMessage(message, 'user');
        this.userInput.value = '';
        this.showTypingIndicator();
        this.isTyping = true;

        try {
            const response = await this.sendToAI(message);
            this.removeTypingIndicator();
            this.addMessage(response.assistantMessage, 'assistant');
            
            if (response.conversation_id && !this.currentConversationId) {
                this.currentConversationId = response.conversation_id;
                this.loadConversations();
            }
        } catch (error) {
            this.removeTypingIndicator();
            this.addMessage('Lo siento, ocurrió un error. Por favor, intenta nuevamente.', 'assistant', 'error');
            console.error('Error:', error);
        } finally {
            this.isTyping = false;
        }
    }

    async sendToAI(message) {
        const response = await fetch(window.aiConfig.routes.sendMessage, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message,
                provider: this.providerSelect?.value || 'groq',
                model: this.modelSelect?.value,
                includeContext: this.contextEnabled,
                conversation_id: this.currentConversationId,
                filters: this.getActiveFilters()
            })
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    // Manejo de mensajes en UI
    addMessage(text, sender, type = 'normal') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-item ${sender}-message ${type === 'error' ? 'error-message' : ''}`;
        
        const avatar = this.createAvatar(sender);
        const content = this.createMessageContent(text, sender, type);
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(content);
        
        this.chatMessages.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Animación de entrada
        requestAnimationFrame(() => {
            messageDiv.classList.add('show');
        });
    }

    createAvatar(sender) {
        const avatar = document.createElement('div');
        avatar.className = `message-avatar ${sender}-avatar`;
        
        if (sender === 'user') {
            avatar.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            `;
        } else {
            avatar.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            `;
        }
        
        return avatar;
    }

    createMessageContent(text, sender, type) {
        const content = document.createElement('div');
        content.className = `message-content ${type === 'error' ? 'error-content' : ''}`;
        
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.innerHTML = this.formatMessage(text);
        
        content.appendChild(bubble);
        
        // Timestamp
        const timestamp = document.createElement('div');
        timestamp.className = 'message-timestamp';
        timestamp.textContent = new Date().toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        content.appendChild(timestamp);
        
        return content;
    }

    formatMessage(text) {
        // Preservar espacios en blanco al inicio/final
        text = text.trim();
        
        // Si ya contiene HTML, procesarlo cuidadosamente
        if (text.includes('<') && text.includes('>')) {
            // Saltos de línea 
            text = text.replace(/\n/g, '<br>');
            
            // Solo procesar comandos y URLs que no estén dentro de etiquetas
            text = this.formatTextOutsideHTML(text);
            
            return text;
        } else {
            // Para texto plano, aplicar escape básico y formateo
            text = text.replace(/&/g, '&amp;')
                       .replace(/</g, '&lt;')
                       .replace(/>/g, '&gt;');
            
            // Detectar comandos
            text = text.replace(/\/([a-zA-Z0-9_]+)/g, '<span class="command-highlight">/$1</span>');
            
            // Convertir URLs
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            text = text.replace(urlRegex, '<a href="$1" target="_blank" class="message-link">$1</a>');
            
            // Saltos de línea
            text = text.replace(/\n/g, '<br>');
            
            return text;
        }
    }
    
    formatTextOutsideHTML(text) {
        // Función auxiliar para formatear texto que no esté dentro de etiquetas HTML
        const parts = text.split(/(<[^>]*>)/);
        
        for (let i = 0; i < parts.length; i++) {
            // Solo procesar partes que no sean etiquetas HTML
            if (!parts[i].startsWith('<') || !parts[i].endsWith('>')) {
                // Detectar comandos
                parts[i] = parts[i].replace(/\/([a-zA-Z0-9_]+)/g, '<span class="command-highlight">/$1</span>');
                
                // Convertir URLs (más cuidadoso)
                const urlRegex = /(^|[^"'=])(https?:\/\/[^\s<>"']+)/g;
                parts[i] = parts[i].replace(urlRegex, '$1<a href="$2" target="_blank" class="message-link">$2</a>');
            }
        }
        
        return parts.join('');
    }

    showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'typingIndicator';
        indicator.className = 'message-item assistant-message typing';
        
        indicator.innerHTML = `
            <div class="message-avatar assistant-avatar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <div class="message-content">
                <div class="message-bubble">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        this.chatMessages.appendChild(indicator);
        this.scrollToBottom();
    }

    removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.remove();
        }
    }

    scrollToBottom() {
        if (this.chatMessages) {
            this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        }
    }

    // Manejo de comandos
    handleCommands(message) {
        const cmd = message.trim().toLowerCase();
        
        switch (cmd) {
            case '/limpiar':
            case '/clear':
                this.clearChat();
                return true;
            case '/ayuda':
            case '/help':
                this.showHelp();
                return true;
            default:
                return false;
        }
    }

    clearChat() {
        this.chatMessages.innerHTML = '';
        this.currentConversationId = null;
        this.showWelcomeMessage();
    }

    showHelp() {
        const helpText = `
            <strong>Comandos disponibles:</strong><br>
            • <span class="command-highlight">/limpiar</span> - Limpiar chat<br>
            • <span class="command-highlight">/ayuda</span> - Mostrar esta ayuda<br>
            • <span class="command-highlight">/buscar [término]</span> - Buscar expedientes<br>
            • <span class="command-highlight">/estatus</span> - Ver estados del sistema
        `;
        this.addMessage(helpText, 'assistant');
    }

    showWelcomeMessage() {
        const welcomeText = `
            ¡Hola! Soy <strong>SAES-AI</strong>, tu asistente virtual. 
            Puedo ayudarte con consultas sobre expedientes, auditorías y procedimientos del sistema.
            <br><br>
            ¿En qué puedo ayudarte hoy?
        `;
        this.addMessage(welcomeText, 'assistant');
    }

    // Conversaciones
    async loadConversations() {
        try {
            const response = await fetch(window.aiConfig.routes.getConversations);
            const conversations = await response.json();
            this.updateConversationsList(conversations);
        } catch (error) {
            console.error('Error cargando conversaciones:', error);
        }
    }

    updateConversationsList(conversations) {
        if (!this.conversationList) return;
        
        this.conversationList.innerHTML = '';
        
        if (Object.keys(conversations).length === 0) {
            this.conversationList.innerHTML = `
                <div class="empty-state">
                    <p class="text-sm text-gray-500">No hay conversaciones guardadas</p>
                </div>
            `;
            return;
        }

        Object.values(conversations).forEach(conv => {
            const item = document.createElement('div');
            item.className = `conversation-item ${conv.id === this.currentConversationId ? 'active' : ''}`;
            item.innerHTML = `
                <div class="conversation-title">${conv.title}</div>
                <div class="conversation-date">${this.formatDate(conv.created_at)}</div>
            `;
            item.addEventListener('click', () => this.loadConversation(conv.id));
            this.conversationList.appendChild(item);
        });
    }

    async loadConversation(id) {
        try {
            const url = window.aiConfig.routes.getConversation.replace('__ID__', id);
            const response = await fetch(url);
            const conversation = await response.json();
            
            this.currentConversationId = id;
            this.chatMessages.innerHTML = '';
            
            conversation.messages.forEach(msg => {
                this.addMessage(msg.content, msg.role);
            });
            
            this.updateConversationsList(await this.getConversations());
            this.closeMobileSidebars();
        } catch (error) {
            console.error('Error cargando conversación:', error);
        }
    }

    startNewConversation() {
        this.currentConversationId = null;
        this.clearChat();
    }

    // Filtros
    applyFilters(e) {
        e.preventDefault();
        
        const filters = this.getActiveFilters();
        const hasFilters = Object.values(filters).some(value => value);
        
        // La IA siempre debe recibir contexto
        this.contextEnabled = true;
        this.updateURL(filters);
        this.clearChat();
        
        if (hasFilters) {
            this.showFiltersAppliedMessage(filters);
        }
    }

    getActiveFilters() {
        return {
            entrega: this.entregaSelect?.value || null,
            cuenta_publica: this.cuentaPublicaSelect?.value || null,
            uaa_id: this.uaaSelect?.value || null,
            dg_id: this.dgSelect?.value || null
        };
    }

    loadFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        
        if (this.entregaSelect) this.entregaSelect.value = params.get('entrega') || '';
        if (this.cuentaPublicaSelect) this.cuentaPublicaSelect.value = params.get('cuenta_publica') || '';
        if (this.uaaSelect) this.uaaSelect.value = params.get('uaa_id') || '';
        if (this.dgSelect) this.dgSelect.value = params.get('dg_id') || '';
        
        // La IA siempre debe recibir contexto, con o sin filtros
        this.contextEnabled = true;
    }

    updateURL(filters) {
        const url = new URL(window.location.href);
        
        Object.entries(filters).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });
        
        window.history.pushState({}, '', url);
    }

    showFiltersAppliedMessage(filters) {
        const filterNames = [];
        
        if (filters.entrega && this.entregaSelect?.selectedOptions[0]) {
            filterNames.push(`Entrega: ${this.entregaSelect.selectedOptions[0].text}`);
        }
        if (filters.cuenta_publica && this.cuentaPublicaSelect?.selectedOptions[0]) {
            filterNames.push(`Cuenta Pública: ${this.cuentaPublicaSelect.selectedOptions[0].text}`);
        }
        if (filters.uaa_id && this.uaaSelect?.selectedOptions[0]) {
            filterNames.push(`UAA: ${this.uaaSelect.selectedOptions[0].text}`);
        }
        if (filters.dg_id && this.dgSelect?.selectedOptions[0]) {
            filterNames.push(`DG: ${this.dgSelect.selectedOptions[0].text}`);
        }
        
        const message = `
            <div class="filter-applied-message">
                <strong>🔍 Filtros aplicados:</strong><br>
                ${filterNames.join('<br>')}
                <br><br>
                <em>El asistente utilizará el contexto de los datos filtrados.</em>
            </div>
        `;
        
        this.addMessage(message, 'assistant');
    }

    // Utilidades
    updateModels() {
        if (!this.providerSelect || !this.modelSelect) return;
        
        const provider = this.providerSelect.value;
        const models = window.aiConfig?.models?.[provider] || {};
        
        // Limpiar opciones actuales
        this.modelSelect.innerHTML = '';
        
        // Agregar nuevas opciones
        Object.entries(models).forEach(([modelId, modelName]) => {
            const option = document.createElement('option');
            option.value = modelId;
            option.textContent = modelName;
            
            // Seleccionar modelo por defecto
            if (modelId === window.aiConfig?.defaultModel) {
                option.selected = true;
            }
            
            this.modelSelect.appendChild(option);
        });
    }

    autoResizeInput() {
        if (this.userInput) {
            this.userInput.style.height = 'auto';
            this.userInput.style.height = Math.min(this.userInput.scrollHeight, 120) + 'px';
        }
    }

    handleKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.handleSendMessage(e);
        }
    }

    toggleSidebar() {
        const sidebar = document.getElementById('chatSidebar');
        const filters = document.getElementById('filtersSidebar');
        const overlay = document.getElementById('mobileOverlay');
        
        if (sidebar) {
            // Cerrar filtros si están abiertos
            if (filters && filters.classList.contains('open')) {
                filters.classList.remove('open');
            }
            
            // Toggle sidebar
            sidebar.classList.toggle('open');
            
            // Mostrar/ocultar overlay
            if (overlay) {
                if (sidebar.classList.contains('open')) {
                    overlay.classList.add('active');
                    overlay.onclick = () => this.closeMobileSidebars();
                } else {
                    overlay.classList.remove('active');
                    overlay.onclick = null;
                }
            }
        }
    }

    toggleFilters() {
        const filters = document.getElementById('filtersSidebar');
        const sidebar = document.getElementById('chatSidebar');
        const overlay = document.getElementById('mobileOverlay');
        
        if (filters) {
            // Cerrar sidebar si está abierto
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
            
            // Toggle filtros
            filters.classList.toggle('open');
            
            // Mostrar/ocultar overlay
            if (overlay) {
                if (filters.classList.contains('open')) {
                    overlay.classList.add('active');
                    overlay.onclick = () => this.closeMobileSidebars();
                } else {
                    overlay.classList.remove('active');
                    overlay.onclick = null;
                }
            }
        }
    }

    closeMobileSidebars() {
        const sidebar = document.getElementById('chatSidebar');
        const filters = document.getElementById('filtersSidebar');
        const overlay = document.getElementById('mobileOverlay');
        
        if (sidebar) sidebar.classList.remove('open');
        if (filters) filters.classList.remove('open');
        if (overlay) {
            overlay.classList.remove('active');
            overlay.onclick = null;
        }
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    async getConversations() {
        const response = await fetch(window.aiConfig.routes.getConversations);
        return await response.json();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.aiChat = new AIChat();
}); 
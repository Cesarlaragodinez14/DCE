<style>
    :root {
        /* Colores primarios */
        --primary-color: #2563eb;
        --primary-light: #3b82f6;
        --primary-dark: #1d4ed8;
        --primary-gradient: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        
        /* Colores de éxito */
        --success-color: #10b981;
        --success-light: #34d399;
        --success-dark: #059669;
        --success-gradient: linear-gradient(135deg, var(--success-color), var(--success-light));
        
        /* Colores de advertencia */
        --warning-color: #f59e0b;
        --warning-light: #fbbf24;
        --warning-dark: #d97706;
        
        /* Colores de error */
        --error-color: #ef4444;
        --error-light: #f87171;
        --error-dark: #dc2626;
        
        /* Colores neutrales */
        --border-color: #e5e7eb;
        --text-color: #1f2937;
        --text-muted: #6b7280;
        --white: #ffffff;
        --bg-light: #f9fafb;
        --bg-gray: #f3f4f6;
        
        /* Sombras */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        
        /* Bordes redondeados */
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
        
        /* Transiciones */
        --transition-normal: all 0.3s ease;
        --transition-fast: all 0.15s ease;
        
        /* Espaciado */
        --spacing-xs: 0.25rem;
        --spacing-sm: 0.5rem;
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
    }

    /* General Styles */
    body {
        color: var(--text-color);
        background-color: var(--bg-gray);
        font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .card {
        background-color: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        transition: var(--transition-normal);
        border: 1px solid var(--border-color);
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--bg-light);
        border-top-left-radius: var(--radius-lg);
        border-top-right-radius: var(--radius-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-body {
        padding: 1.25rem;
    }

    /* Botones */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        transition: var(--transition-fast);
        cursor: pointer;
        gap: 0.375rem;
    }

    /* Primary Button */
    .btn-primary {
        background: var(--primary-gradient);
        color: var(--white);
        border: none;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* Success Button */
    .btn-success {
        background: var(--success-gradient);
        color: var(--white);
        border: none;
        box-shadow: var(--shadow-sm);
    }

    .btn-success:hover {
        background: var(--success-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* Secondary Button */
    .btn-secondary {
        background-color: var(--bg-light);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background-color: #e5e7eb;
    }

    /* Danger Button */
    .btn-danger {
        background-color: var(--error-color);
        color: var(--white);
        border: none;
    }

    .btn-danger:hover {
        background-color: var(--error-dark);
        transform: translateY(-1px);
    }

    /* Small button */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: var(--radius-lg);
        overflow: hidden;
        font-size: 0.75rem;
    }

    .data-table th {
        background-color: var(--primary-color);
        color: var(--white);
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        padding: 0.625rem 0.5rem;
        text-align: left;
        position: sticky;
        top: 0;
        white-space: nowrap;
    }

    .data-table tr:nth-child(even) {
        background-color: rgba(243, 244, 246, 0.5);
    }

    .data-table tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }

    .data-table td {
        padding: 0.625rem 0.5rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        line-height: 1.2;
    }

    /* Estados de filas */
    .row-success {
        background-color: rgba(16, 185, 129, 0.05) !important;
    }

    .row-warning {
        background-color: rgba(245, 158, 11, 0.05) !important;
    }

    /* Custom Form Elements */
    .form-group {
        margin-bottom: 0.75rem;
    }

    .form-select, .form-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background-color: var(--white);
        transition: var(--transition-fast);
        font-size: 0.875rem;
        color: var(--text-color);
    }

    .form-select:focus, .form-input:focus {
        border-color: var(--primary-light);
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }

    /* Form Labels */
    .form-label {
        display: block;
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.375rem;
        font-size: 0.75rem;
    }

    /* Custom Checkboxes */
    .custom-checkbox {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 1.125rem;
        height: 1.125rem;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-sm);
        position: relative;
        cursor: pointer;
        transition: var(--transition-fast);
        background-color: var(--white);
    }

    .custom-checkbox:checked {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }

    .custom-checkbox:checked::after {
        content: '✓';
        color: white;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.75rem;
    }

    .custom-checkbox:focus {
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }

    /* Modal styles - ACTUALIZADO para que funcione correctamente con hidden/visible */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
        transition: var(--transition-normal);
        display: none !important; /* Siempre oculto por defecto */
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.visible {
        display: flex !important; /* Solo visible cuando tiene la clase .visible */
    }

    .modal-overlay:not(.hidden) {
        display: flex !important; /* Compatibilidad con el código que usa .hidden */
    }

    .modal-content {
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        max-width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Timeline styles */
    .timeline-wrapper {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-line {
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: var(--primary-light);
        opacity: 0.5;
    }

    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }

    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: var(--primary-light);
        border: 2px solid var(--white);
        z-index: 1;
    }

    .timeline-content {
        background-color: var(--white);
        border-radius: var(--radius-md);
        padding: 1rem;
        box-shadow: var(--shadow-sm);
        border-left: 3px solid var(--primary-light);
    }

    /* Alerts */
    .alert {
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-md);
    }

    .alert-error {
        background-color: #fee2e2;
        border: 1px solid var(--error-color);
        color: var(--error-dark);
    }

    .alert-success {
        background-color: #d1fae5;
        border: 1px solid var(--success-color);
        color: var(--success-dark);
    }

    /* Text color styles */
    .text-primary {
        color: var(--primary-color);
        font-weight: 600;
    }

    .text-success {
        color: var(--success-color);
        font-weight: 600;
    }

    .text-warning {
        color: var(--warning-color);
        font-weight: 600;
    }

    .text-error {
        color: var(--error-color);
        font-weight: 600;
    }
    
    .text-sm {
        font-size: 0.875rem;
    }
    
    .text-xs {
        font-size: 0.75rem;
    }

    /* Animation for feedback */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }

    .pulse {
        animation: pulse 1.5s infinite;
    }

    /* Tooltip styles */
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltip-text {
        visibility: hidden;
        width: 120px;
        background-color: var(--text-color);
        color: var(--white);
        text-align: center;
        padding: 5px 8px;
        border-radius: var(--radius-sm);
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.675rem;
        box-shadow: var(--shadow-md);
    }

    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: var(--text-color) transparent transparent transparent;
    }

    /* Responsive table container */
    .table-container {
        overflow-x: auto;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }

    /* Loading spinner */
    .spinner {
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid rgba(59, 130, 246, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-light);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Badge */
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        line-height: 1;
    }

    .badge-primary {
        background-color: var(--primary-light);
        color: var(--white);
    }

    .badge-success {
        background-color: var(--success-light);
        color: var(--white);
    }

    .badge-warning {
        background-color: var(--warning-light);
        color: var(--white);
    }

    /* Animaciones de entrada y salida */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(10px); }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.3s ease-in-out forwards;
    }

    .timeline-wrapper .timeline-item {
        animation: fadeIn 0.5s ease-out;
        animation-fill-mode: both;
    }

    .timeline-wrapper .timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .timeline-wrapper .timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .timeline-wrapper .timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .timeline-wrapper .timeline-item:nth-child(4) { animation-delay: 0.4s; }
    .timeline-wrapper .timeline-item:nth-child(5) { animation-delay: 0.5s; }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr !important;
        }
        
        .hidden-mobile {
            display: none;
        }
        
        .data-table th, .data-table td {
            padding: 0.375rem;
            font-size: 0.7rem;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }

        .btn {
            padding: 0.375rem 0.75rem;
        }
    }

    /* Compact filter layout */
    .compact-filters .form-label {
        margin-bottom: 0.125rem;
    }
    
    .compact-filters .form-select,
    .compact-filters .form-input {
        padding: 0.375rem 0.625rem;
        font-size: 0.75rem;
    }
    
    /* Filtros mejorados */
    .filter-section {
        padding: 1.25rem;
    }
    
    .filter-section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    
    .filter-section-title svg {
        margin-right: 0.5rem;
    }
    
    .filter-grid {
        display: grid;
        gap: 1rem;
    }
    
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
        margin-bottom: 1rem;
    }
    
    .filter-col {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    /* Nuevos componentes */
    .chip {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background-color: var(--bg-light);
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-color);
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border: 1px solid var(--border-color);
    }

    /* Toast styles - ACTUALIZADO */
    #errorAlert, #successToast {
        display: none !important; /* Oculto por defecto */
    }

    #errorAlert.visible, #successToast.visible {
        display: flex !important; /* Solo visible con la clase .visible */
    }

    #errorAlert:not(.hidden), #successToast:not(.hidden) {
        display: flex !important; /* Compatibilidad con código que usa .hidden */
    }

    /* Loading overlay - ACTUALIZADO */
    #loadingOverlay {
        display: none !important; /* Oculto por defecto */
    }

    #loadingOverlay.visible {
        display: flex !important; /* Solo visible con clase .visible */
    }

    #loadingOverlay:not(.hidden) {
        display: flex !important; /* Compatibilidad con código que usa .hidden */
    }
    /* Tarjeta de estado */
    .status-card {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        gap: 0.375rem;
    }
    
    .status-badge-success {
        background-color: var(--success-color);
        color: var(--white);
    }
    
    .status-badge-warning {
        background-color: var(--warning-color);
        color: var(--white);
    }
    
    .status-badge-neutral {
        background-color: var(--bg-gray);
        color: var(--text-color);
    }
    
    .status-icon {
        display: flex;
        align-items: center;
    }
    
    .status-svg {
        width: 0.875rem;
        height: 0.875rem;
    }
    
    /* Botones de acción en la celda de estado */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-md);
        transition: var(--transition-fast);
        background-color: var(--white);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        text-decoration: none;
    }
    
    .action-signature {
        color: var(--primary-color);
        border-color: var(--primary-light);
        background-color: rgba(59, 130, 246, 0.1);
    }
    
    .action-completed {
        color: var(--success-color);
        border-color: var(--success-light);
        background-color: rgba(16, 185, 129, 0.1);
    }
    
    .action-returned {
        color: var(--warning-color);
        border-color: var(--warning-light);
        background-color: rgba(245, 158, 11, 0.1);
    }
    
    .action-disabled {
        color: var(--text-muted);
        background-color: var(--bg-gray);
        cursor: not-allowed;
    }
    
    .action-icon {
        display: flex;
        align-items: center;
    }
    
    .action-svg {
        width: 0.875rem;
        height: 0.875rem;
    }
</style>
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

    /* Contenedor principal de filtros */
    .filter-action-container {
        background-color: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
        transition: var(--transition-normal);
    }
    
    .filter-action-container:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-1px);
    }
    
    /* Header con gradiente */
    .filter-action-header {
        background: var(--primary-gradient);
        color: var(--white);
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .filter-action-title {
        display: flex;
        align-items: center;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .filter-icon {
        margin-right: 0.5rem;
        font-size: 1.125rem;
    }
    
    .filter-counter {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        backdrop-filter: blur(4px);
    }
    
    /* Contenido principal */
    .filter-action-content {
        padding: 1rem;
    }
    
    .filter-action-body {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    @media (min-width: 768px) {
        .filter-action-body {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }
    
    /* Controles de filtro */
    .filter-controls {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .filter-controls {
            flex-direction: row;
            align-items: center;
            max-width: 70%;
        }
    }
    
    /* Control de búsqueda */
    .search-control {
        position: relative;
        width: 100%;
    }
    
    .search-icon-container {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    
    .search-icon {
        width: 1rem;
        height: 1rem;
        color: var(--primary-light);
    }
    
    .search-input {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 2.5rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        color: var(--text-color);
        background-color: var(--white);
        transition: var(--transition-fast);
        box-shadow: var(--shadow-sm);
    }
    
    .search-input:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        outline: none;
    }
    
    .search-input::placeholder {
        color: var(--text-muted);
    }
    
    /* Selectores de filtro */
    .filter-selectors {
        display: flex;
        gap: 0.75rem;
        width: 100%;
        flex-wrap: wrap;
    }
    
    /* Botones de acción */
    .action-controls {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    
    @media (min-width: 768px) {
        .action-controls {
            margin-top: 0;
        }
    }
    
    .action-button {
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 500;
        font-size: 0.875rem;
        color: var(--white);
        cursor: pointer;
        transition: var(--transition-normal);
        border: none;
        text-decoration: none;
        box-shadow: var(--shadow-sm);
    }
    
    .action-button:focus {
        outline: none;
    }
    
    .action-button:active {
        transform: translateY(1px);
    }
    
    .action-button-content {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }
    
    .action-create {
        background: var(--primary-gradient);
    }
    
    .action-export {
        background: var(--success-gradient);
    }
    
    .action-icon {
        width: 1.125rem;
        height: 1.125rem;
        margin-right: 0.5rem;
    }
    
    .action-text {
        font-weight: 500;
    }
    
    /* Efecto de brillo al pasar el cursor */
    .action-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        transition: var(--transition-normal);
    }
    
    .action-button:hover .action-shine {
        left: 100%;
        transition: all 0.8s ease;
    }
    
    .action-button:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    /* Icono de carga */
    .loading-icon {
        animation: spin 1s linear infinite;
        margin-right: 0.375rem;
        width: 1rem;
        height: 1rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Tabla premium */
    .premium-table-container {
        background-color: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: var(--transition-normal);
        margin-bottom: 1.5rem;
    }
    
    .premium-table-container:hover {
        box-shadow: var(--shadow-lg);
    }
    
    .premium-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    
    .premium-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .premium-table-head {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .premium-th {
        padding: 0.875rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: left;
        color: var(--white);
        background: var(--primary-gradient);
        position: relative;
        white-space: nowrap;
    }
    
    .premium-th:first-child {
        border-top-left-radius: var(--radius-sm);
    }
    
    .premium-th:last-child {
        border-top-right-radius: var(--radius-sm);
    }
    
    .premium-th.sortable {
        cursor: pointer;
    }
    
    .premium-th.sortable:hover {
        background-color: var(--primary-dark);
    }
    
    .th-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .sort-icon {
        width: 1rem;
        height: 1rem;
        margin-left: 0.375rem;
        opacity: 0.7;
    }
    
    .premium-td {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
        color: var(--text-color);
        border-bottom: 1px solid var(--border-color);
        background-color: var(--white);
        transition: var(--transition-fast);
    }
    
    .premium-row:hover .premium-td {
        background-color: var(--bg-light);
    }
    
    .premium-td-empty {
        padding: 3rem 1rem;
        text-align: center;
    }
    
    /* Columnas especiales */
    .action-column {
        width: 100px;
    }
    
    .status-column {
        width: 180px;
    }
    
    .admin-column {
        width: 120px;
    }
    
    /* Celda destacada */
    .highlight-cell {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .cell-content {
        display: flex;
        flex-direction: column;
    }
    
    .primary-text {
        font-weight: 500;
    }
    
    .secondary-text {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
    
    /* Truncar texto */
    .truncate-text {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Paginación mejorada */
    .premium-pagination {
        padding: 1rem;
        background-color: var(--bg-light);
        border-top: 1px solid var(--border-color);
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
    
    /* Botones de acciones rápidas */
    .action-buttons-container {
        display: flex;
        gap: 0.5rem;
    }
    
    .quick-action-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: var(--radius-md);
        transition: var(--transition-fast);
        background-color: var(--bg-light);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }
    
    .quick-action-button svg {
        width: 1rem;
        height: 1rem;
    }
    
    .quick-action-button:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .edit-action:hover {
        background-color: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        border-color: var(--primary-light);
    }
    
    .reset-action:hover {
        background-color: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
        border-color: var(--warning-light);
    }
    
    /* Botones de admin */
    .admin-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .admin-button {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-md);
        transition: var(--transition-fast);
        background-color: var(--white);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        cursor: pointer;
    }
    
    .admin-button-icon {
        width: 0.875rem;
        height: 0.875rem;
    }
    
    .admin-button:hover {
        transform: translateY(-2px);
    }
    
    .edit-button:hover {
        background-color: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        border-color: var(--primary-light);
    }
    
    .delete-button:hover {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--error-color);
        border-color: var(--error-light);
    }
    
    /* Estado vacío */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem;
        text-align: center;
    }
    
    .empty-state-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        background-color: var(--bg-light);
        border-radius: 9999px;
        margin-bottom: 1rem;
        color: var(--text-muted);
    }
    
    .empty-state-icon svg {
        width: 2.5rem;
        height: 2.5rem;
    }
    
    .empty-state-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }
    
    .empty-state-description {
        color: var(--text-muted);
        max-width: 30rem;
        margin-bottom: 1.5rem;
    }
    
    .empty-state-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        background: var(--primary-gradient);
        color: var(--white);
        font-weight: 500;
        transition: var(--transition-fast);
        border: none;
        cursor: pointer;
        box-shadow: var(--shadow-sm);
    }
    
    .empty-state-button:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }
    
    .empty-state-button-icon {
        width: 1rem;
        height: 1rem;
    }
    
    /* Modal de Reset */
    #resetModal {
        z-index: 50;
    }
    
    /* Animaciones */
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .premium-td, .premium-th {
            padding: 0.625rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .action-button, .admin-button {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
        
        .premium-table-container {
            margin-left: -1rem;
            margin-right: -1rem;
            border-radius: 0;
        }
    }
</style>
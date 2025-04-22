<div class="mx-auto sm:px-6 lg:px-8">
    <style>
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
    --radius-sm: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --transition-normal: all 0.3s ease;
    --transition-fast: all 0.15s ease;
}

/* Contenedor principal */
.filter-action-container {
    background-color: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    margin-bottom: 1rem;
    transition: var(--transition-normal);
    font-size: 0.875rem;
}

.filter-action-container:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-1px);
}

/* Header con gradiente */
.filter-action-header {
    background: var(--primary-gradient);
    color: var(--white);
    padding: 0.625rem 0.875rem;
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
    margin-right: 0.375rem;
    font-size: 1rem;
}

.filter-counter {
    background-color: rgba(255, 255, 255, 0.2);
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    backdrop-filter: blur(4px);
}

/* Contenido principal */
.filter-action-content {
    padding: 0.875rem;
}

.filter-action-body {
    display: flex;
    flex-direction: column;
    gap: 0.875rem;
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
    left: 0.625rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}

.search-icon {
    width: 0.875rem;
    height: 0.875rem;
    color: var(--primary-light);
}

.search-input {
    width: 100%;
    padding: 0.375rem 0.5rem 0.375rem 2rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 0.8125rem;
    color: var(--text-color);
    background-color: var(--white);
    transition: var(--transition-fast);
    box-shadow: var(--shadow-sm);
    height: 2.25rem;
}

.search-input:focus {
    border-color: var(--primary-light);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    outline: none;
}

.search-input::placeholder {
    color: var(--text-muted);
}

/* Selectores de filtro */
.filter-selectors {
    display: flex;
    gap: 0.5rem;
    width: 100%;
    flex-wrap: wrap;
}

/* Botones de acción */
.action-controls {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
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
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.8125rem;
    color: var(--white);
    cursor: pointer;
    transition: var(--transition-normal);
    border: none;
    text-decoration: none;
    box-shadow: var(--shadow-sm);
    height: 2.25rem;
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
    width: 1rem;
    height: 1rem;
    margin-right: 0.375rem;
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

/* Animación de carga */
.loading-icon {
    animation: spin 1s linear infinite;
    width: 0.875rem;
    height: 0.875rem;
    margin-right: 0.375rem;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.action-button.loading {
    opacity: 0.8;
    cursor: not-allowed;
}

/* Mejoras para animaciones y transiciones */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.filter-action-container {
    animation: fadeIn 0.4s ease-out forwards;
}

:root {
    /* Colores base */
    --primary-color: #1e40af;
    --primary-light: #3b82f6;
    --primary-dark: #1e3a8a;
    --primary-gradient: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    --primary-gradient-hover: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    
    /* Colores de estado */
    --success-color: #10b981;
    --success-light: #d1fae5;
    --success-border: #34d399;
    --warning-color: #f59e0b;
    --warning-light: #fef3c7;
    --warning-border: #fbbf24;
    --danger-color: #ef4444;
    --danger-light: #fee2e2;
    --danger-border: #f87171;
    --neutral-color: #6b7280;
    --neutral-light: #f3f4f6;
    --neutral-border: #d1d5db;
    
    /* Colores de acción */
    --purple-color: #8b5cf6;
    --purple-light: #ede9fe;
    --purple-border: #a78bfa;
    --blue-color: #3b82f6;
    --blue-light: #dbeafe;
    --blue-border: #60a5fa;
    
    /* Colores de interfaz */
    --bg-white: #ffffff;
    --bg-light: #f9fafb;
    --text-primary: #1f2937;
    --text-secondary: #4b5563;
    --text-muted: #6b7280;
    --border-color: #e5e7eb;
    --border-color-hover: #d1d5db;
    
    /* Sombras */
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 3px 4px -1px rgba(0, 0, 0, 0.1), 0 1px 3px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 8px 10px -3px rgba(0, 0, 0, 0.1), 0 3px 4px -2px rgba(0, 0, 0, 0.05);
    --shadow-inner: inset 0 1px 3px 0 rgba(0, 0, 0, 0.06);
    
    /* Radios */
    --radius-sm: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --radius-xl: 0.625rem;
    --radius-full: 9999px;
    
    /* Transiciones */
    --transition-normal: all 0.3s ease;
    --transition-fast: all 0.15s ease;
}

/* Contenedor principal de la tabla */
.premium-table-container {
    background-color: var(--bg-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    transition: var(--transition-normal);
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

.premium-table-container:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-1px);
}

/* Wrapper de tabla con scrolling */
.premium-table-wrapper {
    overflow-x: auto;
    position: relative;
}

/* Tabla premium */
.premium-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.8125rem;
}

/* Encabezado de tabla */
.premium-table-head {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Estilos para celdas de encabezado */
.premium-th {
    padding: 0.625rem 0.75rem;
    background: var(--primary-gradient);
    color: white;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-align: left;
    position: relative;
    transition: var(--transition-fast);
    white-space: nowrap;
}

/* Efecto hover en encabezados */
.premium-th.sortable:hover {
    background: var(--primary-gradient-hover);
    cursor: pointer;
}

/* Contenido de celda de encabezado */
.th-content {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 0.375rem;
}

/* Ícono de ordenamiento */
.sort-icon {
    width: 0.875rem;
    height: 0.875rem;
    fill: none;
    stroke: currentColor;
    transition: var(--transition-fast);
}

.premium-th.sortable:hover .sort-icon {
    transform: scale(1.1);
}

/* Columnas específicas */
.action-column, .status-column, .admin-column {
    width: 1%;
    white-space: nowrap;
}

/* Filas de datos */
.premium-row {
    transition: var(--transition-normal);
    position: relative;
}

.premium-row:hover {
    background-color: rgba(219, 234, 254, 0.5);
    z-index: 5;
}

.premium-row:hover .premium-td {
    border-color: var(--border-color-hover);
}

/* Celdas de datos */
.premium-td {
    padding: 0.625rem 0.75rem;
    border-bottom: 1px solid var(--border-color);
    transition: var(--transition-normal);
    vertical-align: top;
}

/* Contenido de celda */
.cell-content {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

/* Texto primario y secundario */
.primary-text {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.8125rem;
}

.secondary-text {
    font-size: 0.6875rem;
    color: var(--text-muted);
}

/* Celda destacada */
.highlight-cell {
    font-weight: 600;
    color: var(--primary-color);
}

/* Truncamiento de texto */
.truncate-text {
    max-width: 14rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Contenedor de botones de acción rápida */
.action-buttons-container {
    display: flex;
    gap: 0.375rem;
}

/* Botones de acción rápida */
.quick-action-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.875rem;
    height: 1.875rem;
    border-radius: var(--radius-md);
    transition: var(--transition-normal);
    border: 1px solid transparent;
    box-shadow: var(--shadow-sm);
}

.quick-action-button svg {
    width: 1rem;
    height: 1rem;
    fill: none;
    stroke: currentColor;
    transition: var(--transition-fast);
}

.quick-action-button:hover {
    transform: translateY(-1px) scale(1.03);
    box-shadow: var(--shadow-md);
}

.quick-action-button:active {
    transform: translateY(0) scale(0.97);
}

/* Estilos específicos para botones de acción */
.edit-action {
    background-color: var(--blue-light);
    color: var(--blue-color);
    border-color: var(--blue-border);
}

.edit-action:hover {
    background-color: var(--blue-color);
    color: white;
}

.reset-action {
    background-color: var(--danger-light);
    color: var(--danger-color);
    border-color: var(--danger-border);
}

.reset-action:hover {
    background-color: var(--danger-color);
    color: white;
}

/* Botones de admin */
.admin-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}

.admin-button {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-md);
    font-size: 0.6875rem;
    font-weight: 500;
    transition: var(--transition-normal);
    border: 1px solid transparent;
    box-shadow: var(--shadow-sm);
}

.admin-button-icon {
    width: 0.875rem;
    height: 0.875rem;
    fill: none;
    stroke: currentColor;
    margin-right: 0.25rem;
}

.admin-button:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.edit-button {
    background-color: var(--blue-light);
    color: var(--blue-color);
    border-color: var(--blue-border);
}

.edit-button:hover {
    background-color: var(--blue-color);
    color: white;
}

.delete-button {
    background-color: var(--danger-light);
    color: var(--danger-color);
    border-color: var(--danger-border);
}

.delete-button:hover {
    background-color: var(--danger-color);
    color: white;
}

/* Estado vacío */
.premium-td-empty {
    padding: 2rem 1rem;
    text-align: center;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    max-width: 28rem;
    margin: 0 auto;
}

.empty-state-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    background-color: var(--neutral-light);
    border-radius: var(--radius-full);
    margin-bottom: 0.75rem;
}

.empty-state-icon svg {
    width: 1.75rem;
    height: 1.75rem;
    stroke: var(--text-muted);
}

.empty-state-title {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.375rem;
}

.empty-state-description {
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
    max-width: 22rem;
}

.empty-state-button {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    background-color: var(--blue-light);
    color: var(--blue-color);
    border: 1px solid var(--blue-border);
    border-radius: var(--radius-md);
    font-size: 0.8125rem;
    font-weight: 500;
    transition: var(--transition-normal);
}

.empty-state-button-icon {
    width: 0.875rem;
    height: 0.875rem;
    stroke: currentColor;
    margin-right: 0.375rem;
}

.empty-state-button:hover {
    background-color: var(--blue-color);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Paginación */
.premium-pagination {
    padding: 0.75rem 1rem;
    background-color: var(--bg-light);
    border-top: 1px solid var(--border-color);
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    font-size: 0.8125rem;
}

/* ESTILOS PARA STATUS Y BOTONES */
/* Tarjeta de estado */
.status-card {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    width: 100%;
    max-width: 250px;
}

/* Badges de estado */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-full);
    font-weight: 600;
    font-size: 0.75rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-normal);
    border: 1px solid transparent;
}

.status-badge:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.status-badge-success {
    background-color: var(--success-light);
    color: var(--success-color);
    border-color: var(--success-border);
}

.status-badge-warning {
    background-color: var(--warning-light);
    color: var(--warning-color);
    border-color: var(--warning-border);
}

.status-badge-neutral {
    background-color: var(--neutral-light);
    color: var(--neutral-color);
    border-color: var(--neutral-border);
}

.status-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.375rem;
}

.status-svg {
    width: 0.75rem;
    height: 0.75rem;
    fill: none;
}

.status-text {
    font-weight: 600;
    font-size: 0.75rem;
}

/* Botones de acción */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.action-button {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.625rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.75rem;
    transition: var(--transition-normal);
    border: 1px solid transparent;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    cursor: pointer;
}

.action-button:hover {
    transform: translateY(-1px) scale(1.01);
    box-shadow: var(--shadow-md);
}

.action-button:active {
    transform: translateY(0) scale(0.98);
}

.action-signature {
    background: linear-gradient(135deg, var(--purple-color), #9333ea);
    color: white;
    border-color: var(--purple-border);
    background-size: 200% 200%;
}

.action-completed {
    background: linear-gradient(135deg, var(--success-color), #059669);
    color: white;
    border-color: var(--success-border);
    background-size: 200% 200%;
}

.action-returned {
    background: linear-gradient(135deg, var(--blue-color), #2563eb);
    color: white;
    border-color: var(--blue-border);
    background-size: 200% 200%;
}

.action-disabled {
    background-color: var(--neutral-light);
    color: var(--neutral-color);
    border-color: var(--neutral-border);
    cursor: not-allowed;
}

.action-disabled:hover {
    transform: none;
    box-shadow: var(--shadow-sm);
}

.action-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.375rem;
}

.action-svg {
    width: 0.875rem;
    height: 0.875rem;
    fill: none;
}

.action-text {
    font-weight: 500;
    font-size: 0.75rem;
}

/* Animación para brillar en hover */
@keyframes shine {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.action-button:not(.action-disabled):hover {
    animation: shine 2s linear infinite;
}

/* Animaciones adicionales */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.premium-table-container {
    animation: fadeIn 0.4s ease-out forwards;
}

/* ESTILOS MEJORADOS PARA STATUS Y BOTONES */
/* Tarjeta de estado */
.status-card {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    width: 100%;
    max-width: 220px;
    padding: 0.125rem;
}

/* Badges de estado */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.325rem 0.625rem;
    border-radius: var(--radius-full);
    font-weight: 600;
    font-size: 0.75rem;
    box-shadow: var(--shadow-sm);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
}

.status-badge:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.status-badge-success {
    background-color: rgba(16, 185, 129, 0.12);
    color: var(--success-color);
    border-color: rgba(52, 211, 153, 0.4);
}

.status-badge-warning {
    background-color: rgba(245, 158, 11, 0.12);
    color: var(--warning-color);
    border-color: rgba(251, 191, 36, 0.4);
}

.status-badge-neutral {
    background-color: rgba(107, 114, 128, 0.12);
    color: var(--neutral-color);
    border-color: rgba(209, 213, 219, 0.4);
}

.status-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.375rem;
}

.status-svg {
    width: 0.875rem;
    height: 0.875rem;
    fill: none;
    stroke-width: 2;
}

.status-text {
    font-weight: 600;
    letter-spacing: 0.01em;
    line-height: 1.2;
    font-size: 0.75rem;
}

/* Botones de acción */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.action-button {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0.375rem 0.625rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.75rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    cursor: pointer;
    overflow: hidden;
    z-index: 1;
}

.action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.2) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    z-index: -1;
    transition: all 0.6s ease;
}

.action-button:hover::before {
    left: 100%;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.1);
}

.action-button:active {
    transform: translateY(0);
    box-shadow: var(--shadow-sm);
}

.action-signature {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    border-color: #a78bfa;
}

.action-completed {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border-color: #34d399;
}

.action-returned {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-color: #60a5fa;
}

.action-disabled {
    background-color: #f3f4f6;
    color: #6b7280;
    border-color: #d1d5db;
    cursor: not-allowed;
    opacity: 0.8;
}

/* Mejoras para dispositivos móviles */
@media (max-width: 640px) {
    .status-card {
        max-width: 100%;
    }
    
    .action-button {
        width: 100%;
    }
}

/* Animación para destacar la tarjeta cuando cambia de estado */
@keyframes highlight {
    0% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

.status-badge.recently-updated {
    animation: highlight 2s ease-out;
}

/* Reset Modal */
#resetModal .bg-white {
    border-radius: var(--radius-lg);
    max-width: 480px;
}

#resetModal .bg-red-50 {
    padding: 0.625rem 1rem;
}

#resetModal .bg-indigo-50 {
    padding: 0.5rem 1rem;
}

#resetModal .bg-yellow-50 {
    padding: 0.625rem;
}

#resetModal h3 {
    font-size: 1rem;
}

#resetModal p {
    font-size: 0.875rem;
}

#resetModal .text-lg {
    font-size: 0.9375rem;
}

#resetModal .text-sm {
    font-size: 0.8125rem;
}

#resetModal .text-xs {
    font-size: 0.75rem;
}

#resetModal .p-4 {
    padding: 0.75rem;
}

#resetModal .mt-5 {
    margin-top: 1rem;
}

#resetModal .mt-4 {
    margin-top: 0.75rem;
}

#resetModal .mt-3 {
    margin-top: 0.625rem;
}

#resetModal .mt-2 {
    margin-top: 0.375rem;
}

#resetModal .mt-1 {
    margin-top: 0.25rem;
}

#resetModal .mb-4 {
    margin-bottom: 0.75rem;
}

#resetModal .mb-2 {
    margin-bottom: 0.375rem;
}

#resetModal .pl-1 {
    padding-left: 0.25rem;
}

#resetModal input {
    height: 2.5rem;
    font-size: 0.875rem;
}

#resetModal button {
    font-size: 0.8125rem;
    padding: 0.375rem 0.75rem;
}
    </style>
    <!-- Header Section -->
    <div class="mb-6">
        <x-ui.breadcrumbs class="mb-4">
            <x-ui.breadcrumbs.link href="/dashboard" class="text-gray-600 hover:text-indigo-600 transition-colors">
                Dashboard
            </x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active class="font-medium">
                {{ __('crud.allAuditorias.collectionTitle') }}
            </x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        <!-- Notification Messages -->
        @if(session()->has('success'))
            <div class="p-4 mb-4 text-sm bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm transform transition duration-300 ease-in-out animate-fade-in-down" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-green-700">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="p-4 mb-4 text-sm bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm transform transition duration-300 ease-in-out animate-fade-in-down" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-red-700">{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>
    <!-- Barra de filtros y acciones premium con CSS personalizado -->
    <div class="filter-action-container">
        <div class="filter-action-header">
            <div class="filter-action-title">
                <ion-icon name="options-outline" class="filter-icon"></ion-icon>
                <span>Filtros y Acciones</span>
            </div>
            <div class="filter-counter">
                <span>{{ $allAuditorias->total() }} registros</span>
            </div>
        </div>
        
        <div class="filter-action-content">
            <div class="filter-action-body">
                <!-- Controles de búsqueda y filtros -->
                <div class="filter-controls">
                    <div class="search-control">
                        <div class="search-icon-container">
                            <svg class="search-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input
                            wire:model.debounce.300ms="search"
                            type="text"
                            class="search-input"
                            placeholder="Buscar en: {{ __('crud.allAuditorias.collectionTitle') }}..."
                        />
                    </div>

                    <div class="filter-selectors">
                        <x-ui.filter-cp-en
                            :entregas="$entrega"
                            :cuentasPublicas="$cuentaPublica"
                            route="dashboard.all-auditorias.index"
                            defaultEntregaLabel="Seleccionar Entrega"
                            defaultCuentaPublicaLabel="Seleccionar Cuenta Pública"
                        />
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="action-controls">
                    @role('admin')
                        <button wire:click="exportExcel" class="action-button action-export" wire:loading.class="loading" wire:loading.attr="disabled">
                            <div class="action-button-content">
                                <svg class="action-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span class="action-text" wire:loading.remove wire:target="exportExcel">Exportar</span>
                                <span class="action-text" wire:loading wire:target="exportExcel">
                                    <svg class="loading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Exportando...
                                </span>
                            </div>
                            <div class="action-shine"></div>
                        </button>
                    @endrole
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Modal --}}
    <x-ui.modal.confirm wire:model="confirmingDeletion">
        <x-slot name="title"> 
            <div class="flex items-center text-red-600">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Borrar') }} 
            </div>
        </x-slot>

        <x-slot name="content"> 
            <p class="text-gray-700">{{ __('¿Deseas confirmar esta acción?') }}</p>
            <p class="text-sm text-gray-500 mt-2">Esta acción no se puede deshacer.</p>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <x-ui.button
                    wire:click="$toggle('confirmingDeletion')"
                    wire:loading.attr="disabled"
                    class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                >
                    {{ __('Cancelar') }}
                </x-ui.button>

                <x-ui.button.danger
                    wire:click="delete({{ $deletingAuditorias }})"
                    wire:loading.attr="disabled"
                    class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
                >
                    <span wire:loading.remove wire:target="delete">{{ __('Borrar') }}</span>
                    <span wire:loading wire:target="delete" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Borrando...
                    </span>
                </x-ui.button.danger>
            </div>
        </x-slot>
    </x-ui.modal.confirm>
    {{-- Tabla Premium con Efectos Avanzados --}}
    <div class="premium-table-container">
        <div class="premium-table-wrapper">
            <table class="premium-table">
                <thead class="premium-table-head">
                    <tr>
                        <th class="premium-th action-column">
                            <div class="th-content">
                                <span>{{ __('Acciones Rápidas') }}</span>
                            </div>
                        </th>
                        <th class="premium-th status-column">
                            <div class="th-content">
                                <span>{{ __('Estado') }}</span>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('clave_de_accion')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.clave_de_accion.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th">
                            <div class="th-content">
                                <span>Tipo de Acción</span>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('entrega')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.entrega.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('siglas_auditoria_especial')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.siglas_auditoria_especial.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('uaa')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.uaa.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('titulo')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.titulo.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('numero_de_auditoria')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.numero_de_auditoria.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('ente_de_la_accion')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.ente_de_la_accion.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('dgseg_ef')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.dgseg_ef.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('sub_direccion_de_area')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.sub_direccion_de_area.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('nombre_sub_director_de_area')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.nombre_sub_director_de_area.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('jefe_de_departamento')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.jefe_de_departamento.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('cuenta_publica')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.cuenta_publica.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th admin-column">
                            <div class="th-content">
                                <span>Admin</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="premium-table-body">
                    @forelse ($allAuditorias as $auditorias)
                    <tr class="premium-row" wire:loading.class.delay="opacity-50">
                        <!-- Acciones Rápidas -->
                        <td class="premium-td">
                            <div class="action-buttons-container">
                                <a href="{{ route('auditorias.apartados', $auditorias->id) }}" 
                                class="quick-action-button edit-action"
                                title="Editar Apartados">
                                    <svg viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                @if(Auth::user()->id === 1 || Auth::user()->id === 2)
                                <button onclick="openResetModal({{ $auditorias->id }}, '{{ addslashes($auditorias->clave_de_accion) }}')" 
                                        class="quick-action-button reset-action"
                                        title="Resetear Firmas">
                                    <svg viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Estado y Botones de Descarga - Versión Mejorada -->
                        <td class="premium-td">
                            <div class="status-card">
                                <!-- Badge de estado con diseño mejorado -->
                                <div class="status-badge 
                                    {{ $auditorias->estatus_checklist == 'Aceptado' ? 'status-badge-success' : 
                                    ($auditorias->estatus_checklist == 'Devuelto' ? 'status-badge-warning' : 'status-badge-neutral') }}">
                                    <div class="status-icon">
                                        @if($auditorias->estatus_checklist == 'Aceptado')
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($auditorias->estatus_checklist == 'Devuelto')
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        @else
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="status-text">{{ $auditorias->estatus_checklist }}</span>
                                </div>
                                
                                <!-- Botones de Acción Mejorados -->
                                <div class="action-buttons">
                                    @if ($auditorias->estatus_checklist == "Aceptado" && empty($auditorias->archivo_uua))
                                        <a href="{{ route('auditorias.pdf', $auditorias->id) }}" class="action-button action-signature">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Con Firma de Seguimiento</span>
                                        </a>
                                    @elseif($auditorias->estatus_checklist == "Aceptado" && !empty($auditorias->archivo_uua))
                                        <a href="{{ route('auditorias.downloadUua', $auditorias->id) }}" class="action-button action-completed">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Completado</span>
                                        </a>
                                    @elseif ($auditorias->estatus_checklist == "Devuelto")
                                        <a href="/auditorias/{{ $auditorias->id }}/pdf" class="action-button action-returned">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Devuelto</span>
                                        </a>
                                    @else
                                        <div class="action-button action-disabled">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Sin PDF generado</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <!-- Datos de la auditoría con estilos mejorados -->
                        <td class="premium-td highlight-cell">
                            <div class="cell-content">
                                {{ $auditorias->clave_de_accion }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catSiglasTipoAccion->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catEntrega->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catSiglasAuditoriaEspecial->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                <span class="primary-text">{{ $auditorias->catUaa->valor }}</span>
                                <span class="secondary-text">{{ $auditorias->catUaa->nombre }}</span>
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content truncate-text" title="{{ $auditorias->titulo }}">
                                {{ $auditorias->titulo }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catAuditoriaEspecial->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catEnteDeLaAccion->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catDgsegEf->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->sub_direccion_de_area }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->nombre_sub_director_de_area }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->jefe_de_departamento }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catCuentaPublica->valor }}
                            </div>
                        </td>
                        
                        <!-- Botones de Admin -->
                        <td class="premium-td">
                            @role('admin')
                            <div class="admin-actions">
                                @if(Auth::user()->id === 1 || Auth::user()->id === 2 || Auth::user()->id === 3)
                                    @can('update', $auditorias)
                                    <a wire:navigate href="{{ route('dashboard.all-auditorias.edit', $auditorias) }}" 
                                    class="admin-button edit-button">
                                        <svg class="admin-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span class="admin-button-text">Editar</span>
                                    </a>
                                    @endcan 
                                    
                                    @can('delete', $auditorias)
                                    <button wire:click="confirmDeletion({{ $auditorias->id }})" 
                                            class="admin-button delete-button">
                                        <svg class="admin-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="admin-button-text">Borrar</span>
                                    </button>
                                    @endcan
                                @endif
                            </div>
                            @endrole
                        </td>
                    </tr>
                    @empty
                    <!-- Estado Vacío Mejorado -->
                    <tr>
                        <td colspan="16" class="premium-td-empty">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="empty-state-title">No se encontró: {{ __('crud.allAuditorias.collectionTitle') }}</h3>
                                <p class="empty-state-description">Intenta con diferentes términos de búsqueda o quita los filtros aplicados para ver más resultados.</p>
                                
                                <button onclick="resetFilters()" class="empty-state-button">
                                    <svg class="empty-state-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Restablecer filtros</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación Premium -->
        <div class="premium-pagination">
            {{ $allAuditorias->links() }}
        </div>
    </div>
    <!-- Reset Modal mejorado para mejor UX -->
    <div
        id="resetModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto"
        aria-labelledby="resetModalTitle"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50 border border-gray-200">
                <div class="bg-red-50 px-4 py-3 border-b border-red-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg leading-6 font-medium text-red-800" id="resetModalTitle">
                            Confirmar Reseteo de Clave de Acción
                        </h3>
                    </div>
                </div>
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <p class="text-gray-700 mb-4">
                                Estás a punto de reiniciar las firmas de la clave de acción:
                            </p>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3 mb-4">
                                <p class="text-indigo-800 font-medium text-lg text-center" id="modalClaveAccion"></p>
                            </div>
                            
                            <div class="mt-3 p-4 bg-yellow-50 border border-yellow-100 rounded-lg flex items-start">
                                <svg class="h-6 w-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm text-yellow-800 font-medium">Advertencia:</p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Esta acción es irreversible y eliminará todas las firmas existentes para este expediente. Los usuarios tendrán que volver a firmar todos los documentos.
                                    </p>
                                </div>
                            </div>
                            
                            <form id="resetForm" method="POST" action="" class="mt-5">
                                @csrf
                                @method('POST')
                                <div class="mt-4">
                                    <label for="confirmation_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        Para confirmar, escribe exactamente:
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            name="confirmation_text"
                                            id="confirmation_text"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 pl-4 pr-4 py-3"
                                            placeholder='Deseo reiniciar esta clave de acción'
                                            required
                                        />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none opacity-0 confirmation-check text-green-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 pl-1">Frase requerida: <span class="font-mono bg-gray-100 px-1 py-0.5 rounded text-gray-700">"Deseo reiniciar esta clave de acción"</span></p>
                                </div>
                                <input type="hidden" name="auditoria_id" id="auditoria_id" value="">
                                <input type="hidden" name="clave_accion" id="clave_accion" value="">
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button
                        type="button"
                        id="confirmResetBtn"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="submitReset()"
                        disabled
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Confirmar Reseteo
                    </button>
                    <button
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150"
                        onclick="closeResetModal()"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicializar los scripts cuando el DOM está listo
        document.addEventListener('DOMContentLoaded', function() {
            // Validación interactiva para el campo de confirmación
            const confirmField = document.getElementById('confirmation_text');
            const confirmButton = document.getElementById('confirmResetBtn');
            const checkIcon = document.querySelector('.confirmation-check');
            
            if (confirmField) {
                confirmField.addEventListener('input', function() {
                    const isValid = this.value === 'Deseo reiniciar esta clave de acción';
                    
                    // Habilitar/deshabilitar botón
                    confirmButton.disabled = !isValid;
                    
                    // Mostrar/ocultar el ícono de verificación
                    checkIcon.classList.toggle('opacity-0', !isValid);
                    checkIcon.classList.toggle('opacity-100', isValid);
                    
                    // Estilizar el campo según validación
                    if (this.value && !isValid) {
                        this.classList.add('border-red-300', 'bg-red-50');
                        this.classList.remove('border-green-300', 'bg-green-50');
                    } else if (isValid) {
                        this.classList.add('border-green-300', 'bg-green-50');
                        this.classList.remove('border-red-300', 'bg-red-50');
                    } else {
                        this.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
                    }
                });
            }
        });
    
        // Función para abrir el modal de reset con animación mejorada
        function openResetModal(auditoriaId, claveAccion) {
            const modal = document.getElementById('resetModal');
            modal.classList.remove('hidden');
            
            // Animar entrada
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.add('animate-modal-in');
            
            // Configurar datos
            document.getElementById('modalClaveAccion').innerText = claveAccion;
            document.getElementById('auditoria_id').value = auditoriaId;
            document.getElementById('clave_accion').value = claveAccion;
            document.getElementById('resetForm').action = `/dashboard/all-auditorias/${auditoriaId}/reset`;
            
            // Restablecer campo de confirmación
            const confirmField = document.getElementById('confirmation_text');
            confirmField.value = '';
            confirmField.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
            document.getElementById('confirmResetBtn').disabled = true;
            document.querySelector('.confirmation-check').classList.add('opacity-0');
            document.querySelector('.confirmation-check').classList.remove('opacity-100');
            
            // Enfocar en el campo de confirmación
            setTimeout(() => {
                confirmField.focus();
            }, 300);
            
            // Eliminar mensajes de error previos
            const errorMessage = document.querySelector('.confirmation-error');
            if (errorMessage) {
                errorMessage.remove();
            }
        }
    
        // Función para cerrar el modal con animación
        function closeResetModal() {
            const modal = document.getElementById('resetModal');
            const modalContent = modal.querySelector('.bg-white');
            
            // Animar salida
            modalContent.classList.remove('animate-modal-in');
            modalContent.classList.add('animate-modal-out');
            
            // Ocultar después de la animación
            setTimeout(() => {
                modal.classList.add('hidden');
                modalContent.classList.remove('animate-modal-out');
                document.getElementById('resetForm').reset();
            }, 300);
        }
    
        // Función para enviar el formulario con validación mejorada
        function submitReset() {
            const confirmationText = document.getElementById('confirmation_text').value;
            if (confirmationText === 'Deseo reiniciar esta clave de acción') {
                // Mostrar indicador de carga en el botón
                const confirmButton = document.getElementById('confirmResetBtn');
                const originalContent = confirmButton.innerHTML;
                confirmButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                `;
                confirmButton.disabled = true;
                
                // Enviar el formulario
                document.getElementById('resetForm').submit();
            } else {
                // Mostrar error con efectos visuales mejorados
                const inputElement = document.getElementById('confirmation_text');
                inputElement.classList.add('border-red-300', 'bg-red-50');
                inputElement.classList.remove('border-green-300', 'bg-green-50');
                
                // Agregar mensaje de error si no existe
                let errorContainer = document.querySelector('.confirmation-error');
                if (!errorContainer) {
                    const errorMessage = document.createElement('p');
                    errorMessage.className = 'text-xs text-red-600 mt-2 confirmation-error animate-bounce-once';
                    errorMessage.innerHTML = `
                        <svg class="inline-block h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        La confirmación no coincide. Por favor, escribe exactamente la frase indicada.
                    `;
                    inputElement.parentNode.appendChild(errorMessage);
                }
                
                // Enfocar el campo de entrada y seleccionar todo el texto
                inputElement.focus();
                inputElement.select();
                
                // Sacudir el modal para indicar error
                const modal = document.querySelector('#resetModal > div > div.bg-white');
                modal.classList.add('animate-shake');
                setTimeout(() => {
                    modal.classList.remove('animate-shake');
                }, 600);
            }
        }
    
        // Cerrar el modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('resetModal');
            if (event.target === modal) {
                closeResetModal();
            }
        }
        
        // Escuchar la tecla Escape para cerrar el modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeResetModal();
            }
            
            // Enviar el formulario al presionar Enter si la validación es correcta
            if (event.key === 'Enter' && document.activeElement === document.getElementById('confirmation_text')) {
                if (document.getElementById('confirmation_text').value === 'Deseo reiniciar esta clave de acción') {
                    submitReset();
                    event.preventDefault();
                }
            }
        });
        
        // Función para resetear filtros (para el mensaje de "no hay resultados")
        function resetFilters() {
            // Aquí puedes agregar código para resetear los filtros de LiveWire o redirigir a la página sin filtros
            window.location.href = window.location.pathname;
        }
        
        // Agregar animación de sacudida y otras animaciones si no existen
        if (!document.querySelector('style#custom-animations')) {
            const styleElement = document.createElement('style');
            styleElement.id = 'custom-animations';
            styleElement.textContent = `
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }
                .animate-shake {
                    animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
                }
                
                @keyframes fadeInDown {
                    from {
                        opacity: 0;
                        transform: translate3d(0, -20px, 0);
                    }
                    to {
                        opacity: 1;
                        transform: translate3d(0, 0, 0);
                    }
                }
                .animate-fade-in-down {
                    animation: fadeInDown 0.3s ease-out forwards;
                }
                
                @keyframes bounceOnce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-5px); }
                }
                .animate-bounce-once {
                    animation: bounceOnce 0.5s ease-in-out;
                }
                
                @keyframes modalIn {
                    from {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
                .animate-modal-in {
                    animation: modalIn 0.3s ease-out forwards;
                }
                
                @keyframes modalOut {
                    from {
                        opacity: 1;
                        transform: scale(1);
                    }
                    to {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                }
                .animate-modal-out {
                    animation: modalOut 0.2s ease-in forwards;
                }
            `;
            document.head.appendChild(styleElement);
        }
    </script>
</div>
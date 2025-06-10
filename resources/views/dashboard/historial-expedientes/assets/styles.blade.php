<style>
    :root {
        --primary-color: #3b82f6;         /* Azul primario como en las otras vistas */
        --primary-dark: #2563eb;          /* Azul oscuro para hover */
        --secondary-color: #6b7280;       /* Gris neutro */
        --success-color: #10b981;         /* Verde para éxito */
        --success-dark: #059669;          /* Verde oscuro para hover */
        --warning-color: #f59e0b;         /* Amarillo para advertencias */
        --warning-dark: #d97706;          /* Amarillo oscuro para hover */
        --error-color: #ef4444;           /* Rojo para errores */
        --error-dark: #dc2626;            /* Rojo oscuro para hover */
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
    }

    /* Card Styles mejorados */
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
        border: 1px solid var(--gray-100);
        margin-bottom: 1.5rem;
        transition: box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-top: 1rem;
        padding-bottom: 1rem;
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-100);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-body.p-0 {
        padding: 0;
    }

    .filter-section-title {
        font-size: 1rem;
        font-weight: 500;
        color: var(--gray-800);
        display: flex;
        align-items: center;
    }

    .filter-section-title svg {
        margin-right: 0.5rem;
        color: var(--primary-color);
    }

    /* Button Styles mejorados */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-width: 1px;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition-property: color, background-color, border-color, box-shadow;
        transition-duration: 150ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .btn:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        border-color: transparent;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .btn-secondary {
        background-color: white;
        color: var(--gray-700);
        border-color: var(--gray-300);
    }

    .btn-secondary:hover {
        background-color: var(--gray-50);
        color: var(--gray-900);
    }

    .btn-success {
        background-color: var(--success-color);
        color: white;
        border-color: transparent;
    }

    .btn-success:hover {
        background-color: var(--success-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Form Controls mejorados */
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-700);
        margin-bottom: 0.25rem;
    }

    .form-input, .form-select {
        margin-top: 0.25rem;
        display: block;
        width: 100%;
        border-radius: 0.375rem;
        border: 1px solid var(--gray-300);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        padding: 0.5rem 0.75rem;
        transition: all 150ms ease-in-out;
        background-color: white;
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .form-input::placeholder {
        color: var(--gray-400);
    }

    /* Filter Layouts */
    .filter-row {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    @media (min-width: 640px) {
        .filter-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .filter-row {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .filter-row {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .filter-col {
        display: flex;
        flex-direction: column;
    }

    .filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    .filter-actions > * + * {
        margin-left: 0.75rem;
    }

    /* Table Styles mejorados */
    .table-container {
        overflow-x: auto;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    .data-table {
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .data-table thead {
        background-color: var(--gray-50);
    }

    .data-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid var(--gray-200);
        white-space: nowrap;
    }

    .data-table tbody {
        background-color: white;
    }

    .data-table tbody tr {
        transition: background-color 150ms ease;
    }

    .data-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .data-table td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    /* Estado de fila */
    .row-success {
        background-color: rgba(16, 185, 129, 0.05);
    }

    .row-success:hover {
        background-color: rgba(16, 185, 129, 0.1);
    }

    /* Badge Styles mejorados */
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .badge-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .badge-secondary {
        background-color: var(--gray-200);
        color: var(--gray-800);
    }

    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Animations */
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Status Colors */
    .text-primary {
        color: var(--primary-color);
    }

    .text-success {
        color: var(--success-color);
    }

    .text-warning {
        color: var(--warning-color);
    }

    .text-error {
        color: var(--error-color);
    }

    /* Alert components */
    .alert {
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        border-width: 1px;
        display: flex;
        align-items: flex-start;
    }

    .alert-error {
        background-color: #fef2f2;
        color: #b91c1c;
        border-color: #ef4444;
    }

    .alert-success {
        background-color: #f0fdf4;
        color: #166534;
        border-color: #10b981;
    }
    
    /* Utility classes para el nuevo diseño */
    .hidden {
        display: none;
    }
    
    .font-medium {
        font-weight: 500;
    }
    
    .font-semibold {
        font-weight: 600;
    }
    
    .text-gray-400 {
        color: var(--gray-400);
    }
    
    .text-gray-500 {
        color: var(--gray-500);
    }
    
    .text-gray-700 {
        color: var(--gray-700);
    }
    
    /* Spacing utilities */
    .mt-4 {
        margin-top: 1rem;
    }
    
    /* Estado vacío mejorado */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .empty-state svg {
        width: 3rem;
        height: 3rem;
        color: var(--gray-400);
        margin-bottom: 1rem;
    }
    
    .empty-state-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }
    
    .empty-state-description {
        color: var(--gray-500);
        max-width: 24rem;
        margin: 0 auto;
    }
</style>

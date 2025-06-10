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
        margin-bottom: 1.5rem;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--primary-color) !important;
        color: var(--white) !important;
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

    /* Secondary Button */
    .btn-secondary {
        background-color: var(--bg-light);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background-color: #e5e7eb;
    }

    /* Small button */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Formularios */
    .form-control, .form-select, .form-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background-color: var(--white);
        transition: var(--transition-fast);
        font-size: 0.875rem;
        color: var(--text-color);
    }

    .form-control:focus, .form-select:focus, .form-input:focus {
        border-color: var(--primary-light);
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }

    .form-control-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Form Labels */
    .form-label {
        display: block;
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.375rem;
        font-size: 0.75rem;
    }

    /* Tabla */
    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(243, 244, 246, 0.5);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid var(--border-color);
    }

    .table th {
        background-color: var(--primary-color);
        color: var(--white);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.75rem 0.5rem;
        text-align: left;
        position: sticky;
        top: 0;
        white-space: nowrap;
    }

    .table td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        line-height: 1.2;
        font-size: 0.875rem;
    }

    .thead-dark th {
        background-color: var(--primary-color);
        color: var(--white);
    }

    /* Alerts */
    .alert {
        padding: 1rem;
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        border: 1px solid transparent;
    }

    .alert-info {
        background-color: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
        color: var(--primary-dark);
    }

    /* Badges */
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

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }

    /* Espaciado */
    .mr-1 {
        margin-right: 0.25rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    /* Flex */
    .d-flex {
        display: flex;
    }

    .align-items-center {
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .flex-grow-1 {
        flex-grow: 1;
    }

    /* Text */
    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .font-weight-bold {
        font-weight: 700;
    }

    .text-white {
        color: var(--white);
    }

    .text-muted {
        color: var(--text-muted);
    }

    .text-primary {
        color: var(--primary-color);
    }

    /* Background */
    .bg-light {
        background-color: #f8f9fa;
    }
    
    .bg-white {
        background-color: #ffffff;
    }

    .bg-primary {
        background-color: var(--primary-color);
    }

    .bg-dark {
        background-color: var(--text-color);
    }

    /* Progress bar */
    .progress {
        display: block;
        width: 100%;
        height: 0.5rem;
        border-radius: var(--radius-sm);
        background-color: var(--bg-light);
        overflow: hidden;
    }

    .progress-bar {
        background-color: var(--primary-color);
        height: 100%;
    }

    /* Responsive */
    .container-fluid {
        width: 100%;
        padding-right: 1rem;
        padding-left: 1rem;
        margin-right: auto;
        margin-left: auto;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -0.5rem;
        margin-left: -0.5rem;
    }

    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 0 0.5rem;
    }

    .col-md-12 {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0 0.5rem;
    }

    .col-lg-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
        padding: 0 0.5rem;
    }

    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Sombras adicionales */
    .shadow {
        box-shadow: var(--shadow-md);
    }

    .shadow-sm {
        box-shadow: var(--shadow-sm);
    }

    /* Estilos para impresión */
    @media print {
        .btn-light, .form-group, form {
            display: none !important;
        }
        
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #343a40 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }
        
        .progress {
            border: 1px solid #dee2e6 !important;
        }
        
        .progress-bar {
            background-color: #007bff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style> 
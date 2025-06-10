<style>
    /* Variables de color */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --secondary-color: #6b7280;
        --success-color: #059669;
        --success-light: #d1fae5;
        --warning-color: #ea580c;
        --warning-light: #ffedd5;
        --danger-color: #dc2626;
        --danger-light: #fee2e2;
        --info-color: #0891b2;
        --info-light: #cffafe;

        --card-border-color: #e5e7eb;
        --table-row-hover: #f3f4f6;
        --table-border-color: #e5e7eb;
    }

    /* Estilos para la tabla mejorada */
    .premium-table-container {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--card-border-color);
        overflow: hidden;
    }

    .premium-table-wrapper {
        overflow-x: auto;
    }

    .premium-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }

    .premium-table-head {
        background-color: #f9fafb;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .premium-th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #4b5563;
        border-bottom: 1px solid var(--table-border-color);
        white-space: nowrap;
        transition: background-color 0.2s;
    }

    .th-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sort-icon {
        width: 1rem;
        height: 1rem;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .premium-th.sortable:hover {
        background-color: #f3f4f6;
        cursor: pointer;
    }

    .premium-th.sortable:hover .sort-icon {
        opacity: 1;
    }

    .premium-td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--table-border-color);
        color: #4b5563;
    }

    .premium-row {
        transition: background-color 0.2s;
    }

    .premium-row:hover {
        background-color: var(--table-row-hover);
    }

    .cell-content {
        display: flex;
        flex-direction: column;
    }

    .primary-text {
        font-weight: 600;
        color: #111827;
    }

    .secondary-text {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .highlight-cell {
        font-weight: 600;
        color: var(--primary-color);
    }

    .truncate-text {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Badges y estados */
    .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 0.375rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
    }

    .status-badge-success {
        background-color: var(--success-color);
    }

    .status-badge-warning {
        background-color: var(--warning-color);
    }

    .status-badge-neutral {
        background-color: var(--secondary-color);
    }

    .status-icon {
        margin-right: 0.375rem;
        display: flex;
        align-items: center;
    }

    .status-svg {
        width: 0.875rem;
        height: 0.875rem;
    }

    /* Estilos para la paginación */
    .premium-pagination {
        padding: 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
        border-top: 1px solid var(--table-border-color);
        background-color: #f9fafb;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state svg {
        width: 4rem;
        height: 4rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.5rem;
    }

    .empty-state-description {
        color: #6b7280;
        max-width: 32rem;
        margin-bottom: 1.5rem;
    }

    .empty-state-button {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
        transition: background-color 0.2s;
        border: none;
        cursor: pointer;
    }

    .empty-state-button:hover {
        background-color: var(--primary-hover);
    }

    .empty-state-button-icon {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }

    /* Filter and Action Container */
    .filter-action-container {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--card-border-color);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .filter-action-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1.25rem;
        background-color: #f9fafb;
        border-bottom: 1px solid var(--card-border-color);
    }

    .filter-action-title {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #374151;
    }

    .filter-icon {
        margin-right: 0.5rem;
        color: var(--primary-color);
        width: 1.25rem;
        height: 1.25rem;
    }

    .filter-counter {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .filter-action-content {
        padding: 1rem 1.25rem;
    }

    .filter-action-body {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .filter-controls {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-control {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .search-icon-container {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .search-icon {
        width: 1rem;
        height: 1rem;
    }

    .search-input {
        width: 100%;
        padding: 0.625rem 0.75rem 0.625rem 2.25rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }

    .filter-selectors {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        flex: 2;
    }

    .action-controls {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }

    .action-button {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        overflow: hidden;
        cursor: pointer;
        border: none;
    }

    .action-button-content {
        display: flex;
        align-items: center;
        z-index: 1;
    }

    .action-icon {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }

    .action-export {
        background-color: #1d4ed8;
        color: white;
    }

    .action-export:hover {
        background-color: #1e40af;
    }

    .action-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(
            to right,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% {
            left: -100%;
        }
        20% {
            left: 100%;
        }
        100% {
            left: 100%;
        }
    }

    .loading-icon {
        animation: spin 1s linear infinite;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .action-button.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Estilos para tarjetas de contenido */
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--card-border-color);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #f9fafb;
        border-bottom: 1px solid var(--card-border-color);
    }

    .card-title {
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
    }

    .card-icon {
        margin-right: 0.5rem;
        color: var(--primary-color);
        width: 1.25rem;
        height: 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }
</style> 
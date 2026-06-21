<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Estilos para integrar TomSelect con nuestro diseño transparente */
    .ts-control {
        background: transparent !important;
        border: none !important;
        padding: 4px 0 !important;
        box-shadow: none !important;
        font-family: inherit;
        font-weight: 500;
        color: #334155;
    }
    .ts-control > input {
        font-weight: 500;
        color: #334155;
    }
    .ts-wrapper.multi .ts-control > div {
        background: #3b82f6 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 2px 8px !important;
        margin: 2px !important;
        text-transform: uppercase;
    }
    .ts-wrapper.multi .ts-control > div .remove {
        border-left: 1px solid rgba(255,255,255,0.2) !important;
        color: white !important;
    }
    .ts-dropdown {
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .ts-dropdown .option {
        padding: 10px 14px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .ts-dropdown .active {
        background-color: #f1f5f9;
        color: #2563eb;
    }
    .ts-wrapper.single .ts-control::after {
        border-color: #94a3b8 transparent transparent transparent;
    }
</style>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="SAES - Sistema de Archivo de Expedientes de Seguimiento para gestión eficiente de expedientes de sanción">

        <title>SAES - Sistema de Archivo de Expedientes de Seguimiento</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Icons -->
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

        <!-- Styles -->
        <style>
            /* Tailwind CSS */
            @import url('https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css');
            
            :root {
                --primary-color: #1e40af;
                --secondary-color: #3b82f6;
                --accent-color: #60a5fa;
                --success-color: #10b981;
                --warning-color: #f59e0b;
                --danger-color: #ef4444;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                scroll-behavior: smooth;
            }
            
            .bg-primary {
                background-color: var(--primary-color);
            }
            
            .text-primary {
                color: var(--primary-color);
            }
            
            .bg-gradient {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            }
            
            .hero-pattern {
                background-color: #f9fafb;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23d1d5db' fill-opacity='0.2'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            
            /* Card animations */
            .feature-card {
                transition: all 0.3s ease;
                border-radius: 0.75rem;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            
            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            
            .feature-card-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                display: inline-block;
                padding: 1rem;
                border-radius: 9999px;
                background-color: rgba(59, 130, 246, 0.1);
                color: var(--primary-color);
                transition: all 0.3s ease;
            }
            
            .feature-card:hover .feature-card-icon {
                background-color: var(--primary-color);
                color: white;
                transform: scale(1.1);
            }
            
            /* Buttons */
            .btn-primary {
                background-color: var(--primary-color);
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.375rem;
                font-weight: 500;
                transition: all 0.2s ease;
                text-decoration: none;
                display: inline-block;
                box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.2);
            }
            
            .btn-primary:hover {
                background-color: var(--secondary-color);
                transform: translateY(-1px);
                box-shadow: 0 6px 10px -2px rgba(59, 130, 246, 0.25);
            }
            
            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes scaleIn {
                from { transform: scale(0.95); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out forwards;
            }
            
            .animate-scale-in {
                animation: scaleIn 0.4s ease-out forwards;
            }
            
            /* Utilities */
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #c5c5c5;
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-gray-800 dark:text-gray-200">
        <div class="min-h-screen flex flex-col">
            <!-- Header / Navigation -->
            <header class="w-full bg-gradient text-white py-4 sticky top-0 z-50 shadow-md">
                <div class="container mx-auto flex flex-wrap justify-between items-center px-4">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white rounded-full p-2 shadow-sm">
                            <ion-icon name="folder-open" class="text-primary text-xl"></ion-icon>
                        </div>
                        <span class="text-2xl font-bold">SAES</span>
                    </div>
                    <nav class="mt-2 sm:mt-0">
                        @if (Route::has('login'))
                            <div class="space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn-primary">
                                        <ion-icon name="apps-outline" class="mr-1"></ion-icon> 
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn-primary">
                                        <ion-icon name="log-in-outline" class="mr-1"></ion-icon>
                                        Ingresar
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </nav>
                </div>
            </header>

            <!-- Hero Section -->
            <section class="hero-pattern py-12 md:py-20">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row items-center">
                        <div class="md:w-1/2 mb-8 md:mb-0 pr-0 md:pr-8 animate-fade-in">
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                                Sistema de Archivo de Expedientes de Seguimiento
                            </h1>
                            <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                                Gestiona y monitorea tus expedientes de seguimiento con eficiencia y precisión.
                                Optimiza tus procesos y asegura el cumplimiento de las normativas con SAES.
                            </p>
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary">
                                    <ion-icon name="speedometer-outline" class="mr-1"></ion-icon> 
                                    Ir al Sistema
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary">
                                    <ion-icon name="log-in-outline" class="mr-1"></ion-icon>
                                    Iniciar Sesión
                                </a>
                            @endauth
                        </div>
                        <div class="md:w-1/2 animate-scale-in delay-100">
                            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                                <div class="bg-primary text-white px-6 py-4 flex items-center">
                                    <ion-icon name="folder-open-outline" class="mr-2 text-xl"></ion-icon>
                                    <span class="font-semibold">Dashboard de Expedientes</span>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="bg-blue-50 rounded-lg p-4 flex flex-col items-center">
                                            <span class="text-3xl font-bold text-blue-600">782</span>
                                            <span class="text-sm text-gray-600">Expedientes</span>
                                        </div>
                                        <div class="bg-green-50 rounded-lg p-4 flex flex-col items-center">
                                            <span class="text-3xl font-bold text-green-600">94%</span>
                                            <span class="text-sm text-gray-600">Completados</span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center">
                                        <div class="text-center">
                                            <ion-icon name="bar-chart-outline" class="text-4xl text-gray-400"></ion-icon>
                                            <p class="text-sm text-gray-500 mt-2">Vista previa de estadísticas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-16 bg-white">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Funcionalidades Principales</h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            SAES ofrece herramientas completas para la gestión eficaz de expedientes, 
                            simplificando tu flujo de trabajo y aumentando la productividad.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature Card 1 -->
                        <div class="feature-card bg-white p-6 animate-fade-in delay-100">
                            <div class="text-center">
                                <span class="feature-card-icon">
                                    <ion-icon name="cloud-upload-outline"></ion-icon>
                                </span>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Carga de Acciones</h3>
                                <p class="text-gray-600">
                                    Carga expedientes y acciones emitidas, organiza la información y prepárala para su procesamiento eficiente.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Feature Card 2 -->
                        <div class="feature-card bg-white p-6 animate-fade-in delay-200">
                            <div class="text-center">
                                <span class="feature-card-icon">
                                    <ion-icon name="calendar-number-outline"></ion-icon>
                                </span>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Programación de Entrega</h3>
                                <p class="text-gray-600">
                                    Planifica y programa la entrega de expedientes, asegurando el cumplimiento puntual de todos los plazos establecidos.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Feature Card 3 -->
                        <div class="feature-card bg-white p-6 animate-fade-in delay-300">
                            <div class="text-center">
                                <span class="feature-card-icon">
                                    <ion-icon name="checkbox-outline"></ion-icon>
                                </span>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Revisión de Expedientes</h3>
                                <p class="text-gray-600">
                                    Realiza revisiones detalladas de expedientes, asegurando la integridad y completitud de toda la información.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Feature Card 4 -->
                        <div class="feature-card bg-white p-6 animate-fade-in delay-400">
                            <div class="text-center">
                                <span class="feature-card-icon">
                                    <ion-icon name="analytics-outline"></ion-icon>
                                </span>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Estadísticas y Análisis</h3>
                                <p class="text-gray-600">
                                    Visualiza datos importantes mediante gráficos interactivos para tomar decisiones informadas y estratégicas.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Feature Card 5 -->
                        <div class="feature-card bg-white p-6 animate-fade-in delay-400">
                            <div class="text-center">
                                <span class="feature-card-icon">
                                    <ion-icon name="file-tray-full-outline"></ion-icon>
                                </span>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Gestión de Recepción</h3>
                                <p class="text-gray-600">
                                    Controla la recepción de expedientes con un sistema organizado que facilita el seguimiento y la trazabilidad.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Feature Card 6 -->
                        <div class="feature-card bg-white p-6 animate-fade-in delay-400">
                            <div class="text-center">
                                <span class="feature-card-icon">
                                    <ion-icon name="book-outline"></ion-icon>
                                </span>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Normatividad y Documentación</h3>
                                <p class="text-gray-600">
                                    Accede a toda la normativa y documentación necesaria para asegurar el cumplimiento de las disposiciones legales.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Call to Action -->
            <section class="py-16 bg-gradient text-white">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-3xl font-bold mb-6">¿Listo para optimizar la gestión de tus expedientes?</h2>
                    <p class="text-lg mb-8 max-w-2xl mx-auto">
                        Comienza a utilizar SAES hoy mismo y experimenta una nueva forma de gestionar tus expedientes
                        con eficiencia, control y resultados.
                    </p>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block bg-white text-primary font-semibold px-6 py-3 rounded-lg shadow-lg hover:bg-gray-100 transition duration-300 transform hover:-translate-y-1">
                            <ion-icon name="apps-outline" class="mr-1"></ion-icon> 
                            Acceder al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block bg-white text-primary font-semibold px-6 py-3 rounded-lg shadow-lg hover:bg-gray-100 transition duration-300 transform hover:-translate-y-1">
                            <ion-icon name="log-in-outline" class="mr-1"></ion-icon>
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </section>
            
            <!-- Footer -->
            <footer class="bg-gray-800 text-white py-8">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center space-x-2">
                                <div class="bg-white rounded-full p-1 shadow-sm">
                                    <ion-icon name="folder-open" class="text-primary text-lg"></ion-icon>
                                </div>
                                <span class="text-xl font-bold">SAES</span>
                            </div>
                            <p class="text-gray-400 text-sm mt-2">Sistema de Archivo de Expedientes de Seguimiento</p>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-gray-400 text-sm">© 2025 SAES. Todos los derechos reservados.</p>
                            <div class="mt-2">
                                <a href="#" class="text-gray-300 hover:text-white mx-2">Términos de Servicio</a>
                                <a href="#" class="text-gray-300 hover:text-white mx-2">Políticas de Privacidad</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
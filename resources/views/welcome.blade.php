<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SAES - Sistema Automatizado de Evaluación de Sanciones</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            /* Tailwind CSS */
            @import url('https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css');

            .bg-primary {
                background-color: #FF2D20;
            }

            .text-primary {
                color: #FF2D20;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-gray-800 dark:text-gray-200">
        <div class="bg-gray-50 dark:bg-gray-800 min-h-screen flex flex-col items-center">
            <header class="w-full bg-primary text-white py-4">
                <div class="container mx-auto flex justify-between items-center">
                    <div class="text-2xl font-bold">
                        SAES - Sistema Automatizado de Evaluación de Sanciones
                    </div>
                    <nav>
                        @if (Route::has('login'))
                            <div class="space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="hover:text-gray-300">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="hover:text-gray-300">Register</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </nav>
                </div>
            </header>

            <main class="container mx-auto flex-1 px-6 py-12">
                <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
                    <!-- Card: Entrega Recepción de Expedientes -->
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Entrega Recepción de Expedientes</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Gestiona la recepción de expedientes de acción, incluyendo la carga de archivos y programación de entregas.
                        </p>
                        <a href="{{ url('/entrega-recepcion') }}" class="mt-4 inline-block text-primary hover:text-red-700">Ver más &rarr;</a>
                    </div>

                    <!-- Card: Programación de Entrega -->
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Programación de Entrega</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Planifica y programa la entrega de expedientes a los responsables designados, asegurando el cumplimiento de los plazos establecidos.
                        </p>
                        <a href="{{ url('/programacion-entrega') }}" class="mt-4 inline-block text-primary hover:text-red-700">Ver más &rarr;</a>
                    </div>

                    <!-- Card: Revisión de Expedientes -->
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Revisión de Expedientes</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Realiza la revisión detallada de los expedientes recibidos, verificando la integridad y completitud de la información.
                        </p>
                        <a href="{{ url('/revision-expedientes') }}" class="mt-4 inline-block text-primary hover:text-red-700">Ver más &rarr;</a>
                    </div>
                </div>
            </main>

            <footer class="w-full bg-gray-100 dark:bg-gray-700 text-center py-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}) | © 2024 ASF
                </div>
            </footer>
        </div>
    </body>
</html>

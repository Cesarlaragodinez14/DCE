<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SAES - Sistema de Archivo de Expedientes de Seguimiento</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            /* Tailwind CSS */
            @import url('https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css');

            .bg-primary {
                background-color: #1f2937;
            }

            .text-primary {
                color: #1f2937;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-gray-800 dark:text-gray-200">
        <div class="bg-gray-50 dark:bg-gray-800 min-h-screen flex flex-col items-center">
            <header class="w-full bg-primary text-white py-4">
                <div class="container mx-auto flex justify-between items-center">
                    <div class="text-2xl font-bold">
                        SAES - Sistema de Archivo de Expedientes de Seguimiento
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
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Bienvenido al SAES</h1>
                    <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                        El Sistema de Archivo de Expedientes de Seguimiento (SAES) te permite gestionar y seguir de manera eficiente los expedientes de sanción. Accede a las distintas funcionalidades para optimizar tus procesos y asegurar el cumplimiento de las normativas.
                    </p>
                </div>
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

                <div class="mt-12 grid gap-6 lg:grid-cols-3 lg:gap-8">
                    <!-- Card: Normatividad y Documentación -->
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Normatividad y Documentación</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Accede a la normativa y documentación relacionada con la gestión de expedientes para asegurar el cumplimiento de las disposiciones legales.
                        </p>
                        <a href="{{ url('/normatividad-documentacion') }}" class="mt-4 inline-block text-primary hover:text-red-700">Ver más &rarr;</a>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>

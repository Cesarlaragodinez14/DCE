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
                <div class="container mx-auto flex flex-wrap justify-between items-center px-4">
                    <div class="text-2xl font-bold">
                        SAES
                    </div>
                    <nav class="mt-2 sm:mt-0">
                        @if (Route::has('login'))
                            <div class="space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="hover:text-gray-300">Ingresar</a>
                                @endauth
                            </div>
                        @endif
                    </nav>
                </div>
            </header>

            <main class="container mx-auto flex-1 px-4 py-6 sm:px-6 sm:py-12" style="background: #FFF; text-align:center">
                <img style="max-width: 50vw; margin: 0 auto" src="https://static.vecteezy.com/system/resources/previews/024/527/469/non_2x/sleeping-white-cat-error-404-flash-message-tilted-zero-number-lazy-cat-lying-down-empty-state-ui-design-page-not-found-popup-cartoon-image-flat-illustration-concept-on-white-background-vector.jpg" alt="">
                <a href="/dashboard" class="btn">Ir al home</a>
            </main>
        </div>
    </body>
</html>

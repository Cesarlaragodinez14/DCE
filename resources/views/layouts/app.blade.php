<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SAES') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
        <!-- Styles -->
        @livewireStyles

        <style>
            .max-w-7xl {
                max-width: 100%!important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')
            @if(session()->has('impersonated_by'))
            <form action="{{ route('stopImpersonation') }}" method="GET">
                @csrf
                <button type="submit">Dejar de Impersonar</button>
            </form>
        @endif
        
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="mx-auto py-6" style="max-width: 90%">
                        {{ $header }}
                    </div>
                </header>
            @endif
            @if(session('success'))
                <style>
                .alert-success {
                    background: darkseagreen;
                    text-align: center;
                    font-size: 24px;
                    margin: 10px 50px;
                    padding: 11px;
                }
                </style>
                <div class="alert alert-success bg-green-600 text-white p-4 rounded mb-4 shadow-md">
                    {{ session('success') }}
                </div>
            @endif



            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Contenedor para mensajes de error -->
        <div id="validation-error" class="mt-4 hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden" role="alert">
            <strong class="font-bold">Error:</strong>
            <span id="validation-error-text" class="block sm:inline"></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg id="close-error-alert" class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Cerrar</title>
                    <path d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"/>
                </svg>
            </span>
        </div>


        @stack('modals')
        @stack('scripts')

        @livewireScripts
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
        
    </body>
</html>

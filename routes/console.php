<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\GenerarEtiquetasCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Programar la generación automática de etiquetas cada 24 horas
Schedule::command('etiquetas:generar')
    ->daily()
    ->at('02:00') // Ejecutar a las 2:00 AM
    ->withoutOverlapping() // Evitar ejecuciones simultáneas
    ->runInBackground() // Ejecutar en background
    ->appendOutputTo(storage_path('logs/etiquetas_generacion.log')); // Log de salida

// Programar la limpieza automática del caché de estadísticas cada hora
Schedule::command('cache:limpiar-estadisticas-auditorias --force')
    ->hourly() // Ejecutar cada hora
    ->withoutOverlapping() // Evitar ejecuciones simultáneas
    ->runInBackground() // Ejecutar en background
    ->appendOutputTo(storage_path('logs/cache_limpieza.log')); // Log de salida

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para el dashboard de IA
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/charts/data', [App\Http\Controllers\Api\DashboardController::class, 'getChartsData']);
    Route::get('/dashboard/charts/entregas/data', [App\Http\Controllers\Api\DashboardController::class, 'getEntregasData']);
});

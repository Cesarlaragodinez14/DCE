<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelUploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard/upload-excel', [ExcelUploadController::class, 'showUploadForm'])->name('dashboard.upload-excel.form');
    Route::post('/dashboard/upload-excel', [ExcelUploadController::class, 'uploadExcel'])->name('dashboard.upload-excel.upload');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelUploadController;
use App\Http\Controllers\ExpedientesController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\ApartadosController;
use App\Http\Controllers\PdfController;

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
    Route::get('/dashboard/progress', [ExcelUploadController::class, 'showProgress'])->name('dashboard.progress');
    Route::get('/dashboard/distribucion', [ExcelUploadController::class, 'mostrarReporte'])->name('dashboard.distribucion');
    Route::get('/dashboard/oficio-uaa', [ExcelUploadController::class, 'mostrarReporteOficio'])->name('dashboard.oficio-uaa');
    Route::get('/import/{id}/show', [ExcelUploadController::class, 'showImportedData'])->name('dashboard.show-imported-data');

    Route::get('/dashboard/expedientes/entrega', [ExpedientesController::class, 'show'])->name('dashboard.expedientes.entrega');
    Route::get('/dashboard/expedientes/recepcion', [EntregaController::class, 'mostrarRecepcion'])->name('dashboard.expedientes.recepcion');


    // Ruta para mostrar los apartados de una auditoría
    Route::get('/dashboard/auditorias/{auditoria_id}/apartados', [ApartadosController::class, 'index'])->name('auditorias.apartados');
    // Ruta para guardar el checklist de apartados
    Route::post('/dashboard/auditorias/apartados/checklist', [ApartadosController::class, 'storeChecklist'])->name('apartados.checklist.store');

    Route::get('/auditorias/{auditoria_id}/pdf', [PdfController::class, 'generateChecklistPdf'])->name('auditorias.pdf');

    
    Route::get('/expedientes/detalle', [ExpedientesController::class, 'detalle'])->name('expedientes.detalle');
    Route::post('/expedientes/validar', [ExpedientesController::class, 'validarEntrega'])->name('expedientes.validar');
    Route::post('/expedientes/confirmar', [ExpedientesController::class, 'confirmEntrega'])->name('expedientes.confirmar');
    
    Route::resource('entregas', EntregaController::class);

});
// routes/web.php

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/roles-permissions', [RolePermissionController::class, 'index'])->name('admin.roles-permissions');
    Route::post('/admin/roles-permissions/{user}', [RolePermissionController::class, 'update'])->name('admin.roles-permissions.update');
    
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');

    Route::get('/admin/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
    Route::post('/admin/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');

    Route::resource('users', UserController::class);

});

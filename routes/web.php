<?php

use App\Helpers\MailHelper;
use App\Http\Controllers\ApartadosController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\ExcelUploadController;
use App\Http\Controllers\ExpedientesController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SignatureController;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/firma/{filename}', [SignatureController::class, 'show'])
    ->name('firma.show')
    ->middleware('auth'); 
    
Route::get('/test-dynamic-email', function () {
    $subject = 'Correo de Prueba Dinámico';
    $content = '<p>Este es un <strong>correo de prueba</strong> usando la plantilla dinámica.</p>';
    $recipients = ['al@mdtch.mx', 'al@pwa.mx'];

    MailHelper::sendDynamicMail($recipients, $subject, $content, [
        'footer' => 'Este es un correo de prueba.'
    ]);

    return 'Correo de prueba enviado!';
});

// Rutas para el perfil de usuario (sin middleware de verificación de perfil)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Ruta para mostrar y actualizar el perfil del usuario
    Route::get('/user/profile', function () {
        // Retornar la vista del perfil o utilizar un controlador
        return view('profile.show');
    })->name('profile.show');

    // Aquí puedes incluir otras rutas que deban ser accesibles sin el middleware 'check.user.profile'
});

// Rutas que requieren autenticación y verificación de perfil
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'check.user.profile',
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
    Route::get('/auditorias/{id}/downloadUua', [PdfController::class, 'downloadUua'])->name('auditorias.downloadUua');

    // Ruta para subir Firma de la UAA
    Route::post('/apartados/uua', [ApartadosController::class, 'storeUua'])->name('apartados.storeUua');   

    Route::get('/expedientes/detalle', [ExpedientesController::class, 'detalle'])->name('expedientes.detalle');
    Route::post('/expedientes/validar', [ExpedientesController::class, 'validarEntrega'])->name('expedientes.validar');
    Route::post('/expedientes/confirmar', [ExpedientesController::class, 'confirmEntrega'])->name('expedientes.confirmar');
    
    Route::resource('entregas', EntregaController::class);
});

// Rutas de administración con middleware de rol y verificación de perfil
Route::middleware(['auth', 'role:admin', 'check.user.profile'])->group(function () {
    Route::get('/admin/roles-permissions', [RolePermissionController::class, 'index'])->name('admin.roles-permissions');
    Route::post('/admin/roles-permissions/{user}', [RolePermissionController::class, 'update'])->name('admin.roles-permissions.update');
    
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');

    Route::get('/admin/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
    Route::post('/admin/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');

    Route::resource('users', UserController::class);
});

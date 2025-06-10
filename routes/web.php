<?php

    use App\Helpers\MailHelper;
    use App\Http\Controllers\AIController;
    use App\Http\Controllers\ApartadosController;
    use App\Http\Controllers\AuditoriaController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\DashboardEntregasController;
    use App\Http\Controllers\DebugController;
    use App\Http\Controllers\EntregaController;
    use App\Http\Controllers\ExcelUploadController;
    use App\Http\Controllers\ExpedientesController;
    use App\Http\Controllers\GeneradorTarjetasController;
    use App\Http\Controllers\ImpersonationController;
    use App\Http\Controllers\PdfController;
    use App\Http\Controllers\PermissionController;
    use App\Http\Controllers\RecepcionController;
    use App\Http\Controllers\RecepcionHistoryController;
    use App\Http\Controllers\ReporteController;
    use App\Http\Controllers\RoleController;
    use App\Http\Controllers\RolePermissionController;
    use App\Http\Controllers\SignatureController;
    use App\Http\Controllers\UserController;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Rutas públicas
    |--------------------------------------------------------------------------
    */

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/validador/{hash}', [PdfController::class, 'validador'])->name('validador');
    Route::get('/validador/download/{hash}', [PdfController::class, 'downloadPdf'])->name('validador.download');
    Route::get('validador-entregas/{hash}', [RecepcionController::class, 'validadorEntregas'])
        ->name('validador-entregas');
    Route::get('validador-entregas/{hash}/download', [RecepcionController::class, 'downloadValidadorEntregas'])
        ->name('validador-entregas.download');

    /*
    |--------------------------------------------------------------------------
    | Rutas de prueba
    |--------------------------------------------------------------------------
    */

    Route::get('/test-dynamic-email', function () {
        $subject = 'Correo de Prueba Dinámico';
        $content = '<p>Este es un <strong>correo de prueba</strong> usando la plantilla dinámica.</p>';
        $recipients = ['al@mdtch.mx', 'al@pwa.mx'];

        MailHelper::sendDynamicMail($recipients, $subject, $content, [
            'footer' => 'Este es un correo de prueba.'
        ]);

        return 'Correo de prueba enviado!';
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas autenticadas sin verificación de perfil
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        // Ruta para mostrar y actualizar el perfil del usuario
        Route::get('/user/profile', function () {
            return view('profile.show');
        })->name('profile.show');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/users/impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate');
        Route::get('/users/stop-impersonation', [UserController::class, 'stopImpersonation'])->name('stopImpersonation');
        
        Route::get('/firma/{filename}', [SignatureController::class, 'show'])
            ->name('firma.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas autenticadas con verificación de perfil
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'check.user.profile',
    ])->group(function () {
        
        // Dashboard principal
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::get('/dashboard/charts', [DashboardController::class, 'dashboardIndex'])->name('dashboard.charts.index');
        Route::get('/dashboard/charts/entregas', [DashboardEntregasController::class, 'dashboardEntregasIndex'])->name('dashboard.charts.entregas');
        Route::get('/dashboard/debug/entregas', [DebugController::class, 'debugEntregas'])->name('debug.entregas');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para Excel y cargas de archivos
        |--------------------------------------------------------------------------
        */
        
        Route::get('/dashboard/upload-excel', [ExcelUploadController::class, 'showUploadForm'])->name('dashboard.upload-excel.form');
        Route::post('/dashboard/upload-excel', [ExcelUploadController::class, 'uploadExcel'])->name('dashboard.upload-excel.upload');
        Route::get('/dashboard/progress', [ExcelUploadController::class, 'showProgress'])->name('dashboard.progress');
        Route::get('/dashboard/distribucion', [ExcelUploadController::class, 'mostrarReporte'])->name('dashboard.distribucion');
        Route::get('/dashboard/oficio-uaa', [ExcelUploadController::class, 'mostrarReporteOficio'])->name('dashboard.oficio-uaa');
        Route::get('/import/{id}/show', [ExcelUploadController::class, 'showImportedData'])->name('dashboard.show-imported-data');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para expedientes y entregas
        |--------------------------------------------------------------------------
        */
        
        Route::get('/dashboard/expedientes/entrega', [ExpedientesController::class, 'show'])->name('dashboard.expedientes.entrega');
        Route::get('/dashboard/expedientes/recepcion', [EntregaController::class, 'mostrarRecepcion'])->name('dashboard.expedientes.recepcion');
        Route::post('/dashboard/entregas/confirmar', [EntregaController::class, 'confirmarEntrega'])->name('entregas.confirmar');
        
        Route::get('/expedientes/detalle', [ExpedientesController::class, 'detalle'])->name('expedientes.detalle');
        Route::post('/expedientes/validar', [ExpedientesController::class, 'validarEntrega'])->name('expedientes.validar');
        Route::post('/expedientes/confirmar', [ExpedientesController::class, 'confirmEntrega'])->name('expedientes.confirmar');
        
        Route::resource('entregas', EntregaController::class);
        
        Route::prefix('dashboard/expedientes')->group(function () {
            Route::get('/recepcion', [RecepcionController::class, 'index'])
                ->name('recepcion.index');

            Route::get('/historial-programacion', [RecepcionHistoryController::class, 'index'])
                ->name('programacion-historial.index');
        
            Route::post('/ajax-toggle-entregado', [RecepcionController::class, 'ajaxToggleEntregado'])
                ->name('recepcion.ajaxToggleEntregado');
        
            Route::post('/generar-acuse', [RecepcionController::class, 'generarAcuse'])
                ->name('recepcion.generarAcuse');
        });
        
        Route::get('/contraparte/firma/{entregaId}', [RecepcionController::class, 'generateAcusePdfContraparte'])
            ->name('contraparte.firma');
            
        Route::get('/recepcion/rastreo/{id}', [RecepcionController::class, 'getRastreo'])
            ->name('recepcion.rastreo');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para auditorías y apartados
        |--------------------------------------------------------------------------
        */
        
        Route::get('/dashboard/auditorias/{auditoria_id}/apartados', [ApartadosController::class, 'index'])->name('auditorias.apartados');
        Route::post('/dashboard/auditorias/apartados/checklist', [ApartadosController::class, 'storeChecklist'])->name('apartados.checklist.store');
        Route::get('/dashboard/auditorias/historico', [ApartadosController::class, 'show'])->name('auditorias.show');
        Route::post('/apartados/uua', [ApartadosController::class, 'storeUua'])->name('apartados.storeUua');
        
        Route::get('/auditorias/{auditoria_id}/pdf', [PdfController::class, 'generateChecklistPdf'])->name('auditorias.pdf');
        Route::get('/auditorias/{id}/downloadUua', [PdfController::class, 'downloadUua'])->name('auditorias.downloadUua');
        Route::post('/pdf/generate-signed/{auditoria_id}', [PdfController::class, 'generateSignedChecklistPdf'])->name('pdf.generateSignedChecklistPdf');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para IA
        |--------------------------------------------------------------------------
        */
        
        Route::get('/dashboard/ai', [AIController::class, 'index'])->name('ai.index');
        Route::post('/dashboard/ai/send', [AIController::class, 'sendMessage'])->name('ai.sendMessage');
        Route::post('/dashboard/ai/clear', [AIController::class, 'clearChat'])->name('ai.clearChat');
        Route::get('/dashboard/ai/conversations', [AIController::class, 'getConversations'])->name('ai.getConversations');
        Route::get('/dashboard/ai/conversation/{id}', [AIController::class, 'getConversation'])->name('ai.getConversation');
        Route::delete('/dashboard/ai/conversation/{id}', [AIController::class, 'deleteConversation'])->name('ai.deleteConversation');
        Route::post('/dashboard/ai/generate-summary', [AIController::class, 'generateSummary'])->name('ai.generateSummary');
        Route::post('/dashboard/ai/summarize-descriptions', [AIController::class, 'summarizeDescriptions'])->name('ai.summarizeDescriptions');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para reportes
        |--------------------------------------------------------------------------
        */
        
        Route::get('/exportar-reporte', [ReporteController::class, 'exportarReporte'])->name('exportar.reporte');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para el Generador de Tarjetas
        |--------------------------------------------------------------------------
        */
        
        Route::get('/generador-tarjetas', [GeneradorTarjetasController::class, 'index'])->name('generador.tarjetas');
        
        /*
        |--------------------------------------------------------------------------
        | Rutas para reportes específicos
        |--------------------------------------------------------------------------
        */
        
        Route::get('/tarjeta-auditor-esp', [App\Http\Controllers\TarjetaAuditorEspController::class, 'index'])->name('tarjeta-auditor-esp.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas para administración
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth', 'role:admin', 'check.user.profile'])->group(function () {
        Route::get('/admin/roles-permissions', [RolePermissionController::class, 'index'])->name('admin.roles-permissions');
        Route::post('/admin/roles-permissions/{user}', [RolePermissionController::class, 'update'])->name('admin.roles-permissions.update');
        
        Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');

        Route::get('/admin/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
        Route::post('/admin/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
        
        Route::post('/dashboard/all-auditorias/{auditoria}/reset', [AuditoriaController::class, 'reset'])->name('dashboard.all-auditorias.reset');

        Route::resource('users', UserController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas para reportes específicos
    |--------------------------------------------------------------------------
    */

    Route::get('/reportes/observaciones-apartados/{entrega_id?}', [App\Http\Controllers\ReporteController::class, 'observacionesApartados'])->name('reportes.observaciones-apartados');

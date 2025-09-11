<?php

    use App\Helpers\MailHelper;
    use App\Http\Controllers\AIController;
    use App\Http\Controllers\ApartadosController;
    use App\Http\Controllers\AuditoriaController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\DashboardEntregasController;
    use App\Http\Controllers\EntregaController;
    use App\Http\Controllers\ExcelUploadController;
    use App\Http\Controllers\ExpedientesController;
    use App\Http\Controllers\GeneradorTarjetasController;
    use App\Http\Controllers\ImpersonationController;
    use App\Http\Controllers\PdfController;
    use App\Http\Controllers\PermissionController;
    use App\Http\Controllers\RecepcionController;
    use App\Http\Controllers\PrivacyNoticeController;
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

        // Aviso de privacidad
        Route::get('/aceptar-aviso-privacidad', [PrivacyNoticeController::class, 'show'])->name('privacy.notice');
        Route::post('/aceptar-aviso-privacidad', [PrivacyNoticeController::class, 'accept'])->name('privacy.notice.accept');
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
        Route::get('/dashboard/auditorias/resumen', [\App\Livewire\ResumenAuditorias::class, '__invoke'])->name('auditorias.resumen');
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

    // Ruta temporal para diagnosticar Claude API en servidor
    Route::get('/debug/claude-api', function() {
        $apiKey = trim(env('CLAUDE_API'));
        $model = env('CLAUDE_MODEL', 'claude-3-5-haiku-20241022');
        
        // Clean API key of any whitespace/line breaks
        $apiKey = preg_replace('/\s+/', '', $apiKey);
        
        $diagnosis = [
            'timestamp' => now()->toDateTimeString(),
            'api_key_configured' => !empty($apiKey),
            'api_key_length' => strlen($apiKey ?? ''),
            'api_key_format_valid' => str_starts_with($apiKey ?? '', 'sk-ant-'),
            'api_key_preview' => substr($apiKey ?? '', 0, 15) . '...' . substr($apiKey ?? '', -10),
            'model_configured' => $model,
            'has_line_breaks' => preg_match('/\r|\n/', $apiKey ?? '') ? true : false,
            'has_spaces' => preg_match('/\s/', $apiKey ?? '') ? true : false,
        ];
        
        // Intentar hacer una llamada de prueba a Claude
        if (!empty($apiKey)) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'anthropic-version' => '2023-06-01',
                ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                    'model' => $model,
                    'max_tokens' => 50,
                    'temperature' => 0.7,
                    'system' => 'Responde brevemente',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Di "Test exitoso" en una palabra'
                        ]
                    ]
                ]);
                
                if ($response->successful()) {
                    $responseData = $response->json();
                    $diagnosis['test_result'] = 'SUCCESS';
                    $diagnosis['claude_response'] = $responseData['content'][0]['text'] ?? 'Sin contenido';
                } else {
                    $diagnosis['test_result'] = 'FAILED';
                    $diagnosis['error_code'] = $response->status();
                    $diagnosis['error_body'] = $response->body();
                }
            } catch (\Exception $e) {
                $diagnosis['test_result'] = 'EXCEPTION';
                $diagnosis['error_message'] = $e->getMessage();
            }
        } else {
            $diagnosis['test_result'] = 'NO_API_KEY';
        }
        
        return response()->json($diagnosis, 200, [], JSON_PRETTY_PRINT);
    })->middleware(['auth']); // Solo para usuarios autenticados

    // Ruta temporal para limpiar caché desde navegador
    Route::get('/debug/clear-cache', function() {
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Caché limpiado exitosamente',
                'timestamp' => now()->toDateTimeString()
            ], 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500, [], JSON_PRETTY_PRINT);
                 }
     })->middleware(['auth']);

     // Ruta temporal para probar API key específica
     Route::post('/debug/test-claude-key', function(\Illuminate\Http\Request $request) {
         $apiKey = trim($request->input('api_key'));
         $model = env('CLAUDE_MODEL', 'claude-3-5-haiku-20241022');
         
         if (empty($apiKey)) {
             return response()->json(['error' => 'API key requerida'], 400);
         }
         
         // Clean API key of any whitespace/line breaks
         $apiKey = preg_replace('/\s+/', '', $apiKey);
         
         $diagnosis = [
             'timestamp' => now()->toDateTimeString(),
             'api_key_length' => strlen($apiKey),
             'api_key_preview' => substr($apiKey, 0, 15) . '...' . substr($apiKey, -10),
             'model_tested' => $model,
         ];
         
         try {
             $response = \Illuminate\Support\Facades\Http::withHeaders([
                 'x-api-key' => $apiKey,
                 'Content-Type' => 'application/json',
                 'anthropic-version' => '2023-06-01',
             ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                 'model' => $model,
                 'max_tokens' => 50,
                 'temperature' => 0.7,
                 'system' => 'Responde brevemente',
                 'messages' => [
                     [
                         'role' => 'user',
                         'content' => 'Di "Test exitoso" en una palabra'
                     ]
                 ]
             ]);
             
             if ($response->successful()) {
                 $responseData = $response->json();
                 $diagnosis['test_result'] = 'SUCCESS';
                 $diagnosis['claude_response'] = $responseData['content'][0]['text'] ?? 'Sin contenido';
             } else {
                 $diagnosis['test_result'] = 'FAILED';
                 $diagnosis['error_code'] = $response->status();
                 $diagnosis['error_body'] = $response->body();
             }
         } catch (\Exception $e) {
             $diagnosis['test_result'] = 'EXCEPTION';
             $diagnosis['error_message'] = $e->getMessage();
         }
         
                return response()->json($diagnosis, 200, [], JSON_PRETTY_PRINT);
   })->middleware(['auth']);

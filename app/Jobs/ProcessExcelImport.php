<?php

namespace App\Jobs;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditoriasImport;
use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Log;

class ProcessExcelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importId;

    public function __construct($importId)
    {
        $this->importId = $importId;
    }

    public function handle()
    {
        try {
            Log::info("Iniciando el trabajo de importación para Import ID: " . $this->importId);
            
            $import = Import::find($this->importId);
            if (!$import) {
                Log::error("No se encontró el registro de Import con ID: " . $this->importId);
                return;
            }

            $import->update(['status' => 'processing']);
            Log::info("Estado actualizado a 'processing' para Import ID: " . $this->importId);

            $filePath = storage_path('app/' . $import->file_path);
            Log::info("Procesando el archivo en: " . $filePath);

            $totalRows = Excel::toCollection(new AuditoriasImport($import), $filePath)->first()->count();
            Log::info("Total de filas en el archivo: " . $totalRows);

            $import->update(['total_rows' => $totalRows]);
            Log::info("Actualización del total de filas en la base de datos para Import ID: " . $this->importId);

            Excel::import(new AuditoriasImport($import), $filePath);
            Log::info("Importación completada para Import ID: " . $this->importId);

            $import->update(['status' => 'completed']);
            Log::info("Estado actualizado a 'completed' para Import ID: " . $this->importId);
        } catch (Exception $e) {
            Log::error('Error procesando la importación para Import ID: ' . $this->importId . ' - ' . $e->getMessage());
            $import->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\RoundBlockSizeMode;

use App\Helpers\MailHelper;

class RecepcionController extends Controller
{
    public function index(Request $request)
    {
        // Tus filtros y combos, sin cambios
        $entregaId   = $request->input('entrega');
        $cpId        = $request->input('cuenta_publica');
        $estatus     = $request->input('estatus');
        $responsable = $request->input('responsable');

        $entregas = DB::table('cat_entrega')->get();
        $cuentasPublicas = DB::table('cat_cuenta_publica')->get();

        // Ejemplo de UAA por defecto, o algo similar
        $uaa = DB::table('cat_uaa')->orderBy('id','asc')->first();
        $uaaName = $uaa ? $uaa->valor : 'Sin UAA';

        // Consulta principal
        $query = DB::table('aditorias')
            ->leftJoin('entregas', 'entregas.auditoria_id', '=', 'aditorias.id')
            ->leftJoin('cat_cuenta_publica','aditorias.cuenta_publica','=','cat_cuenta_publica.id')
            ->leftJoin('cat_entrega','aditorias.entrega','=','cat_entrega.id')
            ->leftJoin('cat_siglas_tipo_accion','aditorias.siglas_tipo_accion','=','cat_siglas_tipo_accion.id')
            ->leftJoin('cat_siglas_auditoria_especial as csae','aditorias.siglas_auditoria_especial','=','csae.id')
            ->leftJoin('cat_uaa','aditorias.uaa','=','cat_uaa.id')
            ->leftJoin('cat_auditoria_especial as n_auditoria','aditorias.auditoria_especial','=','n_auditoria.id')
            ->select(
                'aditorias.id',
                'cat_cuenta_publica.valor as cuenta_publica_valor',
                'cat_entrega.valor as entrega_valor',
                'csae.valor as ae_siglas',
                'cat_uaa.valor as uaa_valor',
                'aditorias.auditoria_especial',
                'aditorias.estatus_checklist as estatus_revision',
                'n_auditoria.valor as numero_auditoria',
                'aditorias.titulo',
                'aditorias.clave_de_accion',
                'cat_siglas_tipo_accion.valor as tipo_accion_valor',
                'entregas.id as id_entrega',
                'entregas.estado',
                'entregas.numero_legajos',
                'entregas.fecha_entrega',
                'entregas.fecha_real_entrega',
                'entregas.responsable as responsable_uaa',
                // Suponiendo "responsable_seg" o algo similar
                'aditorias.jefe_de_departamento as responsable_seg',
                // "ya_entregado" => si la 'estado' no es nula y es un valor no "Recibido..."
                DB::raw("CASE WHEN entregas.estado not like '%Recibido%' THEN 1 ELSE 0 END as ya_entregado")
            );

        // Aplicar filtros
        if ($entregaId) {
            $query->where('aditorias.entrega', $entregaId);
        }
        if ($cpId) {
            $query->where('aditorias.cuenta_publica', $cpId);
        }
        if ($estatus) {
            // Ejemplo: filtrar donde la "estado" sea "Programado" o "Entregado"
            $query->where('entregas.estado', $estatus);
        }
        if ($responsable) {
            $query->where('entregas.responsable', 'like', '%' . $responsable . '%');
        }
        
        $query->whereNotNull('entregas.id');

        $query->orderBy('aditorias.id');

        $expedientes = $query->get();
        $users = DB::table('users')->pluck('name')->toArray();

        return view('dashboard.recepcion', [
            'expedientes' => $expedientes,
            'entregas' => $entregas,
            'cuentasPublicas' => $cuentasPublicas,
            'uaaName' => $uaaName,
            'users' => $users,
        ]);
    }

    public function ajaxToggleEntregado(Request $request)
    {
        try {
            $data = $request->validate([
                'expediente_id' => 'required|exists:aditorias,id',
                'entregado' => 'required|boolean'
            ]);

            $entrega = DB::table('entregas')
                ->where('auditoria_id', $data['expediente_id'])
                ->first();
            if (!$entrega) {
                return response()->json(['message' => 'No existe programación de entrega para ese expediente'], 404);
            }

            return response()->json(['message' => 'OK toggled'], 200);

        } catch (\Exception $e) {
            Log::error('Error toggleEntregado: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function generarAcuse(Request $request)
    {
        $val = $request->validate([
            'estado_recepcion'       => 'required|string',
            'expedientes_seleccionados' => 'required|json'
        ]);

        $estado = $val['estado_recepcion'];
        $expIds = json_decode($val['expedientes_seleccionados'], true);

        if (!is_array($expIds) || empty($expIds)) {
            return redirect()->route('recepcion.index')
                ->with('error','No se recibieron expedientes o el formato es inválido.');
        }

        // 1) Actualizar la tabla "entregas" con el estado
        DB::table('entregas')
            ->whereIn('auditoria_id', $expIds)
            ->update([
                'estado'             => $estado,
                'fecha_real_entrega' => now(),
            ]);

        // 2) Generar y obtener el PDF
        $pdfPath = $this->generateAcusePdfDce($expIds, $estado);

        // 3) Registrar en “entregas_historial” (uno por cada $expId, o uno general)
        foreach ($expIds as $auditoriaId) {
            $ent = DB::table('entregas')->where('auditoria_id', $auditoriaId)->first();
            if($ent) {
                DB::table('entregas_historial')->insert([
                    'entrega_id'   => $ent->id,
                    'usuario_recibe_id'   => Auth::id(),
                    'estado'       => $estado,
                    'fecha_estado' => now(),
                    'pdf_path'     => $pdfPath, // la ruta devuelta
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        return redirect()->route('recepcion.index')
            ->with('success','Acuse generado correctamente con estado: '.$estado);
    }

    /**
     * Función auxiliar: Dado un ID de entrega, retorna el registro completo o null.
     */
    private function getEntregaById($entregaId)
    {
        return DB::table('entregas')->where('id', $entregaId)->first();
    }

    /**
     * Función auxiliar: Dado el ID (o array de IDs) de auditoría(s),
     * retorna la información necesaria para armar las filas de "expedienteRows".
     */
    private function buildExpedienteRowsFromAuditorias(array $expIds)
    {
        $expedienteRows = [];

        foreach ($expIds as $auditoriaId) {
            $ent = DB::table('entregas')->where('auditoria_id', $auditoriaId)->first();
            if (!$ent) {
                continue;
            }

            $auditoria = DB::table('aditorias')
                ->leftJoin('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
                ->leftJoin('cat_ente_de_la_accion', 'aditorias.ente_de_la_accion', '=', 'cat_ente_de_la_accion.id')
                ->leftJoin('cat_clave_accion', 'aditorias.clave_accion', '=', 'cat_clave_accion.id')
                ->leftJoin('cat_siglas_tipo_accion', 'aditorias.siglas_tipo_accion', '=', 'cat_siglas_tipo_accion.id')
                ->leftJoin('cat_auditoria_especial as n_auditoria', 'aditorias.auditoria_especial', '=', 'n_auditoria.id')
                ->leftJoin('cat_entrega as entrega_anho', 'aditorias.entrega', '=', 'entrega_anho.id')
                ->select(
                    'aditorias.id',
                    'cat_cuenta_publica.valor as cuenta_publica_valor',
                    'aditorias.titulo as num_titulo_auditoria',
                    'n_auditoria.valor as numero_auditoria',
                    'cat_ente_de_la_accion.valor as entidad_responsable_valor',
                    'cat_clave_accion.valor as clave_accion_valor',
                    'cat_siglas_tipo_accion.valor as tipo_accion_valor',
                    'entrega_anho.valor as periodo_entrega'
                )
                ->where('aditorias.id', $ent->auditoria_id)
                ->first();

            if (!$auditoria) {
                continue;
            }

            $expedienteRows[] = [
                'numeroConsecutivo'  => $ent->id,
                'cuentaPublica'      => $auditoria->cuenta_publica_valor      ?? 'Sin cuenta pública',
                'periodoEntrega'     => $auditoria->periodo_entrega            ?? 'Sin periodo',
                'numero_auditoria'   => $auditoria->numero_auditoria           ?? 'Sin título',
                'numTituloAuditoria' => $auditoria->num_titulo_auditoria       ?? 'Sin título',
                'entidadResponsable' => $auditoria->entidad_responsable_valor  ?? 'Sin entidad',
                'claveAccion'        => $auditoria->clave_accion_valor         ?? 'Sin clave',
                'tipoAccion'         => $auditoria->tipo_accion_valor          ?? 'Sin tipo de acción',
                'legajos'            => $ent->numero_legajos                   ?? 0,
            ];
        }

        return $expedienteRows;
    }

    /**
     * Función auxiliar: Dado un array de IDs de entrega,
     * construye las filas de "expedienteRows" (segunda firma).
     */
    private function buildExpedienteRowsFromEntregas(array $entregaIds)
    {
        $expedienteRows = [];

        foreach ($entregaIds as $entId) {
            $ent = $this->getEntregaById($entId);
            if (!$ent) {
                continue;
            }

            $auditoria = DB::table('aditorias')
                ->leftJoin('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
                ->leftJoin('cat_ente_de_la_accion', 'aditorias.ente_de_la_accion', '=', 'cat_ente_de_la_accion.id')
                ->leftJoin('cat_clave_accion', 'aditorias.clave_accion', '=', 'cat_clave_accion.id')
                ->leftJoin('cat_siglas_tipo_accion', 'aditorias.siglas_tipo_accion', '=', 'cat_siglas_tipo_accion.id')
                ->leftJoin('cat_auditoria_especial as n_auditoria', 'aditorias.auditoria_especial', '=', 'n_auditoria.id')
                ->leftJoin('cat_entrega as entrega_anho', 'aditorias.entrega', '=', 'entrega_anho.id')
                ->select(
                    'aditorias.id',
                    'cat_cuenta_publica.valor as cuenta_publica_valor',
                    'aditorias.titulo as num_titulo_auditoria',
                    'n_auditoria.valor as numero_auditoria',
                    'cat_ente_de_la_accion.valor as entidad_responsable_valor',
                    'cat_clave_accion.valor as clave_accion_valor',
                    'cat_siglas_tipo_accion.valor as tipo_accion_valor',
                    'entrega_anho.valor as periodo_entrega'
                )
                ->where('aditorias.id', $ent->auditoria_id)
                ->first();

            if (!$auditoria) {
                continue;
            }

            $expedienteRows[] = [
                'numeroConsecutivo'  => $ent->id,
                'cuentaPublica'      => $auditoria->cuenta_publica_valor      ?? 'Sin cuenta pública',
                'periodoEntrega'     => $auditoria->periodo_entrega            ?? 'Sin periodo',
                'numero_auditoria'   => $auditoria->numero_auditoria           ?? 'Sin título',
                'numTituloAuditoria' => $auditoria->num_titulo_auditoria       ?? 'Sin título',
                'entidadResponsable' => $auditoria->entidad_responsable_valor  ?? 'Sin entidad',
                'claveAccion'        => $auditoria->clave_accion_valor         ?? 'Sin clave',
                'tipoAccion'         => $auditoria->tipo_accion_valor          ?? 'Sin tipo de acción',
                'legajos'            => $ent->numero_legajos                   ?? 0,
            ];
        }

        return $expedienteRows;
    }

    /**
     * Función auxiliar para obtener userUaa, dgsegEfVal, userSEG y dgsegEfValSeg (si aplican),
     * reproduciendo la lógica solicitada.
     *
     * @param  object $entrega    Registro de la tabla 'entregas'
     * @param  object $firstAudit Registro con info de la auditoría
     * @return array
     */
    private function resolveUaaSegData($entrega, $firstAudit)
    {
        // 1) user UAA => basado en $entrega->responsable
        $userUaa = null;
        if (!empty($entrega->responsable)) {
            $userUaa = DB::table('users')
                ->where('name', 'like', '%' . $entrega->responsable . '%')
                ->first();
        }

        // 2) user SEG => basado en $firstAudit->jefe_de_departamento
        $userSEG = null;
        if (!empty($firstAudit->jefe_de_departamento)) {
            $userSEG = DB::table('users')
                ->where('name', 'like', '%' . $firstAudit->jefe_de_departamento . '%')
                ->first();
        }

        // 3) cat_uaa / dgseg_ef (UAA)
        $uaaRow = null;
        $dgsegEfVal = null;
        if ($userUaa && $userUaa->uaa_id) {
            $uaaRow = DB::table('cat_uaa')->where('id', $userUaa->uaa_id)->first();
            if ($uaaRow) {
                // Si se cuenta con un dgseg_ef_id y además:
                // - El campo "valor" es un string de 4 dígitos
                // - El campo "nombre" es un string de 2 dígitos
                // se compone un ID concatenando dgseg_ef_id y valor y se consulta la tabla cat_dgseg_ef
                if  (
                        $uaaRow->dgseg_ef_id != null 
                        && preg_match('/^\d{4}$/', $uaaRow->valor) 
                        && preg_match('/^\d{2}$/', $uaaRow->nombre)
                    ) {

                    // Se asume que la composición es concatenando: dgseg_ef_id + valor
                    $compositeId = $uaaRow->dgseg_ef_id . $uaaRow->valor;

                    // Se consulta la tabla en el esquema saes_asf
                    $dgsegRow = DB::table('saes_asf.cat_dgseg_ef')
                        ->where('id', $compositeId)
                        ->first();
                    $dgsegEfVal = $dgsegRow ? $dgsegRow->valor : $uaaRow->valor;
                } else {
                    // Si la condición no se cumple, se usa el string de cat_uaa.valor
                    $dgsegEfVal = $uaaRow->nombre ?? "Sin Información";
                }
            } else {
                $dgsegEfVal = "Sin Información";
            }
        }

        // 4) cat_uaa / dgseg_ef (SEG)
        $segRow = null;
        $dgsegEfValSeg = null;
        if ($userSEG && $userSEG->uaa_id) {
            $segRow = DB::table('cat_uaa')->where('id', $userSEG->uaa_id)->first();
            $dgsegValor = DB::table('cat_dgseg_ef')->where('id', $segRow->dgseg_ef_id)->first();
            if ($segRow) {
                
                if ($segRow->dgseg_ef_id != null 
                    && preg_match('/^\d{4}$/', $segRow->valor) 
                    && preg_match('/^\d{2}$/', $segRow->nombre)) {

                    $compositeId = $segRow->dgseg_ef_id . $segRow->valor;
                    $dgsegRow = DB::table('cat_dgseg_ef')
                        ->where('id', $compositeId)
                        ->first();
                    $dgsegEfValSeg = $dgsegRow ? $dgsegRow->valor : $segRow->valor;
                    dd($userSEG);
                } else {
                    // Si la condición no se cumple, se usa el string de cat_uaa.valor
                    $dgsegEfValSeg = $dgsegValor->valor ?? "Sin Información";
                }
            } else {
                $dgsegEfValSeg = "Sin Información";
            }
        }

        // IMPORTANTE: Respeta el orden de retorno EXACTO que usas en tu destructuring
        // [ $userUaa, $dgsegEfVal, $userSEG, $dgsegEfValSeg ]
        return [$userUaa, $dgsegEfVal, $userSEG, $dgsegEfValSeg];
    }

    /**
     * Función auxiliar para asignar quién entrega, quién recibe, correos, etc.
     * Retorna un array con las variables $respEntregaName, $respEntregaCargo, etc.
     */
    private function buildEntregaRecibeData(
        $estado,
        $entrega,
        $firstAudit,
        $userUaa,
        $dgsegEfVal,
        $userSEG = null,
        $dgsegEfValSeg = null
    ) {
        // Valores por defecto
        $respEntregaName  = 'Desconocido';
        $respEntregaCargo = '---';
        $respEntregaAe    = '---';
        $respEntregaDg    = 'No Aplica';

        $respRecibeName   = Auth::user()->name;
        $respRecibeCargo  = Auth::user()->puesto ?? 'Cargo';
        $respRecibeAe     = 'AE Recibe';
        $respRecibeDg     = 'DG Recibe';

        $correoUsuarioFirma = null;

        // Lógica if/elseif
        if (str_contains($estado, 'UAA – DCE')) {
            $respEntregaName  = $entrega->responsable;
            $respEntregaCargo = $userUaa->puesto ?? 'Sin cargo';
            $respEntregaAe    = $firstAudit->auditoria_especial_t ?? $firstAudit->direccion_de_area ?? '---4';
            $respEntregaDg    = $dgsegEfVal ?? 'No Aplica';

            $respRecibeName   = Auth::user()->name;
            $respRecibeCargo  = Auth::user()->puesto ?? 'Cargo DCE';
            $respRecibeAe     = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respRecibeDg     = 'No aplica';

            $correoUsuarioFirma = $userUaa->email ?? null;

        } elseif (str_contains($estado, 'DGSEG – DCE')) {
            $respEntregaName  = $firstAudit->jefe_de_departamento ?? 'User DGSEG';
            $respEntregaCargo = "Jefe de ".$firstAudit->jd ?? 'Cargo DGSEG';
            $respEntregaAe    = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respEntregaDg    = $dgsegEfValSeg ?? 'DG No hallada';

            $respRecibeName   = Auth::user()->name;
            $respRecibeCargo  = Auth::user()->puesto ?? 'Cargo DCE';
            $respRecibeAe     = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respRecibeDg     = 'No aplica';

            // Ojo: en tu código, $userSEG->email
            $correoUsuarioFirma = $userSEG->email ?? null;

        } elseif (str_contains($estado, 'DCE - DGSEG')) {
            $respEntregaName  = Auth::user()->name;
            $respEntregaCargo = Auth::user()->puesto ?? 'Cargo DCE';
            $respEntregaAe    = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respEntregaDg    = 'No aplica';

            $respRecibeName   = $firstAudit->jefe_de_departamento ?? 'User DGSEG';
            $respRecibeCargo  = "Jefe de ".$firstAudit->jd ?? 'Cargo DGSEG';
            $respRecibeAe     = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respRecibeDg     = $dgsegEfValSeg ?? 'DG No hallada';

            $correoUsuarioFirma = $userSEG->email ?? null;

        } elseif (str_contains($estado, 'DCE - UAA')) {
            $respEntregaName  = Auth::user()->name;
            $respEntregaCargo = Auth::user()->puesto ?? 'Cargo DCE';
            $respEntregaAe    = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respEntregaDg    = 'No aplica';

            $respRecibeName   = $entrega->responsable;
            $respRecibeCargo  = $userUaa->puesto ?? 'Cargo UAA';
            $respRecibeAe     = $firstAudit->auditoria_especial_t ?? $firstAudit->direccion_de_area ?? '---6';
            $respRecibeDg     = $dgsegEfVal ?? '---7';

            $correoUsuarioFirma = $userUaa->email ?? null;
        }

        return [
            'respEntregaName'   => $respEntregaName,
            'respEntregaCargo'  => $respEntregaCargo,
            'respEntregaAe'     => $respEntregaAe,
            'respEntregaDg'     => $respEntregaDg,
            'respRecibeName'    => $respRecibeName,
            'respRecibeCargo'   => $respRecibeCargo,
            'respRecibeAe'      => $respRecibeAe,
            'respRecibeDg'      => $respRecibeDg,
            'correoUsuarioFirma'=> $correoUsuarioFirma,
        ];
    }

    /**
     * PRIMER PASO:
     * Genera el PDF de acuse con la firma/hashing de DCE (quien crea la acción).
     * Envia notificación a la contraparte.
     *
     * @param  array  $expIds
     * @param  string $estado  Tipo de movimiento (ej: 'UAA - DCE', 'DGSEG - DCE', etc)
     * @return string Ruta relativa donde se guardó el PDF
     */
    public function generateAcusePdfDce(array $expIds, $estado)
    {
        // 1) Tomar la primera entrega
        $primerExpId = reset($expIds);
        $entrega = DB::table('entregas')
            ->where('auditoria_id', $primerExpId)
            ->first();
        if (!$entrega) {
            return null;
        }

        // 2) Auditoría con info de seguimiento
        $firstAudit = DB::table('aditorias')
            ->leftJoin('cat_siglas_auditoria_especial as csae', 'aditorias.siglas_auditoria_especial', '=', 'csae.id')
            ->select(
                'aditorias.id',
                'aditorias.titulo',
                'aditorias.jefe_de_departamento',
                'aditorias.jd',
                'aditorias.direccion_de_area',
                'csae.descripcion as auditoria_especial_t'
            )
            ->where('aditorias.id', $entrega->auditoria_id)
            ->first();

        // 3) Construir las filas de la tabla
        $expedienteRows = $this->buildExpedienteRowsFromAuditorias($expIds);

        // 4) Resolver userUaa, userSEG, etc.
        [$userUaa, $dgsegEfVal, $userSEG, $dgsegEfValSeg] = $this->resolveUaaSegData($entrega, $firstAudit);

        // 4.b) Obtener quién entrega / quién recibe según $estado
        $dataEntregaRecibe = $this->buildEntregaRecibeData($estado, $entrega, $firstAudit, $userUaa, $dgsegEfVal, $userSEG, $dgsegEfValSeg);

        $respEntregaName  = $dataEntregaRecibe['respEntregaName'];
        $respEntregaCargo = $dataEntregaRecibe['respEntregaCargo'];
        $respEntregaAe    = $dataEntregaRecibe['respEntregaAe'];
        $respEntregaDg    = $dataEntregaRecibe['respEntregaDg'];

        $respRecibeName   = $dataEntregaRecibe['respRecibeName'];
        $respRecibeCargo  = $dataEntregaRecibe['respRecibeCargo'];
        $respRecibeAe     = $dataEntregaRecibe['respRecibeAe'];
        $respRecibeDg     = $dataEntregaRecibe['respRecibeDg'];

        $correoUsuarioFirma = $dataEntregaRecibe['correoUsuarioFirma'];

        // 5) Generar hash y QR
        $user       = Auth::user(); // Quien genera el PDF (DCE)
        $ipAddress  = request()->ip();
        $generatedAt= now();
        $hashString = $user->email.'|'.$ipAddress.'|'.$generatedAt->toDateTimeString();
        $hash       = hash('sha256', $hashString);

        $validadorUrl   = route('validador-entregas', ['hash' => $hash]);
        $qrCodeDataUri  = $this->buildQrDataUri($validadorUrl);

        // 6) placeholders
        $placeholders = [
            'placeholder1'  => $estado,
            'placeholder2'  => $generatedAt->format('d/m/Y'),

            'placeholder11' => $respEntregaName,
            'placeholder12' => $respEntregaCargo,
            'placeholder13' => $respEntregaAe,
            'placeholder14' => $respEntregaDg,

            'placeholder15' => $respRecibeName,
            'placeholder16' => $respRecibeCargo,
            'placeholder17' => $respRecibeAe,
            'placeholder18' => $respRecibeDg,

            'placeholder19' => 'Firma Quien Entrega',
            'placeholder20' => 'Firma Quien Recibe',
            'placeholder21' => '___21___',
            'placeholder22' => '___22___',

            'expedienteRows'=> $expedienteRows,

            'qrCodeDataUri' => $qrCodeDataUri,
            'hash'          => $hash,
            'ipAddress'     => $ipAddress,
            'generatedAt'   => $generatedAt,
            'userEmail'     => $user->email,
        ];

        // 7) Generar PDF => guardarlo
        $pdf = PDF::loadView('pdf.entregas.acuse', $placeholders);
        $fileName     = 'Acuse_DCE_'.$entrega->id.'_'.time().'.pdf';
        $relativePath = 'public/acuses/'.$fileName;
        Storage::put($relativePath, $pdf->output());

        // Generar la URL pública del PDF
        $pdfUrl = asset('storage/acuses/'.$fileName);

        // 8) Registrar en 'entregas_pdf_histories'
        DB::table('entregas_pdf_histories')->insert([
            'entrega_id'     => $entrega->id,
            'hash'           => $hash,
            'user_ip'        => $ipAddress,
            'pdf_path'       => $fileName,
            'generated_by'   => $user->id,
            'created_at'     => now(),
            'updated_at'     => now(),
            'signature_type' => 'dce', // Marca la primera firma como de DCE
        ]);

        // 9) Enviar mail a la contraparte
        $usuarioEntrega = $correoUsuarioFirma;
        $subject  = 'Favor de Firmar la Entrega';
        $content  = 'Se ha generado un acuse firmado por DCE. Para revisarlo, haz clic en el siguiente enlace: <a href="'.$pdfUrl.'">Ver acuse PDF</a>.<br><br>
        Nota: Al presionar el botón "Firmar Acuse" procederás a firmar el acuse, por lo que te recomendamos revisarlo detenidamente antes de hacerlo.';

        $mailData = [
            'footer' => 'Correo automático.',
            'action' => [
                'text' => 'Firmar Acuse',
                'url'  => route('contraparte.firma', ['entregaId' => $entrega->id])
            ]
        ];

        if($usuarioEntrega == null){
            dd($dataEntregaRecibe);
        }

        MailHelper::sendDynamicMail([$usuarioEntrega], $subject, $content, $mailData);

        return 'acuses/'.$fileName; // o devolver la ruta
    }

    /**
     * Segunda firma de contraparte.
     * Genera un nuevo PDF con las 2 firmas visibles (DCE y Contraparte),
     * y actualiza el pdf_path de todas las entregas involucradas.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $entregaId  ID en la tabla "entregas"
     * @return \Illuminate\Http\Response
     */
    public function generateAcusePdfContraparte(Request $request, $entregaId)
    {
        // 1) Verificar si existe la entrega:
        $entrega = $this->getEntregaById($entregaId);
        if (!$entrega) {
            return view('validador-entregas.contraparte-error')->with(
                'error',
                'No se encontró la entrega con id: '.$entregaId
            );
        }

        // 2) Obtener de 'entregas_historial' el PDF que se generó en la primera firma
        $historial = DB::table('entregas_historial')
            ->where('entrega_id', $entrega->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$historial || !$historial->pdf_path) {
            return view('validador-entregas.contraparte-error')->with(
                'error',
                'No existe PDF previo (primera firma) para esta entrega.'
            );
        }

        $primerPdfPath = $historial->pdf_path; // Ej: "acuses/Acuse_DCE_....pdf"
        // Solo para quedarte con el filename:
        $primerPdfPath = explode("/", $primerPdfPath)[1] ?? $primerPdfPath;

        // 3) Verificar si *ya* existe una segunda firma para ese pdf_path:
        $existeSegundaFirma = DB::table('entregas_pdf_histories')
            ->where('pdf_path', 'like', "%$primerPdfPath%")
            ->where('signature_type', 'contraparte')
            ->exists();

        if ($existeSegundaFirma) {
            // Ya hubo firma de la contraparte: prevenimos doble firma.
            return view('validador-entregas.contraparte-error')->with(
                'error',
                'Este acuse ya fue firmado por la contraparte. No se puede volver a firmar.'
            );
        }

        // 4) (Ejemplo) verificar usuario actual, etc. (Opcional)
        $userActual = Auth::user();

        // 5) Reunir TODOS los 'entregas_historial' que comparten el MISMO pdf_path
        $historialGroup = DB::table('entregas_historial')
            ->where('pdf_path', 'like', "%$primerPdfPath%")
            ->get(); // Todos comparten la misma ruta

        if ($historialGroup->isEmpty()) {
            return view('validador-entregas.contraparte-error')->with(
                'error',
                'No se encontraron más expedientes en el acuse. (pdf_path = '.$primerPdfPath.')'
            );
        }

        // Extraer la lista de entrega_id involucrados
        $entregaIds = $historialGroup->pluck('entrega_id')->unique()->values();

        // 6) Obtener la info de cada entrega y su auditoría, para la tabla final:
        $expedienteRows = $this->buildExpedienteRowsFromEntregas($entregaIds->toArray());

        // 7) Recuperar DATOS DE LA PRIMERA FIRMA
        $primeraFirma = DB::table('entregas_pdf_histories')
            ->where('pdf_path', 'like', "%$primerPdfPath%")
            ->where('signature_type', 'dce') // la 1a firma
            ->orderBy('id', 'asc')
            ->first();

        if (!$primeraFirma) {
            return view('validador-entregas.contraparte-error')->with(
                'error',
                'No se encontró la primera firma del acuse en entregas_pdf_histories.'
            );
        }

        // QR para la primera firma:
        $validadorUrlPrimera = route('validador-entregas', ['hash' => $primeraFirma->hash]);
        $qrCodeDataUri1      = $this->buildQrDataUri($validadorUrlPrimera);

        // 8) Generar DATOS DE LA SEGUNDA FIRMA:
        $ipSegunda    = $request->ip();
        $fechaSegunda = now();
        $hashString   = $userActual->email.'|'.$ipSegunda.'|'.$fechaSegunda;
        $hashSegunda  = hash('sha256', $hashString);

        // QR para la segunda firma:
        $validadorUrlSegunda = route('validador-entregas', ['hash' => $hashSegunda]);
        $qrCodeDataUri2      = $this->buildQrDataUri($validadorUrlSegunda);

        // 9) Insertar registro de la 2a firma en la tabla 'entregas_pdf_histories'
        $pdfHistoryId = DB::table('entregas_pdf_histories')->insertGetId([
            'entrega_id'    => $entrega->id,
            'hash'          => $hashSegunda,
            'user_ip'       => $ipSegunda,
            'pdf_path'      => '', // Se completará después
            'generated_by'  => $userActual->id,
            'signature_type'=> 'contraparte',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 10) Resolvemos datos complementarios (parecido a la primera función)
        //     Auditoría de la entrega
        $firstAudit = DB::table('aditorias')
            ->leftJoin('cat_siglas_auditoria_especial as csae', 'aditorias.siglas_auditoria_especial', '=', 'csae.id')
            ->select(
                'aditorias.id',
                'aditorias.titulo',
                'aditorias.jefe_de_departamento',
                'aditorias.jd',
                'aditorias.direccion_de_area',
                'csae.descripcion as auditoria_especial_t'
            )
            ->where('aditorias.id', $entrega->auditoria_id)
            ->first();

        // Obtenemos también userUaa, userSEG, etc. para reusar la lógica if/elseif
        [$userUaa, $dgsegEfVal, $userSEG, $dgsegEfValSeg] = $this->resolveUaaSegData($entrega, $firstAudit);

        // El estado lo tomamos del último "historial" (o del propio $entrega->estado)
        $estado = $historial->estado ?? $entrega->estado;

        // Quien generó la primera firma
        $usuarioPrimera = DB::table('users')
            ->where('id', $primeraFirma->generated_by)
            ->first();

        $nombrePrimera = $usuarioPrimera->name   ?? 'Desconocido';
        $puestoPrimera = $usuarioPrimera->puesto ?? '---';
        $emailPrimera  = $usuarioPrimera->email  ?? '---';

        // Reutilizamos el mismo if/elseif para "quién entrega / quién recibe"
        // pero adaptado para que la primera firma sea "quien entrega" en algunos casos:
        $respEntregaName  = '---';
        $respEntregaCargo = '---';
        $respEntregaAe    = '---';
        $respEntregaDg    = 'No Aplica';

        $respRecibeName   = '---';
        $respRecibeCargo  = '---';
        $respRecibeAe     = '---';
        $respRecibeDg     = '---';

        $correoUsuarioFirma = null;

        if (str_contains($estado, 'UAA – DCE')) {
            $respEntregaName  = $entrega->responsable;
            $respEntregaCargo = $userUaa->puesto ?? 'Sin cargo';
            $respEntregaAe    = $firstAudit->auditoria_especial_t ?? $firstAudit->direccion_de_area ?? '---4';
            $respEntregaDg    = $dgsegEfVal ?? 'No Aplica';

            $respRecibeName   = $usuarioPrimera->name;
            $respRecibeCargo  = $usuarioPrimera->puesto ?? 'Cargo DCE';
            $respRecibeAe     = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respRecibeDg     = 'No aplica';

            $correoUsuarioFirma = $userUaa->email ?? null;

        } elseif (str_contains($estado, 'DGSEG – DCE')) {
            $respEntregaName  = $firstAudit->jefe_de_departamento ?? 'User DGSEG';
            $respEntregaCargo = "Jefe de ".$firstAudit->jd ?? 'Cargo DGSEG';
            $respEntregaAe    = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respEntregaDg    = $dgsegEfValSeg ?? 'DG No hallada';

            $respRecibeName   = $usuarioPrimera->name;
            $respRecibeCargo  = $usuarioPrimera->puesto ?? 'Cargo DCE';
            $respRecibeAe     = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respRecibeDg     = 'No aplica';

            $correoUsuarioFirma = $userSEG->email ?? null;

        } elseif (str_contains($estado, 'DCE - DGSEG')) {
            $respEntregaName  = $usuarioPrimera->name;
            $respEntregaCargo = $usuarioPrimera->puesto ?? 'Cargo DCE';
            $respEntregaAe    = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respEntregaDg    = 'No aplica';

            $respRecibeName   = $firstAudit->jefe_de_departamento ?? 'User DGSEG';
            $respRecibeCargo  = "Jefe de ".$firstAudit->jd ?? 'Cargo DGSEG';
            $respRecibeAe     = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respRecibeDg     = $dgsegEfValSeg ?? 'DG No hallada 6';

            $correoUsuarioFirma = $userSEG->email ?? null;

        } elseif (str_contains($estado, 'DCE - UAA')) {
            $respEntregaName  = $usuarioPrimera->name;
            $respEntregaCargo = $usuarioPrimera->puesto ?? 'Cargo DCE';
            $respEntregaAe    = 'Auditoría Especial de Seguimiento, Informes e Investigación';
            $respEntregaDg    = 'No aplica';

            $respRecibeName   = $entrega->responsable;
            $respRecibeCargo  = $userUaa->puesto ?? 'Cargo UAA';
            $respRecibeAe     = $firstAudit->auditoria_especial_t ?? $firstAudit->direccion_de_area ?? '---6';
            $respRecibeDg     = $dgsegEfVal ?? '---7';

            $correoUsuarioFirma = $userUaa->email ?? null;
        }

        // 11) Preparar placeholders para la vista del PDF (ambas firmas)
        $placeholders = [
            // 1a Firma
            'hashPrimera'     => $primeraFirma->hash,
            'ipPrimera'       => $primeraFirma->user_ip,
            'fechaPrimera'    => $primeraFirma->created_at,
            'qrCodeDataUri1'  => $qrCodeDataUri1,
            'nombrePrimera'   => $nombrePrimera,
            'puestoPrimera'   => $puestoPrimera,
            'emailPrimera'    => $emailPrimera,

            // 2a Firma
            'hashSegunda'     => $hashSegunda,
            'ipSegunda'       => $ipSegunda,
            'fechaSegunda'    => $fechaSegunda,
            'qrCodeDataUri2'  => $qrCodeDataUri2,
            'emailSegunda'    => $userActual->email,
            'nombreSegunda'   => $userActual->name,
            'puestoSegunda'   => $userActual->puesto ?? '---14',

            // Quién entrega vs. quién recibe (2da firma)
            'respEntregaName'  => $respEntregaName,
            'respEntregaCargo' => $respEntregaCargo,
            'respEntregaAe'    => $respEntregaAe,
            'respEntregaDg'    => $respEntregaDg,

            'respRecibeName'   => $respRecibeName,
            'respRecibeCargo'  => $respRecibeCargo,
            'respRecibeAe'     => $respRecibeAe,
            'respRecibeDg'     => $respRecibeDg,

            // Tabla de expedientes
            'expedienteRows'  => $expedienteRows,

            // Lo que gustes sobre "estado"
            'estado'          => $entrega->estado ?? 'Estado X',
        ];

        // 12) Generar el nuevo PDF (ya con ambas firmas)
        $pdf = PDF::loadView('pdf.entregas.acuse-2firmas', $placeholders);

        // 13) Guardar el nuevo PDF
        $newFileName = 'Acuse_Conformidad_'.time().'.pdf';
        $storePath   = 'public/acuses/'.$newFileName;
        Storage::put($storePath, $pdf->output());

        // 14) Registrar en “entregas_historial” (uno por cada entrega en $entregaIds)
        foreach ($entregaIds as $eId) {
            $ent = $this->getEntregaById($eId);
            if($ent) {
                // También actualizamos la tabla 'entregas' para reflejar el estado
                DB::table('entregas')->where('id', $ent->id)->update([
                    'estado'     => $estado . " - Firmado",
                    'updated_at' => now(),
                ]);

                DB::table('entregas_historial')->insert([
                    'entrega_id'       => $ent->id,
                    'usuario_recibe_id'=> Auth::id(),
                    'estado'           => $estado . " - Firmado",
                    'fecha_estado'     => now(),
                    'pdf_path'         => "acuses/".$newFileName,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }

        // 15) Actualizar 'pdf_path' en la fila de la segunda firma
        DB::table('entregas_pdf_histories')
            ->where('id', $pdfHistoryId)
            ->update(['pdf_path' => $newFileName]);

        // 16) Mostrar vista de confirmación o redirigir
        $pdfUrl = asset('storage/acuses/'.$newFileName);
        return view('validador-entregas.acuse-contraparte-confirm', [
            'pdfUrl' => $pdfUrl,
            'hash2'  => $hashSegunda
        ]);
    }

    /**
     * Helper que genera un DataURI de un QR, para no repetir en el código.
     */
    private function buildQrDataUri(string $url)
    {
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 150,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        $writer    = new PngWriter();
        $qrImage   = $writer->write($qrCode);
        return $qrImage->getDataUri();
    }


    public function getRastreo($id)
    {
        try {
            // Consulta en la tabla "entregas_historial", uniendo con la tabla "entregas"
            $historial = DB::table('entregas_historial as eh')
                ->join('entregas as e', 'eh.entrega_id', '=', 'e.id') // Relación con la entrega
                ->join('users as u', 'eh.usuario_recibe_id', '=', 'u.id') // Relación con la entrega
                ->select(
                    'eh.estado',
                    'eh.fecha_estado',
                    'eh.pdf_path',
                    'e.responsable as responsable',
                    'u.name as recibio_entrega',
                    'e.numero_legajos',
                    'e.fecha_real_entrega'
                )
                ->where('eh.entrega_id', $id)
                ->orderBy('eh.fecha_estado', 'asc')
                ->get();

            // Formatear resultados para el JSON esperado
            $result = [];
            foreach ($historial as $row) {
                $result[] = [
                    'estado'        => $row->estado,
                    'fecha'         => $row->fecha_estado,
                    'responsable'   => $row->responsable ?? 'Desconocido', // Ya se toma de "entregas"
                    'recibio_entrega'   => $row->recibio_entrega ?? 'Desconocido', // Ya se toma de "entregas"
                    'pdf_path'      => $row->pdf_path, // Ruta del PDF si existe
                    'numero_legajos' => $row->numero_legajos,
                    'fecha_real_entrega'  => $row->fecha_real_entrega,
                ];
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            Log::error('Error en getRastreo: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra información sobre el acuse/entrega si el hash es válido,
     * o retorna una vista de error si no existe el hash.
     */
    public function validadorEntregas($hash)
    {
        // Buscar en la tabla donde guardaste los hashes (p.e. 'entregas_pdf_histories')
        $hashRecord = DB::table('entregas_pdf_histories as e')
            ->leftJoin('users as u', 'e.generated_by', '=', 'u.id')
            ->select(
                "e.entrega_id as entrega_id",
                "e.user_ip as user_ip",
                "e.created_at as created_at",
                "u.email as email",
            )
            ->where('hash', $hash)
            ->orderBy('e.id','desc')
            ->first();

        if (!$hashRecord) {
            // No se encontró, mostrar vista de error
            return view('validador-entregas.hash_not_found');
        }

        // Opcional: buscar la entrega
        //  asumiendo que guardaste 'entrega_id' en 'entregas_pdf_histories'. 
        //  O si guardaste 'auditoria_id', 
        //  y deseas buscar la 'entrega' => ajusta la lógica
        $entrega = null; 
        if (isset($hashRecord->entrega_id)) {
            $entrega = DB::table('entregas')->where('id', $hashRecord->entrega_id)->first();
        } 

        // Pasar la info a la vista:
        return view('validador-entregas.hash_info', [
            'hash'          => $hash,
            'userEmail'     => $hashRecord->email,
            'ipAddress'     => $hashRecord->user_ip,
            'generatedAt'   => $hashRecord->created_at,
        ]);
    }

    /**
     * Descarga el PDF asociado al hash, 
     * buscando en 'entregas_pdf_histories' u otra tabla. 
     */
    public function downloadValidadorEntregas($hash)
    {
        // 1) buscar en 'entregas_pdf_histories' si existe
        $hashRecord = DB::table('entregas_pdf_histories')
            ->where('hash', $hash)
            ->orderBy('id','desc')
            ->first();

        if(!$hashRecord){
            return redirect()->back()->with('error','Hash no válido.');
        }

        // La columna 'pdf_path' en la tabla guarda algo como "Acuse_recepcion_123_1670711.pdf"
        // O "acuses/Acuse_recepcion_..."
        $pdfRelativePath = $hashRecord->pdf_path;
        // Si en la tabla guardaste solo el nombre del archivo, ajusta.
        // Ruta completa en storage:
        $fullPath = storage_path('app/public/acuses/' . $pdfRelativePath);

        // Verificar si existe
        if(!file_exists($fullPath)){
            return redirect()->back()->with('error','El archivo PDF no existe en el servidor.');
        }

        // 3) Retornar la descarga
        return response()->download($fullPath, basename($fullPath));
    }

}

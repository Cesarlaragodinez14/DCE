<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirector;
use Illuminate\Support\Facades\Auth;
use App\Models\Entrega;
use App\Models\Auditorias;
use App\Models\User;

use App\Helpers\MailHelper;

class ExpedientesController extends Controller
{
    public function show(Request $request)
    {
        // Filtros
        $cuentaPublicaId = $request->input('cuenta_publica');
        $entregaId       = $request->input('entrega');
        $uaa_user = Auth::user()->uaa_id;

        // Valores por defecto
        if (is_null($cuentaPublicaId)) {
            $cuentaPublicaId = DB::table('cat_cuenta_publica')->max('id');
        }
        if (is_null($entregaId)) {
            $entregaId = DB::table('cat_entrega')->min('id');
        }

        $query = DB::table('aditorias')
            ->join('cat_siglas_auditoria_especial', 'aditorias.siglas_auditoria_especial', '=', 'cat_siglas_auditoria_especial.id')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_uaa', 'aditorias.uaa', '=', 'cat_uaa.id')
            ->leftJoin('entregas', function ($join) {
                $join->on('aditorias.id', '=', 'entregas.auditoria_id')
                    ->where('entregas.estado', 'like', '%Recibido%');
            })
            ->select(
                'cat_siglas_auditoria_especial.valor as auditoria_especial',
                'cat_cuenta_publica.valor as CP',
                'cat_entrega.valor as entrega',
                'cat_uaa.valor as uaa',
                DB::raw('COUNT(DISTINCT aditorias.id) as total_entregar'),
                DB::raw('COUNT(DISTINCT entregas.id) as total_entregados'),
                DB::raw("
                    SUM(
                        CASE 
                            WHEN EXISTS (
                                SELECT 1 
                                FROM entregas e2
                                WHERE e2.auditoria_id = aditorias.id
                                AND e2.estado = 'Programado'
                            )
                            THEN 1
                            ELSE 0
                        END
                    ) as total_programados
                ")
            )
            ->where('aditorias.cuenta_publica', $cuentaPublicaId)
            ->where('aditorias.entrega', $entregaId);

        // Si el usuario no es administrador, se filtra por la UAA correspondiente
        if (auth()->user()->roles->pluck('name')->first() !== 'admin') {
            $query->where('aditorias.uaa', $uaa_user);
        }

        $auditorias = $query->groupBy(
            'cat_siglas_auditoria_especial.valor',
            'cat_cuenta_publica.valor',
            'cat_entrega.valor',
            'cat_uaa.valor'
        )->get();


        // Selectores
        $cuentasPublicas = DB::table('cat_cuenta_publica')->select('id', 'valor')->get();
        $entregas = DB::table('cat_entrega')->select('id', 'valor')->get();

        return view('dashboard.expedientes.entrega', [
            'auditorias' => $auditorias,
            'cuentasPublicas' => $cuentasPublicas,
            'entregas' => $entregas,
            'selectedCuentaPublica' => $cuentaPublicaId,
            'selectedEntrega' => $entregaId,
        ]);
    }

    public function detalle(Request $request)
    {
        // Obtener los filtros de UAA, cuenta pública y entrega
        $uaaName = $request->input('uaa');
        $cuentaPublicaId = $request->input('cuenta_publica', null);
        $entregaId = $request->input('entrega', null);
        $uaa_user = Auth::user()->uaa_id;

        // Si no se pasa UAA en el request, usar la UAA de menor ID por defecto
        if (is_null($uaaName)) {
            $uaa = DB::table('cat_uaa')->orderBy('id', 'asc')->first();
            $uaaName = $uaa->valor;
        } else {
            $uaa = DB::table('cat_uaa')->where('valor', $uaaName)->first();
        }
        if (!$uaa) {
            return redirect()->back()->with('error', 'No se encontró la UAA especificada.');
        }

        // Si no se pasan valores en el request, tomar valores por defecto
        if (is_null($cuentaPublicaId)) {
            $cuentaPublicaId = DB::table('cat_cuenta_publica')->max('id');
        }
        if (is_null($entregaId)) {
            $entregaId = DB::table('cat_entrega')->min('id');
        }

        // Consulta principal: se usa LEFT JOIN a entregas y se agrupa por aditorias.id para evitar duplicados
        $expedientes = DB::table('aditorias')
            ->leftJoin('entregas', 'entregas.auditoria_id', '=', 'aditorias.id')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_ente_de_la_accion', 'aditorias.ente_de_la_accion', '=', 'cat_ente_de_la_accion.id')
            ->join('cat_clave_accion', 'aditorias.clave_accion', '=', 'cat_clave_accion.id')
            ->join('cat_siglas_tipo_accion', 'aditorias.siglas_tipo_accion', '=', 'cat_siglas_tipo_accion.id')
            ->leftJoin('cat_auditoria_especial as n_auditoria','aditorias.auditoria_especial','=','n_auditoria.id')
            ->select(
                'aditorias.id',
                'cat_cuenta_publica.valor as CP',
                'cat_entrega.valor as entrega',
                'n_auditoria.valor as numero_auditoria',
                'cat_ente_de_la_accion.valor as ente_accion',
                'cat_clave_accion.valor as clave_accion',
                'cat_siglas_tipo_accion.valor as tipo_accion',
                // Para los campos de entregas usamos MAX (o cualquier función de agregación) para tomar un único valor:
                DB::raw("MAX(entregas.fecha_entrega) as entrega_programada"),
                DB::raw("MAX(entregas.fecha_real_entrega) as entrega_realizada"),
                DB::raw("MAX(entregas.estado) as estado_entrega"),
                DB::raw("MAX(entregas.numero_legajos) as numero_legajos"),
                DB::raw("MAX(entregas.responsable) as responsable_entrega")
            )
            ->where('aditorias.uaa', $uaa->id)
            ->where('aditorias.cuenta_publica', $cuentaPublicaId)
            ->where('aditorias.entrega', $entregaId)
            ->groupBy(
                'aditorias.id',
                'cat_cuenta_publica.valor',
                'cat_entrega.valor',
                'n_auditoria.valor',
                'cat_ente_de_la_accion.valor',
                'cat_clave_accion.valor',
                'cat_siglas_tipo_accion.valor'
            )
            ->get();

        // Obtener los valores para los selectores de filtros
        $cuentasPublicas = DB::table('cat_cuenta_publica')->select('id', 'valor')->get();
        $entregas = DB::table('cat_entrega')->select('id', 'valor')->get();
        // Usuarios registrados
        $users = DB::table('users')->select('name')->where('uaa_id',$uaa_user)->pluck('name')->toArray();

        return view('dashboard.expedientes.detalle', [
            'expedientes' => $expedientes,
            'uaaName' => $uaaName,
            'cuentasPublicas' => $cuentasPublicas,
            'entregas' => $entregas,
            'selectedCuentaPublica' => $cuentaPublicaId,
            'selectedEntrega' => $entregaId,
            'users' => $users,
        ]);
    }

    public function validarEntrega(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'legajos.*' => 'required|numeric|min:1',
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
            'expedientes' => 'required|array',
        ], [
            'legajos.*.required' => 'Debe ingresar el número de legajos para cada expediente seleccionado.',
            'fecha_entrega.required' => 'Debe seleccionar una fecha de entrega.',
            'responsable.required' => 'Debe ingresar el nombre del responsable.',
        ]);

        // Obtener los expedientes seleccionados
        // Se unen las tablas necesarias para obtener la información de cada expediente (clave de acción y tipo de acción, por ejemplo)
        $expedientes = DB::table('aditorias')
            ->join('cat_clave_accion', 'aditorias.clave_accion', '=', 'cat_clave_accion.id')
            ->join('cat_siglas_tipo_accion', 'aditorias.siglas_tipo_accion', '=', 'cat_siglas_tipo_accion.id')
            ->select(
                'aditorias.id',
                'cat_clave_accion.valor as clave_accion',
                'cat_siglas_tipo_accion.valor as tipo_accion'
            )
            ->whereIn('aditorias.id', $validatedData['expedientes'])
            ->get();

        // Retornar la vista "validar-entrega" y pasar los datos validados,
        // asegurando que el array de legajos tenga índices consecutivos
        return view('dashboard.expedientes.validar-entrega', [
            'expedientes' => $expedientes,
            'legajos' => array_values($validatedData['legajos']),
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'responsable' => $validatedData['responsable'],
        ]);
    }

    public function confirmEntrega(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'fecha_entrega' => 'required|date',
            'responsable'   => 'required|string',
            'expedientes'   => 'required|array',
            'legajos'       => 'required|array',
        ]);
    
        // Array para almacenar los datos que se mostrarán en la tabla del correo
        $tablaDatos = [];
        $entrega = null;
    
        foreach ($validatedData['expedientes'] as $index => $expedienteId) {
            // Obtener los datos necesarios de cada expediente
            $expediente = Auditorias::findOrFail($expedienteId);
            $expediente->auditor_nombre = $validatedData['responsable'];
            $expediente->update();
    
            // Para el correo, obtener los valores descriptivos a partir de las relaciones.
            // Se asume que en los modelos relacionados el campo descriptivo es "nombre".
            $claveAccionDisplay       = $expediente->catClaveAccion ? $expediente->catClaveAccion->valor : $expediente->clave_accion;
            $siglasTipoAccionDisplay  = $expediente->catSiglasTipoAccion ? $expediente->catSiglasTipoAccion->valor : $expediente->siglas_tipo_accion;
            $cuentaPublicaDisplay     = $expediente->catCuentaPublica ? $expediente->catCuentaPublica->valor : $expediente->cuenta_publica;
    
            // Almacenar la información para la tabla del correo (mostrando los valores descriptivos)
            $tablaDatos[] = [
                'clave_accion'       => $claveAccionDisplay,
                'numero_legajos'     => $validatedData['legajos'][$index],
                'siglas_tipo_accion' => $siglasTipoAccionDisplay,
                'cuenta_publica'     => $cuentaPublicaDisplay,
            ];
    
            // Crear el registro de entrega en la base de datos utilizando los IDs originales
            $entrega = Entrega::create([
                'auditoria_id'    => $expediente->id,
                'clave_accion'    => $expediente->clave_accion,    // Se guarda el ID original
                'tipo_accion'     => $expediente->siglas_tipo_accion, // Se guarda el ID original
                'CP'              => $expediente->cuenta_publica,     // Se guarda el ID original
                'entrega'         => $expediente->entrega,
                'fecha_entrega'   => $validatedData['fecha_entrega'],
                'responsable'     => $validatedData['responsable'],
                'numero_legajos'  => $validatedData['legajos'][$index],
                'confirmado_por'  => auth()->id(),
                'estado'          => "Programado",
            ]);
        }
    
        // Obtener el usuario responsable (contraparte) según el nombre
        $usuarioEntrega = User::where('name', 'like', '%' . $validatedData['responsable'] . '%')->first();
        $usuarioRecibe = $usuarioEntrega ? $usuarioEntrega->email : null;
    
        // Preparar la lista de destinatarios, agregando los correos fijos
        $recipientEmails = [];
        if ($usuarioRecibe) {
            $recipientEmails[] = $usuarioRecibe;
        }
        $recipientEmails = array_merge($recipientEmails, [
            Auth::user()->email,
            'jatenorio@asf.gob.mx',
            'jgonzalezb@asf.gob.mx',
            'lnunez@asf.gob.mx'
        ]);
    
        // Generar la tabla HTML con la información de cada expediente (usando los valores descriptivos)
        $table = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th>Clave de Acción</th>';
        $table .= '<th>Número de Legajos</th>';
        $table .= '<th>Siglas Tipo Acción</th>';
        $table .= '<th>Cuenta Pública</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        foreach ($tablaDatos as $fila) {
            $table .= '<tr>';
            $table .= '<td>' . $fila['clave_accion'] . '</td>';
            $table .= '<td>' . $fila['numero_legajos'] . '</td>';
            $table .= '<td>' . $fila['siglas_tipo_accion'] . '</td>';
            $table .= '<td>' . $fila['cuenta_publica'] . '</td>';
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';
    
        // Ajustar el asunto y el contenido del correo
        $subject = 'Se programo una entrega de expedientes';
    
        $content = '
            <div style="text-align:left">
                Responsable del Archivo de Trámite de la AESII<br>
                <small><b>P R E S E N T E</b></small>
            </div>
            <br>
            Se le informa que se realizó una programación de entrega de expedientes
            de acción con la siguiente información:<br>
            <br>
            <b>Datos de la Programación</b><br>
            <br>
            <strong>Usuario que Programa:</strong> ' . Auth::user()->name . '<br>
            <strong>Usuario que Entrega:</strong> ' . $validatedData['responsable'] . '<br>
            <strong>Fecha de Entrega:</strong> ' . $validatedData['fecha_entrega'] . '<br><br>
            <b>Detalle de Claves de Acción:</b><br>' . $table;
    
        $mailData = [
            'footer' => 'Correo automático.'
        ];
    
        // Envío del correo a los destinatarios indicados
        MailHelper::sendDynamicMail($recipientEmails, $subject, $content, $mailData);
    
        return redirect()->route('dashboard.expedientes.entrega')->with('success', 'Entrega confirmada correctamente.');
    }
    
    


}

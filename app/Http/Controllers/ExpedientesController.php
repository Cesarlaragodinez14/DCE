<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirector;
use App\Models\Entrega;
use App\Models\Auditorias;

class ExpedientesController extends Controller
{
    public function show(Request $request)
    {
        // Obtener los filtros de cuenta_publica y entrega
        $cuentaPublicaId = $request->input('cuenta_publica', null);
        $entregaId = $request->input('entrega', null);

        // Si no se pasan valores en el request, tomar los valores por defecto
        if (is_null($cuentaPublicaId)) {
            $cuentaPublicaId = DB::table('cat_cuenta_publica')->max('id');  // Tomar el valor máximo de cuenta pública
        }

        if (is_null($entregaId)) {
            $entregaId = DB::table('cat_entrega')->min('id');  // Tomar el valor mínimo de entrega
        }

        // Construir la consulta SQL usando los filtros seleccionados
        $auditorias = DB::table('aditorias')
            ->join('cat_siglas_auditoria_especial', 'aditorias.siglas_auditoria_especial', '=', 'cat_siglas_auditoria_especial.id')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_uaa', 'aditorias.uaa', '=', 'cat_uaa.id')
            ->select(
                'cat_siglas_auditoria_especial.valor as auditoria_especial',
                'cat_cuenta_publica.valor as CP',
                'cat_entrega.valor as entrega',
                'cat_uaa.valor as uaa',
                DB::raw('COUNT(aditorias.id) as total_entregar')
            )
            ->where('aditorias.cuenta_publica', '=', $cuentaPublicaId) // Aplicar filtro de cuenta pública
            ->where('aditorias.entrega', '=', $entregaId) // Aplicar filtro de entrega
            ->groupBy(
                'cat_siglas_auditoria_especial.valor',
                'cat_cuenta_publica.valor',
                'cat_entrega.valor',
                'cat_uaa.valor'
            )
            ->get();

        // Obtener los valores para los selectores de los filtros
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

        // Si no se pasan valores en el request, tomar los valores por defecto
        if (is_null($cuentaPublicaId)) {
            $cuentaPublicaId = DB::table('cat_cuenta_publica')->max('id');  // Valor máximo de cuenta pública
        }

        if (is_null($entregaId)) {
            $entregaId = DB::table('cat_entrega')->min('id');  // Valor mínimo de entrega
        }

        // Obtener los registros de auditorías basados en los filtros
        $expedientes = DB::table('aditorias')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_ente_de_la_accion', 'aditorias.ente_de_la_accion', '=', 'cat_ente_de_la_accion.id')
            ->join('cat_clave_accion', 'aditorias.clave_accion', '=', 'cat_clave_accion.id')
            ->join('cat_siglas_tipo_accion', 'aditorias.siglas_tipo_accion', '=', 'cat_siglas_tipo_accion.id')
            ->select(
                'aditorias.id',
                'cat_cuenta_publica.valor as CP',
                'cat_entrega.valor as entrega',
                'aditorias.auditoria_especial',
                'cat_ente_de_la_accion.valor as ente_accion',
                'cat_clave_accion.valor as clave_accion',
                'cat_siglas_tipo_accion.valor as tipo_accion'
            )
            ->where('aditorias.uaa', $uaa->id)
            ->where('aditorias.cuenta_publica', $cuentaPublicaId)
            ->where('aditorias.entrega', $entregaId)
            ->get();

        // Obtener los valores para los selectores de los filtros
        $cuentasPublicas = DB::table('cat_cuenta_publica')->select('id', 'valor')->get();
        $entregas = DB::table('cat_entrega')->select('id', 'valor')->get();

        return view('dashboard.expedientes.detalle', [
            'expedientes' => $expedientes,
            'uaaName' => $uaaName,
            'cuentasPublicas' => $cuentasPublicas,
            'entregas' => $entregas,
            'selectedCuentaPublica' => $cuentaPublicaId,
            'selectedEntrega' => $entregaId,
        ]);
    }

    public function validarEntrega(Request $request)
    {
        // Validate the request data as before
        $validatedData = $request->validate([
            'legajos.*' => 'required|numeric|min:1',
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
            'expedientes' => 'required|array',
        ], [
            'legajos.*.required' => 'Debe ingresar el número de legajos para cada expediente seleccionado.',
            'fecha_entrega.required' => 'Debe seleccionar una fecha de entrega.',
            'responsable.required' => 'Debe seleccionar un responsable.',
        ]);

        // Fetch additional details for the expedientes based on their IDs
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

        // Pass the data to the view, ensuring that both arrays have the same indices
        return view('dashboard.expedientes.validar-entrega', [
            'expedientes' => $expedientes,
            'legajos' => array_values($validatedData['legajos']), // Ensure indices are numeric and consecutive
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'responsable' => $validatedData['responsable'],
        ]);
    }

    public function confirmEntrega(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
            'expedientes' => 'required|array',
            'legajos' => 'required|array',
        ]);

        foreach ($validatedData['expedientes'] as $index => $expedienteId) {
            // Fetch the necessary data for each expediente
            $expediente = Auditorias::findOrFail($expedienteId);

            // Save the entrega information to the database
            Entrega::create([
                'auditoria_id' => $expediente->id,
                'clave_accion' => $expediente->clave_accion,
                'tipo_accion' => $expediente->siglas_tipo_accion,
                'CP' => $expediente->cuenta_publica,
                'entrega' => $expediente->entrega,
                'fecha_entrega' => $validatedData['fecha_entrega'],
                'responsable' => $validatedData['responsable'],
                'numero_legajos' => $validatedData['legajos'][$index],
                'confirmado_por' => auth()->id(),
            ]);
        }

        return redirect()->route('dashboard.expedientes.entrega')->with('success', 'Entrega confirmada correctamente.');
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirector;

class ExpedientesController extends Controller
{
    public function show()
    {
        // Escribir una consulta SQL usando el DB facade
        $auditorias = DB::table('aditorias')
            ->join('cat_siglas_auditoria_especial', 'aditorias.siglas_auditoria_especial', '=', 'cat_siglas_auditoria_especial.id')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_uaa', 'aditorias.uaa', '=', 'cat_uaa.id')
            ->select(
                'cat_siglas_auditoria_especial.valor as auditoria_especial',
                'cat_cuenta_publica.valor as CP',  // Cambiado para obtener el valor de cat_cuenta_publica
                'cat_entrega.valor as entrega',  // Cambiado para obtener el valor de cat_entrega
                'cat_uaa.valor as uaa',  // Cambiado para obtener el valor de cat_uaa
                DB::raw('COUNT(aditorias.id) as total_entregar')
            )
            ->groupBy(
                'cat_siglas_auditoria_especial.valor',
                'cat_cuenta_publica.valor',
                'cat_entrega.valor',
                'cat_uaa.valor'
            )
            ->get();

        return view('dashboard.expedientes.entrega', compact('auditorias'));
    }
    public function detalle(Request $request)
    {
        // Obtener el nombre de la UAA recibido por GET
        $uaaName = $request->input('uaa');

        // Buscar el ID correspondiente en el catálogo de UAA
        $uaa = DB::table('cat_uaa')->where('valor', $uaaName)->first();

        if (!$uaa) {
            return redirect()->back()->with('error', 'No se encontró la UAA especificada.');
        }

        // Obtener los registros de auditorías basados en el ID de UAA
        $expedientes = DB::table('aditorias')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_ente_de_la_accion', 'aditorias.ente_de_la_accion', '=', 'cat_ente_de_la_accion.id')
            ->join('cat_clave_accion', 'aditorias.clave_accion', '=', 'cat_clave_accion.id')
            ->join('cat_siglas_tipo_accion', 'aditorias.siglas_tipo_accion', '=', 'cat_siglas_tipo_accion.id')
            ->select(
                'aditorias.id',  // Incluye el ID de aditorias aquí
                'cat_cuenta_publica.valor as CP',
                'cat_entrega.valor as entrega',
                'aditorias.auditoria_especial',
                'cat_ente_de_la_accion.valor as ente_accion',
                'cat_clave_accion.valor as clave_accion',
                'cat_siglas_tipo_accion.valor as tipo_accion'
            )
            ->where('aditorias.uaa', $uaa->id)
            ->get();

        return view('dashboard.expedientes.detalle', compact('expedientes', 'uaaName'));
    }
    public function validarEntrega(Request $request)
    {
        // Validar que haya expedientes seleccionados
        if (!$request->has('expedientes')) {
            return redirect()->back()->withErrors(['expedientes' => 'Debe seleccionar al menos un expediente.'])->withInput();
        }
    
        // Validar que cada expediente tenga un número de legajos asignado
        $validator = Validator::make($request->all(), [
            'legajos.*' => 'required|numeric|min:1',
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
        ], [
            'legajos.*.required' => 'Debe ingresar el número de legajos para cada expediente seleccionado.',
            'fecha_entrega.required' => 'Debe seleccionar una fecha de entrega.',
            'responsable.required' => 'Debe seleccionar un responsable.',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Obtener los expedientes seleccionados y sus detalles
        $expedientesIds = $request->input('expedientes');
        $fecha_entrega = $request->input('fecha_entrega');
        $responsable = $request->input('responsable');
    
        // Redirigir a la vista de validación con los datos necesarios
        return view('dashboard.expedientes.validar-entrega', compact('expedientesIds', 'fecha_entrega', 'responsable'));
    }
    
     
}

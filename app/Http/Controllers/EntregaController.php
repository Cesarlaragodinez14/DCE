<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\RecepcionEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    public function index()
    {
        $entregas = Entrega::with('expediente', 'confirmadoPor')->get();
        return view('entregas.index', compact('entregas'));
    }

    public function create()
    {
        return view('entregas.create');
    }

    public function store(Request $request)
    {
        // Validar los datos de la entrega
        $request->validate([
            'expediente_id' => 'required|exists:aditorias,id',
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
            'numero_legajos' => 'required|integer|min:1',
            'confirmado_por' => 'required|exists:users,id',
        ]);

        try {
            // Iniciar transacción
            DB::beginTransaction();

            // Guardar la entrega
            $entrega = Entrega::create($request->only([
                'expediente_id', 'fecha_entrega', 'responsable', 'numero_legajos', 'confirmado_por'
            ]));

            // Guardar los datos en la tabla de recepciones
            RecepcionEntrega::create([
                'entrega_id' => $entrega->id, // Relación con la entrega
                'fecha_recepcion' => now(),   // Se puede ajustar la fecha como "ahora" o agregar un input para modificarla
            ]);

            // Confirmar la transacción
            DB::commit();

            return redirect()->route('entregas.index')->with('success', 'Entrega registrada correctamente.');

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            Log::error('Error al registrar la entrega: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Hubo un problema al registrar la entrega. Inténtalo de nuevo.');
        }
    }

    public function show(Entrega $entrega)
    {
        return view('entregas.show', compact('entrega'));
    }

    public function edit(Entrega $entrega)
    {
        return view('entregas.edit', compact('entrega'));
    }

    public function update(Request $request, Entrega $entrega)
    {
        $request->validate([
            'expediente_id' => 'required|exists:aditorias,id',
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
            'numero_legajos' => 'required|integer|min:1',
            'confirmado_por' => 'required|exists:users,id',
        ]);

        $entrega->update($request->all());

        return redirect()->route('entregas.index')->with('success', 'Entrega actualizada correctamente.');
    }

    public function destroy(Entrega $entrega)
    {
        $entrega->delete();

        return redirect()->route('entregas.index')->with('success', 'Entrega eliminada correctamente.');
    }

    public function mostrarRecepcion(Request $request) {
        // Filtros
        $ae = $request->input('ae');
        $dg = $request->input('dg');
    
        // Obtener las auditorías especiales y direcciones generales para los selectores
        $auditoriasEspeciales = DB::table('cat_siglas_auditoria_especial')->get();
        $direccionesGenerales = DB::table('cat_dgseg_ef')->get();
    
        // Consulta principal, con conteo y agrupamiento correcto
        $query = DB::table('entregas')
            ->join('aditorias', 'entregas.auditoria_id', '=', 'aditorias.id')
            ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
            ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->join('cat_siglas_auditoria_especial', 'aditorias.siglas_auditoria_especial', '=', 'cat_siglas_auditoria_especial.id')
            ->join('cat_dgseg_ef', 'aditorias.dgseg_ef', '=', 'cat_dgseg_ef.id')
            ->select(
                'cat_cuenta_publica.valor as CP',
                'cat_entrega.valor as entrega',
                'cat_siglas_auditoria_especial.valor as AE',
                'cat_dgseg_ef.valor as DG',
                'entregas.fecha_entrega',
                'entregas.responsable',
                DB::raw('COUNT(entregas.id) as total_entregas')  // Correct count
            )
            ->groupBy(
                'cat_cuenta_publica.valor',
                'cat_entrega.valor',
                'cat_siglas_auditoria_especial.valor',
                'cat_dgseg_ef.valor',
                'entregas.fecha_entrega',
                'entregas.responsable'
            );
    
        // Aplicar filtros si existen
        if ($ae) {
            $query->where('aditorias.siglas_auditoria_especial', $ae);
        }
        if ($dg) {
            $query->where('aditorias.dgseg_ef', $dg);
        }
    
        // Obtener resultados agrupados
        $entregasContadas = $query->get();
    
        // Para cada grupo, obtener los entregas que pertenecen al grupo
        foreach ($entregasContadas as $entregaGroup) {
            $entregaGroup->entregas = DB::table('entregas')
                ->join('aditorias', 'entregas.auditoria_id', '=', 'aditorias.id')
                ->join('cat_cuenta_publica', 'aditorias.cuenta_publica', '=', 'cat_cuenta_publica.id')
                ->join('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
                ->join('cat_siglas_auditoria_especial', 'aditorias.siglas_auditoria_especial', '=', 'cat_siglas_auditoria_especial.id')
                ->join('cat_dgseg_ef', 'aditorias.dgseg_ef', '=', 'cat_dgseg_ef.id')
                ->where('cat_cuenta_publica.valor', $entregaGroup->CP)
                ->where('cat_entrega.valor', $entregaGroup->entrega)
                ->where('cat_siglas_auditoria_especial.valor', $entregaGroup->AE)
                ->where('cat_dgseg_ef.valor', $entregaGroup->DG)
                ->where('entregas.fecha_entrega', $entregaGroup->fecha_entrega)
                ->where('entregas.responsable', $entregaGroup->responsable)
                ->select(
                    'entregas.id as entrega_id',
                    'entregas.fecha_entrega',
                    'entregas.responsable',
                    'aditorias.clave_de_accion',
                    'aditorias.titulo',
                    'aditorias.auditoria_especial',
                    'entregas.tipo_accion',
                    'entregas.entrega',
                    'entregas.auditoria_id',
                    'entregas.CP',
                    'entregas.fecha_entrega',
                    'entregas.responsable',
                    'entregas.numero_legajos',
                    'entregas.confirmado_por'
                )
                ->get();
        }
    
        // Días hábiles restantes
        $dias_habiles = 18; // Este valor puede calcularse dinámicamente si es necesario
    
        return view('dashboard.recepcion', compact(
            'entregasContadas', 
            'auditoriasEspeciales', 
            'direccionesGenerales', 
            'dias_habiles'
        ));
    }
    

    
}

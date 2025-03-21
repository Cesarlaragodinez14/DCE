<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\RecepcionEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;    // si quieres loguear errores
use Illuminate\Support\Facades\Auth;   // para obtener el user actual

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
    
    public function confirmarEntrega(Request $request)
    {
        $request->validate([
            'entrega_id' => 'required|exists:entregas,id',
        ]);

        try {
            DB::beginTransaction();

            $entrega = Entrega::findOrFail($request->entrega_id);
            $entrega->estado = 'Entregado';
            $entrega->fecha_real_entrega = now();
            $entrega->recibido_por = Auth::id();
            $entrega->save();

            DB::commit();

            return redirect()->route('dashboard.expedientes.recepcion')
                ->with('success', 'La entrega se confirmó correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirmando la entrega: '.$e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al confirmar la entrega.');
        }
    }

    
}

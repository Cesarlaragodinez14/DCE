<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use Illuminate\Http\Request;

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
        $request->validate([
            'expediente_id' => 'required|exists:aditorias,id',
            'fecha_entrega' => 'required|date',
            'responsable' => 'required|string',
            'numero_legajos' => 'required|integer|min:1',
            'confirmado_por' => 'required|exists:users,id',
        ]);

        Entrega::create($request->all());

        return redirect()->route('entregas.index')->with('success', 'Entrega registrada correctamente.');
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

}

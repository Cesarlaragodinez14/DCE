<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatEnteFiscalizado;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditoriasResource;
use App\Http\Resources\AuditoriasCollection;

class CatEnteFiscalizadosAuditoriasController extends Controller
{
    public function index(
        Request $request,
        CatEnteFiscalizado $catEnteFiscalizado
    ): AuditoriasCollection {
        $search = $request->get('search', '');

        $allAuditorias = $this->getSearchQuery($search, $catEnteFiscalizado)
            ->latest()
            ->paginate();

        return new AuditoriasCollection($allAuditorias);
    }

    public function store(
        Request $request,
        CatEnteFiscalizado $catEnteFiscalizado
    ): AuditoriasResource {
        $validated = $request->validate([
            'clave_de_accion' => [
                'required',
                'string',
                Rule::unique('aditorias', 'clave_de_accion'),
            ],
            'entrega' => ['required'],
            'auditoria_especial' => ['required'],
            'tipo_de_auditoria' => ['required'],
            'siglas_auditoria_especial' => ['required'],
            'siglas_dg_uaa' => ['required'],
            'titulo' => ['required', 'string'],
            'numero_de_auditoria' => ['required'],
            'ente_de_la_accion' => ['required'],
            'clave_accion' => ['required'],
            'siglas_tipo_accion' => ['required'],
            'nombre_director_general' => ['required', 'string'],
            'direccion_de_area' => ['required', 'string'],
            'nombre_director_de_area' => ['required', 'string'],
            'sub_direccion_de_area' => ['required', 'string'],
            'nombre_sub_director_de_area' => ['required', 'string'],
            'jefe_de_departamento' => ['required', 'string'],
        ]);

        $auditorias = $catEnteFiscalizado->allAuditorias()->create($validated);

        return new AuditoriasResource($auditorias);
    }

    public function getSearchQuery(
        string $search,
        CatEnteFiscalizado $catEnteFiscalizado
    ) {
        return $catEnteFiscalizado
            ->allAuditorias()
            ->where('clave_de_accion', 'like', "%{$search}%");
    }
}

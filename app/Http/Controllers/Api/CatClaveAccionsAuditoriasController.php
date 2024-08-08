<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CatClaveAccion;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditoriasResource;
use App\Http\Resources\AuditoriasCollection;

class CatClaveAccionsAuditoriasController extends Controller
{
    public function index(
        Request $request,
        CatClaveAccion $catClaveAccion
    ): AuditoriasCollection {
        $search = $request->get('search', '');

        $allAuditorias = $this->getSearchQuery($search, $catClaveAccion)
            ->latest()
            ->paginate();

        return new AuditoriasCollection($allAuditorias);
    }

    public function store(
        Request $request,
        CatClaveAccion $catClaveAccion
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
            'uaa' => ['required'],
            'titulo' => ['required', 'string'],
            'ente_fiscalizado' => ['required'],
            'numero_de_auditoria' => ['required'],
            'ente_de_la_accion' => ['required'],
            'siglas_tipo_accion' => ['required'],
            'nombre_director_general' => ['required', 'string'],
            'direccion_de_area' => ['required', 'string'],
            'nombre_director_de_area' => ['required', 'string'],
            'sub_direccion_de_area' => ['required', 'string'],
            'nombre_sub_director_de_area' => ['required', 'string'],
            'jefe_de_departamento' => ['required', 'string'],
        ]);

        $auditorias = $catClaveAccion->auditorias()->create($validated);

        return new AuditoriasResource($auditorias);
    }

    public function getSearchQuery(
        string $search,
        CatClaveAccion $catClaveAccion
    ) {
        return $catClaveAccion
            ->auditorias()
            ->where('clave_de_accion', 'like', "%{$search}%");
    }
}

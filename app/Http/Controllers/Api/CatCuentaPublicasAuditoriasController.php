<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatCuentaPublica;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditoriasResource;
use App\Http\Resources\AuditoriasCollection;

class CatCuentaPublicasAuditoriasController extends Controller
{
    public function index(
        Request $request,
        CatCuentaPublica $catCuentaPublica
    ): AuditoriasCollection {
        $search = $request->get('search', '');

        $allAuditorias = $this->getSearchQuery($search, $catCuentaPublica)
            ->latest()
            ->paginate();

        return new AuditoriasCollection($allAuditorias);
    }

    public function store(
        Request $request,
        CatCuentaPublica $catCuentaPublica
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
            'clave_accion' => ['required'],
            'siglas_tipo_accion' => ['required'],
            'nombre_director_general' => ['required', 'string'],
            'direccion_de_area' => ['required', 'string'],
            'nombre_director_de_area' => ['required', 'string'],
            'sub_direccion_de_area' => ['required', 'string'],
            'nombre_sub_director_de_area' => ['required', 'string'],
            'jefe_de_departamento' => ['required', 'string'],
        ]);

        $auditorias = $catCuentaPublica->allAuditorias()->create($validated);

        return new AuditoriasResource($auditorias);
    }

    public function getSearchQuery(
        string $search,
        CatCuentaPublica $catCuentaPublica
    ) {
        return $catCuentaPublica
            ->allAuditorias()
            ->where('clave_de_accion', 'like', "%{$search}%");
    }
}

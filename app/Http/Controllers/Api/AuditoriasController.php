<?php

namespace App\Http\Controllers\Api;

use App\Models\Auditorias;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditoriasResource;
use App\Http\Resources\AuditoriasCollection;
use App\Http\Requests\AuditoriasStoreRequest;
use App\Http\Requests\AuditoriasUpdateRequest;

class AuditoriasController extends Controller
{
    public function index(Request $request): AuditoriasCollection
    {
        $search = $request->get('search', '');

        $allAuditorias = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new AuditoriasCollection($allAuditorias);
    }

    public function store(AuditoriasStoreRequest $request): AuditoriasResource
    {
        $validated = $request->validated();

        $auditorias = Auditorias::create($validated);

        return new AuditoriasResource($auditorias);
    }

    public function show(
        Request $request,
        Auditorias $auditorias
    ): AuditoriasResource {
        return new AuditoriasResource($auditorias);
    }

    public function update(
        AuditoriasUpdateRequest $request,
        Auditorias $auditorias
    ): AuditoriasResource {
        $validated = $request->validated();

        $auditorias->update($validated);

        return new AuditoriasResource($auditorias);
    }

    public function destroy(Request $request, Auditorias $auditorias): Response
    {
        $auditorias->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return Auditorias::query()->where(
            'clave_de_accion',
            'like',
            "%{$search}%"
        );
    }
}

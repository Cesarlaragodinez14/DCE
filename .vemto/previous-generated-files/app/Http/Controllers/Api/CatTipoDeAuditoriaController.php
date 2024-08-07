<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatTipoDeAuditoria;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatTipoDeAuditoriaResource;
use App\Http\Resources\CatTipoDeAuditoriaCollection;
use App\Http\Requests\CatTipoDeAuditoriaStoreRequest;
use App\Http\Requests\CatTipoDeAuditoriaUpdateRequest;

class CatTipoDeAuditoriaController extends Controller
{
    public function index(Request $request): CatTipoDeAuditoriaCollection
    {
        $search = $request->get('search', '');

        $catTipoDeAuditorias = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatTipoDeAuditoriaCollection($catTipoDeAuditorias);
    }

    public function store(
        CatTipoDeAuditoriaStoreRequest $request
    ): CatTipoDeAuditoriaResource {
        $validated = $request->validated();

        $catTipoDeAuditoria = CatTipoDeAuditoria::create($validated);

        return new CatTipoDeAuditoriaResource($catTipoDeAuditoria);
    }

    public function show(
        Request $request,
        CatTipoDeAuditoria $catTipoDeAuditoria
    ): CatTipoDeAuditoriaResource {
        return new CatTipoDeAuditoriaResource($catTipoDeAuditoria);
    }

    public function update(
        CatTipoDeAuditoriaUpdateRequest $request,
        CatTipoDeAuditoria $catTipoDeAuditoria
    ): CatTipoDeAuditoriaResource {
        $validated = $request->validated();

        $catTipoDeAuditoria->update($validated);

        return new CatTipoDeAuditoriaResource($catTipoDeAuditoria);
    }

    public function destroy(
        Request $request,
        CatTipoDeAuditoria $catTipoDeAuditoria
    ): Response {
        $catTipoDeAuditoria->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatTipoDeAuditoria::query()->where(
            'valor',
            'like',
            "%{$search}%"
        );
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatAuditoriaEspecial;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatAuditoriaEspecialResource;
use App\Http\Resources\CatAuditoriaEspecialCollection;
use App\Http\Requests\CatAuditoriaEspecialStoreRequest;
use App\Http\Requests\CatAuditoriaEspecialUpdateRequest;

class CatAuditoriaEspecialController extends Controller
{
    public function index(Request $request): CatAuditoriaEspecialCollection
    {
        $search = $request->get('search', '');

        $catAuditoriaEspecials = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatAuditoriaEspecialCollection($catAuditoriaEspecials);
    }

    public function store(
        CatAuditoriaEspecialStoreRequest $request
    ): CatAuditoriaEspecialResource {
        $validated = $request->validated();

        $catAuditoriaEspecial = CatAuditoriaEspecial::create($validated);

        return new CatAuditoriaEspecialResource($catAuditoriaEspecial);
    }

    public function show(
        Request $request,
        CatAuditoriaEspecial $catAuditoriaEspecial
    ): CatAuditoriaEspecialResource {
        return new CatAuditoriaEspecialResource($catAuditoriaEspecial);
    }

    public function update(
        CatAuditoriaEspecialUpdateRequest $request,
        CatAuditoriaEspecial $catAuditoriaEspecial
    ): CatAuditoriaEspecialResource {
        $validated = $request->validated();

        $catAuditoriaEspecial->update($validated);

        return new CatAuditoriaEspecialResource($catAuditoriaEspecial);
    }

    public function destroy(
        Request $request,
        CatAuditoriaEspecial $catAuditoriaEspecial
    ): Response {
        $catAuditoriaEspecial->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatAuditoriaEspecial::query()->where(
            'valor',
            'like',
            "%{$search}%"
        );
    }
}

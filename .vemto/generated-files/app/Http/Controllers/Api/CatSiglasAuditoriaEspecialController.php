<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Http\Resources\CatSiglasAuditoriaEspecialResource;
use App\Http\Resources\CatSiglasAuditoriaEspecialCollection;
use App\Http\Requests\CatSiglasAuditoriaEspecialStoreRequest;
use App\Http\Requests\CatSiglasAuditoriaEspecialUpdateRequest;

class CatSiglasAuditoriaEspecialController extends Controller
{
    public function index(
        Request $request
    ): CatSiglasAuditoriaEspecialCollection {
        $search = $request->get('search', '');

        $catSiglasAuditoriaEspecials = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatSiglasAuditoriaEspecialCollection(
            $catSiglasAuditoriaEspecials
        );
    }

    public function store(
        CatSiglasAuditoriaEspecialStoreRequest $request
    ): CatSiglasAuditoriaEspecialResource {
        $validated = $request->validated();

        $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::create(
            $validated
        );

        return new CatSiglasAuditoriaEspecialResource(
            $catSiglasAuditoriaEspecial
        );
    }

    public function show(
        Request $request,
        CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial
    ): CatSiglasAuditoriaEspecialResource {
        return new CatSiglasAuditoriaEspecialResource(
            $catSiglasAuditoriaEspecial
        );
    }

    public function update(
        CatSiglasAuditoriaEspecialUpdateRequest $request,
        CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial
    ): CatSiglasAuditoriaEspecialResource {
        $validated = $request->validated();

        $catSiglasAuditoriaEspecial->update($validated);

        return new CatSiglasAuditoriaEspecialResource(
            $catSiglasAuditoriaEspecial
        );
    }

    public function destroy(
        Request $request,
        CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial
    ): Response {
        $catSiglasAuditoriaEspecial->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatSiglasAuditoriaEspecial::query()->where(
            'valor',
            'like',
            "%{$search}%"
        );
    }
}

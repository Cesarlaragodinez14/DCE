<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatSiglasTipoAccion;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatSiglasTipoAccionResource;
use App\Http\Resources\CatSiglasTipoAccionCollection;
use App\Http\Requests\CatSiglasTipoAccionStoreRequest;
use App\Http\Requests\CatSiglasTipoAccionUpdateRequest;

class CatSiglasTipoAccionController extends Controller
{
    public function index(Request $request): CatSiglasTipoAccionCollection
    {
        $search = $request->get('search', '');

        $catSiglasTipoAcciones = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatSiglasTipoAccionCollection($catSiglasTipoAcciones);
    }

    public function store(
        CatSiglasTipoAccionStoreRequest $request
    ): CatSiglasTipoAccionResource {
        $validated = $request->validated();

        $catSiglasTipoAccion = CatSiglasTipoAccion::create($validated);

        return new CatSiglasTipoAccionResource($catSiglasTipoAccion);
    }

    public function show(
        Request $request,
        CatSiglasTipoAccion $catSiglasTipoAccion
    ): CatSiglasTipoAccionResource {
        return new CatSiglasTipoAccionResource($catSiglasTipoAccion);
    }

    public function update(
        CatSiglasTipoAccionUpdateRequest $request,
        CatSiglasTipoAccion $catSiglasTipoAccion
    ): CatSiglasTipoAccionResource {
        $validated = $request->validated();

        $catSiglasTipoAccion->update($validated);

        return new CatSiglasTipoAccionResource($catSiglasTipoAccion);
    }

    public function destroy(
        Request $request,
        CatSiglasTipoAccion $catSiglasTipoAccion
    ): Response {
        $catSiglasTipoAccion->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatSiglasTipoAccion::query()->where(
            'valor',
            'like',
            "%{$search}%"
        );
    }
}

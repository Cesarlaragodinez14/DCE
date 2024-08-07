<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatEnteDeLaAccion;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatEnteDeLaAccionResource;
use App\Http\Resources\CatEnteDeLaAccionCollection;
use App\Http\Requests\CatEnteDeLaAccionStoreRequest;
use App\Http\Requests\CatEnteDeLaAccionUpdateRequest;

class CatEnteDeLaAccionController extends Controller
{
    public function index(Request $request): CatEnteDeLaAccionCollection
    {
        $search = $request->get('search', '');

        $catEnteDeLaAccions = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatEnteDeLaAccionCollection($catEnteDeLaAccions);
    }

    public function store(
        CatEnteDeLaAccionStoreRequest $request
    ): CatEnteDeLaAccionResource {
        $validated = $request->validated();

        $catEnteDeLaAccion = CatEnteDeLaAccion::create($validated);

        return new CatEnteDeLaAccionResource($catEnteDeLaAccion);
    }

    public function show(
        Request $request,
        CatEnteDeLaAccion $catEnteDeLaAccion
    ): CatEnteDeLaAccionResource {
        return new CatEnteDeLaAccionResource($catEnteDeLaAccion);
    }

    public function update(
        CatEnteDeLaAccionUpdateRequest $request,
        CatEnteDeLaAccion $catEnteDeLaAccion
    ): CatEnteDeLaAccionResource {
        $validated = $request->validated();

        $catEnteDeLaAccion->update($validated);

        return new CatEnteDeLaAccionResource($catEnteDeLaAccion);
    }

    public function destroy(
        Request $request,
        CatEnteDeLaAccion $catEnteDeLaAccion
    ): Response {
        $catEnteDeLaAccion->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatEnteDeLaAccion::query()->where(
            'valor',
            'like',
            "%{$search}%"
        );
    }
}

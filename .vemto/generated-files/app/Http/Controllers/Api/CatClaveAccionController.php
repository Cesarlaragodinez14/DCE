<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CatClaveAccion;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatClaveAccionResource;
use App\Http\Resources\CatClaveAccionCollection;
use App\Http\Requests\CatClaveAccionStoreRequest;
use App\Http\Requests\CatClaveAccionUpdateRequest;

class CatClaveAccionController extends Controller
{
    public function index(Request $request): CatClaveAccionCollection
    {
        $search = $request->get('search', '');

        $catClaveAccions = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatClaveAccionCollection($catClaveAccions);
    }

    public function store(
        CatClaveAccionStoreRequest $request
    ): CatClaveAccionResource {
        $validated = $request->validated();

        $catClaveAccion = CatClaveAccion::create($validated);

        return new CatClaveAccionResource($catClaveAccion);
    }

    public function show(
        Request $request,
        CatClaveAccion $catClaveAccion
    ): CatClaveAccionResource {
        return new CatClaveAccionResource($catClaveAccion);
    }

    public function update(
        CatClaveAccionUpdateRequest $request,
        CatClaveAccion $catClaveAccion
    ): CatClaveAccionResource {
        $validated = $request->validated();

        $catClaveAccion->update($validated);

        return new CatClaveAccionResource($catClaveAccion);
    }

    public function destroy(
        Request $request,
        CatClaveAccion $catClaveAccion
    ): Response {
        $catClaveAccion->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatClaveAccion::query()->where('valor', 'like', "%{$search}%");
    }
}

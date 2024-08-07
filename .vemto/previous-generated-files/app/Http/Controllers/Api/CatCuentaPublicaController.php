<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatCuentaPublica;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatCuentaPublicaResource;
use App\Http\Resources\CatCuentaPublicaCollection;
use App\Http\Requests\CatCuentaPublicaStoreRequest;
use App\Http\Requests\CatCuentaPublicaUpdateRequest;

class CatCuentaPublicaController extends Controller
{
    public function index(Request $request): CatCuentaPublicaCollection
    {
        $search = $request->get('search', '');

        $catCuentaPublicas = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatCuentaPublicaCollection($catCuentaPublicas);
    }

    public function store(
        CatCuentaPublicaStoreRequest $request
    ): CatCuentaPublicaResource {
        $validated = $request->validated();

        $catCuentaPublica = CatCuentaPublica::create($validated);

        return new CatCuentaPublicaResource($catCuentaPublica);
    }

    public function show(
        Request $request,
        CatCuentaPublica $catCuentaPublica
    ): CatCuentaPublicaResource {
        return new CatCuentaPublicaResource($catCuentaPublica);
    }

    public function update(
        CatCuentaPublicaUpdateRequest $request,
        CatCuentaPublica $catCuentaPublica
    ): CatCuentaPublicaResource {
        $validated = $request->validated();

        $catCuentaPublica->update($validated);

        return new CatCuentaPublicaResource($catCuentaPublica);
    }

    public function destroy(
        Request $request,
        CatCuentaPublica $catCuentaPublica
    ): Response {
        $catCuentaPublica->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatCuentaPublica::query()->where('valor', 'like', "%{$search}%");
    }
}

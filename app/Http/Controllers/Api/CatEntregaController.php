<?php

namespace App\Http\Controllers\Api;

use App\Models\CatEntrega;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatEntregaResource;
use App\Http\Resources\CatEntregaCollection;
use App\Http\Requests\CatEntregaStoreRequest;
use App\Http\Requests\CatEntregaUpdateRequest;

class CatEntregaController extends Controller
{
    public function index(Request $request): CatEntregaCollection
    {
        $search = $request->get('search', '');

        $catEntregas = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatEntregaCollection($catEntregas);
    }

    public function store(CatEntregaStoreRequest $request): CatEntregaResource
    {
        $validated = $request->validated();

        $catEntrega = CatEntrega::create($validated);

        return new CatEntregaResource($catEntrega);
    }

    public function show(
        Request $request,
        CatEntrega $catEntrega
    ): CatEntregaResource {
        return new CatEntregaResource($catEntrega);
    }

    public function update(
        CatEntregaUpdateRequest $request,
        CatEntrega $catEntrega
    ): CatEntregaResource {
        $validated = $request->validated();

        $catEntrega->update($validated);

        return new CatEntregaResource($catEntrega);
    }

    public function destroy(Request $request, CatEntrega $catEntrega): Response
    {
        $catEntrega->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatEntrega::query()->where('valor', 'like', "%{$search}%");
    }
}

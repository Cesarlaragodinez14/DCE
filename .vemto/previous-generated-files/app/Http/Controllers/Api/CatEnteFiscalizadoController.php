<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\CatEnteFiscalizado;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatEnteFiscalizadoResource;
use App\Http\Resources\CatEnteFiscalizadoCollection;
use App\Http\Requests\CatEnteFiscalizadoStoreRequest;
use App\Http\Requests\CatEnteFiscalizadoUpdateRequest;

class CatEnteFiscalizadoController extends Controller
{
    public function index(Request $request): CatEnteFiscalizadoCollection
    {
        $search = $request->get('search', '');

        $catEnteFiscalizados = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatEnteFiscalizadoCollection($catEnteFiscalizados);
    }

    public function store(
        CatEnteFiscalizadoStoreRequest $request
    ): CatEnteFiscalizadoResource {
        $validated = $request->validated();

        $catEnteFiscalizado = CatEnteFiscalizado::create($validated);

        return new CatEnteFiscalizadoResource($catEnteFiscalizado);
    }

    public function show(
        Request $request,
        CatEnteFiscalizado $catEnteFiscalizado
    ): CatEnteFiscalizadoResource {
        return new CatEnteFiscalizadoResource($catEnteFiscalizado);
    }

    public function update(
        CatEnteFiscalizadoUpdateRequest $request,
        CatEnteFiscalizado $catEnteFiscalizado
    ): CatEnteFiscalizadoResource {
        $validated = $request->validated();

        $catEnteFiscalizado->update($validated);

        return new CatEnteFiscalizadoResource($catEnteFiscalizado);
    }

    public function destroy(
        Request $request,
        CatEnteFiscalizado $catEnteFiscalizado
    ): Response {
        $catEnteFiscalizado->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatEnteFiscalizado::query()->where(
            'valor',
            'like',
            "%{$search}%"
        );
    }
}

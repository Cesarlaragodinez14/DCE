<?php

namespace App\Http\Controllers\Api;

use App\Models\CatUaa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatUaaResource;
use App\Http\Resources\CatUaaCollection;
use App\Http\Requests\CatUaaStoreRequest;
use App\Http\Requests\CatUaaUpdateRequest;

class CatUaaController extends Controller
{
    public function index(Request $request): CatUaaCollection
    {
        $search = $request->get('search', '');

        $catUaas = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatUaaCollection($catUaas);
    }

    public function store(CatUaaStoreRequest $request): CatUaaResource
    {
        $validated = $request->validated();

        $catUaa = CatUaa::create($validated);

        return new CatUaaResource($catUaa);
    }

    public function show(Request $request, CatUaa $catUaa): CatUaaResource
    {
        return new CatUaaResource($catUaa);
    }

    public function update(
        CatUaaUpdateRequest $request,
        CatUaa $catUaa
    ): CatUaaResource {
        $validated = $request->validated();

        $catUaa->update($validated);

        return new CatUaaResource($catUaa);
    }

    public function destroy(Request $request, CatUaa $catUaa): Response
    {
        $catUaa->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatUaa::query()->where('valor', 'like', "%{$search}%");
    }
}

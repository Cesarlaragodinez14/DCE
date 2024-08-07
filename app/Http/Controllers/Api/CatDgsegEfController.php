<?php

namespace App\Http\Controllers\Api;

use App\Models\CatDgsegEf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatDgsegEfResource;
use App\Http\Resources\CatDgsegEfCollection;
use App\Http\Requests\CatDgsegEfStoreRequest;
use App\Http\Requests\CatDgsegEfUpdateRequest;

class CatDgsegEfController extends Controller
{
    public function index(Request $request): CatDgsegEfCollection
    {
        $search = $request->get('search', '');

        $catDgsegEfs = $this->getSearchQuery($search)
            ->latest()
            ->paginate();

        return new CatDgsegEfCollection($catDgsegEfs);
    }

    public function store(CatDgsegEfStoreRequest $request): CatDgsegEfResource
    {
        $validated = $request->validated();

        $catDgsegEf = CatDgsegEf::create($validated);

        return new CatDgsegEfResource($catDgsegEf);
    }

    public function show(
        Request $request,
        CatDgsegEf $catDgsegEf
    ): CatDgsegEfResource {
        return new CatDgsegEfResource($catDgsegEf);
    }

    public function update(
        CatDgsegEfUpdateRequest $request,
        CatDgsegEf $catDgsegEf
    ): CatDgsegEfResource {
        $validated = $request->validated();

        $catDgsegEf->update($validated);

        return new CatDgsegEfResource($catDgsegEf);
    }

    public function destroy(Request $request, CatDgsegEf $catDgsegEf): Response
    {
        $catDgsegEf->delete();

        return response()->noContent();
    }

    public function getSearchQuery(string $search)
    {
        return CatDgsegEf::query()->where('valor', 'like', "%{$search}%");
    }
}

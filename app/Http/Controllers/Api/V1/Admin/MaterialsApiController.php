<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Resources\Admin\MaterialResource;
use App\Models\Material;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaterialsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('material_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MaterialResource(Material::with(['unit', 'warehouse'])->get());
    }

    public function store(StoreMaterialRequest $request)
    {
        $material = Material::create($request->all());

        return (new MaterialResource($material))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Material $material)
    {
        abort_if(Gate::denies('material_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MaterialResource($material->load(['unit', 'warehouse']));
    }

    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $material->update($request->all());

        return (new MaterialResource($material))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Material $material)
    {
        abort_if(Gate::denies('material_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $material->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookVariantRequest;
use App\Http\Requests\UpdateBookVariantRequest;
use App\Http\Resources\Admin\BookVariantResource;
use App\Models\BookVariant;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookVariantApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('book_variant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BookVariantResource(BookVariant::with(['book', 'parent', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit'])->get());
    }

    public function store(StoreBookVariantRequest $request)
    {
        $bookVariant = BookVariant::create($request->all());

        return (new BookVariantResource($bookVariant))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BookVariantResource($bookVariant->load(['book', 'parent', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit']));
    }

    public function update(UpdateBookVariantRequest $request, BookVariant $bookVariant)
    {
        $bookVariant->update($request->all());

        return (new BookVariantResource($bookVariant))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookVariant->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

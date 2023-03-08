<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingAreaRequest;
use App\Http\Requests\UpdateMarketingAreaRequest;
use App\Http\Resources\Admin\MarketingAreaResource;
use App\Models\MarketingArea;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarketingAreaApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('marketing_area_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MarketingAreaResource(MarketingArea::all());
    }

    public function store(StoreMarketingAreaRequest $request)
    {
        $marketingArea = MarketingArea::create($request->all());

        return (new MarketingAreaResource($marketingArea))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MarketingArea $marketingArea)
    {
        abort_if(Gate::denies('marketing_area_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MarketingAreaResource($marketingArea);
    }

    public function update(UpdateMarketingAreaRequest $request, MarketingArea $marketingArea)
    {
        $marketingArea->update($request->all());

        return (new MarketingAreaResource($marketingArea))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MarketingArea $marketingArea)
    {
        abort_if(Gate::denies('marketing_area_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketingArea->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

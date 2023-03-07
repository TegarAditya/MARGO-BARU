<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Http\Resources\Admin\StockMovementResource;
use App\Models\StockMovement;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockMovementApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('stock_movement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockMovementResource(StockMovement::with(['warehouse', 'product', 'material', 'reversal_of'])->get());
    }

    public function store(StoreStockMovementRequest $request)
    {
        $stockMovement = StockMovement::create($request->all());

        return (new StockMovementResource($stockMovement))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(StockMovement $stockMovement)
    {
        abort_if(Gate::denies('stock_movement_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockMovementResource($stockMovement->load(['warehouse', 'product', 'material', 'reversal_of']));
    }

    public function update(UpdateStockMovementRequest $request, StockMovement $stockMovement)
    {
        $stockMovement->update($request->all());

        return (new StockMovementResource($stockMovement))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(StockMovement $stockMovement)
    {
        abort_if(Gate::denies('stock_movement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockMovement->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

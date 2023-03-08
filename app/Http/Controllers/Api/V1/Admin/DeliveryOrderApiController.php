<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryOrderRequest;
use App\Http\Requests\UpdateDeliveryOrderRequest;
use App\Http\Resources\Admin\DeliveryOrderResource;
use App\Models\DeliveryOrder;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryOrderApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('delivery_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DeliveryOrderResource(DeliveryOrder::with(['semester', 'salesperson'])->get());
    }

    public function store(StoreDeliveryOrderRequest $request)
    {
        $deliveryOrder = DeliveryOrder::create($request->all());

        return (new DeliveryOrderResource($deliveryOrder))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        abort_if(Gate::denies('delivery_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DeliveryOrderResource($deliveryOrder->load(['semester', 'salesperson']));
    }

    public function update(UpdateDeliveryOrderRequest $request, DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->update($request->all());

        return (new DeliveryOrderResource($deliveryOrder))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        abort_if(Gate::denies('delivery_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryOrder->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

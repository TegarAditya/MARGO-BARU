<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use App\Http\Resources\Admin\SalesOrderResource;
use App\Models\SalesOrder;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalesOrderApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sales_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesOrderResource(SalesOrder::with(['semester', 'salesperson', 'product', 'jenjang', 'kurikulum'])->get());
    }

    public function store(StoreSalesOrderRequest $request)
    {
        $salesOrder = SalesOrder::create($request->all());

        return (new SalesOrderResource($salesOrder))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesOrderResource($salesOrder->load(['semester', 'salesperson', 'product', 'jenjang', 'kurikulum']));
    }

    public function update(UpdateSalesOrderRequest $request, SalesOrder $salesOrder)
    {
        $salesOrder->update($request->all());

        return (new SalesOrderResource($salesOrder))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesOrder->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

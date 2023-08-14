<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductionFeeRequest;
use App\Http\Requests\StoreProductionFeeRequest;
use App\Http\Requests\UpdateProductionFeeRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductionFeeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('production_fee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productionFees.index');
    }

    public function create()
    {
        abort_if(Gate::denies('production_fee_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productionFees.create');
    }

    public function store(StoreProductionFeeRequest $request)
    {
        $productionFee = ProductionFee::create($request->all());

        return redirect()->route('admin.production-fees.index');
    }

    public function edit(ProductionFee $productionFee)
    {
        abort_if(Gate::denies('production_fee_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productionFees.edit', compact('productionFee'));
    }

    public function update(UpdateProductionFeeRequest $request, ProductionFee $productionFee)
    {
        $productionFee->update($request->all());

        return redirect()->route('admin.production-fees.index');
    }

    public function show(ProductionFee $productionFee)
    {
        abort_if(Gate::denies('production_fee_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productionFees.show', compact('productionFee'));
    }

    public function destroy(ProductionFee $productionFee)
    {
        abort_if(Gate::denies('production_fee_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionFee->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductionFeeRequest $request)
    {
        $productionFees = ProductionFee::find(request('ids'));

        foreach ($productionFees as $productionFee) {
            $productionFee->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

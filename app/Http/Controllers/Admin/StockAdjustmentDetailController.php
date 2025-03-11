<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockAdjustmentDetailRequest;
use App\Http\Requests\StoreStockAdjustmentDetailRequest;
use App\Http\Requests\UpdateStockAdjustmentDetailRequest;
use App\Models\BookVariant;
use App\Models\Material;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentDetailController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_adjustment_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockAdjustmentDetail::with(['product', 'material', 'stock_adjustment'])->select(sprintf('%s.*', (new StockAdjustmentDetail)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'stock_adjustment_detail_show';
                $editGate      = 'stock_adjustment_detail_edit';
                $deleteGate    = 'stock_adjustment_detail_delete';
                $crudRoutePart = 'stock-adjustment-details';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->addColumn('material_code', function ($row) {
                return $row->material ? $row->material->code : '';
            });

            $table->addColumn('stock_adjustment_reason', function ($row) {
                return $row->stock_adjustment ? $row->stock_adjustment->reason : '';
            });

            $table->editColumn('stock_adjustment.reason', function ($row) {
                return $row->stock_adjustment ? (is_string($row->stock_adjustment) ? $row->stock_adjustment : $row->stock_adjustment->reason) : '';
            });
            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'material', 'stock_adjustment']);

            return $table->make(true);
        }

        return view('admin.stockAdjustmentDetails.index');
    }

    public function create()
    {
        abort_if(Gate::denies('stock_adjustment_detail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stock_adjustments = StockAdjustment::pluck('reason', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockAdjustmentDetails.create', compact('materials', 'products', 'stock_adjustments'));
    }

    public function store(StoreStockAdjustmentDetailRequest $request)
    {
        $stockAdjustmentDetail = StockAdjustmentDetail::create($request->all());

        return redirect()->route('admin.stock-adjustment-details.index');
    }

    public function edit(StockAdjustmentDetail $stockAdjustmentDetail)
    {
        abort_if(Gate::denies('stock_adjustment_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stock_adjustments = StockAdjustment::pluck('reason', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stockAdjustmentDetail->load('product', 'material', 'stock_adjustment');

        return view('admin.stockAdjustmentDetails.edit', compact('materials', 'products', 'stockAdjustmentDetail', 'stock_adjustments'));
    }

    public function update(UpdateStockAdjustmentDetailRequest $request, StockAdjustmentDetail $stockAdjustmentDetail)
    {
        $stockAdjustmentDetail->update($request->all());

        return redirect()->route('admin.stock-adjustment-details.index');
    }

    public function show(StockAdjustmentDetail $stockAdjustmentDetail)
    {
        abort_if(Gate::denies('stock_adjustment_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockAdjustmentDetail->load('product', 'material', 'stock_adjustment');

        return view('admin.stockAdjustmentDetails.show', compact('stockAdjustmentDetail'));
    }

    public function destroy(StockAdjustmentDetail $stockAdjustmentDetail)
    {
        abort_if(Gate::denies('stock_adjustment_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockAdjustmentDetail->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockAdjustmentDetailRequest $request)
    {
        $stockAdjustmentDetails = StockAdjustmentDetail::find(request('ids'));

        foreach ($stockAdjustmentDetails as $stockAdjustmentDetail) {
            $stockAdjustmentDetail->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

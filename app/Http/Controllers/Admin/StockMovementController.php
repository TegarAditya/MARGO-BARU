<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockMovementRequest;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Models\BookVariant;
use App\Models\Material;
use App\Models\StockMovement;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_movement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockMovement::with(['warehouse', 'product', 'material', 'reversal_of'])->select(sprintf('%s.*', (new StockMovement)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'stock_movement_show';
                $editGate      = 'stock_movement_edit';
                $deleteGate    = 'stock_movement_delete';
                $crudRoutePart = 'stock-movements';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('movement_type', function ($row) {
                return $row->movement_type ? StockMovement::MOVEMENT_TYPE_SELECT[$row->movement_type] : '';
            });
            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->addColumn('material_code', function ($row) {
                return $row->material ? $row->material->code : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('transaction_type', function ($row) {
                return $row->transaction_type ? StockMovement::TRANSACTION_TYPE_SELECT[$row->transaction_type] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'material']);

            return $table->make(true);
        }

        return view('admin.stockMovements.index');
    }

    public function create()
    {
        abort_if(Gate::denies('stock_movement_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockMovements.create', compact('materials', 'products'));
    }

    public function store(StoreStockMovementRequest $request)
    {
        $stockMovement = StockMovement::create($request->all());

        return redirect()->route('admin.stock-movements.index');
    }

    public function edit(StockMovement $stockMovement)
    {
        abort_if(Gate::denies('stock_movement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stockMovement->load('warehouse', 'product', 'material', 'reversal_of');

        return view('admin.stockMovements.edit', compact('materials', 'products', 'stockMovement'));
    }

    public function update(UpdateStockMovementRequest $request, StockMovement $stockMovement)
    {
        $stockMovement->update($request->all());

        return redirect()->route('admin.stock-movements.index');
    }

    public function show(StockMovement $stockMovement)
    {
        abort_if(Gate::denies('stock_movement_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockMovement->load('warehouse', 'product', 'material', 'reversal_of');

        return view('admin.stockMovements.show', compact('stockMovement'));
    }

    public function destroy(StockMovement $stockMovement)
    {
        abort_if(Gate::denies('stock_movement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockMovement->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockMovementRequest $request)
    {
        $stockMovements = StockMovement::find(request('ids'));

        foreach ($stockMovements as $stockMovement) {
            $stockMovement->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

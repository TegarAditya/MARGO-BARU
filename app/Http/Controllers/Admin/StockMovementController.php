<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockMovementRequest;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Models\BookVariant;
use App\Models\Material;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_movement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockMovement::with(['warehouse', 'product', 'material', 'reversal_of'])->select(sprintf('%s.*', (new StockMovement)->table))->orderBy('id', 'DESC');
            $table = Datatables::of($query);

            $table->editColumn('id', function ($row) {
                return '#'.$row->id;
            });

            $table->editColumn('movement_type', function ($row) {
                return $row->movement_type ? StockMovement::MOVEMENT_TYPE_SELECT[$row->movement_type] : '';
            });

            $table->addColumn('product_code', function ($row) {
                if ($row->product) {
                    return $row->product->code. ' <a class="px-1" title="Product" href="'.route('admin.book-variants.show', $row->product_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                } else {
                    return $row->material->code. ' <a class="px-1" title="Material" href="'.route('admin.materials.show', $row->material_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                }
            });

            $table->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : $row->material->name;
            });

            $table->addColumn('reference', function ($row) {
                if ($row->transaction_type == 'adjustment') {
                    return 'Adjustment <a class="px-1" title="Reference" href="'.route('admin.stock-adjustments.show', $row->reference_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                } else if ($row->transaction_type == 'delivery') {
                    return 'Delivery <a class="px-1" title="Reference" href="'.route('admin.delivery-orders.show', $row->reference_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                } else if ($row->transaction_type == 'retur') {
                    return 'Retur <a class="px-1" title="Reference" href="'.route('admin.return-goods.show', $row->reference_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                } else if ($row->transaction_type == 'cetak') {
                    return 'Cetak <a class="px-1" title="Reference" href="'.route('admin.cetaks.show', $row->reference_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                } else if ($row->transaction_type == 'produksi') {
                    if ($row->finishing_masuk) {
                        return 'Finishing <a class="px-1" title="Reference" href="'.route('admin.finishing-masuks.show', $row->finishing_masuk).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                    } else {
                        return 'Produksi <a class="px-1" title="Reference" href="'.route('admin.finishings.show', $row->reference_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                    }
                } else if ($row->transaction_type == 'plating') {
                    return 'Produksi <a class="px-1" title="Reference" href="'.route('admin.plate-prints.show', $row->reference_id).'"><i class="fas fa-eye text-success fa-lg"></i></a>';
                } else if ($row->transaction_type == 'awal') {
                    return 'Stock Awal';
                }
            });

            $table->editColumn('quantity', function ($row) {
                return angka($row->quantity);
            });

            $table->editColumn('transaction_type', function ($row) {
                return $row->transaction_type ? StockMovement::TRANSACTION_TYPE_SELECT[$row->transaction_type] : '';
            });

            $table->addColumn('pengedit', function ($row) {
                return $row->pengedit ? $row->pengedit->name : '';
            });

            $table->rawColumns(['product_code', 'product_name', 'reference', 'pengedit']);

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

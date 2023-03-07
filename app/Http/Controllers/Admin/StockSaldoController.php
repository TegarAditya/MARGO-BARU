<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockSaldoRequest;
use App\Http\Requests\StoreStockSaldoRequest;
use App\Http\Requests\UpdateStockSaldoRequest;
use App\Models\BookVariant;
use App\Models\Material;
use App\Models\StockSaldo;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockSaldoController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_saldo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockSaldo::with(['product', 'material'])->select(sprintf('%s.*', (new StockSaldo)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'stock_saldo_show';
                $editGate      = 'stock_saldo_edit';
                $deleteGate    = 'stock_saldo_delete';
                $crudRoutePart = 'stock-saldos';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->addColumn('material_code', function ($row) {
                return $row->material ? $row->material->code : '';
            });

            $table->editColumn('periode', function ($row) {
                return $row->periode ? $row->periode : '';
            });

            $table->editColumn('qty_awal', function ($row) {
                return $row->qty_awal ? $row->qty_awal : '';
            });
            $table->editColumn('in', function ($row) {
                return $row->in ? $row->in : '';
            });
            $table->editColumn('out', function ($row) {
                return $row->out ? $row->out : '';
            });
            $table->editColumn('qty_akhir', function ($row) {
                return $row->qty_akhir ? $row->qty_akhir : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'material']);

            return $table->make(true);
        }

        return view('admin.stockSaldos.index');
    }

    public function create()
    {
        abort_if(Gate::denies('stock_saldo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockSaldos.create', compact('materials', 'products'));
    }

    public function store(StoreStockSaldoRequest $request)
    {
        $stockSaldo = StockSaldo::create($request->all());

        return redirect()->route('admin.stock-saldos.index');
    }

    public function edit(StockSaldo $stockSaldo)
    {
        abort_if(Gate::denies('stock_saldo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stockSaldo->load('product', 'material');

        return view('admin.stockSaldos.edit', compact('materials', 'products', 'stockSaldo'));
    }

    public function update(UpdateStockSaldoRequest $request, StockSaldo $stockSaldo)
    {
        $stockSaldo->update($request->all());

        return redirect()->route('admin.stock-saldos.index');
    }

    public function show(StockSaldo $stockSaldo)
    {
        abort_if(Gate::denies('stock_saldo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockSaldo->load('product', 'material');

        return view('admin.stockSaldos.show', compact('stockSaldo'));
    }

    public function destroy(StockSaldo $stockSaldo)
    {
        abort_if(Gate::denies('stock_saldo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockSaldo->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockSaldoRequest $request)
    {
        $stockSaldos = StockSaldo::find(request('ids'));

        foreach ($stockSaldos as $stockSaldo) {
            $stockSaldo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

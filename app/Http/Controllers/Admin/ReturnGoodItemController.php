<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyReturnGoodItemRequest;
use App\Http\Requests\StoreReturnGoodItemRequest;
use App\Http\Requests\UpdateReturnGoodItemRequest;
use App\Models\BookVariant;
use App\Models\ReturnGood;
use App\Models\ReturnGoodItem;
use App\Models\SalesOrder;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ReturnGoodItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('return_good_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ReturnGoodItem::with(['retur', 'salesperson', 'semester', 'product', 'sales_order'])->select(sprintf('%s.*', (new ReturnGoodItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'return_good_item_show';
                $editGate      = 'return_good_item_edit';
                $deleteGate    = 'return_good_item_delete';
                $crudRoutePart = 'return-good-items';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('retur_no_retur', function ($row) {
                return $row->retur ? $row->retur->no_retur : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('total', function ($row) {
                return $row->total ? $row->total : '';
            });
            $table->addColumn('sales_order_quantity', function ($row) {
                return $row->sales_order ? $row->sales_order->quantity : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'retur', 'salesperson', 'semester', 'product', 'sales_order']);

            return $table->make(true);
        }

        return view('admin.returnGoodItems.index');
    }

    public function create()
    {
        abort_if(Gate::denies('return_good_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returs = ReturnGood::pluck('no_retur', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_orders = SalesOrder::pluck('quantity', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.returnGoodItems.create', compact('products', 'returs', 'sales_orders', 'salespeople', 'semesters'));
    }

    public function store(StoreReturnGoodItemRequest $request)
    {
        $returnGoodItem = ReturnGoodItem::create($request->all());

        return redirect()->route('admin.return-good-items.index');
    }

    public function edit(ReturnGoodItem $returnGoodItem)
    {
        abort_if(Gate::denies('return_good_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returs = ReturnGood::pluck('no_retur', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_orders = SalesOrder::pluck('quantity', 'id')->prepend(trans('global.pleaseSelect'), '');

        $returnGoodItem->load('retur', 'salesperson', 'semester', 'product', 'sales_order');

        return view('admin.returnGoodItems.edit', compact('products', 'returnGoodItem', 'returs', 'sales_orders', 'salespeople', 'semesters'));
    }

    public function update(UpdateReturnGoodItemRequest $request, ReturnGoodItem $returnGoodItem)
    {
        $returnGoodItem->update($request->all());

        return redirect()->route('admin.return-good-items.index');
    }

    public function show(ReturnGoodItem $returnGoodItem)
    {
        abort_if(Gate::denies('return_good_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returnGoodItem->load('retur', 'salesperson', 'semester', 'product', 'sales_order');

        return view('admin.returnGoodItems.show', compact('returnGoodItem'));
    }

    public function destroy(ReturnGoodItem $returnGoodItem)
    {
        abort_if(Gate::denies('return_good_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returnGoodItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyReturnGoodItemRequest $request)
    {
        $returnGoodItems = ReturnGoodItem::find(request('ids'));

        foreach ($returnGoodItems as $returnGoodItem) {
            $returnGoodItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

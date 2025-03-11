<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDeliveryOrderItemRequest;
use App\Http\Requests\StoreDeliveryOrderItemRequest;
use App\Http\Requests\UpdateDeliveryOrderItemRequest;
use App\Models\BookVariant;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\SalesOrder;
use App\Models\Salesperson;
use App\Models\Semester;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DeliveryOrderItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('delivery_order_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DeliveryOrderItem::with(['semester', 'salesperson', 'sales_order', 'delivery_order', 'product'])->select(sprintf('%s.*', (new DeliveryOrderItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'delivery_order_item_show';
                $editGate      = 'delivery_order_item_edit';
                $deleteGate    = 'delivery_order_item_delete';
                $crudRoutePart = 'delivery-order-items';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('sales_order_quantity', function ($row) {
                return $row->sales_order ? $row->sales_order->quantity : '';
            });

            $table->addColumn('delivery_order_no_suratjalan', function ($row) {
                return $row->delivery_order ? $row->delivery_order->no_suratjalan : '';
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson', 'sales_order', 'delivery_order', 'product']);

            return $table->make(true);
        }

        return view('admin.deliveryOrderItems.index');
    }

    public function create()
    {
        abort_if(Gate::denies('delivery_order_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_orders = SalesOrder::pluck('quantity', 'id')->prepend(trans('global.pleaseSelect'), '');

        $delivery_orders = DeliveryOrder::pluck('no_suratjalan', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.deliveryOrderItems.create', compact('delivery_orders', 'products', 'sales_orders', 'salespeople', 'semesters'));
    }

    public function store(StoreDeliveryOrderItemRequest $request)
    {
        $deliveryOrderItem = DeliveryOrderItem::create($request->all());

        return redirect()->route('admin.delivery-order-items.index');
    }

    public function edit(DeliveryOrderItem $deliveryOrderItem)
    {
        abort_if(Gate::denies('delivery_order_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_orders = SalesOrder::pluck('quantity', 'id')->prepend(trans('global.pleaseSelect'), '');

        $delivery_orders = DeliveryOrder::pluck('no_suratjalan', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $deliveryOrderItem->load('semester', 'salesperson', 'sales_order', 'delivery_order', 'product');

        return view('admin.deliveryOrderItems.edit', compact('deliveryOrderItem', 'delivery_orders', 'products', 'sales_orders', 'salespeople', 'semesters'));
    }

    public function update(UpdateDeliveryOrderItemRequest $request, DeliveryOrderItem $deliveryOrderItem)
    {
        $deliveryOrderItem->update($request->all());

        return redirect()->route('admin.delivery-order-items.index');
    }

    public function show(DeliveryOrderItem $deliveryOrderItem)
    {
        abort_if(Gate::denies('delivery_order_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryOrderItem->load('semester', 'salesperson', 'sales_order', 'delivery_order', 'product');

        return view('admin.deliveryOrderItems.show', compact('deliveryOrderItem'));
    }

    public function destroy(DeliveryOrderItem $deliveryOrderItem)
    {
        abort_if(Gate::denies('delivery_order_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryOrderItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyDeliveryOrderItemRequest $request)
    {
        $deliveryOrderItems = DeliveryOrderItem::find(request('ids'));

        foreach ($deliveryOrderItems as $deliveryOrderItem) {
            $deliveryOrderItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

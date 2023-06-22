<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDeliveryOrderRequest;
use App\Http\Requests\StoreDeliveryOrderRequest;
use App\Http\Requests\UpdateDeliveryOrderRequest;
use App\Models\BookVariant;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use App\Services\EstimationService;
use App\Services\StockService;
use App\Services\DeliveryService;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('delivery_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DeliveryOrder::with(['semester', 'salesperson'])->select(sprintf('%s.*', (new DeliveryOrder)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'delivery_order_show';
                $editGate      = 'delivery_order_edit';
                $deleteGate    = 'delivery_order_delete';
                $crudRoutePart = 'delivery-orders';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_suratjalan', function ($row) {
                return $row->no_suratjalan ? $row->no_suratjalan : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        return view('admin.deliveryOrders.index');
    }

    public function create()
    {
        abort_if(Gate::denies('delivery_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::whereHas('estimasi')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.deliveryOrders.create', compact('salespeople', 'semesters'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'semester_id' =>'required',
            'salesperson_id' => 'required',
            'payment_type' => 'required',
            'orders' => 'required|array',
            'orders.*' => 'exists:sales_orders,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = $validatedData['semester_id'];
        $salesperson = $validatedData['salesperson_id'];
        $payment_type = $validatedData['payment_type'];
        $products = $validatedData['products'];
        $orders = $validatedData['orders'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $delivery = DeliveryOrder::create([
                'no_suratjalan' => DeliveryOrder::generateNoSJ($semester),
                'date' => $date,
                'semester_id' => $semester,
                'salesperson_id' => $salesperson,
                'payment_type' => $payment_type,
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];
                $order = $orders[$i];
                $quantity = $quantities[$i];

                $delivery_item = DeliveryOrderItem::create([
                    'delivery_order_id' => $delivery->id,
                    'sales_order_id' => $order,
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'product_id' => $product,
                    'quantity' => $quantity
                ]);

                StockService::createMovement('out', 'delivery', $delivery->id, $product, -1 * $quantity);
                StockService::updateStock($product, -1 * $quantity);

                EstimationService::updateMoved($order, $quantity);
            }

            DB::commit();

            Alert::success('Success', 'Delivery Order berhasil di simpan');

            return redirect()->route('admin.delivery-orders.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(DeliveryOrder $deliveryOrder)
    {
        abort_if(Gate::denies('delivery_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $deliveryOrder->load('semester', 'salesperson');

        return view('admin.deliveryOrders.edit', compact('deliveryOrder', 'salespeople', 'semesters'));
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'delivery_items' => 'required|array',
            'delivery_items.*' => 'exists:delivery_order_items,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $delivery_items = $validatedData['delivery_items'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $deliveryOrder->update([
                'date' => $date,
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];
                $delivery_item = $delivery_items[$i];
                $quantity = $quantities[$i];

                $delivery_order_item = DeliveryOrderItem::find($delivery_item);

                $old_quantity = $delivery_order_item->quantity;
                $order = $delivery_order_item->sales_order_id;

                $delivery_order_item->quantity = $quantity;
                $delivery_order_item->save();

                StockService::editMovement('out', 'delivery', $deliveryOrder->id, $product, -1 * $quantity);
                StockService::updateStock($product, -1 * ($quantity - $old_quantity));

                EstimationService::updateMoved($order, ($quantity - $old_quantity));
            }

            DB::commit();

            Alert::success('Success', 'Delivery Order berhasil di simpan');

            return redirect()->route('admin.delivery-orders.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show(DeliveryOrder $deliveryOrder, Request $request)
    {
        abort_if(Gate::denies('delivery_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryOrder->load('semester', 'salesperson');

        $delivery_items = DeliveryOrderItem::where('delivery_order_id', $deliveryOrder->id)->get();

        if ($request->print) {
            return view('admin.deliveryOrders.prints.surat-jalan', compact('deliveryOrder', 'delivery_items'));
        }

        return view('admin.deliveryOrders.show', compact('deliveryOrder', 'delivery_items'));
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        abort_if(Gate::denies('delivery_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryOrder->delete();

        return back();
    }

    public function massDestroy(MassDestroyDeliveryOrderRequest $request)
    {
        $deliveryOrders = DeliveryOrder::find(request('ids'));

        foreach ($deliveryOrders as $deliveryOrder) {
            $deliveryOrder->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

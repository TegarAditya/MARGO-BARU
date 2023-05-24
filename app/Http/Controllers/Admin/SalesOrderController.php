<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySalesOrderRequest;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use App\Models\BookVariant;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\SalesOrder;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesOrder::with(['semester', 'salesperson', 'product', 'jenjang', 'kurikulum'])->select(sprintf('%s.*', (new SalesOrder)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'sales_order_show';
                $editGate      = 'sales_order_edit';
                $deleteGate    = 'sales_order_delete';
                $crudRoutePart = 'sales-orders';

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

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->addColumn('jenjang_code', function ($row) {
                return $row->jenjang ? $row->jenjang->code : '';
            });

            $table->addColumn('kurikulum_code', function ($row) {
                return $row->kurikulum ? $row->kurikulum->code : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson', 'product', 'jenjang', 'kurikulum']);

            return $table->make(true);
        }

        return view('admin.salesOrders.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.salesOrders.create', compact('jenjangs', 'kurikulums', 'products', 'salespeople', 'semesters'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required',
            'order_number' => 'required',
            'address' => 'required',
            'product' => 'required|array',
            'product.*' => 'distinct',
            'quantity' => 'required|array',
            'quantity.*' => 'numeric|min:1',
            'price' => 'required|array',
            'price.*' => 'numeric|min:0',
        ]);

        $customer = Customer::create([
            'name' => $validatedData['customer_name'],
            'order_number' => $validatedData['order_number'],
            'address' => $validatedData['address'],
        ]);

        foreach ($validatedData['product'] as $index => $product) {
            $order = new Order;
            $order->customer_id = $customer->id;
            $order->product_id = $product;
            $order->quantity = $validatedData['quantity'][$index];
            $order->price = $validatedData['price'][$index];
            $order->save();
        }

        return redirect()->back()->with('success', 'Order submitted successfully.');
    }

    public function edit(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salesOrder->load('semester', 'salesperson', 'product', 'jenjang', 'kurikulum');

        return view('admin.salesOrders.edit', compact('jenjangs', 'kurikulums', 'products', 'salesOrder', 'salespeople', 'semesters'));
    }

    public function update(UpdateSalesOrderRequest $request, SalesOrder $salesOrder)
    {
        $salesOrder->update($request->all());

        return redirect()->route('admin.sales-orders.index');
    }

    public function show(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesOrder->load('semester', 'salesperson', 'product', 'jenjang', 'kurikulum');

        return view('admin.salesOrders.show', compact('salesOrder'));
    }

    public function destroy(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesOrder->delete();

        return back();
    }

    public function massDestroy(MassDestroySalesOrderRequest $request)
    {
        $salesOrders = SalesOrder::find(request('ids'));

        foreach ($salesOrders as $salesOrder) {
            $salesOrder->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

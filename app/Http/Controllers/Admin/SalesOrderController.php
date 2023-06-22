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
use App\Models\EstimationMovement;
use App\Models\ProductionEstimation;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Carbon\Carbon;
use App\Services\EstimationService;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesOrder::with(['semester', 'salesperson', 'product', 'jenjang', 'kurikulum'])->select(sprintf('%s.*', (new SalesOrder)->table));
            // $query = $query = SalesOrder::select('semester_id', 'salesperson_id', 'payment_type')->distinct()->with(['semester', 'salesperson']);
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

            $table->editColumn('payment_type', function ($row) {
                return $row->payment_type ? SalesOrder::PAYMENT_TYPE_SELECT[$row->payment_type] : '';
            });

            // $table->addColumn('jenjang_code', function ($row) {
            //     return $row->jenjang ? $row->jenjang->code : '';
            // });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson', 'jenjang']);

            return $table->make(true);
        }

        return view('admin.salesOrders.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.salesOrders.create', compact('jenjangs', 'salespeople', 'semesters'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'semester_id' =>'required',
            'salesperson_id' => 'required',
            'payment_type' => 'required',
            'jenjang_id' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $semester = $validatedData['semester_id'];
        $salesperson = $validatedData['salesperson_id'];
        $payment_type = $validatedData['payment_type'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $today = Carbon::now()->format('d-m-Y');

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];

                $order = SalesOrder::updateOrCreate([
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'payment_type' => $payment_type,
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id
                ], [
                    'quantity' => DB::raw("quantity + $quantity"),
                    'moved' => 0,
                    'retur' => 0
                ]);

                EstimationService::createMovement('in', 'sales_order', $order->id, $product->id, $quantity, $product->type);
                EstimationService::createProduction($product->id, $quantity, $product->type);

                foreach($product->child as $item) {
                    EstimationService::createMovement('in', 'sales_order', $order->id, $item->id, $quantity, $item->type);
                    EstimationService::createProduction($item->id, $quantity, $item->type);
                }
            }

            DB::commit();

            Alert::success('Success', 'Sales Order berhasil di simpan');

            return redirect()->route('admin.sales-orders.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }

        return redirect()->back()->with('success', 'Sales Order submitted successfully.');
    }

    public function edit(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $orders = SalesOrder::where('salesperson_id', $salesOrder->salesperson_id)
                    ->where('semester_id', $salesOrder->semester_id)
                    ->where('jenjang_id', $salesOrder->jenjang_id)
                    ->where('kurikulum_id', $salesOrder->kurikulum_id)
                    ->orderBy('product_id', 'ASC')
                    ->get();

        return view('admin.salesOrders.edit', compact('jenjangs', 'salesOrder', 'salespeople', 'semesters', 'orders'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
         // Validate the form data
         $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $today = Carbon::now()->format('d-m-Y');
        $semester = $salesOrder->semester_id;
        $salesperson = $salesOrder->salesperson_id;
        $payment_type = $salesOrder->payment_type;

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];

                $order = SalesOrder::where('semester_id', $semester)
                        ->where('salesperson_id', $salesperson)
                        ->where('payment_type', $payment_type)
                        ->where('product_id', $product->id)
                        ->first();

                $old_quantity = $order->quantity;

                $order->quantity = $quantity;
                $order->save();

                EstimationService::editMovement('in', 'sales_order', $order->id, $product->id, $quantity, $product->type);
                EstimationService::editProduction($product->id, ($quantity - $old_quantity), $product->type);

                foreach($product->child as $item) {
                    EstimationService::editMovement('in', 'sales_order', $order->id, $item->id, $quantity, $item->type);
                    EstimationService::editProduction($item->id, ($quantity - $old_quantity), $item->type);
                }
            }

            DB::commit();

            Alert::success('Success', 'Sales Order berhasil di simpan');

            return redirect()->route('admin.sales-orders.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show(SalesOrder $salesOrder, Request $request)
    {
        abort_if(Gate::denies('sales_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $orders = SalesOrder::where('salesperson_id', $salesOrder->salesperson_id)
            ->where('jenjang_id', $salesOrder->jenjang_id)
            ->where('kurikulum_id', $salesOrder->kurikulum_id)
            ->get();

        $salesOrder->load('semester', 'salesperson', 'product', 'jenjang', 'kurikulum');

        if ($request->print) {
            return view('admin.salesOrders.prints.estimasi', compact('salesOrder', 'orders'));
        }

        return view('admin.salesOrders.show', compact('salesOrder', 'orders'));
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

    public function printEstimasi(SalesOrder $salesOrder)
    {
        $orders = SalesOrder::where('salesperson_id', $salesOrder->salesperson_id)
            ->where('jenjang_id', $salesOrder->jenjang_id)
            ->where('kurikulum_id', $salesOrder->kurikulum_id)
            ->get();

        $salesOrder->load('semester', 'salesperson', 'product', 'jenjang', 'kurikulum');

        return view('admin.salesOrders.show', compact('salesOrder', 'orders'));
    }

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        try {
            Excel::import(new BookImport(), $file);
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return redirect()->back();
        }

        Alert::success('Success', 'Sales Order berhasil di import');
        return redirect()->back();
    }

    public function template_import()
    {
        $filepath = public_path('import-template\SALES_ORDER_TEMPLATE.xlsx');
        return response()->download($filepath);
    }
}

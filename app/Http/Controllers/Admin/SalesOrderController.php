<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySalesOrderRequest;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use App\Models\BookVariant;
use App\Models\Estimation;
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
use App\Imports\SalesOrderImport;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            // $query = SalesOrder::with(['semester', 'salesperson', 'product', 'jenjang', 'kurikulum'])->select(sprintf('%s.*', (new SalesOrder)->table));
            $query = SalesOrder::select('semester_id', 'salesperson_id')->distinct()->with(['semester', 'salesperson'])
                ->orderBy('semester_id', 'DESC')->OrderBy('salesperson_id', 'ASC');

            if (!empty($request->salesperson)) {
                $query->where('salesperson_id', $request->salesperson);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                if (!$row->salesperson) {
                    return '
                        <a class="px-1" href="'.route('admin.sales-orders.show', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]).'" title="Show">
                            <i class="fas fa-eye text-success fa-lg"></i>
                        </a>
                    ';
                }

                return '
                    <a class="px-1" href="'.route('admin.sales-orders.show', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.sales-orders.estimasi', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]).'" target="_blank" title="Show" >
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                ';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->short_name : 'Internal';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('short_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.salesOrders.index', compact('salespeople', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_order = SalesOrder::generateNoOrderTemp(setting('current_semester'));

        return view('admin.salesOrders.create', compact('jenjangs', 'salespeople', 'semesters', 'no_order'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            // 'semester_id' =>'required',
            'salesperson_id' => 'required',
            'jenjang_id' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $semester = setting('current_semester');
        $salesperson = $validatedData['salesperson_id'];
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
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id
                ], [
                    'no_order' => SalesOrder::generateNoOrder($semester, $salesperson),
                    'quantity' => DB::raw("quantity + $quantity"),
                ]);

                if ($product->semester_id == $semester) {
                    EstimationService::createMovement('in', 'sales_order', $order->id, $product->id, $quantity, 'sales');
                    EstimationService::createProduction($product->id, $quantity, $product->type);

                    foreach($product->components as $item) {
                        EstimationService::createMovement('in', 'sales_order', $order->id, $item->id, $quantity, 'sales');
                        EstimationService::createProduction($item->id, $quantity, $item->type);
                    }
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

    public function edit(Request $request)
    {
        abort_if(Gate::denies('sales_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semester = $request->semester;
        $salesperson = $request->salesperson;

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $orders = SalesOrder::where('salesperson_id', $salesperson)
                    ->where('semester_id', $semester)
                    ->orderBy('product_id', 'ASC')
                    ->get();

        $salesOrder = $orders->first();

        return view('admin.salesOrders.edit', compact('jenjangs', 'salespeople', 'semesters', 'orders', 'salesOrder'));
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

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];

                $order = SalesOrder::where('semester_id', $semester)
                        ->where('salesperson_id', $salesperson)
                        ->where('product_id', $product->id)
                        ->first();

                $old_quantity = $order->quantity;

                $order->quantity = $quantity;
                $order->save();

                EstimationService::editMovement('in', 'sales_order', $order->id, $product->id, $quantity, 'sales');
                EstimationService::editProduction($product->id, ($quantity - $old_quantity), $product->type);

                foreach($product->components as $item) {
                    EstimationService::editMovement('in', 'sales_order', $order->id, $item->id, $quantity, 'sales');
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

    public function show(Request $request)
    {
        abort_if(Gate::denies('sales_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semester = $request->semester;
        $salesperson = $request->salesperson ?? null;

        $orders = BookVariant::whereHas('estimasi', function ($q) use ($salesperson, $semester) {
                    $q->where('salesperson_id', $salesperson)
                    ->where('semester_id', $semester);
                })->with('estimasi')->orderBy('code', 'ASC')->get();

        $salesOrder = SalesOrder::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();

        $grouped = $orders->sortBy('product.kelas_id')->sortBy('product.mapel_id')->groupBy('jen_kum');

        $semester = Semester::find($semester);
        $salesperson = Salesperson::find($salesperson);

        return view('admin.salesOrders.show', compact('salesOrder', 'orders', 'grouped', 'semester', 'salesperson'));
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

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        try {
            Excel::import(new SalesOrderImport(), $file);
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return redirect()->back();
        }

        Alert::success('Success', 'Sales Order berhasil di import');
        return redirect()->back();
    }

    public function template_import()
    {
        $filepath = public_path('import-template\TEMPLATE_ESTIMASI_SALES.xlsx');
        return response()->download($filepath);
    }

    public function estimasi(Request $request)
    {
        $semester = $request->semester;
        $salesperson = $request->salesperson;

        $orders = SalesOrder::with('product')
            ->where('salesperson_id', $salesperson)
            ->where('semester_id', $semester)
            ->get();

        $salesOrder = $orders->first();
        $salesOrder->load('semester', 'salesperson', 'product', 'jenjang', 'kurikulum');

        $grouped = $orders->where('product.type', 'L')->sortBy('product.kelas_id')->sortBy('product.mapel_id')->groupBy('jen_kum');

        $kelengkapan = $orders->whereNotIn('product.type', ['L']);

        return view('admin.salesOrders.prints.estimasi', compact('salesOrder', 'orders', 'grouped', 'kelengkapan'));
    }

}

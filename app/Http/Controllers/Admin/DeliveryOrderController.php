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
use Carbon\Carbon;
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
                $btn = '
                    <a class="px-1" href="'.route('admin.delivery-orders.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.delivery-orders.printSj', $row->id).'" title="Print SJ" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.delivery-orders.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                ';

                if (!$row->faktur) {
                    $btn = $btn. '
                        <a class="px-1" href="'.route('admin.invoices.generate', $row->id).'" title="Generate Invoice">
                            <i class="fas fa-receipt text-danger fa-lg"></i>
                        </a>
                    ';
                }

                return $btn;
            });

            $table->editColumn('no_suratjalan', function ($row) {
                return $row->no_suratjalan ? $row->no_suratjalan : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->short_name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('short_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.deliveryOrders.index', compact('salespeople', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('delivery_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::whereHas('estimasi')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_suratjalan = DeliveryOrder::generateNoSJ(setting('current_semester'));

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.deliveryOrders.create', compact('salespeople', 'semesters', 'no_suratjalan', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            // 'semester_id' =>'required',
            'salesperson_id' => 'required',
            'orders' => 'required|array',
            'orders.*' => 'exists:sales_orders,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = setting('current_semester');
        $salesperson = $validatedData['salesperson_id'];
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

                StockService::createMovement('out', 'delivery', $delivery->id, $date, $product, -1 * $quantity);
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
            'no_suratjalan' => 'required',
            'date' => 'required',
            'delivery_items' => 'required|array',
            'delivery_items.*' => 'exists:delivery_order_items,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:0',
        ]);

        $no_suratjalan = $validatedData['no_suratjalan'];
        $date = $validatedData['date'];
        $delivery_items = $validatedData['delivery_items'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $deliveryOrder->update([
                'no_suratjalan' => $no_suratjalan,
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

                StockService::editMovement('out', 'delivery', $deliveryOrder->id, $date, $product, -1 * $quantity);
                StockService::updateStock($product, -1 * ($quantity - $old_quantity));

                EstimationService::updateMoved($order, ($quantity - $old_quantity));
            }

            DB::commit();

            Alert::success('Success', 'Silahkan Update Invoicenya Juga')->showConfirmButton('Oke', '#3085d6');

            return redirect()->route('admin.invoices.generate', $deliveryOrder->id);
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

        return view('admin.deliveryOrders.show', compact('deliveryOrder', 'delivery_items'));
    }

    public function printSj(DeliveryOrder $deliveryOrder, Request $request)
    {
        $deliveryOrder->load('semester', 'salesperson');

        $delivery_items = DeliveryOrderItem::with('product')->where('delivery_order_id', $deliveryOrder->id)->get();

        $lks = $delivery_items->where('product.type', 'L')->sortBy('product.type')->sortBy('product.kelas_id')->sortBy('product.mapel_id')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        $kelengkapan =  $delivery_items->whereNotIn('product.type', ['L']);

        return view('admin.deliveryOrders.prints.surat-jalan', compact('deliveryOrder', 'lks', 'kelengkapan'));
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

    public function getDeliveryOrder(Request $request) {
        $semester = setting('current_semester');
        $salesperson = $request->input('salesperson');

        $query = DeliveryOrder::query();

        if (!empty($semester)) {
            $query->where('semester_id', $semester);
        }

        if (!empty($salesperson)) {
            $query->where('salesperson_id', $salesperson);
        }

        $items = $query->latest()->get();

        $formatted = [];

        foreach ($items as $item) {
            $formatted[] = [
                'id' => $item->id,
                'text' => '('.$item->date.') - '. $item->no_suratjalan,
            ];
        }

        return response()->json($formatted);
    }

    public function getEstimasi(Request $request)
    {
        $query = $request->input('q');
        $semester = setting('current_semester');
        $salesperson = $request->input('salesperson');
        $jenjang = $request->input('jenjang');

        $query = BookVariant::whereHas('estimasi', function ($q) use ($semester, $salesperson) {
                    $q->where('salesperson_id', $salesperson)
                    ->where('semester_id', $semester);
                })->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC');

        if (!empty($jenjang)) {
            $query->where('jenjang_id', $jenjang);
        }

        $products = $query->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoEstimasi(Request $request)
    {
        $id = $request->input('id');
        $semester = setting('current_semester');
        $salesperson = $request->input('salesperson');

        $product = BookVariant::join('sales_orders', 'sales_orders.product_id', '=', 'book_variants.id')
                ->where('book_variants.id', $id)
                ->where('sales_orders.semester_id', $semester)
                ->where('sales_orders.salesperson_id', $salesperson)
                ->first(['book_variants.*', 'sales_orders.quantity as estimasi', 'sales_orders.moved as terkirim', 'sales_orders.id as order_id']);
        $product->load('book', 'jenjang', 'cover', 'kurikulum', 'isi');

        return response()->json($product);
    }
}

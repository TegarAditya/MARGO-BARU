<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyReturnGoodRequest;
use App\Http\Requests\StoreReturnGoodRequest;
use App\Http\Requests\UpdateReturnGoodRequest;
use App\Models\ReturnGood;
use App\Models\ReturnGoodItem;
use App\Models\Salesperson;
use App\Models\Semester;
use App\Models\BookVariant;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SalesOrder;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Carbon\Carbon;
use App\Services\EstimationService;
use App\Services\StockService;
use App\Services\TransactionService;

class ReturnGoodController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('return_good_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ReturnGood::with(['salesperson', 'semester'])->select(sprintf('%s.*', (new ReturnGood)->table))->latest();

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
                return '
                    <a class="px-1" href="'.route('admin.return-goods.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.return-goods.print-faktur', $row->id).'" target="_blank" title="Print Faktur" >
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.return-goods.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('no_retur', function ($row) {
                return $row->no_retur ? $row->no_retur : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('nominal', function ($row) {
                return $row->nominal ? money($row->nominal) : '';
            });

            $table->addColumn('updated_by', function ($row) {
                return $row->updated_by ? $row->updated_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'salesperson', 'semester', 'updated_by']);

            return $table->make(true);
        }

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('short_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.returnGoods.index', compact('semesters', 'salespeople'));
    }

    public function create()
    {
        abort_if(Gate::denies('return_good_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::whereHas('estimasi')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_retur = ReturnGood::generateNoRetur(setting('current_semester'));

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.returnGoods.create', compact('salespeople', 'semesters', 'no_retur', 'today'));
    }

    public function store(Request $request)
    {
         // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'semester_id' =>'required',
            'salesperson_id' => 'required',
            'orders' => 'required|array',
            'orders.*' => 'exists:sales_orders,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:0',
        ]);

        $date = $validatedData['date'];
        $semester_retur = $validatedData['semester_id'];
        $semester = setting('current_semester');
        $salesperson = $validatedData['salesperson_id'];
        $products = $validatedData['products'];
        $orders = $validatedData['orders'];
        $quantities = $validatedData['quantities'];
        $prices = $validatedData['prices'];

        DB::beginTransaction();
        try {
            $retur = ReturnGood::create([
                'no_retur' => ReturnGood::generateNoRetur(setting('current_semester')),
                'date' => $date,
                'semester_id' => $semester,
                'semester_retur_id' => $semester_retur,
                'salesperson_id' => $salesperson,
            ]);

            $nominal = 0;
            // $flag_cash = false;

            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];
                $order = $orders[$i];
                $price = $prices[$i];

                // $invoice_item = InvoiceItem::where('semester_id', $semester_retur)->where('salesperson_id', $salesperson)->where('product_id', $product )->first();
                // if (!$invoice_item) {
                //     throw new \Exception('Product tidak ditemukan');
                // }
                // $price = $invoice_item->price - $invoice_item->discount;

                $quantity = $quantities[$i];
                $total = (int) $price * $quantity;
                $nominal += $total;

                $retur_item = ReturnGoodItem::create([
                    'retur_id' => $retur->id,
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'sales_order_id' => $order,
                    'product_id' => $product,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $total
                ]);

                StockService::createMovement('in', 'retur', $retur->id, $date, $product, $quantity);
                StockService::updateStock($product, $quantity);

                EstimationService::updateRetur($order, $quantity);
            }

            $note = 'Retur from '.$retur->no_retur;
            TransactionService::createTransaction($date, $note, $salesperson, $semester, 'retur', $retur->id, $retur->no_retur, $nominal, 'credit');

            // if ($flag_cash) {
            //     InvoiceItem::where('salesperson_id', $salesperson)->where('semester_id', $semester)->where('discount', '>', 0)
            //     ->update([
            //         'discount' => 0,
            //         'total_discount' => 0
            //     ]);

            //     $invoices = Invoice::where('salesperson_id', $salesperson)->where('semester_id', $semester)->where('discount', '>', 0)->get();
            //     $note_transaksi = 'Diskon di cancel karena retur no '.$retur->no_retur;

            //     foreach($invoices as $invoice) {
            //         $total = $invoice->total;
            //         TransactionService::editTransaction($date, $note_transaksi, $salesperson, $semester, 'diskon', $invoice->id, $invoice->no_faktur, 0, 'credit');
            //         $invoice->update([
            //             'discount' => 0,
            //             'nominal' => $total,
            //             'retur' => 1
            //         ]);
            //     }
            // }

            $retur->nominal = $nominal;
            $retur->save();

            DB::commit();

            Alert::success('Success', 'Retur berhasil di simpan');

            return redirect()->route('admin.return-goods.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        };
    }

    public function edit(ReturnGood $returnGood)
    {
        abort_if(Gate::denies('return_good_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $returnGood->load('salesperson', 'semester');

        return view('admin.returnGoods.edit', compact('returnGood', 'salespeople', 'semesters'));
    }

    public function update(Request $request, ReturnGood $returnGood)
    {
         // Validate the form data
        $validatedData = $request->validate([
            'no_retur' => 'required',
            'date' => 'required',
            'retur_id' =>'required',
            'retur_items' => 'required|array',
            'retur_items.*' => 'exists:return_good_items,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:0',
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:0',
        ]);

        $no_retur = $validatedData['no_retur'];
        $date = $validatedData['date'];
        $retur = $validatedData['retur_id'];
        $products = $validatedData['products'];
        $retur_items = $validatedData['retur_items'];
        $quantities = $validatedData['quantities'];
        $prices = $validatedData['prices'];
        $semester_retur = $returnGood->semester_retur_id;
        $salesperson = $returnGood->salesperson_id;

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];
                $price = $prices[$i];

                // $invoice_item = InvoiceItem::where('semester_id', $semester_retur)->where('salesperson_id', $salesperson)->where('product_id', $product )->first();
                // if (!$invoice_item) {
                //     throw new \Exception('Product tidak ditemukan');
                // }
                // $price = $invoice_item->price - $invoice_item->discount;

                $quantity = $quantities[$i];
                $total = (int) $price * $quantity;

                $retur_item = $retur_items[$i];
                $retur_good_item = ReturnGoodItem::find($retur_item);

                $old_quantity = $retur_good_item->quantity;
                $order = $retur_good_item->sales_order_id;

                $retur_good_item->quantity = $quantity;
                $retur_good_item->price = $price;
                $retur_good_item->total = $total;
                $retur_good_item->save();

                StockService::editMovement('in', 'retur', $returnGood->id, $date, $product, $quantity);
                StockService::updateStock($product, ($quantity - $old_quantity));

                EstimationService::updateRetur($order, ($quantity - $old_quantity));
            }

            $nominal = ReturnGoodItem::where('retur_id', $returnGood->id)->sum('total');

            $returnGood->update([
                'semester_id' => setting('current_semester'),
                'no_retur' => $no_retur,
                'date' => $date,
                'nominal' => $nominal
            ]);

            TransactionService::editTransaction($date, 'Retur from '.$no_retur, $salesperson, setting('current_semester'), 'retur', $returnGood->id, $no_retur, $nominal, 'credit');

            DB::commit();

            Alert::success('Success', 'Retur berhasil di simpan');

            return redirect()->route('admin.return-goods.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        };
    }

    public function show(ReturnGood $returnGood)
    {
        abort_if(Gate::denies('return_good_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returnGood->load('salesperson', 'semester');

        $details = ReturnGoodItem::with('product')->where('retur_id', $returnGood->id)->get();

        $retur_items = $details->sortBy('product.nama_urut')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        return view('admin.returnGoods.show', compact('returnGood', 'retur_items'));
    }

    public function destroy(ReturnGood $returnGood)
    {
        abort_if(Gate::denies('return_good_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returnGood->delete();

        return back();
    }

    public function massDestroy(MassDestroyReturnGoodRequest $request)
    {
        $returnGoods = ReturnGood::find(request('ids'));

        foreach ($returnGoods as $returnGood) {
            $returnGood->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function printFaktur(ReturnGood $retur)
    {
        $retur->load('salesperson', 'semester');

        $details = ReturnGoodItem::with('product')->where('retur_id', $retur->id)->get();

        $retur_items = $details->sortBy('product.kelas_id')->sortBy('product.nama_urut')->sortBy('product.jenjang_id');

        return view('admin.returnGoods.faktur', compact('retur', 'retur_items'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use App\Services\TransactionService;
use App\Services\DeliveryService;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Invoice::with(['delivery_order', 'semester', 'salesperson'])->select(sprintf('%s.*', (new Invoice)->table))->latest();

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
                    <a class="px-1" href="'.route('admin.invoices.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.invoices.print-faktur', $row->id).'" target="_blank" title="Print Faktur" >
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                ';

                if ($row->type == 'jual') {
                    $btn = $btn. '
                        <a class="px-1" href="'.route('admin.invoices.edit', $row->id).'" title="Edit">
                            <i class="fas fa-edit fa-lg"></i>
                        </a>
                    ';
                } else {
                    $btn = $btn. '
                        <a class="px-1" href="'.route('admin.invoices.editInvoice', $row->id).'" title="Edit">
                            <i class="fas fa-edit fa-lg"></i>
                        </a>
                    ';
                }
                return $btn;
            });

            $table->editColumn('no_faktur', function ($row) {
                return $row->no_faktur ? $row->no_faktur : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->short_name : '';
            });

            $table->editColumn('total', function ($row) {
                return $row->total ? money($row->total) : 0;
            });

            $table->editColumn('discount', function ($row) {
                return $row->discount ? money($row->discount) : 0;
            });

            $table->editColumn('nominal', function ($row) {
                return $row->nominal ? $row->nominal : 0;
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('short_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.invoices.index', compact('salespeople', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $delivery_orders = DeliveryOrder::pluck('no_suratjalan', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::whereHas('estimasi')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_faktur = Invoice::generateNoInvoice(setting('current_semester'));

        return view('admin.invoices.create', compact('delivery_orders', 'salespeople', 'semesters', 'no_faktur'));
    }

    public function generate(DeliveryOrder $delivery)
    {
        $delivery->load(['semester', 'salesperson']);

        $delivery_item = DeliveryOrderItem::with('delivery_order', 'product', 'product.book', 'sales_order')->where('delivery_order_id', $delivery->id)->orderBy('product_id', 'ASC')->get();

        $invoice = Invoice::where('type', 'jual')->where('delivery_order_id', $delivery->id)->first();

        if ($invoice) {
            $invoice->load('delivery_order', 'semester', 'salesperson');

            $invoice_item = InvoiceItem::with('product', 'product.book', 'delivery_order')->where('invoice_id', $invoice->id)->orderBy('product_id', 'ASC')->get();

            return view('admin.invoices.edit-generate', compact('invoice', 'invoice_item', 'delivery_item'));
        }

        $no_faktur = Invoice::generateNoInvoice($delivery_order->semester_id);

        return view('admin.invoices.generate', compact('delivery', 'delivery_item', 'no_faktur'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'delivery' =>'required|exists:delivery_orders,id',
            // 'note' => 'nullable',
            'nominal' => 'numeric|min:0',
            'total_price' => 'numeric|min:0',
            'total_diskon' => 'numeric|min:0',
            'delivery_items' => 'required|array',
            'delivery_items.*' => 'exists:delivery_order_items,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:0',
            'diskons' => 'nullable|array',
            'diskons.*' => 'numeric|min:0',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'subtotals' => 'required|array',
            'subtotals.*' => 'numeric|min:0',
            'subdiscounts' => 'required|array',
            'subdiscounts.*' => 'numeric|min:0',
        ]);

        $date = $validatedData['date'];
        $delivery = $validatedData['delivery'];
        // $note = $validatedData['note'];
        $nominal = $validatedData['nominal'];
        $total_price = $validatedData['total_price'];
        $total_diskon = $validatedData['total_diskon'];
        $delivery_items = $validatedData['delivery_items'];
        $products = $validatedData['products'];
        $prices = $validatedData['prices'];
        $quantities = $validatedData['quantities'];
        $subtotals = $validatedData['subtotals'];
        $diskons = $validatedData['diskons'];
        $subdiscounts = $validatedData['subdiscounts'];

        $delivery_order = DeliveryOrder::find($delivery);
        $semester = $delivery_order->semester_id;
        $salesperson = $delivery_order->salesperson_id;

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'no_faktur' => Invoice::generateNoInvoice($semester),
                'date' => $date,
                'delivery_order_id' => $delivery,
                'semester_id' => $semester,
                'salesperson_id' => $salesperson,
                'type' => 'jual',
                'discount' => $total_diskon,
                'total' => $total_price,
                'nominal' => $nominal,
                'note' => 'Generated Invoice From '. $delivery_order->no_faktur,
                'created_by_id' => auth()->user()->id
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $delivery_item = $delivery_items[$i];
                $product = $products[$i];
                $price = $prices[$i];
                $quantity = $quantities[$i];
                $subtotal = $subtotals[$i];
                $diskon = $diskons[$i];
                $subdiscount = $subdiscounts[$i];

                $invoice_item = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'delivery_order_id' => $delivery,
                    'delivery_order_item_id' => $delivery_item,
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'product_id' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $subtotal,
                    'discount' => $diskon,
                    'total_discount' => $subdiscount,
                ]);
            }

            TransactionService::createTransaction($date, 'Faktur from '. $invoice->no_faktur, $salesperson, $semester, 'faktur', $invoice->id, $invoice->no_faktur, $total_price, 'debet');
            TransactionService::createTransaction($date, 'Diskon from '. $invoice->no_faktur, $salesperson, $semester, 'diskon', $invoice->id, $invoice->no_faktur, $total_diskon, 'credit');
            DeliveryService::generateFaktur($delivery);

            DB::commit();

            Alert::success('Success', 'Faktur berhasil di simpan');

            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function storeInvoice(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'type' => 'required',
            'salesperson_id' =>'required',
            // 'semester_id' =>'required',
            'delivery_order_id' =>'required|exists:delivery_orders,id',
            'note' => 'required',
            'nominal' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $type = $validatedData['type'];
        $salesperson = $validatedData['salesperson_id'];
        $semester = setting('current_semester');
        $delivery = $validatedData['delivery_order_id'];
        $note = $validatedData['note'];
        $nominal = $validatedData['nominal'];

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'no_faktur' => Invoice::generateNoInvoice($semester),
                'date' => $date,
                'delivery_order_id' => $delivery,
                'semester_id' => $semester,
                'salesperson_id' => $salesperson,
                'type' => $type,
                'discount' => 0,
                'total' => $nominal,
                'nominal' => $nominal,
                'note' => $note,
                'created_by_id' => auth()->user()->id
            ]);

            $note = 'Faktur From '. $invoice->no_faktur. '. Catatan :'. $note;

            TransactionService::createTransaction($date, $note, $salesperson, $semester, 'faktur', $invoice->id, $invoice->no_faktur, $nominal, 'debet');

            DB::commit();

            Alert::success('Success', 'Faktur berhasil di simpan');

            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->load('delivery_order', 'semester', 'salesperson');

        $invoice_item = InvoiceItem::with('product', 'product.book', 'delivery_order')->where('invoice_id', $invoice->id)->orderBy('product_id', 'ASC')->get();

        return view('admin.invoices.edit', compact('invoice', 'invoice_item'));
    }

    public function editInvoice(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->load('delivery_order', 'semester', 'salesperson');

        return view('admin.invoices.edit-invoice', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'note' => 'nullable',
            'nominal' => 'numeric|min:1',
            'total_price' => 'numeric|min:1',
            'total_diskon' => 'numeric|min:1',
            'invoice_items' => 'required|array',
            'invoice_items.*' => 'exists:invoice_items,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:1',
            'diskons' => 'nullable|array',
            'diskons.*' => 'numeric|min:0',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'subtotals' => 'required|array',
            'subtotals.*' => 'numeric|min:1',
            'subdiscounts' => 'required|array',
            'subdiscounts.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        // $note = $validatedData['note'];
        $nominal = $validatedData['nominal'];
        $total_price = $validatedData['total_price'];
        $total_diskon = $validatedData['total_diskon'];
        $invoice_items = $validatedData['invoice_items'];
        $products = $validatedData['products'];
        $prices = $validatedData['prices'];
        $quantities = $validatedData['quantities'];
        $subtotals = $validatedData['subtotals'];
        $diskons = $validatedData['diskons'];
        $subdiscounts = $validatedData['subdiscounts'];

        $salesperson = $invoice->salesperson_id;
        $semester = $invoice->semester_id;
        $no_faktur = $invoice->no_faktur;
        $note = $invoice->note;

        DB::beginTransaction();
        try {
            $invoice->update([
                'date' => $date,
                'discount' => $total_diskon,
                'total' => $total_price,
                'nominal' => $nominal,
                'note' => $note
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $invoice_item = $invoice_items[$i];
                $product = $products[$i];
                $price = $prices[$i];
                $quantity = $quantities[$i];
                $subtotal = $subtotals[$i];
                $diskon = $diskons[$i];
                $subdiscount = $subdiscounts[$i];

                InvoiceItem::where('id', $invoice_item)->update([
                    'product_id' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $subtotal,
                    'discount' => $diskon,
                    'total_discount' => $subdiscount,
                ]);
            }

            TransactionService::editTransaction($date, $note, $salesperson, $semester, 'faktur', $invoice->id, $no_faktur, $total_price, 'debet');
            TransactionService::editTransaction($date, $note, $salesperson, $semester, 'diskon', $invoice->id, $no_faktur, $total_diskon, 'credit');

            DB::commit();

            Alert::success('Success', 'Faktur berhasil di simpan');

            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function updateInvoice(Request $request, Invoice $invoice)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'type' => 'required',
            'note' => 'required',
            'nominal' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $type = $validatedData['type'];
        $note = $validatedData['note'];
        $nominal = $validatedData['nominal'];

        $salesperson = $invoice->salesperson_id;
        $semester = $invoice->semester_id;
        $no_faktur = $invoice->no_faktur;

        DB::beginTransaction();
        try {
            $invoice->update([
                'date' => $date,
                'type' => $type,
                'discount' => 0,
                'total' => $nominal,
                'nominal' => $nominal,
                'note' => $note
            ]);

            $note = 'Faktur From '. $no_faktur. '. Catatan :'. $note;

            TransactionService::editTransaction($date, $note, $salesperson, $semester, 'faktur', $invoice->id, $no_faktur, $nominal, 'debet');

            DB::commit();

            Alert::success('Success', 'Faktur berhasil di simpan');

            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->load('delivery_order', 'semester', 'salesperson');

        if ($invoice->type !== 'jual') {
            return view('admin.invoices.show-additional', compact('invoice'));
        }

        $details = InvoiceItem::with('product')->where('invoice_id', $invoice->id)->get();

        $invoice_items = $details->sortBy('product.kelas_id')->sortBy('product.mapel_id')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        return view('admin.invoices.show', compact('invoice', 'invoice_items'));
    }

    public function printFaktur(Invoice $invoice)
    {
        $invoice->load('delivery_order', 'semester', 'salesperson');

        if ($invoice->type !== 'jual') {
            return view('admin.invoices.faktur-additional', compact('invoice'));
        }

        $details = InvoiceItem::with('product')->where('invoice_id', $invoice->id)->get();

        $invoice_items = $details->sortBy('product.kelas_id')->sortBy('product.mapel_id')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        return view('admin.invoices.faktur', compact('invoice', 'invoice_items'));
    }

    public function destroy(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->delete();

        return back();
    }

    public function massDestroy(MassDestroyInvoiceRequest $request)
    {
        $invoices = Invoice::find(request('ids'));

        foreach ($invoices as $invoice) {
            $invoice->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

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
            $query = Invoice::with(['delivery_order', 'semester', 'salesperson'])->select(sprintf('%s.*', (new Invoice)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'invoice_show';
                $editGate      = 'invoice_edit';
                $deleteGate    = 'invoice_delete';
                $crudRoutePart = 'invoices';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_faktur', function ($row) {
                return $row->no_faktur ? $row->no_faktur : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->editColumn('nominal', function ($row) {
                return $row->nominal ? $row->nominal : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        return view('admin.invoices.index');
    }

    public function create()
    {
        abort_if(Gate::denies('invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $delivery_orders = DeliveryOrder::pluck('no_suratjalan', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.invoices.create', compact('delivery_orders', 'salespeople', 'semesters'));
    }

    public function generate(DeliveryOrder $delivery)
    {
        $delivery->load(['semester', 'salesperson']);

        $delivery_item = DeliveryOrderItem::with('delivery_order', 'product', 'product.book', 'sales_order')->where('delivery_order_id', $delivery->id)->orderBy('product_id', 'ASC')->get();

        return view('admin.invoices.generate', compact('delivery', 'delivery_item'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'delivery' =>'required|exists:delivery_orders,id',
            'note' => 'nullable',
            'nominal' => 'numeric|min:1',
            'delivery_items' => 'required|array',
            'delivery_items.*' => 'exists:delivery_order_items,id',
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
        ]);

        $date = $validatedData['date'];
        $delivery = $validatedData['delivery'];
        $note = $validatedData['note'];
        $nominal = $validatedData['nominal'];
        $delivery_items = $validatedData['delivery_items'];
        $products = $validatedData['products'];
        $prices = $validatedData['prices'];
        $diskons = $validatedData['diskons'];
        $quantities = $validatedData['quantities'];
        $subtotals = $validatedData['subtotals'];

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
                'nominal' => $nominal,
                'note' => $note
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $delivery_item = $delivery_items[$i];
                $product = $products[$i];
                $price = $prices[$i];
                $diskon = $diskons[$i];
                $quantity = $quantities[$i];
                $subtotal = $subtotals[$i];

                $invoice_item = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'delivery_order_id' => $delivery,
                    'delivery_order_item_id' => $delivery_item,
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'product_id' => $product,
                    'quantity' => $quantity,
                    'price_unit' => $price,
                    'discount' => $diskon,
                    'price' => ($price - $diskon),
                    'total' => $subtotal
                ]);

            }

            TransactionService::createTransaction($date, $note, $salesperson, $semester, 'faktur', $invoice->id, $invoice->no_faktur, $nominal, 'debet');
            DeliveryService::generateFaktur($delivery);

            DB::commit();

            Alert::success('Success', 'Faktur berhasil di simpan');

            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            DB::rollback();

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

    public function update(Request $request, Invoice $invoice)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'note' => 'nullable',
            'nominal' => 'numeric|min:1',
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
        ]);

        $date = $validatedData['date'];
        $note = $validatedData['note'];
        $nominal = $validatedData['nominal'];
        $invoice_items = $validatedData['invoice_items'];
        $products = $validatedData['products'];
        $prices = $validatedData['prices'];
        $diskons = $validatedData['diskons'];
        $quantities = $validatedData['quantities'];
        $subtotals = $validatedData['subtotals'];

        $salesperson = $invoice->salesperson_id;
        $semester = $invoice->semester_id;
        $no_faktur = $invoice->no_faktur;
        
        DB::beginTransaction();
        try {
            $invoice->update([
                'nominal' => $nominal,
                'note' => $note
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $invoice_item = $invoice_items[$i];
                $product = $products[$i];
                $price = $prices[$i];
                $diskon = $diskons[$i];
                $quantity = $quantities[$i];
                $subtotal = $subtotals[$i];

                InvoiceItem::where('id', $invoice_item)->update([
                    'product_id' => $product,
                    'quantity' => $quantity,
                    'price_unit' => $price,
                    'discount' => $diskon,
                    'price' => ($price - $diskon),
                    'total' => $subtotal
                ]);
            }

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

        return view('admin.invoices.show', compact('invoice'));
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

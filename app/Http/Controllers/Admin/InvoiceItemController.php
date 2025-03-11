<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInvoiceItemRequest;
use App\Http\Requests\StoreInvoiceItemRequest;
use App\Http\Requests\UpdateInvoiceItemRequest;
use App\Models\BookVariant;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Salesperson;
use App\Models\Semester;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class InvoiceItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('invoice_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = InvoiceItem::with(['invoice', 'delivery_order', 'delivery_order_item', 'semester', 'salesperson', 'product'])->select(sprintf('%s.*', (new InvoiceItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'invoice_item_show';
                $editGate      = 'invoice_item_edit';
                $deleteGate    = 'invoice_item_delete';
                $crudRoutePart = 'invoice-items';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('invoice_no_faktur', function ($row) {
                return $row->invoice ? $row->invoice->no_faktur : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('price_unit', function ($row) {
                return $row->price_unit ? $row->price_unit : '';
            });
            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount : '';
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('total', function ($row) {
                return $row->total ? $row->total : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'invoice', 'salesperson', 'product']);

            return $table->make(true);
        }

        return view('admin.invoiceItems.index');
    }

    public function create()
    {
        abort_if(Gate::denies('invoice_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoices = Invoice::pluck('no_faktur', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.invoiceItems.create', compact('invoices', 'products', 'salespeople', 'semesters'));
    }

    public function store(StoreInvoiceItemRequest $request)
    {
        $invoiceItem = InvoiceItem::create($request->all());

        return redirect()->route('admin.invoice-items.index');
    }

    public function edit(InvoiceItem $invoiceItem)
    {
        abort_if(Gate::denies('invoice_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoices = Invoice::pluck('no_faktur', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $invoiceItem->load('invoice', 'delivery_order', 'delivery_order_item', 'semester', 'salesperson', 'product');

        return view('admin.invoiceItems.edit', compact('invoiceItem', 'invoices', 'products', 'salespeople', 'semesters'));
    }

    public function update(UpdateInvoiceItemRequest $request, InvoiceItem $invoiceItem)
    {
        $invoiceItem->update($request->all());

        return redirect()->route('admin.invoice-items.index');
    }

    public function show(InvoiceItem $invoiceItem)
    {
        abort_if(Gate::denies('invoice_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoiceItem->load('invoice', 'delivery_order', 'delivery_order_item', 'semester', 'salesperson', 'product');

        return view('admin.invoiceItems.show', compact('invoiceItem'));
    }

    public function destroy(InvoiceItem $invoiceItem)
    {
        abort_if(Gate::denies('invoice_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoiceItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyInvoiceItemRequest $request)
    {
        $invoiceItems = InvoiceItem::find(request('ids'));

        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

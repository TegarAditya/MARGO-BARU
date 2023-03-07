<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFinishingItemRequest;
use App\Http\Requests\StoreFinishingItemRequest;
use App\Http\Requests\UpdateFinishingItemRequest;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\FinishingItem;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FinishingItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('finishing_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FinishingItem::with(['semester', 'buku', 'product'])->select(sprintf('%s.*', (new FinishingItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'finishing_item_show';
                $editGate      = 'finishing_item_edit';
                $deleteGate    = 'finishing_item_delete';
                $crudRoutePart = 'finishing-items';

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

            $table->addColumn('buku_code', function ($row) {
                return $row->buku ? $row->buku->code : '';
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('cost', function ($row) {
                return $row->cost ? $row->cost : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'buku', 'product']);

            return $table->make(true);
        }

        return view('admin.finishingItems.index');
    }

    public function create()
    {
        abort_if(Gate::denies('finishing_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $bukus = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.finishingItems.create', compact('bukus', 'products', 'semesters'));
    }

    public function store(StoreFinishingItemRequest $request)
    {
        $finishingItem = FinishingItem::create($request->all());

        return redirect()->route('admin.finishing-items.index');
    }

    public function edit(FinishingItem $finishingItem)
    {
        abort_if(Gate::denies('finishing_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $bukus = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishingItem->load('semester', 'buku', 'product');

        return view('admin.finishingItems.edit', compact('bukus', 'finishingItem', 'products', 'semesters'));
    }

    public function update(UpdateFinishingItemRequest $request, FinishingItem $finishingItem)
    {
        $finishingItem->update($request->all());

        return redirect()->route('admin.finishing-items.index');
    }

    public function show(FinishingItem $finishingItem)
    {
        abort_if(Gate::denies('finishing_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishingItem->load('semester', 'buku', 'product');

        return view('admin.finishingItems.show', compact('finishingItem'));
    }

    public function destroy(FinishingItem $finishingItem)
    {
        abort_if(Gate::denies('finishing_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishingItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyFinishingItemRequest $request)
    {
        $finishingItems = FinishingItem::find(request('ids'));

        foreach ($finishingItems as $finishingItem) {
            $finishingItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

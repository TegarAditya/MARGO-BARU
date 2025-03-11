<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCetakItemRequest;
use App\Http\Requests\StoreCetakItemRequest;
use App\Http\Requests\UpdateCetakItemRequest;
use App\Models\BookVariant;
use App\Models\CetakItem;
use App\Models\Halaman;
use App\Models\Material;
use App\Models\Semester;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CetakItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('cetak_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CetakItem::with(['semester', 'product', 'halaman', 'plate', 'paper'])->select(sprintf('%s.*', (new CetakItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'cetak_item_show';
                $editGate      = 'cetak_item_edit';
                $deleteGate    = 'cetak_item_delete';
                $crudRoutePart = 'cetak-items';

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

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->addColumn('halaman_name', function ($row) {
                return $row->halaman ? $row->halaman->name : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('cost', function ($row) {
                return $row->cost ? $row->cost : '';
            });
            $table->addColumn('plate_code', function ($row) {
                return $row->plate ? $row->plate->code : '';
            });

            $table->editColumn('plate_cost', function ($row) {
                return $row->plate_cost ? $row->plate_cost : '';
            });
            $table->addColumn('paper_code', function ($row) {
                return $row->paper ? $row->paper->code : '';
            });

            $table->editColumn('paper_cost', function ($row) {
                return $row->paper_cost ? $row->paper_cost : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'product', 'halaman', 'plate', 'paper']);

            return $table->make(true);
        }

        return view('admin.cetakItems.index');
    }

    public function create()
    {
        abort_if(Gate::denies('cetak_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $plates = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $papers = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.cetakItems.create', compact('halamen', 'papers', 'plates', 'products', 'semesters'));
    }

    public function store(StoreCetakItemRequest $request)
    {
        $cetakItem = CetakItem::create($request->all());

        return redirect()->route('admin.cetak-items.index');
    }

    public function edit(CetakItem $cetakItem)
    {
        abort_if(Gate::denies('cetak_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $plates = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $papers = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cetakItem->load('semester', 'product', 'halaman', 'plate', 'paper');

        return view('admin.cetakItems.edit', compact('cetakItem', 'halamen', 'papers', 'plates', 'products', 'semesters'));
    }

    public function update(UpdateCetakItemRequest $request, CetakItem $cetakItem)
    {
        $cetakItem->update($request->all());

        return redirect()->route('admin.cetak-items.index');
    }

    public function show(CetakItem $cetakItem)
    {
        abort_if(Gate::denies('cetak_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cetakItem->load('semester', 'product', 'halaman', 'plate', 'paper');

        return view('admin.cetakItems.show', compact('cetakItem'));
    }

    public function destroy(CetakItem $cetakItem)
    {
        abort_if(Gate::denies('cetak_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cetakItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyCetakItemRequest $request)
    {
        $cetakItems = CetakItem::find(request('ids'));

        foreach ($cetakItems as $cetakItem) {
            $cetakItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

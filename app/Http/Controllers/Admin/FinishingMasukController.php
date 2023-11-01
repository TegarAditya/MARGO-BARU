<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFinishingMasukRequest;
use App\Http\Requests\StoreFinishingMasukRequest;
use App\Http\Requests\UpdateFinishingMasukRequest;
use App\Models\BookVariant;
use App\Models\FinishingItem;
use App\Models\FinishingMasuk;
use App\Models\Semester;
use App\Models\User;
use App\Models\Vendor;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FinishingMasukController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('finishing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FinishingMasuk::select('no_spk', 'date', 'vendor_id')->distinct()->with(['vendor']);

            if (!empty($request->vendor)) {
                $query->where('vendor_id', $request->vendor);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $show = FinishingMasuk::where('no_spk', $row->no_spk)->first();

                return '
                    <a class="px-1" href="'.route('admin.finishing-masuks.show', $show->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('no_spk', function ($row) {
                return $row->no_spk ? $row->no_spk : '';
            });

            $table->addColumn('vendor', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'vendor']);

            return $table->make(true);
        }

        $vendors = Vendor::where('type', 'finishing')->get()->pluck('full_name', 'id')->prepend('All', '');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.finishingMasuks.index', compact('vendors', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('finishing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing_items = FinishingItem::pluck('quantity', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.finishingMasuks.create', compact('created_bies', 'finishing_items', 'products', 'semesters', 'updated_bies', 'vendors'));
    }

    public function store(StoreFinishingMasukRequest $request)
    {
        $finishingMasuk = FinishingMasuk::create($request->all());

        return redirect()->route('admin.finishing-masuks.index');
    }

    public function edit(FinishingMasuk $finishingMasuk)
    {
        abort_if(Gate::denies('finishing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing_items = FinishingItem::pluck('quantity', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishingMasuk->load('vendor', 'semester', 'finishing_item', 'product', 'created_by', 'updated_by');

        return view('admin.finishingMasuks.edit', compact('created_bies', 'finishingMasuk', 'finishing_items', 'products', 'semesters', 'updated_bies', 'vendors'));
    }

    public function update(UpdateFinishingMasukRequest $request, FinishingMasuk $finishingMasuk)
    {
        $finishingMasuk->update($request->all());

        return redirect()->route('admin.finishing-masuks.index');
    }

    public function show(FinishingMasuk $finishingMasuk)
    {
        abort_if(Gate::denies('finishing_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishingMasuk->load('vendor', 'semester', 'created_by', 'updated_by');

        $finishing_items = FinishingMasuk::with('product')->where('no_spk', $finishingMasuk->no_spk)->get();

        return view('admin.finishingMasuks.show', compact('finishingMasuk', 'finishing_items'));
    }

    public function destroy(FinishingMasuk $finishingMasuk)
    {
        abort_if(Gate::denies('finishing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishingMasuk->delete();

        return back();
    }

    public function massDestroy(MassDestroyFinishingMasukRequest $request)
    {
        $finishingMasuks = FinishingMasuk::find(request('ids'));

        foreach ($finishingMasuks as $finishingMasuk) {
            $finishingMasuk->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

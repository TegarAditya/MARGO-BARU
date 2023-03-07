<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCetakRequest;
use App\Http\Requests\StoreCetakRequest;
use App\Http\Requests\UpdateCetakRequest;
use App\Models\Cetak;
use App\Models\Semester;
use App\Models\Vendor;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CetakController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('cetak_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Cetak::with(['semester', 'vendor'])->select(sprintf('%s.*', (new Cetak)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'cetak_show';
                $editGate      = 'cetak_edit';
                $deleteGate    = 'cetak_delete';
                $crudRoutePart = 'cetaks';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_spc', function ($row) {
                return $row->no_spc ? $row->no_spc : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('vendor_name', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? Cetak::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('total_cost', function ($row) {
                return $row->total_cost ? $row->total_cost : '';
            });
            $table->editColumn('total_oplah', function ($row) {
                return $row->total_oplah ? $row->total_oplah : '';
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'vendor']);

            return $table->make(true);
        }

        return view('admin.cetaks.index');
    }

    public function create()
    {
        abort_if(Gate::denies('cetak_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.cetaks.create', compact('semesters', 'vendors'));
    }

    public function store(StoreCetakRequest $request)
    {
        $cetak = Cetak::create($request->all());

        return redirect()->route('admin.cetaks.index');
    }

    public function edit(Cetak $cetak)
    {
        abort_if(Gate::denies('cetak_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cetak->load('semester', 'vendor');

        return view('admin.cetaks.edit', compact('cetak', 'semesters', 'vendors'));
    }

    public function update(UpdateCetakRequest $request, Cetak $cetak)
    {
        $cetak->update($request->all());

        return redirect()->route('admin.cetaks.index');
    }

    public function show(Cetak $cetak)
    {
        abort_if(Gate::denies('cetak_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cetak->load('semester', 'vendor');

        return view('admin.cetaks.show', compact('cetak'));
    }

    public function destroy(Cetak $cetak)
    {
        abort_if(Gate::denies('cetak_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cetak->delete();

        return back();
    }

    public function massDestroy(MassDestroyCetakRequest $request)
    {
        $cetaks = Cetak::find(request('ids'));

        foreach ($cetaks as $cetak) {
            $cetak->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

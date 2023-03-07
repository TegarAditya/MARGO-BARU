<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFinishingRequest;
use App\Http\Requests\StoreFinishingRequest;
use App\Http\Requests\UpdateFinishingRequest;
use App\Models\Finishing;
use App\Models\Semester;
use App\Models\Vendor;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FinishingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('finishing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Finishing::with(['semester', 'vendor'])->select(sprintf('%s.*', (new Finishing)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'finishing_show';
                $editGate      = 'finishing_edit';
                $deleteGate    = 'finishing_delete';
                $crudRoutePart = 'finishings';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_spk', function ($row) {
                return $row->no_spk ? $row->no_spk : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('vendor_name', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
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

        return view('admin.finishings.index');
    }

    public function create()
    {
        abort_if(Gate::denies('finishing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.finishings.create', compact('semesters', 'vendors'));
    }

    public function store(StoreFinishingRequest $request)
    {
        $finishing = Finishing::create($request->all());

        return redirect()->route('admin.finishings.index');
    }

    public function edit(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing->load('semester', 'vendor');

        return view('admin.finishings.edit', compact('finishing', 'semesters', 'vendors'));
    }

    public function update(UpdateFinishingRequest $request, Finishing $finishing)
    {
        $finishing->update($request->all());

        return redirect()->route('admin.finishings.index');
    }

    public function show(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishing->load('semester', 'vendor');

        return view('admin.finishings.show', compact('finishing'));
    }

    public function destroy(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishing->delete();

        return back();
    }

    public function massDestroy(MassDestroyFinishingRequest $request)
    {
        $finishings = Finishing::find(request('ids'));

        foreach ($finishings as $finishing) {
            $finishing->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

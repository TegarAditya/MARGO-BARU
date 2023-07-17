<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyGroupAreaRequest;
use App\Http\Requests\StoreGroupAreaRequest;
use App\Http\Requests\UpdateGroupAreaRequest;
use App\Models\GroupArea;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class GroupAreaController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('group_area_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = GroupArea::query()->select(sprintf('%s.*', (new GroupArea)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'group_area_show';
                $editGate      = 'group_area_edit';
                $deleteGate    = 'group_area_delete';
                $crudRoutePart = 'group-areas';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('provinsi', function ($row) {
                return $row->provinsi ? GroupArea::PROVINSI_SELECT[$row->provinsi] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.groupAreas.index');
    }

    public function create()
    {
        abort_if(Gate::denies('group_area_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.groupAreas.create');
    }

    public function store(StoreGroupAreaRequest $request)
    {
        $groupArea = GroupArea::create($request->all());

        return redirect()->route('admin.group-areas.index');
    }

    public function edit(GroupArea $groupArea)
    {
        abort_if(Gate::denies('group_area_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.groupAreas.edit', compact('groupArea'));
    }

    public function update(UpdateGroupAreaRequest $request, GroupArea $groupArea)
    {
        $groupArea->update($request->all());

        return redirect()->route('admin.group-areas.index');
    }

    public function show(GroupArea $groupArea)
    {
        abort_if(Gate::denies('group_area_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.groupAreas.show', compact('groupArea'));
    }

    public function destroy(GroupArea $groupArea)
    {
        abort_if(Gate::denies('group_area_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $groupArea->delete();

        return back();
    }

    public function massDestroy(MassDestroyGroupAreaRequest $request)
    {
        $groupAreas = GroupArea::find(request('ids'));

        foreach ($groupAreas as $groupArea) {
            $groupArea->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

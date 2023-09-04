<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyGroupAreaRequest;
use App\Http\Requests\StoreGroupAreaRequest;
use App\Http\Requests\UpdateGroupAreaRequest;
use App\Models\GroupArea;
use App\Models\MarketingArea;
use Gate;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;

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

        $marketing_areas = MarketingArea::pluck('name', 'id');

        return view('admin.groupAreas.create', compact('marketing_areas'));
    }

    public function store(StoreGroupAreaRequest $request)
    {
        DB::beginTransaction();
        try {
            $groupArea = GroupArea::create($request->all());

            if ($request->marketing_areas <> '')
            {
                MarketingArea::whereIn('id', $request->marketing_areas)->update([
                    'group_area_id' => $groupArea->id
                ]);
            }

            DB::commit();

            Alert::success('Success', 'Group Area berhasil di simpan');

            return redirect()->route('admin.group-areas.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(GroupArea $groupArea)
    {
        abort_if(Gate::denies('group_area_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketing_areas = MarketingArea::pluck('name', 'id');

        return view('admin.groupAreas.edit', compact('groupArea', 'marketing_areas'));
    }

    public function update(UpdateGroupAreaRequest $request, GroupArea $groupArea)
    {
        DB::beginTransaction();
        try {
            $groupArea->update($request->all());

            $set = $groupArea->marketing_areas()->pluck('id')->toArray();
            if ($set <> '')
            {
                MarketingArea::whereIn('id', $set)->update([
                    'group_area_id' => null
                ]);
            }

            if ($request->marketing_areas <> '')
            {
                MarketingArea::whereIn('id', $request->marketing_areas)->update([
                    'group_area_id' => $groupArea->id
                ]);
            }

            DB::commit();

            Alert::success('Success', 'Group Area berhasil di simpan');

            return redirect()->route('admin.group-areas.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMarketingAreaRequest;
use App\Http\Requests\StoreMarketingAreaRequest;
use App\Http\Requests\UpdateMarketingAreaRequest;
use App\Models\GroupArea;
use App\Models\MarketingArea;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MarketingAreaController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('marketing_area_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MarketingArea::with(['group_area'])->select(sprintf('%s.*', (new MarketingArea)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'marketing_area_show';
                $editGate      = 'marketing_area_edit';
                $deleteGate    = 'marketing_area_delete';
                $crudRoutePart = 'marketing-areas';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->addColumn('group_area_code', function ($row) {
                return $row->group_area ? $row->group_area->code : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'group_area']);

            return $table->make(true);
        }

        return view('admin.marketingAreas.index');
    }

    public function create()
    {
        abort_if(Gate::denies('marketing_area_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $group_areas = GroupArea::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.marketingAreas.create', compact('group_areas'));
    }

    public function store(StoreMarketingAreaRequest $request)
    {
        $marketingArea = MarketingArea::create($request->all());

        return redirect()->route('admin.marketing-areas.index');
    }

    public function edit(MarketingArea $marketingArea)
    {
        abort_if(Gate::denies('marketing_area_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $group_areas = GroupArea::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $marketingArea->load('group_area');

        return view('admin.marketingAreas.edit', compact('group_areas', 'marketingArea'));
    }

    public function update(UpdateMarketingAreaRequest $request, MarketingArea $marketingArea)
    {
        $marketingArea->update($request->all());

        return redirect()->route('admin.marketing-areas.index');
    }

    public function show(MarketingArea $marketingArea)
    {
        abort_if(Gate::denies('marketing_area_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketingArea->load('group_area');

        return view('admin.marketingAreas.show', compact('marketingArea'));
    }

    public function destroy(MarketingArea $marketingArea)
    {
        abort_if(Gate::denies('marketing_area_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketingArea->delete();

        return back();
    }

    public function massDestroy(MassDestroyMarketingAreaRequest $request)
    {
        $marketingAreas = MarketingArea::find(request('ids'));

        foreach ($marketingAreas as $marketingArea) {
            $marketingArea->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

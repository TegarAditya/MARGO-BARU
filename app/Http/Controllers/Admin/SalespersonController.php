<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroySalespersonRequest;
use App\Http\Requests\StoreSalespersonRequest;
use App\Http\Requests\UpdateSalespersonRequest;
use App\Models\MarketingArea;
use App\Models\Salesperson;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;
use Excel;
use App\Imports\SalespersonImport;
use App\Exports\SalespersonTemplate;

class SalespersonController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('salesperson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Salesperson::with(['marketing_area'])->select(sprintf('%s.*', (new Salesperson)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'salesperson_show';
                $editGate      = 'salesperson_edit';
                $deleteGate    = 'salesperson_delete';
                $crudRoutePart = 'salespeople';

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
            $table->addColumn('marketing_area_name', function ($row) {
                return $row->marketing_area ? $row->marketing_area->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'marketing_area']);

            return $table->make(true);
        }

        return view('admin.salespeople.index');
    }

    public function create()
    {
        abort_if(Gate::denies('salesperson_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketing_areas = MarketingArea::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.salespeople.create', compact('marketing_areas'));
    }

    public function store(StoreSalespersonRequest $request)
    {
        $salesperson = Salesperson::create($request->all());

        return redirect()->route('admin.salespeople.index');
    }

    public function edit(Salesperson $salesperson)
    {
        abort_if(Gate::denies('salesperson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketing_areas = MarketingArea::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salesperson->load('marketing_area');

        return view('admin.salespeople.edit', compact('marketing_areas', 'salesperson'));
    }

    public function update(UpdateSalespersonRequest $request, Salesperson $salesperson)
    {
        $salesperson->update($request->all());

        return redirect()->route('admin.salespeople.index');
    }

    public function show(Salesperson $salesperson)
    {
        abort_if(Gate::denies('salesperson_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesperson->load('marketing_area');

        return view('admin.salespeople.show', compact('salesperson'));
    }

    public function destroy(Salesperson $salesperson)
    {
        abort_if(Gate::denies('salesperson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesperson->delete();

        return back();
    }

    public function massDestroy(MassDestroySalespersonRequest $request)
    {
        $salespeople = Salesperson::find(request('ids'));

        foreach ($salespeople as $salesperson) {
            $salesperson->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        Excel::import(new SalespersonImport(), $file);

        Alert::success('Success', 'Sales berhasil di import');
        return redirect()->back();
    }

    public function template_import() {
        $filepath = public_path('import-template\SALESPERSON_TEMPLATE.xlsx');
        return \Response::download($filepath);
    }
}

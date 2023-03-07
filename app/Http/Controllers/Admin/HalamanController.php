<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyHalamanRequest;
use App\Http\Requests\StoreHalamanRequest;
use App\Http\Requests\UpdateHalamanRequest;
use App\Models\Halaman;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class HalamanController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('halaman_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Halaman::query()->select(sprintf('%s.*', (new Halaman)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'halaman_show';
                $editGate      = 'halaman_edit';
                $deleteGate    = 'halaman_delete';
                $crudRoutePart = 'halamen';

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

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.halamen.index');
    }

    public function create()
    {
        abort_if(Gate::denies('halaman_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.halamen.create');
    }

    public function store(StoreHalamanRequest $request)
    {
        $halaman = Halaman::create($request->all());

        return redirect()->route('admin.halamen.index');
    }

    public function edit(Halaman $halaman)
    {
        abort_if(Gate::denies('halaman_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.halamen.edit', compact('halaman'));
    }

    public function update(UpdateHalamanRequest $request, Halaman $halaman)
    {
        $halaman->update($request->all());

        return redirect()->route('admin.halamen.index');
    }

    public function show(Halaman $halaman)
    {
        abort_if(Gate::denies('halaman_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.halamen.show', compact('halaman'));
    }

    public function destroy(Halaman $halaman)
    {
        abort_if(Gate::denies('halaman_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $halaman->delete();

        return back();
    }

    public function massDestroy(MassDestroyHalamanRequest $request)
    {
        $halamen = Halaman::find(request('ids'));

        foreach ($halamen as $halaman) {
            $halaman->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyKelaRequest;
use App\Http\Requests\StoreKelaRequest;
use App\Http\Requests\UpdateKelaRequest;
use App\Models\Kelas;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('kela_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Kelas::query()->select(sprintf('%s.*', (new Kelas)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'kela_show';
                $editGate      = 'kela_edit';
                $deleteGate    = 'kela_delete';
                $crudRoutePart = 'kelas';

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

        return view('admin.kelas.index');
    }

    public function create()
    {
        abort_if(Gate::denies('kela_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kelas.create');
    }

    public function store(StoreKelaRequest $request)
    {
        $kela = Kelas::create($request->all());

        return redirect()->route('admin.kelas.index');
    }

    public function edit(Kelas $kela)
    {
        abort_if(Gate::denies('kela_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kelas.edit', compact('kela'));
    }

    public function update(UpdateKelaRequest $request, Kelas $kela)
    {
        $kela->update($request->all());

        return redirect()->route('admin.kelas.index');
    }

    public function show(Kelas $kela)
    {
        abort_if(Gate::denies('kela_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kelas.show', compact('kela'));
    }

    public function destroy(Kelas $kela)
    {
        abort_if(Gate::denies('kela_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kela->delete();

        return back();
    }

    public function massDestroy(MassDestroyKelaRequest $request)
    {
        $kelas = Kelas::find(request('ids'));

        foreach ($kelas as $kela) {
            $kela->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

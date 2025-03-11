<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMapelRequest;
use App\Http\Requests\StoreMapelRequest;
use App\Http\Requests\UpdateMapelRequest;
use App\Models\Mapel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class MapelController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('mapel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Mapel::query()->select(sprintf('%s.*', (new Mapel)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'mapel_show';
                $editGate      = 'mapel_edit';
                $deleteGate    = 'mapel_delete';
                $crudRoutePart = 'mapels';

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

        return view('admin.mapels.index');
    }

    public function create()
    {
        abort_if(Gate::denies('mapel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mapels.create');
    }

    public function store(StoreMapelRequest $request)
    {
        $mapel = Mapel::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.mapels.index');
    }

    public function edit(Mapel $mapel)
    {
        abort_if(Gate::denies('mapel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mapels.edit', compact('mapel'));
    }

    public function update(UpdateMapelRequest $request, Mapel $mapel)
    {
        $mapel->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.mapels.index');
    }

    public function show(Mapel $mapel)
    {
        abort_if(Gate::denies('mapel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mapels.show', compact('mapel'));
    }

    public function destroy(Mapel $mapel)
    {
        abort_if(Gate::denies('mapel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($mapel->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Mapel telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $mapel->delete();

        return back();
    }

    public function massDestroy(MassDestroyMapelRequest $request)
    {
        $mapels = Mapel::find(request('ids'));

        foreach ($mapels as $mapel) {
            $mapel->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

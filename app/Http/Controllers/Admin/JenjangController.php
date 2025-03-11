<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyJenjangRequest;
use App\Http\Requests\StoreJenjangRequest;
use App\Http\Requests\UpdateJenjangRequest;
use App\Models\Jenjang;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class JenjangController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('jenjang_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Jenjang::query()->select(sprintf('%s.*', (new Jenjang)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'jenjang_show';
                $editGate      = 'jenjang_edit';
                $deleteGate    = 'jenjang_delete';
                $crudRoutePart = 'jenjangs';

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

        return view('admin.jenjangs.index');
    }

    public function create()
    {
        abort_if(Gate::denies('jenjang_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.jenjangs.create');
    }

    public function store(StoreJenjangRequest $request)
    {
        $jenjang = Jenjang::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.jenjangs.index');
    }

    public function edit(Jenjang $jenjang)
    {
        abort_if(Gate::denies('jenjang_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.jenjangs.edit', compact('jenjang'));
    }

    public function update(UpdateJenjangRequest $request, Jenjang $jenjang)
    {
        $jenjang->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.jenjangs.index');
    }

    public function show(Jenjang $jenjang)
    {
        abort_if(Gate::denies('jenjang_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.jenjangs.show', compact('jenjang'));
    }

    public function destroy(Jenjang $jenjang)
    {
        abort_if(Gate::denies('jenjang_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($jenjang->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Jenjang telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $jenjang->delete();

        return back();
    }

    public function massDestroy(MassDestroyJenjangRequest $request)
    {
        $jenjangs = Jenjang::find(request('ids'));

        foreach ($jenjangs as $jenjang) {
            $jenjang->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

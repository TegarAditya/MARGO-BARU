<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyIsiRequest;
use App\Http\Requests\StoreIsiRequest;
use App\Http\Requests\UpdateIsiRequest;
use App\Models\Isi;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;

class IsiController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('isi_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Isi::query()->select(sprintf('%s.*', (new Isi)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'isi_show';
                $editGate      = 'isi_edit';
                $deleteGate    = 'isi_delete';
                $crudRoutePart = 'isis';

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

        return view('admin.isis.index');
    }

    public function create()
    {
        abort_if(Gate::denies('isi_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.isis.create');
    }

    public function store(StoreIsiRequest $request)
    {
        $isi = Isi::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.isis.index');
    }

    public function edit(Isi $isi)
    {
        abort_if(Gate::denies('isi_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.isis.edit', compact('isi'));
    }

    public function update(UpdateIsiRequest $request, Isi $isi)
    {
        $isi->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.isis.index');
    }

    public function show(Isi $isi)
    {
        abort_if(Gate::denies('isi_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.isis.show', compact('isi'));
    }

    public function destroy(Isi $isi)
    {
        abort_if(Gate::denies('isi_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($isi->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Isi telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $isi->delete();

        return back();
    }

    public function massDestroy(MassDestroyIsiRequest $request)
    {
        $isis = Isi::find(request('ids'));

        foreach ($isis as $isi) {
            $isi->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

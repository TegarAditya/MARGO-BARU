<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyHalamanRequest;
use App\Http\Requests\StoreHalamanRequest;
use App\Http\Requests\UpdateHalamanRequest;
use App\Models\Halaman;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Excel;
use App\Imports\HalamanImport;

class HalamanController extends Controller
{

    public function index(Request $request)
    {
        abort_if(Gate::denies('halaman_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Halaman::query()->select(sprintf('%s.*', (new Halaman)->table))->latest();
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'halaman_show';
                $editGate      = 'halaman_edit';
                $deleteGate    = 'halaman_delete';
                $crudRoutePart = 'halaman';

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
            $table->editColumn('value', function ($row) {
                return $row->value ? $row->value : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.halaman.index');
    }

    public function create()
    {
        abort_if(Gate::denies('halaman_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.halaman.create');
    }

    public function store(StoreHalamanRequest $request)
    {
        $halaman = Halaman::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.halaman.index');
    }

    public function edit(Halaman $halaman)
    {
        abort_if(Gate::denies('halaman_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.halaman.edit', compact('halaman'));
    }

    public function update(UpdateHalamanRequest $request, Halaman $halaman)
    {
        $halaman->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.halaman.index');
    }

    public function show(Halaman $halaman)
    {
        abort_if(Gate::denies('halaman_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.halaman.show', compact('halaman'));
    }

    public function destroy(Halaman $halaman)
    {
        abort_if(Gate::denies('halaman_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($halaman->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Halaman telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

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

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        Excel::import(new HalamanImport(), $file);

        Alert::success('Success', 'Halaman berhasil di import');
        return redirect()->back();
    }
}

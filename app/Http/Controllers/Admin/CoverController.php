<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyCoverRequest;
use App\Http\Requests\StoreCoverRequest;
use App\Http\Requests\UpdateCoverRequest;
use App\Models\Cover;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class CoverController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('cover_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Cover::query()->select(sprintf('%s.*', (new Cover)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'cover_show';
                $editGate      = 'cover_edit';
                $deleteGate    = 'cover_delete';
                $crudRoutePart = 'covers';

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

        return view('admin.covers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('cover_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.covers.create');
    }

    public function store(StoreCoverRequest $request)
    {
        $cover = Cover::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.covers.index');
    }

    public function edit(Cover $cover)
    {
        abort_if(Gate::denies('cover_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.covers.edit', compact('cover'));
    }

    public function update(UpdateCoverRequest $request, Cover $cover)
    {
        $cover->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.covers.index');
    }

    public function show(Cover $cover)
    {
        abort_if(Gate::denies('cover_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.covers.show', compact('cover'));
    }

    public function destroy(Cover $cover)
    {
        abort_if(Gate::denies('cover_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($cover->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Cover telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $cover->delete();

        Alert::success('Berhasil', 'Data berhasil dihapus');

        return back();
    }

    public function massDestroy(MassDestroyCoverRequest $request)
    {
        $covers = Cover::find(request('ids'));

        foreach ($covers as $cover) {
            $cover->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

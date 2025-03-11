<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyKurikulumRequest;
use App\Http\Requests\StoreKurikulumRequest;
use App\Http\Requests\UpdateKurikulumRequest;
use App\Models\Kurikulum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class KurikulumController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('kurikulum_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Kurikulum::query()->select(sprintf('%s.*', (new Kurikulum)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'kurikulum_show';
                $editGate      = 'kurikulum_edit';
                $deleteGate    = 'kurikulum_delete';
                $crudRoutePart = 'kurikulums';

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

        return view('admin.kurikulums.index');
    }

    public function create()
    {
        abort_if(Gate::denies('kurikulum_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kurikulums.create');
    }

    public function store(StoreKurikulumRequest $request)
    {
        $kurikulum = Kurikulum::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.kurikulums.index');
    }

    public function edit(Kurikulum $kurikulum)
    {
        abort_if(Gate::denies('kurikulum_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kurikulums.edit', compact('kurikulum'));
    }

    public function update(UpdateKurikulumRequest $request, Kurikulum $kurikulum)
    {
        $kurikulum->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.kurikulums.index');
    }

    public function show(Kurikulum $kurikulum)
    {
        abort_if(Gate::denies('kurikulum_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kurikulums.show', compact('kurikulum'));
    }

    public function destroy(Kurikulum $kurikulum)
    {
        abort_if(Gate::denies('kurikulum_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($kurikulum->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Kurikulum telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $kurikulum->delete();

        return back();
    }

    public function massDestroy(MassDestroyKurikulumRequest $request)
    {
        $kurikulums = Kurikulum::find(request('ids'));

        foreach ($kurikulums as $kurikulum) {
            $kurikulum->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

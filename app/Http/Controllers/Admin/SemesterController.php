<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroySemesterRequest;
use App\Http\Requests\StoreSemesterRequest;
use App\Http\Requests\UpdateSemesterRequest;
use App\Models\Semester;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class SemesterController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('semester_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Semester::query()->select(sprintf('%s.*', (new Semester)->table))->orderBy('id', 'DESC');
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'semester_show';
                $editGate      = 'semester_edit';
                $deleteGate    = 'semester_delete';
                $crudRoutePart = 'semesters';

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
            $table->editColumn('type', function ($row) {
                return $row->type ? Semester::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('status', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->status ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'status']);

            return $table->make(true);
        }

        return view('admin.semesters.index');
    }

    public function create()
    {
        abort_if(Gate::denies('semester_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.semesters.create');
    }

    public function store(StoreSemesterRequest $request)
    {
        $semester = Semester::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.semesters.index');
    }

    public function edit(Semester $semester)
    {
        abort_if(Gate::denies('semester_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.semesters.edit', compact('semester'));
    }

    public function update(UpdateSemesterRequest $request, Semester $semester)
    {
        $semester->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.semesters.index');
    }

    public function show(Semester $semester)
    {
        abort_if(Gate::denies('semester_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.semesters.show', compact('semester'));
    }

    public function destroy(Semester $semester)
    {
        abort_if(Gate::denies('semester_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['book_variants'];

        foreach ($relationMethods as $relationMethod) {
            if ($semester->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Semester telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $semester->delete();

        Alert::success('Berhasil', 'Data berhasil dihapus');

        return back();
    }

    public function massDestroy(MassDestroySemesterRequest $request)
    {
        $semesters = Semester::find(request('ids'));

        foreach ($semesters as $semester) {
            $semester->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

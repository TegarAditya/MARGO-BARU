<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBookComponentRequest;
use App\Http\Requests\StoreBookComponentRequest;
use App\Http\Requests\UpdateBookComponentRequest;
use App\Models\BookComponent;
use App\Models\BookVariant;
use App\Models\Cover;
use App\Models\Halaman;
use App\Models\Isi;
use App\Models\Jenjang;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Mapel;
use App\Models\Semester;
use App\Models\Unit;
use App\Models\Warehouse;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BookComponentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('book_component_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BookComponent::select(sprintf('%s.*', (new BookComponent)->table));

            if (!empty($request->type)) {
                $query->where('type', $request->type);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }
            if (!empty($request->jenjang)) {
                $query->where('jenjang_id', $request->jenjang);
            }
            if (!empty($request->isi)) {
                $query->where('isi_id', $request->isi);
            }
            if (!empty($request->cover)) {
                $query->where('cover_id', $request->cover);
            }
            if (!empty($request->kurikulum)) {
                $query->where('kurikulum_id', $request->kurikulum);
            }
            if (!empty($request->kelas)) {
                $query->where('kelas_id', $request->kelas);
            }
            if (!empty($request->mapel)) {
                $query->where('mapel_id', $request->mapel);
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'book_component_show';
                $editGate      = 'book_component_edit';
                $deleteGate    = 'book_component_delete';
                $crudRoutePart = 'book-components';

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
                return $row->type ? BookComponent::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });

            $table->editColumn('material_of', function ($row) {
                $labels = [];
                foreach ($row->material_of as $component) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $component->code);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'jenjang', 'material_of']);

            return $table->make(true);
        }

        $jenjangs = Jenjang::pluck('name', 'id')->prepend('All', '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend('All', '');

        $mapels = Mapel::pluck('name', 'id')->prepend('All', '');

        $kelas = Kelas::pluck('name', 'id')->prepend('All', '');

        $covers = Cover::pluck('name', 'id')->prepend('All', '');

        $isis = Isi::pluck('name', 'id')->prepend('All', '');

        $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.bookComponents.index', compact('covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'isis'));
    }

    public function create()
    {
        abort_if(Gate::denies('book_component_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $isis = Isi::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $covers = Cover::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $warehouses = Warehouse::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $units = Unit::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $components = BookVariant::pluck('code', 'id');

        return view('admin.bookComponents.create', compact('components', 'covers', 'halamen', 'isis', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'units', 'warehouses'));
    }

    public function store(StoreBookComponentRequest $request)
    {
        $bookComponent = BookComponent::create($request->all());
        $bookComponent->material_of()->sync($request->input('components', []));

        return redirect()->route('admin.book-components.index');
    }

    public function edit(BookComponent $bookComponent)
    {
        abort_if(Gate::denies('book_component_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $isis = Isi::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $covers = Cover::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $warehouses = Warehouse::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $units = Unit::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $components = BookVariant::pluck('name', 'id');

        $bookComponent->load('jenjang', 'kurikulum', 'isi', 'cover', 'mapel', 'kelas', 'halaman', 'semester', 'warehouse', 'unit', 'material_of', 'created_by', 'updated_by');

        return view('admin.bookComponents.edit', compact('bookComponent', 'components', 'covers', 'halamen', 'isis', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'units', 'warehouses'));
    }

    public function update(UpdateBookComponentRequest $request, BookComponent $bookComponent)
    {
        $bookComponent->update($request->all());
        $bookComponent->material_of()->sync($request->input('components', []));

        return redirect()->route('admin.book-components.index');
    }

    public function show(BookComponent $bookComponent)
    {
        abort_if(Gate::denies('book_component_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookComponent->load('jenjang', 'kurikulum', 'isi', 'cover', 'mapel', 'kelas', 'halaman', 'semester', 'warehouse', 'unit', 'material_of', 'created_by', 'updated_by');

        return view('admin.bookComponents.show', compact('bookComponent'));
    }

    public function destroy(BookComponent $bookComponent)
    {
        abort_if(Gate::denies('book_component_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookComponent->delete();

        return back();
    }

    public function massDestroy(MassDestroyBookComponentRequest $request)
    {
        $bookComponents = BookComponent::find(request('ids'));

        foreach ($bookComponents as $bookComponent) {
            $bookComponent->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

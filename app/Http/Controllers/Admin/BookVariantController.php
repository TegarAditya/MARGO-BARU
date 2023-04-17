<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBookVariantRequest;
use App\Http\Requests\StoreBookVariantRequest;
use App\Http\Requests\UpdateBookVariantRequest;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\Halaman;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\Unit;
use App\Models\Cover;
use App\Models\Kelas;
use App\Models\Mapel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BookVariantController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('book_variant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BookVariant::with(['book', 'parent', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit'])->select(sprintf('%s.*', (new BookVariant)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'book_variant_show';
                $editGate      = 'book_variant_edit';
                $deleteGate    = 'book_variant_delete';
                $crudRoutePart = 'book-variants';

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
            $table->editColumn('type', function ($row) {
                return $row->type ? BookVariant::TYPE_SELECT[$row->type] : '';
            });
            $table->addColumn('jenjang_code', function ($row) {
                return $row->jenjang ? $row->jenjang->code : '';
            });

            $table->addColumn('buku', function ($row) {
                return $row->book ? BookVariant::TYPE_SELECT[$row->type] . ' - '. $row->book->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('kurikulum_code', function ($row) {
                return $row->kurikulum ? $row->kurikulum->code : '';
            });

            $table->addColumn('halaman_name', function ($row) {
                return $row->halaman ? $row->halaman->name : '';
            });

            $table->editColumn('stock', function ($row) {
                return $row->stock ? $row->stock : '';
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('cost', function ($row) {
                return $row->cost ? $row->cost : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'jenjang', 'semester', 'kurikulum', 'halaman', 'buku']);

            return $table->make(true);
        }

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('name', 'id');

        $covers = Cover::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.bookVariants.index', compact('covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('book_variant_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $books = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $parents = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $units = Unit::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.bookVariants.create', compact('books', 'halamen', 'jenjangs', 'kurikulums', 'parents', 'semesters', 'units'));
    }

    public function store(StoreBookVariantRequest $request)
    {
        $bookVariant = BookVariant::create($request->all());

        return redirect()->route('admin.book-variants.index');
    }

    public function edit(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $books = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $parents = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $units = Unit::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $bookVariant->load('book', 'parent', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit');

        return view('admin.bookVariants.edit', compact('bookVariant', 'books', 'halamen', 'jenjangs', 'kurikulums', 'parents', 'semesters', 'units'));
    }

    public function update(UpdateBookVariantRequest $request, BookVariant $bookVariant)
    {
        $bookVariant->update($request->all());

        return redirect()->route('admin.book-variants.index');
    }

    public function show(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookVariant->load('book', 'parent', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit');

        return view('admin.bookVariants.show', compact('bookVariant'));
    }

    public function destroy(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookVariant->delete();

        return back();
    }

    public function massDestroy(MassDestroyBookVariantRequest $request)
    {
        $bookVariants = BookVariant::find(request('ids'));

        foreach ($bookVariants as $bookVariant) {
            $bookVariant->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getProductList(Request $request)
    {
        $term = $request->input('term');

        $products = BookVariant::where('code', 'like', '%' . $term . '%')->get();

        return response()->json([
            'products' => $products
        ]);
    }
}

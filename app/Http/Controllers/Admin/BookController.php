<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\Cover;
use App\Models\Jenjang;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Mapel;
use App\Models\Semester;
use App\Models\Halaman;
use Gate;
use DB;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;
use Excel;
use App\Imports\BookImport;

class BookController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('book_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Book::with(['jenjang', 'kurikulum', 'mapel', 'kelas', 'cover', 'semester'])->select(sprintf('%s.*', (new Book)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'book_show_hide';
                $editGate      = 'book_edit_hide';
                $deleteGate    = 'book_delete';
                $crudRoutePart = 'books';

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

            $table->addColumn('jenjang_name', function ($row) {
                return $row->jenjang ? $row->jenjang->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('stock', function ($row) {
                return $row->buku ? $row->buku->stock : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'jenjang', 'semester']);

            return $table->make(true);
        }

        return view('admin.books.index');
    }

    public function create()
    {
        abort_if(Gate::denies('book_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('name', 'id');

        $covers = Cover::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.books.create', compact('covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'halamen'));
    }

    public function store(StoreBookRequest $request)
    {
        $kelas = $request->kelas;
        $jenjang_id = $request->jenjang_id;
        $kurikulum_id = $request->kurikulum_id;
        $mapel_id = $request->mapel_id;
        $cover_id = $request->cover_id;
        $semester_id = $request->semester_id;
        $halaman_id = $request->halaman_id;

        DB::beginTransaction();
        try {
            foreach($kelas as $kelas_id) {
                $code = Book::generateCode($jenjang_id, $kurikulum_id, $mapel_id, $kelas_id, $cover_id, $semester_id);
                $name = Book::generateName($jenjang_id, $kurikulum_id, $mapel_id, $kelas_id, $cover_id, $semester_id);

                $buku = Book::create([
                    'code' => $code,
                    'name' => $name,
                    'jenjang_id' => $jenjang_id,
                    'kurikulum_id' => $kurikulum_id,
                    'mapel_id' => $mapel_id,
                    'kelas_id' => $kelas_id,
                    'cover_id' => $cover_id,
                    'semester_id' => $semester_id,
                ]);

                $lks = BookVariant::updateOrCreate([
                    'book_id' => $buku->id,
                    'code' => 'L' . '-' .$code,
                    'type' => 'L',
                ],
                [
                    'name' => 'LKS' . ' - '. $buku->name,
                    'jenjang_id' => $jenjang->id,
                    'semester_id' => $semester->id,
                    'kurikulum_id' => $kurikulum->id,
                    'halaman_id' => $halaman->id,
                    'warehouse_id' => 1,
                    'stock' => $row['stok'],
                    'unit_id' => 1,
                    'price' => $row['harga'],
                    'cost' => $row['hpp'],
                    'status' => 1,
                ]);

                foreach(BookVariant::LKS_TYPE as $key => $label) {
                    $variant = BookVariant::updateOrCreate([
                        'book_id' => $buku->id,
                        'code' => $key . '-' .$code,
                        'type' => $key,
                    ],
                    [
                        'name' => BookVariant::TYPE_SELECT[$key] . ' - '. $buku->name,
                        'parent_id' => $lks->id,
                        'jenjang_id' => $jenjang->id,
                        'semester_id' => $semester->id,
                        'kurikulum_id' => $kurikulum->id,
                        'halaman_id' => $halaman->id,
                        'warehouse_id' => 1,
                        'stock' => 0,
                        'unit_id' => 1,
                        'price' => 0,
                        'cost' => 0,
                        'status' => 1,
                    ]);
                }

                $pg = BookVariant::updateOrCreate([
                    'book_id' => $buku->id,
                    'code' => 'P' . '-' .$code,
                    'type' => 'P',
                ],
                [
                    'name' => 'Pegangan Guru' . ' - '. $buku->name,
                    'jenjang_id' => $jenjang->id,
                    'semester_id' => $semester->id,
                    'kurikulum_id' => $kurikulum->id,
                    'halaman_id' => $halaman->id,
                    'warehouse_id' => 1,
                    'stock' => 0,
                    'unit_id' => 1,
                    'price' => 0,
                    'cost' => 0,
                    'status' => 1,
                ]);

                foreach(BookVariant::PG_TYPE as $key => $label) {
                    $variant = BookVariant::updateOrCreate([
                        'book_id' => $buku->id,
                        'code' => $key . '-' .$code,
                        'type' => $key,
                    ],
                    [
                        'name' => BookVariant::TYPE_SELECT[$key] . ' - '. $buku->name,
                        'parent_id' => $pg->id,
                        'jenjang_id' => $jenjang->id,
                        'semester_id' => $semester->id,
                        'kurikulum_id' => $kurikulum->id,
                        'halaman_id' => $halaman->id,
                        'warehouse_id' => 1,
                        'stock' => 0,
                        'unit_id' => 1,
                        'price' => 0,
                        'cost' => 0,
                        'status' => 1,
                    ]);
                }
            }

            DB::commit();
            Alert::success('Success', 'Buku berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            Alert::error('Error', $e->getMessage());

            return redirect()->back()->withInput();
        }

        // foreach ($request->input('photo', []) as $file) {
        //     $book->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        // }

        // if ($media = $request->input('ck-media', false)) {
        //     Media::whereIn('id', $media)->update(['model_id' => $book->id]);
        // }

        return redirect()->route('admin.books.index');
    }

    public function edit(Book $book)
    {
        abort_if(Gate::denies('book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $covers = Cover::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $book->load('jenjang', 'kurikulum', 'mapel', 'kelas', 'cover', 'semester');

        return view('admin.books.edit', compact('book', 'covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->all());

        if (count($book->photo) > 0) {
            foreach ($book->photo as $media) {
                if (! in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $book->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $book->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.books.index');
    }

    public function show(Book $book)
    {
        abort_if(Gate::denies('book_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $book->load('jenjang', 'kurikulum', 'mapel', 'kelas', 'cover', 'semester');

        return view('admin.books.show', compact('book'));
    }

    public function destroy(Book $book)
    {
        abort_if(Gate::denies('book_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $variants = BookVariant::where('book_id', $book->id)->get();
        foreach($variants as $variant) {
            $variant->delete();
        }

        $book->delete();

        return back();
    }

    public function massDestroy(MassDestroyBookRequest $request)
    {
        $books = Book::find(request('ids'));

        foreach ($books as $book) {
            $book->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('book_create') && Gate::denies('book_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Book();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        try {
            Excel::import(new BookImport(), $file);
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return redirect()->back();
        }

        Alert::success('Success', 'Buku berhasil di import');
        return redirect()->back();
    }

    public function template_import()
    {
        $filepath = public_path('import-template\BOOK_TEMPLATE.xlsx');
        return response()->download($filepath);
    }
}

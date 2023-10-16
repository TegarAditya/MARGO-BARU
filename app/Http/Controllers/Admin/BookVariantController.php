<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
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
use App\Models\Isi;
use App\Models\Cover;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\FinishingItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Alert;

class BookVariantController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('book_variant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BookVariant::with(['book', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit'])->select(sprintf('%s.*', (new BookVariant)->table));

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
                $viewGate      = 'book_variant_show';
                $editGate      = 'book_variant_edit_hidden';
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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? BookVariant::TYPE_SELECT[$row->type] : '';
            });

            $table->addColumn('jenjang_code', function ($row) {
                return $row->jenjang ? $row->jenjang->code : '';
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
                return $row->stock ? $row->stock : 0;
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('cost', function ($row) {
                return $row->cost ? $row->cost : '';
            });
            $table->addColumn('pengedit', function ($row) {
                return $row->pengedit ? $row->pengedit->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'jenjang', 'semester', 'kurikulum', 'halaman', 'buku', 'pengedit']);

            return $table->make(true);
        }

        $jenjangs = Jenjang::pluck('name', 'id')->prepend('All', '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend('All', '');

        $mapels = Mapel::pluck('name', 'id')->prepend('All', '');

        $kelas = Kelas::pluck('name', 'id')->prepend('All', '');

        $covers = Cover::pluck('name', 'id')->prepend('All', '');

        $isis = Isi::pluck('name', 'id')->prepend('All', '');

        $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.bookVariants.index', compact('covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'isis'));
    }

    public function create()
    {
        abort_if(Gate::denies('book_variant_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $books = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $isis = Isi::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $covers = Cover::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $units = Unit::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.bookVariants.create', compact('books', 'covers', 'halamen', 'isis', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'units'));
    }

    public function store(StoreBookVariantRequest $request)
    {
        $bookVariant = BookVariant::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $bookVariant->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $bookVariant->id]);
        }

        return redirect()->route('admin.book-variants.index');
    }

    public function edit(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $books = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $isis = Isi::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $covers = Cover::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $halamen = Halaman::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $units = Unit::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $components = BookVariant::where('warehouse_id', 2)->pluck('name', 'id');

        $bookVariant->load('book', 'jenjang', 'kurikulum', 'isi', 'cover', 'mapel', 'kelas', 'halaman', 'semester', 'warehouse', 'unit');

        return view('admin.bookVariants.edit', compact('bookVariant', 'books', 'covers', 'halamen', 'isis', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'units', 'components'));
    }

    public function update(UpdateBookVariantRequest $request, BookVariant $bookVariant)
    {
        $bookVariant->update($request->all());

        $bookVariant->components()->sync($request->input('components', []));

        if (count($bookVariant->photo) > 0) {
            foreach ($bookVariant->photo as $media) {
                if (! in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $bookVariant->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $bookVariant->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        Alert::success('Success', 'Buku Berhasil Diperbarui');

        return redirect()->route('admin.book-variants.index');
    }

    public function show(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookVariant->load('book', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit', 'components', 'isi', 'cover');

        $stockMovements = StockMovement::with(['product'])->where('product_id', $bookVariant->id)->orderBy('id', 'DESC')->get();

        return view('admin.bookVariants.show', compact('bookVariant', 'stockMovements'));
    }

    public function destroy(BookVariant $bookVariant)
    {
        abort_if(Gate::denies('book_variant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['estimasi', 'estimasi_produksi'];

        foreach ($relationMethods as $relationMethod) {
            if ($bookVariant->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Book Variant telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $bookVariant->delete();

        Alert::success('Success', 'Book Variant berhasil dihapus !');

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

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('book_variant_create') && Gate::denies('book_variant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new BookVariant();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updatePrice(Request $request)
    {
        abort_if(Gate::denies('book_variant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = BookVariant::where('type', 'L');

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

        $book_variants = $query->update([
            'price' => $request->price
        ]);

        Alert::success('Success', $book_variants . ' Produk Ditemukan, Harga berhasil diperbarui');

        return redirect()->route('admin.book-variants.index');
    }

    public function getProducts(Request $request)
    {
        $query = $request->input('q');
        $jenjang = $request->input('jenjang');
        $type = $request->input('type');

        $query = BookVariant::where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                });

        if (!empty($jenjang)) {
            $query->where('jenjang_id', $jenjang);
        }

        if (!empty($type)) {
            if ($type == 'isi') {
                $query->whereIn('type', ['I', 'S']);
            } else if ($type == 'cover') {
                $query->whereIn('type', ['C', 'V']);
            } else {
                $query->whereIn('type', ['L', 'P']);
            }
        }

        $products = $query->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getBooks(Request $request)
    {
        $query = $request->input('q');
        $semester = $request->input('semester');
        $jenjang = $request->input('jenjang');

        $query = BookVariant::whereIn('type', ['L', 'P'])->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC');

        if (!empty($semester)) {
            $query->where('semester_id', $semester);
        }

        if (!empty($jenjang)) {
            $query->where('jenjang_id', $jenjang);
        }

        $products = $query->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getBook(Request $request)
    {
        $id = $request->input('id');

        $product = BookVariant::find($id);
        $product->load('book', 'jenjang', 'cover', 'kurikulum', 'isi');

        return response()->json($product);
    }

    public function getPg(Request $request)
    {
        $product_id = $request->input('id');

        $product = BookVariant::find($product_id);

        $kelengkapan = BookVariant::whereIn('type', ['P', 'K'])
                    ->where('jenjang_id', $product->jenjang_id)
                    ->where('kurikulum_id', $product->kurikulum_id)
                    ->where('mapel_id', $product->mapel_id)
                    ->where('kelas_id', $product->kelas_id)
                    ->where('semester_id', $product->semester_id)
                    ->get();

        $formattedMaterials = [];

        $formattedMaterials[] = [
            'id' => '',
            'text' => 'Belum Tahu',
        ];

        foreach ($kelengkapan as $material) {
            $formattedMaterials[] = [
                'id' => $material->id,
                'text' => $material->kelengkapan_name,
                'halaman' => $material->halaman_id
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getEstimasi(Request $request)
    {
        $query = $request->input('q');
        $estimasi = $request->input('estimasi');
        $jenjang = $request->input('jenjang');

        $query = BookVariant::whereHas('estimasi_items', function ($q) use ($estimasi) {
                    $q->where('estimation_id', $estimasi);
                })->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC');

        if (!empty($jenjang)) {
            $query->where('jenjang_id', $jenjang);
        }

        $products = $query->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoEstimasi(Request $request)
    {
        $id = $request->input('id');
        $estimasi = $request->input('estimasi');

        $product = BookVariant::join('estimation_items', 'estimation_items.product_id', '=', 'book_variants.id')
                ->join('sales_orders', 'sales_orders.salesperson_id', '=', 'estimation_items.salesperson_id')
                ->where('book_variants.id', $id)
                ->where('estimation_items.estimation_id', $estimasi)
                ->first(['book_variants.*','estimation_items.id as estimasi_id', 'estimation_items.quantity as estimasi', 'sales_orders.moved as terkirim']);
        $product->load('book', 'jenjang', 'cover', 'kurikulum', 'isi');

        return response()->json($product);
    }

    public function getDelivery(Request $request)
    {
        $query = $request->input('q');
        $delivery = $request->input('delivery');

        $products = BookVariant::whereHas('dikirim', function ($q) use ($delivery) {
                    $q->where('delivery_order_id', $delivery);
                })->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoDelivery(Request $request)
    {
        $id = $request->input('id');
        $delivery = $request->input('delivery');

        $product = BookVariant::join('delivery_order_items', 'delivery_order_items.product_id', '=', 'book_variants.id')
                ->join('sales_orders', 'sales_orders.id', '=', 'delivery_order_items.sales_order_id')
                ->where('book_variants.id', $id)
                ->where('delivery_order_items.delivery_order_id', $delivery)
                ->first(['book_variants.*', 'delivery_order_items.quantity as quantity', 'delivery_order_items.id as delivery_item_id', 'sales_orders.quantity as estimasi', 'sales_orders.moved as terkirim']);
        $product->load('book', 'jenjang', 'isi', 'cover', 'kurikulum');

        return response()->json($product);
    }

    public function getRetur(Request $request)
    {
        $query = $request->input('q');
        $semester = $request->input('semester');
        $salesperson = $request->input('salesperson');

        $products = BookVariant::whereHas('dikirim', function ($q) use ($semester, $salesperson) {
                    $q->where('semester_id', $semester)
                    ->where('salesperson_id', $salesperson);
                })->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoRetur(Request $request)
    {
        $id = $request->input('id');
        $semester = $request->input('semester');
        $salesperson = $request->input('salesperson');

        $product = BookVariant::join('sales_orders', 'sales_orders.product_id', '=', 'book_variants.id')
                ->where('book_variants.id', $id)
                ->where('sales_orders.semester_id', $semester)
                ->where('sales_orders.salesperson_id', $salesperson)
                ->first(['book_variants.*', 'sales_orders.moved as terkirim',
                    'sales_orders.retur as retur', 'sales_orders.id as order_id'
                ]);

        $product->load('book', 'jenjang', 'isi', 'cover', 'kurikulum');

        return response()->json($product);
    }

    public function getEditRetur(Request $request)
    {
        $query = $request->input('q');
        $retur = $request->input('retur');

        $products = BookVariant::whereHas('diretur', function ($q) use ($retur) {
                    $q->where('retur_id', $retur);
                })->where('code', 'like', "%{$query}%")
                ->orderBy('code', 'ASC')
                ->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoEditRetur(Request $request)
    {
        $id = $request->input('id');
        $retur = $request->input('retur');

        $product = BookVariant::join('return_good_items', 'return_good_items.product_id', '=', 'book_variants.id')
                ->join('sales_orders', 'sales_orders.id', '=', 'return_good_items.sales_order_id')
                ->where('book_variants.id', $id)
                ->where('return_good_items.retur_id', $retur)
                ->first(['book_variants.*', 'return_good_items.quantity as quantity', 'return_good_items.id as retur_item_id',
                    'sales_orders.retur as retur', 'sales_orders.moved as terkirim']);
        $product->load('book', 'jenjang', 'isi', 'cover', 'kurikulum');

        return response()->json($product);
    }

    public function getAdjustment(Request $request)
    {
        $query = $request->input('q');
        $adjustment = $request->input('adjustment');

        $products = BookVariant::whereHas('adjustment', function ($q) use ($adjustment) {
                    $q->where('stock_adjustment_id', $adjustment);
                })->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoAdjustment(Request $request)
    {
        $id = $request->input('id');
        $adjustment = $request->input('adjustment');

        $product = BookVariant::join('stock_adjustment_details', 'stock_adjustment_details.product_id', '=', 'book_variants.id')
                ->where('book_variants.id', $id)
                ->where('stock_adjustment_details.stock_adjustment_id', $adjustment)
                ->first(['book_variants.*', 'stock_adjustment_details.quantity as quantity', 'stock_adjustment_details.id as adjustment_detail_id']);
        $product->load('book', 'jenjang', 'isi', 'cover', 'kurikulum');

        return response()->json($product);
    }

    public function getCetakDefault(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type');
        $jenjang = $request->input('jenjang');
        $cover_isi = $request->input('cover_isi');

        if(empty($type)) {
            return response()->json([]);
        }

        $query = BookVariant::where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                });

        if ($type == 'isi') {
            $query->whereIn('type', ['I', 'S', 'U']);
        } else if ($type == 'cover') {
            $query->whereIn('type', ['C', 'V']);
        } else if ($type == 'finishing') {
            $query->whereIn('type', ['L', 'P', 'K']);
        }

        if (!empty($jenjang)) {
            $query->where('jenjang_id', $jenjang);
        }

        if (!empty($cover_isi)) {
            if ($type == 'isi') {
                $query->where('isi_id', $cover_isi);
            } else if ($type == 'cover') {
                $query->where('cover_id', $cover_isi);
            }
        }

        $products = $query->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getCetak(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type');
        $jenjang = $request->input('jenjang');
        $cover_isi = $request->input('cover_isi');
        $estimasi = $request->input('estimasi') ?? 1;

        if(empty($type)) {
            return response()->json([]);
        }

        $query = BookVariant::where(function($q) use ($query) {
            $q->where('code', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%");
        });

        if ($estimasi) {
            $query->whereHas('estimasi_produksi', function ($q) {
                $q->where('estimasi', '>', 0);
            });
        }

        if ($type == 'isi') {
            $query->whereIn('type', ['I', 'S', 'U']);
        } else if ($type == 'cover') {
            $query->whereIn('type', ['C', 'V']);
        } else if ($type == 'finishing') {
            $query->whereIn('type', ['L', 'P', 'K']);
        }

        if (!empty($jenjang)) {
            $query->where('jenjang_id', $jenjang);
        }

        if (!empty($cover_isi)) {
            if ($type == 'isi') {
                $query->where('isi_id', $cover_isi);
            } else if ($type == 'cover') {
                $query->where('cover_id', $cover_isi);
            }
        }

        $products = $query->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoCetak(Request $request)
    {
        $id = $request->input('id');

        $product = BookVariant::find($id);
        $product->load('book', 'jenjang', 'cover', 'kurikulum', 'estimasi_produksi', 'isi');

        return response()->json($product);
    }

    public function getInfoFinishing(Request $request)
    {
        $id = $request->input('id');

        $product = BookVariant::withMin('components as finishing_stock', 'stock')->with('components')->find($id);
        $product->load('book', 'jenjang', 'cover', 'kurikulum', 'estimasi_produksi', 'isi');

        return response()->json($product);
    }

    public function getListFinishing(Request $request)
    {
        $query = $request->input('q');
        $vendor = $request->input('vendor');
        $jenjang = $request->input('jenjang');

        $query = FinishingItem::join('book_variants', 'book_variants.id', '=', 'finishing_items.product_id')
                    ->join('finishings', 'finishing_items.finishing_id', '=', 'finishings.id')
                    ->where(function($q) use ($query) {
                        $q->where('book_variants.code', 'LIKE', "%{$query}%")
                        ->orWhere('book_variants.name', 'LIKE', "%{$query}%");
                    })
                    ->where('finishing_items.done', 0)
                    ->orderBy('book_variants.code', 'ASC');

                    if (!empty($jenjang)) {
                        $query->where('book_variants.jenjang_id', $jenjang);
                    }

                    if (!empty($vendor)) {
                        $query->where('finishings.vendor_id', $vendor);
                    }

        $products = $query->get(['book_variants.*', 'finishings.no_spk as finishing_spk', 'finishing_items.id as finishing_item_id']);

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'finishing_item_id' => $product->finishing_item_id,
                'finishing_spk' => $product->finishing_spk,
                'text' => $product->code,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoFinishingDetail(Request $request)
    {
        $id = $request->input('id');

        $product = FinishingItem::with('finishing', 'product', 'product.jenjang', 'product.cover', 'product.kurikulum', 'product.isi')->find($id);

        return response()->json($product);
    }
}

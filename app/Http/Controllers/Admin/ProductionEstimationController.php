<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductionEstimationRequest;
use App\Http\Requests\StoreProductionEstimationRequest;
use App\Http\Requests\UpdateProductionEstimationRequest;
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
use App\Models\ProductionEstimation;
use App\Models\EstimationMovement;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;
use Alert;
use DB;
use App\Exports\EstimasiCoverExport;
use App\Exports\ProduksiExport;

class ProductionEstimationController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('production_estimation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductionEstimation::with(['product'])->select(sprintf('%s.*', (new ProductionEstimation)->table))->latest();

            $query->whereHas('product', function ($q) use ($request) {
                if (!empty($request->semester)) {
                    $q->where('semester_id', $request->semester);
                } else {
                    $q->where('semester_id', setting('current_semester'));
                }

                if (!empty($request->type)) {
                    $q->where('type', $request->type);
                }
                if (!empty($request->jenjang)) {
                    $q->where('jenjang_id', $request->jenjang);
                }
                if (!empty($request->isi)) {
                    $q->where('isi_id', $request->isi);
                }
                if (!empty($request->cover)) {
                    $q->where('cover_id', $request->cover);
                }
                if (!empty($request->kurikulum)) {
                    $q->where('kurikulum_id', $request->kurikulum);
                }
                if (!empty($request->kelas)) {
                    $q->where('kelas_id', $request->kelas);
                }
                if (!empty($request->mapel)) {
                    $q->where('mapel_id', $request->mapel);
                }
            });

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.production-estimations.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                ';
                return $btn;
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ?
                '<a class="px-1" href="'.route('admin.book-variants.show', $row->product_id).'" title="Show">
                    <i class="fas fa-eye text-success fa-lg"></i>
                </a>'.$row->product->code
                : '';
            });

            $table->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('estimasi', function ($row) {
                return angka($row->estimasi);
            });
            $table->editColumn('estimasi_baru', function ($row) {
                return angka($row->estimasi_baru);
            });
            $table->editColumn('sales', function ($row) {
                return angka($row->sales);
            });
            $table->editColumn('internal', function ($row) {
                return angka($row->internal);
            });
            $table->editColumn('produksi', function ($row) {
                return angka($row->produksi);
            });
            $table->editColumn('realisasi', function ($row) {
                return angka($row->realisasi);
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'product_code']);

            return $table->make(true);
        }

        $jenjangs = Jenjang::pluck('name', 'id')->prepend('All', '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend('All', '');

        $mapels = Mapel::pluck('name', 'id')->prepend('All', '');

        $kelas = Kelas::pluck('name', 'id')->prepend('All', '');

        $covers = Cover::pluck('name', 'id')->prepend('All', '');

        $isis = Isi::pluck('name', 'id')->prepend('All', '');

        $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.productionEstimations.index', compact('covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters', 'isis'));
    }

    public function create()
    {
        abort_if(Gate::denies('production_estimation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productionEstimations.create', compact('products'));
    }

    public function store(StoreProductionEstimationRequest $request)
    {
        $productionEstimation = ProductionEstimation::create($request->all());

        return redirect()->route('admin.production-estimations.index');
    }

    public function edit(ProductionEstimation $productionEstimation)
    {
        abort_if(Gate::denies('production_estimation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Book::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productionEstimation->load('product');

        return view('admin.productionEstimations.edit', compact('productionEstimation', 'products'));
    }

    public function update(UpdateProductionEstimationRequest $request, ProductionEstimation $productionEstimation)
    {
        $productionEstimation->update($request->all());

        return redirect()->route('admin.production-estimations.index');
    }

    public function show(ProductionEstimation $productionEstimation)
    {
        abort_if(Gate::denies('production_estimation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionEstimation->load('product');

        $estimationMovement = EstimationMovement::with(['product'])->where('reference_type', 'sales_order')->where('product_id', $productionEstimation->product_id)->orderBy('id', 'DESC')->get();

        return view('admin.productionEstimations.show', compact('productionEstimation', 'estimationMovement'));
    }

    public function destroy(ProductionEstimation $productionEstimation)
    {
        abort_if(Gate::denies('production_estimation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionEstimation->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductionEstimationRequest $request)
    {
        $productionEstimations = ProductionEstimation::find(request('ids'));

        foreach ($productionEstimations as $productionEstimation) {
            $productionEstimation->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    // public function jangka(Request $request)
    // {
    //     if ($request->has('date') && $request->date && $dates = explode(' - ', $request->date)) {
    //         $start = Date::parse($dates[0])->startOfDay();
    //         $end = !isset($dates[1]) ? $start->clone()->endOfMonth() : Date::parse($dates[1])->endOfDay();
    //     } else {
    //         $start = Carbon::now()->startOfMonth();
    //         $end = Carbon::now();
    //     }

    //     $semester = setting('current_semester');

    //     $awal = BookVariant::whereHas('estimasi_produksi')->withSum(['movement as in' => function ($q) use ($start, $end) {
    //         $q->whereIn('transaction_type', ['cetak', 'produksi'])->where('movement_type', 'in')->where('movement_date', '<', $start)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
    //     }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
    //         $q->whereIn('transaction_type', ['cetak', 'produksi'])->where('movement_type', 'out')->where('movement_date', '<', $start)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
    //     }], 'quantity')->where(function($q) use ($semester) {
    //         $q->where('semester_id', $semester)
    //         ->orWhere('stock' , '>', 0);
    //     });
    //     // ->whereIn('type', ['L', 'P', 'K']);

    //     if (!empty($request->type)) {
    //         $awal->where('type', $request->type);
    //     }
    //     if (!empty($request->jenjang)) {
    //         $awal->where('jenjang_id', $request->jenjang);
    //     }
    //     if (!empty($request->isi)) {
    //         $awal->where('isi_id', $request->isi);
    //     }
    //     if (!empty($request->cover)) {
    //         $awal->where('cover_id', $request->cover);
    //     }
    //     if (!empty($request->kelas)) {
    //         $awal->where('kelas_id', $request->kelas);
    //     }
    //     if (!empty($request->mapel)) {
    //         $awal->where('mapel_id', $request->mapel);
    //     }

    //     $saldo_awal = $awal->get();

    //     $akhir = BookVariant::whereHas('estimasi_produksi')->withSum(['movement as in' => function ($q) use ($start, $end) {
    //         $q->whereIn('transaction_type', ['cetak', 'produksi'])->where('movement_type', 'in')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
    //     }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
    //         $q->whereIn('transaction_type', ['cetak', 'produksi'])->where('movement_type', 'out')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
    //     }], 'quantity')->where(function($q) use ($semester) {
    //         $q->where('semester_id', $semester)
    //         ->orWhere('stock' , '>', 0);
    //     });
    //     // ->whereIn('type', ['L', 'P', 'K']);

    //     if (!empty($request->type)) {
    //         $akhir->where('type', $request->type);
    //     }
    //     if (!empty($request->jenjang)) {
    //         $akhir->where('jenjang_id', $request->jenjang);
    //     }
    //     if (!empty($request->isi)) {
    //         $akhir->where('isi_id', $request->isi);
    //     }
    //     if (!empty($request->cover)) {
    //         $akhir->where('cover_id', $request->cover);
    //     }
    //     if (!empty($request->kelas)) {
    //         $akhir->where('kelas_id', $request->kelas);
    //     }
    //     if (!empty($request->mapel)) {
    //         $akhir->where('mapel_id', $request->mapel);
    //     }
    //     $saldo_akhir = $akhir->orderBy('semester_id', 'DESC')->orderBy('jenjang_id', 'ASC')->orderBy('mapel_id', 'ASC')->orderBy('kelas_id', 'ASC')->orderBy('cover_id', 'ASC')->get();

    //     return view('admin.productionEstimations.jangka', compact('start', 'end', 'saldo_awal', 'saldo_akhir'));
    // }

    public function jangka(Request $request)
    {
        if ($request->has('date') && $request->date && $dates = explode(' - ', $request->date)) {
            $start = Date::parse($dates[0])->startOfDay();
            $end = !isset($dates[1]) ? $start->clone()->endOfMonth() : Date::parse($dates[1])->endOfDay();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
        }

        $semester = setting('current_semester');

        $akhir = BookVariant::whereHas('estimasi_produksi')->withSum(['movement as in' => function ($q) use ($start, $end) {
            $q->whereIn('transaction_type', ['cetak', 'produksi'])->where('movement_type', 'in')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
            $q->whereIn('transaction_type', ['cetak', 'produksi'])->where('movement_type', 'out')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->where(function($q) use ($semester) {
            $q->where('semester_id', $semester)
            ->orWhere('stock' , '>', 0);
        });
        // ->whereIn('type', ['L', 'P', 'K']);

        if (!empty($request->type)) {
            $akhir->where('type', $request->type);
        }
        if (!empty($request->jenjang)) {
            $akhir->where('jenjang_id', $request->jenjang);
        }
        if (!empty($request->isi)) {
            $akhir->where('isi_id', $request->isi);
        }
        if (!empty($request->cover)) {
            $akhir->where('cover_id', $request->cover);
        }
        if (!empty($request->kelas)) {
            $akhir->where('kelas_id', $request->kelas);
        }
        if (!empty($request->mapel)) {
            $akhir->where('mapel_id', $request->mapel);
        }
        $saldo_akhir = $akhir->orderBy('semester_id', 'DESC')->orderBy('jenjang_id', 'ASC')->orderBy('mapel_id', 'ASC')->orderBy('kelas_id', 'ASC')->orderBy('cover_id', 'ASC')->get();

        if ($request->has('export')) {
            return (new ProduksiExport($saldo_akhir))->download('REKAP PRODUKSI PERIODE ' . $start->format('d-F-Y') .' sd '. $end->format('d-F-Y') .'.xlsx');
        } else {
            return view('admin.productionEstimations.jangka', compact('start', 'end', 'saldo_akhir'));
        }
    }

    public function coverExport(Request $request)
    {
        $semester = $request->semester_export_cover;
        return (new EstimasiCoverExport($semester))->download('ESTIMASI PRODUKSI BY COVER ' . str_replace(array("/", "\\", ":", "*", "?", "«", "<", ">", "|"), "-", getSemesterName($semester)) .'.xlsx');
    }
}

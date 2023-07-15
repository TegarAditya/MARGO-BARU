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
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductionEstimationController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('production_estimation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductionEstimation::with(['product'])->select(sprintf('%s.*', (new ProductionEstimation)->table))->latest();

            $query->whereHas('product', function ($q) use ($request) {
                if (!empty($request->type)) {
                    $q->where('type', $request->type);
                }
                if (!empty($request->semester)) {
                    $q->where('semester_id', $request->semester);
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
                $viewGate      = 'production_estimation_show';
                $editGate      = 'production_estimation_edit';
                $deleteGate    = 'production_estimation_delete';
                $crudRoutePart = 'production-estimations';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('estimasi', function ($row) {
                return $row->estimasi ? angka($row->estimasi) : '';
            });
            $table->editColumn('isi', function ($row) {
                return $row->isi ? $row->isi : '';
            });
            $table->editColumn('cover', function ($row) {
                return $row->cover ? $row->cover : '';
            });
            $table->editColumn('finishing', function ($row) {
                return $row->finishing ? $row->finishing : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'product']);

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

        return view('admin.productionEstimations.show', compact('productionEstimation'));
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
}

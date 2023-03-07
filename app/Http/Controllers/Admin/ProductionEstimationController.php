<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductionEstimationRequest;
use App\Http\Requests\StoreProductionEstimationRequest;
use App\Http\Requests\UpdateProductionEstimationRequest;
use App\Models\Book;
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
            $query = ProductionEstimation::with(['product'])->select(sprintf('%s.*', (new ProductionEstimation)->table));
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

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('estimasi', function ($row) {
                return $row->estimasi ? $row->estimasi : '';
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

        return view('admin.productionEstimations.index');
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyVendorCostRequest;
use App\Http\Requests\StoreVendorCostRequest;
use App\Http\Requests\UpdateVendorCostRequest;
use App\Models\Vendor;
use App\Models\VendorCost;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;

class VendorCostController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('vendor_cost_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = VendorCost::with(['vendor'])->select(sprintf('%s.*', (new VendorCost)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'vendor_cost_show';
                $editGate      = 'vendor_cost_edit';
                $deleteGate    = 'vendor_cost_delete';
                $crudRoutePart = 'vendor-costs';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('vendor_code', function ($row) {
                return $row->vendor ? $row->vendor->code : '';
            });

            $table->editColumn('key', function ($row) {
                return $row->key ? $row->key : '';
            });
            $table->editColumn('value', function ($row) {
                return $row->value ? $row->value : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'vendor']);

            return $table->make(true);
        }

        return view('admin.vendorCosts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('vendor_cost_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.vendorCosts.create', compact('vendors'));
    }

    public function store(StoreVendorCostRequest $request)
    {
        $vendorCost = VendorCost::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.vendor-costs.index');
    }

    public function edit(VendorCost $vendorCost)
    {
        abort_if(Gate::denies('vendor_cost_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendorCost->load('vendor');

        return view('admin.vendorCosts.edit', compact('vendorCost', 'vendors'));
    }

    public function update(UpdateVendorCostRequest $request, VendorCost $vendorCost)
    {
        $vendorCost->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.vendor-costs.index');
    }

    public function show(VendorCost $vendorCost)
    {
        abort_if(Gate::denies('vendor_cost_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendorCost->load('vendor');

        return view('admin.vendorCosts.show', compact('vendorCost'));
    }

    public function destroy(VendorCost $vendorCost)
    {
        abort_if(Gate::denies('vendor_cost_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendorCost->delete();

        return back();
    }

    public function massDestroy(MassDestroyVendorCostRequest $request)
    {
        $vendorCosts = VendorCost::find(request('ids'));

        foreach ($vendorCosts as $vendorCost) {
            $vendorCost->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

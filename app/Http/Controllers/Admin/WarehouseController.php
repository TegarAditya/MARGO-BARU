<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyWarehouseRequest;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Models\Warehouse;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;

class WarehouseController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('warehouse_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Warehouse::query()->select(sprintf('%s.*', (new Warehouse)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'warehouse_show';
                $editGate      = 'warehouse_edit';
                $deleteGate    = 'warehouse_delete';
                $crudRoutePart = 'warehouses';

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

        return view('admin.warehouses.index');
    }

    public function create()
    {
        abort_if(Gate::denies('warehouse_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.warehouses.create');
    }

    public function store(StoreWarehouseRequest $request)
    {
        $warehouse = Warehouse::create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan');

        return redirect()->route('admin.warehouses.index');
    }

    public function edit(Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->all());

        Alert::success('Berhasil', 'Data berhasil disimpan');

        return redirect()->route('admin.warehouses.index');
    }

    public function show(Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.warehouses.show', compact('warehouse'));
    }

    public function destroy(Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $warehouse->delete();

        return back();
    }

    public function massDestroy(MassDestroyWarehouseRequest $request)
    {
        $warehouses = Warehouse::find(request('ids'));

        foreach ($warehouses as $warehouse) {
            $warehouse->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

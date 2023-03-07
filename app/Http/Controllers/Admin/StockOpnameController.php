<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockOpnameRequest;
use App\Http\Requests\StoreStockOpnameRequest;
use App\Http\Requests\UpdateStockOpnameRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockOpnameController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('stock_opname_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.index');
    }

    public function create()
    {
        abort_if(Gate::denies('stock_opname_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.create');
    }

    public function store(StoreStockOpnameRequest $request)
    {
        $stockOpname = StockOpname::create($request->all());

        return redirect()->route('admin.stock-opnames.index');
    }

    public function edit(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('stock_opname_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.edit', compact('stockOpname'));
    }

    public function update(UpdateStockOpnameRequest $request, StockOpname $stockOpname)
    {
        $stockOpname->update($request->all());

        return redirect()->route('admin.stock-opnames.index');
    }

    public function show(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('stock_opname_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.show', compact('stockOpname'));
    }

    public function destroy(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('stock_opname_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockOpname->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockOpnameRequest $request)
    {
        $stockOpnames = StockOpname::find(request('ids'));

        foreach ($stockOpnames as $stockOpname) {
            $stockOpname->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEstimasiSaldoRequest;
use App\Http\Requests\StoreEstimasiSaldoRequest;
use App\Http\Requests\UpdateEstimasiSaldoRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstimasiSaldoController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('estimasi_saldo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.estimasiSaldos.index');
    }

    public function create()
    {
        abort_if(Gate::denies('estimasi_saldo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.estimasiSaldos.create');
    }

    public function store(StoreEstimasiSaldoRequest $request)
    {
        $estimasiSaldo = EstimasiSaldo::create($request->all());

        return redirect()->route('admin.estimasi-saldos.index');
    }

    public function edit(EstimasiSaldo $estimasiSaldo)
    {
        abort_if(Gate::denies('estimasi_saldo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.estimasiSaldos.edit', compact('estimasiSaldo'));
    }

    public function update(UpdateEstimasiSaldoRequest $request, EstimasiSaldo $estimasiSaldo)
    {
        $estimasiSaldo->update($request->all());

        return redirect()->route('admin.estimasi-saldos.index');
    }

    public function show(EstimasiSaldo $estimasiSaldo)
    {
        abort_if(Gate::denies('estimasi_saldo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.estimasiSaldos.show', compact('estimasiSaldo'));
    }

    public function destroy(EstimasiSaldo $estimasiSaldo)
    {
        abort_if(Gate::denies('estimasi_saldo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $estimasiSaldo->delete();

        return back();
    }

    public function massDestroy(MassDestroyEstimasiSaldoRequest $request)
    {
        $estimasiSaldos = EstimasiSaldo::find(request('ids'));

        foreach ($estimasiSaldos as $estimasiSaldo) {
            $estimasiSaldo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

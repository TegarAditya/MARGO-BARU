<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRekapBillingRequest;
use App\Http\Requests\StoreRekapBillingRequest;
use App\Http\Requests\UpdateRekapBillingRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RekapBillingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('rekap_billing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rekapBillings.index');
    }

    public function create()
    {
        abort_if(Gate::denies('rekap_billing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rekapBillings.create');
    }

    public function store(StoreRekapBillingRequest $request)
    {
        $rekapBilling = RekapBilling::create($request->all());

        return redirect()->route('admin.rekap-billings.index');
    }

    public function edit(RekapBilling $rekapBilling)
    {
        abort_if(Gate::denies('rekap_billing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rekapBillings.edit', compact('rekapBilling'));
    }

    public function update(UpdateRekapBillingRequest $request, RekapBilling $rekapBilling)
    {
        $rekapBilling->update($request->all());

        return redirect()->route('admin.rekap-billings.index');
    }

    public function show(RekapBilling $rekapBilling)
    {
        abort_if(Gate::denies('rekap_billing_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rekapBillings.show', compact('rekapBilling'));
    }

    public function destroy(RekapBilling $rekapBilling)
    {
        abort_if(Gate::denies('rekap_billing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rekapBilling->delete();

        return back();
    }

    public function massDestroy(MassDestroyRekapBillingRequest $request)
    {
        $rekapBillings = RekapBilling::find(request('ids'));

        foreach ($rekapBillings as $rekapBilling) {
            $rekapBilling->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

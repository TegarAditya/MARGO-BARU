<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRekapBillingRequest;
use App\Http\Requests\StoreRekapBillingRequest;
use App\Http\Requests\UpdateRekapBillingRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Transaction;
use App\Models\Salesperson;
use App\Models\Semester;
use DB;
use Carbon\Carbon;

class RekapBillingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('rekap_billing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semester = empty($request->semester) ? 11 : $request->semester;

        $sales = Salesperson::withSum(['transactions as pengambilan' => function ($q) use ($semester) {
            $q->where('type', 'faktur')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as diskon' => function ($q) use ($semester) {
            $q->where('type', 'diskon')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as retur' => function ($q) use ($semester) {
            $q->where('type', 'retur')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as bayar' => function ($q) use ($semester) {
            $q->where('type', 'bayar')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as potongan' => function ($q) use ($semester) {
            $q->where('type', 'potongan')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->get();

        $semester = Semester::find($semester);

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id');

        return view('admin.rekapBillings.index', compact('sales','semesters', 'semester'));
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEstimasiSaldoRequest;
use App\Http\Requests\StoreEstimasiSaldoRequest;
use App\Http\Requests\UpdateEstimasiSaldoRequest;
use App\Models\Semester;
use App\Models\SalesOrder;
use App\Models\Salesperson;
use App\Models\GroupArea;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class EstimasiSaldoController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('estimasi_saldo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semester = !empty($request->semester_id) ? $request->semester_id : setting('current_semester');
        $group_area = null;

        $query = Salesperson::withSum(['estimasi as pesanan' => function ($q) use ($semester) {
            $q->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->withSum(['estimasi as dikirim' => function ($q) use ($semester) {
            $q->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(moved), 0)'));
        }], 'moved');

        if (!empty($request->area)) {
            $query->whereHas('area', function ($q) use ($request) {
                $q->where('group_area_id', $request->area);
            });

            $group_area = GroupArea::find($request->area);
        }

        $saldo = $query->get();

        $selected_semester = Semester::find($semester);
        $group_areas = GroupArea::pluck('code', 'id')->prepend('All', '');
        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.estimasiSaldos.index', compact('saldo', 'group_area', 'group_areas','selected_semester', 'semesters'));
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

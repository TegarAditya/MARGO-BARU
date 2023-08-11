<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySalesReportRequest;
use App\Http\Requests\StoreSalesReportRequest;
use App\Http\Requests\UpdateSalesReportRequest;
use App\Models\Salesperson;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesReport::with(['salesperson'])->select(sprintf('%s.*', (new SalesReport)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');

            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->editColumn('periode', function ($row) {
                return $row->periode ? $row->periode : '';
            });
            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->editColumn('saldo_awal', function ($row) {
                return $row->saldo_awal ? $row->saldo_awal : '';
            });
            $table->editColumn('jual', function ($row) {
                return $row->jual ? $row->jual : '';
            });
            $table->editColumn('diskon', function ($row) {
                return $row->diskon ? $row->diskon : '';
            });
            $table->editColumn('retur', function ($row) {
                return $row->retur ? $row->retur : '';
            });
            $table->editColumn('bayar', function ($row) {
                return $row->bayar ? $row->bayar : '';
            });
            $table->editColumn('potongan', function ($row) {
                return $row->potongan ? $row->potongan : '';
            });
            $table->editColumn('saldo_akhir', function ($row) {
                return $row->saldo_akhir ? $row->saldo_akhir : '';
            });

            $table->rawColumns(['placeholder', 'salesperson']);

            return $table->make(true);
        }

        return view('admin.salesReports.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sales_report_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.salesReports.create', compact('salespeople'));
    }

    public function store(StoreSalesReportRequest $request)
    {
        $salesReport = SalesReport::create($request->all());

        return redirect()->route('admin.sales-reports.index');
    }

    public function edit(SalesReport $salesReport)
    {
        abort_if(Gate::denies('sales_report_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salesReport->load('salesperson');

        return view('admin.salesReports.edit', compact('salesReport', 'salespeople'));
    }

    public function update(UpdateSalesReportRequest $request, SalesReport $salesReport)
    {
        $salesReport->update($request->all());

        return redirect()->route('admin.sales-reports.index');
    }

    public function show(SalesReport $salesReport)
    {
        abort_if(Gate::denies('sales_report_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesReport->load('salesperson');

        return view('admin.salesReports.show', compact('salesReport'));
    }

    public function destroy(SalesReport $salesReport)
    {
        abort_if(Gate::denies('sales_report_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesReport->delete();

        return back();
    }

    public function massDestroy(MassDestroySalesReportRequest $request)
    {
        $salesReports = SalesReport::find(request('ids'));

        foreach ($salesReports as $salesReport) {
            $salesReport->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

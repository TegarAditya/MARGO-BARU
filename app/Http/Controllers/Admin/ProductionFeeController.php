<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductionTransactionTotal;
use App\Models\Vendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;

class ProductionFeeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('production_fee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductionTransactionTotal::with(['vendor'])->select(sprintf('%s.*', (new ProductionTransactionTotal)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.bills.billing', ['salesperson' => $row->vendor_id]).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.bills.cetakBilling', ['salesperson' => $row->vendor_id]).'" title="Print Saldo" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                ';

                return $btn;
            });

            $table->addColumn('vendor', function ($row) {
                return $row->vendor ? $row->vendor->full_name : '';
            });

            $table->editColumn('total_fee', function ($row) {
                return $row->total_fee ? money($row->total_fee) : 0;
            });
            $table->editColumn('total_payment', function ($row) {
                return $row->total_payment ? money($row->total_payment) : 0;
            });
            $table->editColumn('outstanding_fee', function ($row) {
                return $row->outstanding_fee ? money($row->outstanding_fee) : 0;
            });

            $table->rawColumns(['actions', 'placeholder', 'vendor']);

            return $table->make(true);
        }

        return view('admin.productionFees.index');
    }

    public function jangka(Request $request)
    {
        if ($request->has('date') && $request->date && $dates = explode(' - ', $request->date)) {
            $start = Date::parse($dates[0])->startOfDay();
            $end = !isset($dates[1]) ? $start->clone()->endOfMonth() : Date::parse($dates[1])->endOfDay();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
        }

        $saldo_awal = Vendor::withSum(['transactions as cetak' => function ($q) use ($start) {
            $q->where('type', 'cetak')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as finishing' => function ($q) use ($start) {
            $q->where('type', 'finishing')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as bayar' => function ($q) use ($start) {
            $q->where('type', 'bayar')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->get();

        $vendors = Vendor::withSum(['transactions as cetak' => function ($q) use ($start, $end) {
            $q->where('type', 'cetak')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as finishing' => function ($q) use ($start, $end) {
            $q->where('type', 'finishing')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as bayar' => function ($q) use ($start, $end) {
            $q->where('type', 'bayar')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->get();

        return view('admin.productionFees.billing', compact('start', 'end', 'saldo_awal', 'vendors'));
    }
}

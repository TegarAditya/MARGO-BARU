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
use App\Models\Invoice;
use App\Models\ReturnGood;
use App\Models\Payment;
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

    public function billing(Request $request)
    {
        $salesperson = $request->salesperson;
        $semester = $request->semester ? $request->semester : setting('current_semester');

        $invoices = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $returs = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $payments = Payment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();

        $salesperson = Salesperson::find($salesperson);
        $semester = Semester::find($semester);

        return view('admin.rekapBillings.billing', compact('salesperson', 'semester', 'invoices', 'returs', 'payments'));
    }
}

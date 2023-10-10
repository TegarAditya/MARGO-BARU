<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Models\BillAdjustment;
use App\Models\Salesperson;
use App\Models\Semester;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\ReturnGood;
use App\Models\Payment;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;
use App\Exports\RekapBillingExport;
use App\Exports\BillingExport;

class BillController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $semester = setting('current_semester');
            $query = Bill::with(['semester', 'salesperson'])->where('semester_id', $semester)->select(sprintf('%s.*', (new Bill)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.bills.billing', ['salesperson' => $row->salesperson_id]).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.bills.cetakBilling', ['salesperson' => $row->salesperson_id]).'" title="Print Saldo" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.bills.cetakBilling', ['salesperson' => $row->salesperson_id, 'rekap' => 1]).'" title="Print Rekap Saldo" target="_blank">
                        <i class="fas fa-print text-danger fa-lg"></i>
                    </a>
                ';

                return $btn;
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->full_name : '';
            });

            $table->editColumn('saldo_awal', function ($row) {
                return $row->saldo_awal ? money($row->saldo_awal) : 0;
            });
            $table->editColumn('jual', function ($row) {
                return $row->jual ? money($row->jual) : 0;
            });
            $table->editColumn('diskon', function ($row) {
                return $row->diskon ? money($row->diskon) : 0;
            });
            $table->editColumn('adjustment', function ($row) {
                return $row->adjustment ? money($row->adjustment) : 0;
            });
            $table->editColumn('retur', function ($row) {
                return $row->retur ? money($row->retur) : 0;
            });
            $table->editColumn('bayar', function ($row) {
                return $row->bayar ? money($row->bayar) : 0;
            });
            $table->editColumn('potongan', function ($row) {
                return $row->potongan ? money($row->potongan) : 0;
            });
            $table->editColumn('saldo_akhir', function ($row) {
                return $row->saldo_akhir ? money($row->saldo_akhir) : 0;
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        return view('admin.bills.index');
    }

    public function create()
    {
        abort_if(Gate::denies('bill_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.bills.create', compact('salespeople', 'semesters'));
    }

    public function store(StoreBillRequest $request)
    {
        $bill = Bill::create($request->all());

        return redirect()->route('admin.bills.index');
    }

    public function edit(Bill $bill)
    {
        abort_if(Gate::denies('bill_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $bill->load('semester', 'salesperson');

        return view('admin.bills.edit', compact('bill', 'salespeople', 'semesters'));
    }

    public function update(UpdateBillRequest $request, Bill $bill)
    {
        $bill->update($request->all());

        return redirect()->route('admin.bills.index');
    }

    public function show(Bill $bill)
    {
        abort_if(Gate::denies('bill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bill->load('semester', 'salesperson');

        return view('admin.bills.show', compact('bill'));
    }

    public function generate(Request $request)
    {
        $semester = setting('current_semester');

        $sales = Salesperson::withSum(['transactions as pengambilan' => function ($q) use ($semester) {
            $q->where('type', 'faktur')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as diskon' => function ($q) use ($semester) {
            $q->where('type', 'diskon')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as adjustment' => function ($q) use ($semester) {
            $q->where('type', 'adjustment')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as retur' => function ($q) use ($semester) {
            $q->where('type', 'retur')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as bayar' => function ($q) use ($semester) {
            $q->where('type', 'bayar')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as potongan' => function ($q) use ($semester) {
            $q->where('type', 'potongan')->where('semester_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['payments as payment' => function ($q) use ($semester) {
            $q->where('semester_bayar_id', $semester)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->get();

        DB::beginTransaction();
        try {
            foreach($sales as $sale) {
                $bill = Bill::where('salesperson_id', $sale->id)->where('semester_id', $semester)->first();
                $payment = Payment::selectRaw('COALESCE(SUM(paid), 0) as bayar, COALESCE(SUM(discount), 0) as potongan')->where('salesperson_id', $sale->id)->where('semester_bayar_id', $semester)->first();

                $faktur = $sale->pengambilan;
                $diskon = $sale->diskon;
                $adjustment = $sale->adjustment;
                $retur = $sale->retur;
                $bayar = $payment->bayar;
                $potongan = $payment->potongan;

                $pembayaran = $sale->bayar + $sale->potongan;

                if ($bill) {
                    $saldo_awal = $bill->previous ? $bill->previous->saldo_akhir : 0;
                    $bill->update([
                        'saldo_awal' => $saldo_awal,
                        'jual' => $faktur,
                        'diskon' => $diskon,
                        'adjustment' => $adjustment,
                        'retur' => $retur,
                        'bayar' => $bayar,
                        'potongan' => $potongan,
                        'saldo_akhir' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $bayar + $potongan),
                        'tagihan' => $faktur - ($adjustment + $diskon + $retur),
                        'pembayaran' => $pembayaran,
                        'piutang' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran)
                    ]);
                } else {
                    $previous = Bill::where('salesperson_id', $sale->id)->where('semester_id', prevSemester($semester))->first();

                    $saldo_awal = $previous ? $previous->saldo_akhir : 0;
                    Bill::create([
                        'semester_id' => $semester,
                        'salesperson_id' => $sale->id,
                        'previous_id' => $previous ? $previous->id : null,
                        'saldo_awal' => $saldo_awal,
                        'jual' => $faktur,
                        'diskon' => $diskon,
                        'adjustment' => $adjustment,
                        'retur' => $retur,
                        'bayar' => $bayar,
                        'potongan' => $potongan,
                        'saldo_akhir' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $bayar + $potongan),
                        'tagihan' => $faktur - ($adjustment + $diskon + $retur),
                        'pembayaran' => $pembayaran,
                        'piutang' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran)
                    ]);
                }
            }

            DB::commit();

            Alert::success('Success', 'Billing berhasil di generate');

            return redirect()->route('admin.bills.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
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

        $saldo_awal = Salesperson::withSum(['transactions as pengambilan' => function ($q) use ($start) {
            $q->where('type', 'faktur')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as diskon' => function ($q) use ($start) {
            $q->where('type', 'diskon')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as adjustment' => function ($q) use ($start) {
            $q->where('type', 'adjustment')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as retur' => function ($q) use ($start) {
            $q->where('type', 'retur')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as bayar' => function ($q) use ($start) {
            $q->where('type', 'bayar')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as potongan' => function ($q) use ($start) {
            $q->where('type', 'potongan')->where('transaction_date', '<', $start)->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->get();

        $sales = Salesperson::withSum(['transactions as pengambilan' => function ($q) use ($start, $end) {
            $q->where('type', 'faktur')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as diskon' => function ($q) use ($start, $end) {
            $q->where('type', 'diskon')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as adjustment' => function ($q) use ($start, $end) {
            $q->where('type', 'adjustment')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as retur' => function ($q) use ($start, $end) {
            $q->where('type', 'retur')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as bayar' => function ($q) use ($start, $end) {
            $q->where('type', 'bayar')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->withSum(['transactions as potongan' => function ($q) use ($start, $end) {
            $q->where('type', 'potongan')->whereBetween('transaction_date', [$start, $end])->select(DB::raw('COALESCE(SUM(amount), 0)'));
        }], 'amount')->get();

        if ($request->has('export')) {
            return (new BillingExport($saldo_awal, $sales))->download('REKAP PIUTANG PERIODE' . $start->format('d-F-Y') .' sd '. $end->format('d-F-Y') .'.xlsx');
        } else {
            return view('admin.bills.billing', compact('start', 'end', 'saldo_awal', 'sales'));
        }
    }

    public function billing(Request $request)
    {
        $salesperson = $request->salesperson;
        $semester = setting('current_semester');

        $invoices = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $adjustments = BillAdjustment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $returs = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $payments = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $semester)->get();

        $bills = collect([]);
        $invoices_old = collect([]);
        $adjustments_old = collect([]);
        $returs_old = collect([]);
        $payments_old = collect([]);

        $semester_id = $semester;
        do {
            $semester_id = prevSemester($semester_id);
            $bill = Bill::where('salesperson_id', $salesperson)->where('semester_id', $semester_id)->first();
            if ($bill) {
                $bills->push($bill);
            }
        } while($bill && $bill->saldo_awal > 0);

        if ($bills->count() > 0) {
            foreach($bills as $item) {
                $faktur = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $adjustment = BillAdjustment::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $retur = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $bayar = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $item->semester_id)->get();

                $invoices_old = $invoices_old->merge($faktur);
                $adjustments_old = $adjustments_old->merge($adjustment);
                $returs_old = $returs_old->merge($retur);
                $payments_old = $payments_old->merge($bayar);
            }
        }

        $list_semester = $bills->pluck('semester_id');

        $salesperson = Salesperson::find($salesperson);
        $semester = Semester::find($semester);

        return view('admin.bills.show', compact('salesperson', 'semester', 'invoices', 'adjustments', 'returs', 'payments', 'bills', 'invoices_old','adjustments_old', 'returs_old', 'payments_old', 'list_semester'));
    }

    public function cetakBilling(Request $request)
    {
        $salesperson = $request->salesperson;
        $semester = setting('current_semester');

        $invoices = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $adjustments = BillAdjustment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $returs = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $payments = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $semester)->get();
        $billing = Bill::where('salesperson_id', $salesperson)->where('semester_id', $semester)->first();

        $bills = collect([]);
        $invoices_old = collect([]);
        $adjustments_old = collect([]);
        $returs_old = collect([]);
        $payments_old = collect([]);

        $semester_id = $semester;
        do {
            $semester_id = prevSemester($semester_id);
            $bill = Bill::where('salesperson_id', $salesperson)->where('semester_id', $semester_id)->first();
            if ($bill) {
                $bills->push($bill);
            }
        } while($bill && $bill->saldo_awal > 0);

        if ($bills->count() > 0) {
            foreach($bills as $item) {
                $faktur = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $adjustment = BillAdjustment::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $retur = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $bayar = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $item->semester_id)->get();

                $invoices_old = $invoices_old->merge($faktur);
                $adjustments_old = $adjustments_old->merge($adjustment);
                $returs_old = $returs_old->merge($retur);
                $payments_old = $payments_old->merge($bayar);
            }
        }

        $list_semester = $bills->pluck('semester_id');

        $salesperson = Salesperson::find($salesperson);
        $semester = Semester::find($semester);

        if ($request->rekap == 1) {
            return view('admin.bills.rekap_saldo', compact('salesperson', 'semester', 'invoices', 'adjustments', 'returs', 'payments', 'billing', 'bills', 'invoices_old', 'adjustments_old', 'returs_old', 'payments_old', 'list_semester'));
        }

        return view('admin.bills.saldo', compact('salesperson', 'semester', 'invoices', 'adjustments', 'returs', 'payments', 'billing', 'bills', 'invoices_old', 'adjustments_old', 'returs_old', 'payments_old', 'list_semester'));
    }

    public function eksportRekapBilling(Request $request)
    {
        $today = Carbon::now()->format('d-m-Y');
        $semester = Semester::find(setting('current_semester'))->name;
        return (new RekapBillingExport())->download('REKAP BILLING '. preg_replace('/[^A-Za-z0-9_\-]/', '-', $semester) . ' TANGGAL ' . $today . '.xlsx');
    }
}

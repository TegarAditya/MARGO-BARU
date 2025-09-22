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
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;
use App\Exports\RekapBillingExport;
use App\Exports\DirekturBillingExport;
use App\Exports\BillingExport;
use App\Services\BillingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use RealRashid\SweetAlert\Facades\Alert;

class BillController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semester = $request->semester ? $request->semester : setting('current_semester');

        $old_bills = BillingService::getBillSummary(salesperson: null, semester: $semester, includeCurrent: false, orderDesc: true);
        $new_bills = BillingService::getBillSummary(salesperson: null, semester: $semester, includeCurrent: true, orderDesc: true);

        if ($request->ajax()) {
            $query = Bill::with(['semester', 'salesperson'])->where('semester_id', $semester)->select(sprintf('%s.*', (new Bill)->table));
            $order = $request->order;
            if (is_array($order) && count($order)) {
                $sortBy = $order[0]['column'] ?? null;
                $sort = $order[0]['dir'] ?? null;

                if ($sortBy == 1) {
                    $query->orderBy('salesperson_id', $sort == 'asc' ? 'asc' : 'desc');
                }
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($semester) {
                $btn = '
                    <a class="px-1" href="' . route('admin.bills.billing', ['salesperson' => $row->salesperson_id, 'semester' => $semester]) . '" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="' . route('admin.bills.cetakBilling', ['salesperson' => $row->salesperson_id, 'semester' => $semester]) . '" title="Print Saldo" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="' . route('admin.bills.cetakBilling', ['salesperson' => $row->salesperson_id, 'semester' => $semester, 'rekap' => 1]) . '" title="Print Rekap Saldo" target="_blank">
                        <i class="fas fa-print text-danger fa-lg"></i>
                    </a>
                    <a class="px-1" href="' . route('admin.bills.eksportBillingDetail', ['salesperson' => $row->salesperson_id, 'semester' => $semester]) . '" title="Print Rekap Saldo" target="_blank">
                        <i class="fas fa-file-excel text-success fa-lg"></i>
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

            $table->addColumn('sales', function ($row) {
                return $row->salesperson ? $row->salesperson->full_name : '';
            });

            $table->editColumn('saldo_awal', function ($row) use ($old_bills) {
                $billSummary = $old_bills->where('salesperson_id', $row->salesperson->id);
                return $billSummary->count() ? angka($billSummary->sum('saldo_akhir')) : 0;
            });

            $table->editColumn('jual', function ($row) use ($new_bills) {
                $billSummary = $new_bills->where('salesperson_id', $row->salesperson->id)->first();
                return $billSummary ? angka($billSummary->jual) : 0;
            });

            $table->editColumn('diskon', function ($row) use ($new_bills) {
                $billSummary = $new_bills->where('salesperson_id', $row->salesperson->id)->first();
                return $billSummary ? angka($billSummary->diskon) : 0;
            });

            $table->editColumn('adjustment', fn($row) => $row->adjustment ? angka($row->adjustment) : 0);

            $table->editColumn('retur', function ($row) use ($new_bills) {
                $billSummary = $new_bills->where('salesperson_id', $row->salesperson->id)->first();
                return $billSummary ? angka($billSummary->retur) : 0;
            });

            $table->editColumn('bayar', function ($row) use ($new_bills) {
                $billSummary = $new_bills->where('salesperson_id', $row->salesperson->id)->first();
                return $billSummary ? angka($billSummary->bayar) : 0;
            });

            $table->editColumn('potongan', function ($row) use ($new_bills) {
                $billSummary = $new_bills->where('salesperson_id', $row->salesperson->id)->first();
                return $billSummary ? angka($billSummary->potongan) : 0;
            });

            $table->editColumn('saldo_akhir', function ($row) use ($new_bills) {
                $billSummary = $new_bills->where('salesperson_id', $row->salesperson->id);
                return $billSummary->count() ? angka($billSummary->sum('saldo_akhir')) : 0;
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        $semesters = Semester::where('status', 1)->latest()->pluck('name', 'id');

        return view('admin.bills.index', compact('semesters'));
    }

    public function salespersonIndex(Request $request)
    {
        abort_if(Gate::denies('bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Bill::with(['semester', 'salesperson'])->select(sprintf('%s.*', (new Bill)->table));

            if (!empty($request->salesperson)) {
                $query->where('salesperson_id', $request->salesperson);
            }

            $query = $query->orderBy('salesperson_id')->oldest();

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="' . route('admin.bills.billing', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]) . '" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="' . route('admin.bills.cetakBilling', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]) . '" title="Print Saldo" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="' . route('admin.bills.cetakBilling', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id, 'rekap' => 1]) . '" title="Print Rekap Saldo" target="_blank">
                        <i class="fas fa-print text-danger fa-lg"></i>
                    </a>
                    <a class="px-1" href="' . route('admin.bills.recalculating', ['salesperson' => $row->salesperson_id]) . '" title="Rebalancing" target="_blank">
                        <i class="fas fa-spinner fa-spin text-danger fa-lg"></i>
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

            $table->addColumn('sales', function ($row) {
                return $row->salesperson ? $row->salesperson->full_name : '';
            });

            $table->editColumn('saldo_awal', function ($row) {
                return $row->saldo_awal ? angka($row->saldo_awal) : 0;
            });
            $table->editColumn('jual', function ($row) {
                return $row->jual ? angka($row->jual) : 0;
            });
            $table->editColumn('diskon', function ($row) {
                return $row->diskon ? angka($row->diskon) : 0;
            });
            $table->editColumn('adjustment', function ($row) {
                return $row->adjustment ? angka($row->adjustment) : 0;
            });
            $table->editColumn('retur', function ($row) {
                return $row->retur ? angka($row->retur) : 0;
            });
            $table->editColumn('bayar', function ($row) {
                return $row->bayar ? angka($row->bayar) : 0;
            });
            $table->editColumn('potongan', function ($row) {
                return $row->potongan ? angka($row->potongan) : 0;
            });
            $table->editColumn('saldo_akhir', function ($row) {
                return $row->saldo_akhir ? angka($row->saldo_akhir) : 0;
            });
            $table->editColumn('piutang', function ($row) {
                return $row->piutang ? angka($row->piutang) : 0;
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson']);

            return $table->make(true);
        }

        $salespeople = Salesperson::latest()->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.bills.salesperson_index', compact('salespeople'));
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
            foreach ($sales as $sale) {
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
                        'piutang' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran),
                        'piutang_semester' => $faktur - ($adjustment + $diskon + $retur + $pembayaran)
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
                        'piutang' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran),
                        'piutang_semester' => $faktur - ($adjustment + $diskon + $retur + $pembayaran)
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
            return (new BillingExport($saldo_awal, $sales))->download('REKAP PIUTANG PERIODE' . $start->format('d-F-Y') . ' sd ' . $end->format('d-F-Y') . '.xlsx');
        } else {
            return view('admin.bills.billing', compact('start', 'end', 'saldo_awal', 'sales'));
        }
    }

    public function billing(Request $request)
    {
        $salesperson = $request->salesperson;
        $semester = $request->semester ?? setting('current_semester');

        $invoices = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $adjustments = BillAdjustment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $returs = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_retur_id', $semester)->get();
        $payments = Payment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();

        $bills = collect([]);
        $new_bills = collect([]);
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
        } while ($bill && $bill->saldo_awal > 0);

        if ($bills->count() > 0) {
            foreach ($bills as $item) {
                $faktur = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $adjustment = BillAdjustment::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $retur = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_retur_id', $item->semester_id)->get();
                $bayar = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $item->semester_id)->get();

                $invoices_old = $invoices_old->merge($faktur);
                $adjustments_old = $adjustments_old->merge($adjustment);
                $returs_old = $returs_old->merge($retur);
                $payments_old = $payments_old->merge($bayar);
            }
        }

        $new_bills = BillingService::getBillSummary(salesperson: $salesperson, semester:$semester);

        $list_semester = $bills->pluck('semester_id');

        $salesperson = Salesperson::find($salesperson);
        $semester = Semester::find($semester);

        return view('admin.bills.show', compact('salesperson', 'semester', 'invoices', 'adjustments', 'returs', 'payments', 'bills', 'new_bills', 'invoices_old', 'adjustments_old', 'returs_old', 'payments_old', 'list_semester'));
    }

    public function cetakBilling(Request $request)
    {
        $salesperson = $request->salesperson;
        $semester = $request->semester ?? setting('current_semester');

        $invoices = Invoice::with('invoice_items.product')
            ->where('salesperson_id', $salesperson)
            ->where('semester_id', $semester)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($invoice) use ($semester) {
                $firstItem = $invoice->invoice_items->first();

                if ($firstItem && $firstItem->product && $firstItem->product->semester_id < $semester) {
                    $invoice->type = 'jual_lama'; // override type
                }

                return $invoice;
            });

        $adjustments = BillAdjustment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->get();
        $returs = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_retur_id', $semester)->get();
        $payments = Payment::where('salesperson_id', $salesperson)->where('semester_id', $semester)->where('paid', '!=', '0,00')->get();
        $billing = Bill::where('salesperson_id', $salesperson)->where('semester_id', $semester)->first();

        $bills = collect([]);
        $invoices_old = collect([]);
        $adjustments_old = collect([]);
        $returs_old = collect([]);
        $payments_old = collect([]);
        $new_bills = collect([]);

        $semester_id = $semester;
        do {
            $semester_id = prevSemester($semester_id);
            $bill = Bill::where('salesperson_id', $salesperson)->where('semester_id', $semester_id)->first();
            if ($bill) {
                $bills->push($bill);
            }
        } while ($bill && $bill->saldo_awal > 0);

        $new_bills = BillingService::getBillSummary(salesperson: $salesperson, semester:$semester);

        if ($bills->count() > 0) {
            foreach ($bills as $item) {
                $faktur = Invoice::with('invoice_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $adjustment = BillAdjustment::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_id', $item->semester_id)->get();
                $retur = ReturnGood::with('retur_items')->where('salesperson_id', $salesperson)->where('semester_retur_id', $item->semester_id)->get();
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
            return view('admin.bills.rekap_saldo', compact('salesperson', 'semester', 'invoices', 'adjustments', 'returs', 'payments', 'billing', 'bills', 'new_bills', 'invoices_old', 'adjustments_old', 'returs_old', 'payments_old', 'list_semester'));
        }

        return view('admin.bills.saldo', compact('salesperson', 'semester', 'invoices', 'adjustments', 'returs', 'payments', 'billing', 'bills', 'new_bills', 'invoices_old', 'adjustments_old', 'returs_old', 'payments_old', 'list_semester'));
    }

    public function eksportRekapBilling(Request $request)
    {
        $today = Carbon::now()->format('d-m-Y');
        $semester = Semester::find(setting('current_semester'))->name;
        return (new RekapBillingExport())->download('REKAP BILLING ' . preg_replace('/[^A-Za-z0-9_\-]/', '-', $semester) . ' TANGGAL ' . $today . '.xlsx');
    }

    public function eksportBillingDetail(Request $request)
    {
        $salesperson_id = $request->salesperson;
        $semester = $request->semester ?? setting('current_semester');

        $billing = BillingService::getBillSummary(salesperson: $salesperson_id, semester: $semester)->map(fn($item, $index) => [
            'index' => $index + 1,
            'semester_name' => $item->semester_name,
            'saldo_akhir' => (float) $item->saldo_akhir,
        ])->toArray();

        $currentBilling = BillingService::getBillSummary(salesperson: $salesperson_id, semester: $semester, includeCurrent: true)->sum('saldo_akhir');

        $invoiceRelations = [
            'invoice_items:id,invoice_id,product_id,quantity,price,total,discount,total_discount',
            'invoice_items.product:id,kelas_id,jenjang_id,mapel_id,kurikulum_id,halaman_id,cover_id,semester_id,name,code',
            'invoice_items.product.kurikulum:id,code',
            'invoice_items.product.jenjang:id,name',
            'invoice_items.product.kelas:id,name',
            'invoice_items.product.mapel:id,name',
            'invoice_items.product.halaman:id,code',
            'invoice_items.product.cover:id,name',
            'invoice_items.product.semester:id,code,name',
        ];

        $returRelations = [
            'retur_items:id,retur_id,product_id,quantity,price,total',
            'retur_items.product:id,kelas_id,jenjang_id,mapel_id,kurikulum_id,halaman_id,cover_id,semester_id,name,code',
            'retur_items.product.kurikulum:id,code',
            'retur_items.product.jenjang:id,name',
            'retur_items.product.kelas:id,name',
            'retur_items.product.mapel:id,name',
            'retur_items.product.halaman:id,code',
            'retur_items.product.cover:id,name',
            'retur_items.product.semester:id,code,name',
        ];

        $invoices = Invoice::with($invoiceRelations)
            ->where('salesperson_id', $salesperson_id)
            ->where('semester_id', $semester)
            ->get();

        $returs = ReturnGood::with($returRelations)
            ->where('salesperson_id', $salesperson_id)
            ->where('semester_retur_id', $semester)
            ->get();

        $flatInvoices = $invoices->map(fn($invoice) => $this->mapInvoice($invoice));
        $flatReturs = $returs->map(fn($retur) => $this->mapRetur($retur));

        $adjustments = BillAdjustment::where('salesperson_id', $salesperson_id)
            ->where('semester_id', $semester)->get();

        $payments = Payment::where('salesperson_id', $salesperson_id)
            ->where('semester_id', $semester)
            ->get()
            ->map(function ($item, $index) {
                $array = $item->toArray();
                $array = ['index' => $index + 1] + $array;
                return $array;
            });

        $semesterName = Semester::findOrNew($semester)->name;
        $salesperson = Salesperson::findOrNew($salesperson_id);

        $oldIndex = 1;
        $additionalIndex = 1;

        $data = [
            'date' => now()->format('d-m-Y'),
            'salesperson' => $salesperson->toArray(),
            'semester' => $semesterName,
            'bills' => $billing,
            'current_bills' => (float) $currentBilling,
            'invoices' => $flatInvoices->map(function ($invoice) {
                $invoiceCopy = $invoice;
                unset($invoiceCopy['items']);
                return $invoiceCopy;
            })->toArray(),
            ...$flatInvoices->mapWithKeys(function ($invoice, $index) use ($semester, &$oldIndex, &$additionalIndex) {
                if (!empty($invoice['items']) && $invoice['items'][0]['semester_id'] < $semester) {
                    return ['old_invoices' . ($oldIndex++) => $invoice];
                } else if (!empty($invoice['items'])) {
                    return ['invoices' . ($index + 1) => $invoice];
                } else {
                    return ['additional_invoices' . ($additionalIndex++) => $invoice];
                }
            })->toArray(),
            'adjustments' => $adjustments->toArray(),
            'returs' => $flatReturs->map(function ($invoice) {
                $invoiceCopy = $invoice;
                unset($invoiceCopy['items']);
                return $invoiceCopy;
            })->toArray(),
            ...$flatReturs->mapWithKeys(fn($retur, $index) => ['returs' . ($index + 1) => $retur])->toArray(),
            'payments' => $payments->toArray(),
        ];

        // dd($data);

        return response()->streamDownload(function () use ($data) {
            echo (new \AnourValar\Office\SheetsService)
                ->generate(resource_path('xlsx/export_billing_detail.xlsx'), $data)
                ->save(\AnourValar\Office\Format::Xlsx);
        }, str_replace('/', '_', $salesperson->full_name . ' - ' . $semesterName) . '.xlsx');
    }

    public function reportDirektur(Request $request)
    {
        $today = Carbon::now()->format('d-m-Y');
        $semester = Semester::find(setting('current_semester'))->name;
        return (new DirekturBillingExport())->download('REKAP BILLING ' . preg_replace('/[^A-Za-z0-9_\-]/', '-', $semester) . ' TANGGAL ' . $today . '.xlsx');
    }

    private function mapInvoice($invoice): array
    {
        return [
            'number' => $invoice->no_faktur,
            'date' => $invoice->date,
            'total' => (float) $invoice->total,
            'discount' => (float) $invoice->discount,
            'nominal' => (float) $invoice->nominal,
            'note' => $invoice->note,
            'items' => $invoice->invoice_items->map(fn($item, $index) => $this->mapProductItem($item, $index))->toArray(),
        ];
    }

    private function mapRetur($retur): array
    {
        return [
            'number' => $retur->no_retur,
            'date' => $retur->date,
            'nominal' => (float) $retur->nominal,
            'items' => $retur->retur_items->map(fn($item, $index) => $this->mapProductItem($item, $index))->toArray(),
        ];
    }

    private function mapProductItem($item, int $index): array
    {
        $product = $item->product;

        return [
            'index' => $index + 1,
            'quantity' => (int) $item->quantity,
            'price' => (float) $item->price,
            'total' => (float) $item->total,
            'discount' => (float) ($item->discount ?? 0),
            'total_discount' => (float) ($item->total_discount ?? 0),
            'subtotal' => (float) $item->total - (float) ($item->total_discount ?? 0),
            'jenjang' => optional($product->jenjang)->name . ' - ' . optional($product->kurikulum)->code,
            'mapel' => optional($product->mapel)->name,
            'kelas' => optional($product->kelas)->name,
            'halaman' => optional($product->halaman)->code,
            'cover' => optional($product->cover)->name,
            'code' => $product->code,
            'semester_id' => $product->semester_id,
            'semester_name' => optional($product->semester)->name,
        ];
    }

    public function recalculating(Request $request)
    {
        $salesperson = $request->salesperson;
        $semester = $request->semester ?? setting('current_semester');

        $list_semester = Semester::where('id', '>=', $semester)->where('status', true)->pluck('id');

        foreach ($list_semester as $item) {

            $bill = Bill::where('salesperson_id', $salesperson)->where('semester_id', $item)->first();

            $faktur = Invoice::where('salesperson_id', $salesperson)->where('semester_id', $item)->sum('total');
            $diskon = Invoice::where('salesperson_id', $salesperson)->where('semester_id', $item)->sum('discount');
            $adjustment = BillAdjustment::where('salesperson_id', $salesperson)->where('semester_id', $item)->sum('amount');
            $retur = ReturnGood::where('salesperson_id', $salesperson)->where('semester_retur_id', $item)->sum('nominal');

            $bayar = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $item)->sum('paid');
            $potongan = Payment::where('salesperson_id', $salesperson)->where('semester_bayar_id', $item)->sum('discount');
            $pembayaran = $bayar + $potongan;

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
                    'saldo_akhir' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran),
                    'tagihan' => $faktur - ($diskon + $retur),
                    'pembayaran' => $pembayaran,
                    'piutang' => $faktur - ($adjustment + $diskon + $retur + $pembayaran)
                ]);
            } else {
                $previous = Bill::where('salesperson_id', $salesperson)->where('semester_id', prevSemester($item))->first();

                $saldo_awal = $previous ? $previous->saldo_akhir : 0;
                Bill::create([
                    'semester_id' => $item,
                    'salesperson_id' => $salesperson,
                    'previous_id' => $previous ? $previous->id : null,
                    'saldo_awal' => $saldo_awal,
                    'jual' => $faktur,
                    'diskon' => $diskon,
                    'adjustment' => $adjustment,
                    'retur' => $retur,
                    'bayar' => $bayar,
                    'potongan' => $potongan,
                    'saldo_akhir' => ($saldo_awal + $faktur) - ($adjustment + $diskon + $retur + $pembayaran),
                    'tagihan' => $faktur - ($diskon + $retur),
                    'pembayaran' => $pembayaran,
                    'piutang' => $faktur - ($adjustment + $diskon + $retur + $pembayaran)
                ]);
            }
        }

        return redirect()->back();
    }
}

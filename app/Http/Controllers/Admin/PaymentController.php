<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPaymentRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Salesperson;
use App\Models\Semester;
use App\Models\Invoice;
use App\Models\ReturnGood;
use App\Models\Transaction;
use App\Models\Bill;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use App\Services\TransactionService;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Payment::with(['salesperson', 'semester'])->select(sprintf('%s.*', (new Payment)->table))->latest();

            if (!empty($request->salesperson)) {
                $query->where('salesperson_id', $request->salesperson);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }

            if (!empty($request->payment_method)) {
                $query->where('payment_method', $request->payment_method);
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return '
                    <a class="px-1" href="'.route('admin.payments.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.payments.kwitansi', $row->id).'" target="_blank" title="Print Kwitansi" >
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.payments.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('no_kwitansi', function ($row) {
                return $row->no_kwitansi ? $row->no_kwitansi : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->short_name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('paid', function ($row) {
                return 'Metode Pembayaran : <b>'. Payment::PAYMENT_METHOD_SELECT[$row->payment_method]. '</b><br>Bayar : <b>'. money($row->paid) .'</b><br>Potongan: <b>'.money($row->discount).'</b>';
            });

            $table->editColumn('discount', function ($row) {
                return $row->discount ? money($row->discount) : '';
            });

            $table->editColumn('payment_method', function ($row) {
                return $row->payment_method ? Payment::PAYMENT_METHOD_SELECT[$row->payment_method] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'salesperson', 'semester', 'paid']);

            return $table->make(true);
        }

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('short_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.payments.index', compact('semesters', 'salespeople'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_kwitansi = Payment::generateNoKwitansi(setting('current_semester'));

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.payments.create', compact('salespeople', 'semesters','no_kwitansi', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'salesperson_id' => 'required',
            'semester_id' => 'required',
            'payment_method' => 'required',
            'bayar' => 'required|numeric|min:1',
            'diskon' => 'nullable|numeric',
            'nominal' => 'required|numeric|min:1',
            'note' => 'nullable'
        ]);

        $date = $validatedData['date'];
        $salesperson = $validatedData['salesperson_id'];
        $semester = $validatedData['semester_id'] ?? setting('current_semester');
        $payment_method = $validatedData['payment_method'];
        $bayar = $validatedData['bayar'];
        $diskon = $validatedData['diskon'];
        $nominal = $validatedData['nominal'];
        $note = $validatedData['note'];

        $before = Bill::with('semester')->where('salesperson_id', $salesperson)->where('semester_id', '<', $semester)->where('piutang', '>' , 0)->oldest()->get();

        DB::beginTransaction();
        try {
            if ($before->count() > 0) {
                foreach($before as $bill) {
                    if ($bayar > 0) {
                        $paid = ($bayar < $bill->piutang) ? $bayar : $bill->piutang;
                        $payment = Payment::create([
                            'no_kwitansi' => Payment::generateNoKwitansi($bill->semester_id),
                            'date' => $date,
                            'salesperson_id' => $salesperson,
                            'semester_id' => $bill->semester_id,
                            'semester_bayar_id' => $semester,
                            'paid' => $paid,
                            'discount' => 0,
                            'amount' => $paid,
                            'payment_method' => $payment_method,
                            'note' => $note
                        ]);

                        TransactionService::createTransaction($date, 'Pembayaran dengan No Kwitansi ' .$payment->no_kwitansi.' dan Catatan :'. $note, $salesperson, $bill->semester_id, 'bayar', $payment->id, $payment->no_kwitansi, $paid, 'credit');
                        TransactionService::createTransaction($date, 'Diskon Dari Pembayaran dengan No Kwitansi ' .$payment->no_kwitansi.' dan Catatan :'. $note, $salesperson, $bill->semester_id, 'potongan', $payment->id, $payment->no_kwitansi, 0, 'credit');

                        $bayar -= $paid;
                    }
                }
            }
            if ($bayar > 0 || $diskon > 0) {
                $payment = Payment::create([
                    'no_kwitansi' => Payment::generateNoKwitansi($semester),
                    'date' => $date,
                    'salesperson_id' => $salesperson,
                    'semester_id' => $semester,
                    'semester_bayar_id' => $semester,
                    'paid' => $bayar,
                    'discount' => $diskon,
                    'amount' => $bayar + $diskon,
                    'payment_method' => $payment_method,
                    'note' => $note
                ]);

                TransactionService::createTransaction($date, 'Pembayaran dengan No Kwitansi ' .$payment->no_kwitansi.' dan Catatan :'. $note, $salesperson, $semester, 'bayar', $payment->id, $payment->no_kwitansi, $bayar, 'credit');
                TransactionService::createTransaction($date, 'Diskon Dari Pembayaran dengan No Kwitansi ' .$payment->no_kwitansi.' dan Catatan :'. $note, $salesperson, $semester, 'potongan', $payment->id, $payment->no_kwitansi, $diskon, 'credit');
            }
            DB::commit();

            Alert::success('Success', 'Pembayaran berhasil di simpan');

            return redirect()->route('admin.payments.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(Payment $payment)
    {
        abort_if(Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_kwitansi = noRevisi($payment->no_kwitansi);

        $payment->load('salesperson', 'semester');

        return view('admin.payments.edit', compact('payment', 'salespeople', 'semesters', 'no_kwitansi'));
    }

    public function update(Request $request, Payment $payment)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'no_kwitansi' => 'required',
            'date' => 'required',
            'salesperson_id' => 'required',
            'semester_id' => 'required',
            'payment_method' => 'required',
            'bayar' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric',
            'nominal' => 'required|numeric|min:0',
            'note' => 'nullable'
        ]);

        $no_kwitansi = $validatedData['no_kwitansi'];
        $date = $validatedData['date'];
        $salesperson = $validatedData['salesperson_id'];
        $semester = $validatedData['semester_id'];
        $payment_method = $validatedData['payment_method'];
        $bayar = $validatedData['bayar'];
        $diskon = $validatedData['diskon'];
        $nominal = $validatedData['nominal'];
        $note = $validatedData['note'];

        DB::beginTransaction();
        try {
            $payment->update([
                'no_kwitansi' => $no_kwitansi,
                'date' => $date,
                'salesperson_id' => $salesperson,
                'semester_id' => $semester,
                'paid' => $bayar,
                'discount' => $diskon,
                'amount' => $nominal,
                'payment_method' => $payment_method,
                'note' => $note
            ]);

            TransactionService::editTransaction($date, 'Pembayaran dengan No Kwitansi ' .$no_kwitansi.' dan Catatan :'. $note, $salesperson, $semester, 'bayar', $payment->id, $no_kwitansi, $bayar, 'credit');
            TransactionService::editTransaction($date, 'Diskon dari Pembayaran dengan No Kwitansi ' .$no_kwitansi.' dan Catatan :'. $note, $salesperson, $semester, 'potongan', $payment->id, $no_kwitansi, $diskon, 'credit');

            DB::commit();

            Alert::success('Success', 'Pembayaran berhasil di simpan');

            return redirect()->route('admin.payments.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
        $payment->update($request->all());

        return redirect()->route('admin.payments.index');
    }

    public function show(Payment $payment)
    {
        abort_if(Gate::denies('payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->load('salesperson', 'semester');

        return view('admin.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        abort_if(Gate::denies('payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->delete();

        return back();
    }

    public function massDestroy(MassDestroyPaymentRequest $request)
    {
        $payments = Payment::find(request('ids'));

        foreach ($payments as $payment) {
            $payment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getTagihan(Request $request)
    {
        $semester = setting('current_semester');
        $bills = collect([]);

        $bill = Bill::with('semester')->where('salesperson_id', $request->salesperson)->where('semester_id', $semester)->first();
        if ($bill) {
            $bills->push($bill);
        }
        do {
            $semester = prevSemester($semester);
            $bill = Bill::with('semester')->where('salesperson_id', $request->salesperson)->where('semester_id', $semester)->first();
            if ($bill && $bill->piutang > 0) {
                $bills->push($bill);
            }
        } while($bill && $bill->piutang > 0);

        if ($bills->count() > 0) {
            return response()->json(['status' => 'success', 'bills' => $bills]);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    public function kwitansi(Payment $payment)
    {
        return view('admin.payments.kwitansi', compact('payment'));
    }
}

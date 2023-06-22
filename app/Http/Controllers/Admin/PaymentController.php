<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPaymentRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use App\Services\TransactionService;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Payment::with(['salesperson', 'semester'])->select(sprintf('%s.*', (new Payment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'payment_show';
                $editGate      = 'payment_edit';
                $deleteGate    = 'payment_delete';
                $crudRoutePart = 'payments';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_kwitansi', function ($row) {
                return $row->no_kwitansi ? $row->no_kwitansi : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('paid', function ($row) {
                return $row->paid ? $row->paid : '';
            });
            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount : '';
            });
            $table->editColumn('payment_method', function ($row) {
                return $row->payment_method ? Payment::PAYMENT_METHOD_SELECT[$row->payment_method] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'salesperson', 'semester']);

            return $table->make(true);
        }

        return view('admin.payments.index');
    }

    public function create()
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.payments.create', compact('salespeople', 'semesters'));
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
        $semester = $validatedData['semester_id'];
        $payment_method = $validatedData['payment_method'];
        $bayar = $validatedData['bayar'];
        $diskon = $validatedData['diskon'];
        $nominal = $validatedData['nominal'];
        $note = $validatedData['note'];

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'no_kwitansi' => Payment::generateNoKwitansi($semester),
                'date' => $date,
                'salesperson_id' => $salesperson,
                'semester_id' => $semester,
                'paid' => $bayar,
                'discount' => $diskon,
                'amount' => $nominal,
                'payment_method' => $payment_method,
                'note' => $note
            ]);

            TransactionService::createTransaction($date, $note, $salesperson, $semester, 'bayar', $payment->id, $payment->no_kwitansi, $bayar, 'credit');
            TransactionService::createTransaction($date, $note, $salesperson, $semester, 'diskon', $payment->id, $payment->no_kwitansi, $diskon, 'credit');

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

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $payment->load('salesperson', 'semester');

        return view('admin.payments.edit', compact('payment', 'salespeople', 'semesters'));
    }

    public function update(Request $request, Payment $payment)
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
        $semester = $validatedData['semester_id'];
        $payment_method = $validatedData['payment_method'];
        $bayar = $validatedData['bayar'];
        $diskon = $validatedData['diskon'];
        $nominal = $validatedData['nominal'];
        $note = $validatedData['note'];

        $reference_no = $payment->no_kwitansi;

        DB::beginTransaction();
        try {
            $payment->update([
                'date' => $date,
                'salesperson_id' => $salesperson,
                'semester_id' => $semester,
                'paid' => $bayar,
                'discount' => $diskon,
                'amount' => $nominal,
                'payment_method' => $payment_method,
                'note' => $note
            ]);

            TransactionService::editTransaction($date, $note, $salesperson, $semester, 'bayar', $payment->id, $reference_no, $bayar, 'credit');
            TransactionService::editTransaction($date, $note, $salesperson, $semester, 'diskon', $payment->id, $reference_no, $diskon, 'credit');

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
}

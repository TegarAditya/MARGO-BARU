<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductionPaymentRequest;
use App\Http\Requests\StoreProductionPaymentRequest;
use App\Http\Requests\UpdateProductionPaymentRequest;
use App\Models\ProductionPayment;
use App\Models\ProductionTransactionTotal;
use App\Models\Semester;
use App\Models\Vendor;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use Alert;
use App\Services\TransactionService;

class ProductionPaymentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('production_payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductionPayment::with(['vendor', 'semester'])->select(sprintf('%s.*', (new ProductionPayment)->table));

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
                    <a class="px-1" href="'.route('admin.production-payments.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.production-payments.kwitansi', $row->id).'" target="_blank" title="Print Kwitansi" >
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.production-payments.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('no_payment', function ($row) {
                return $row->no_payment ? $row->no_payment : '';
            });

            $table->addColumn('vendor_name', function ($row) {
                return $row->vendor ? $row->vendor->full_name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('payment_method', function ($row) {
                return $row->payment_method ? ProductionPayment::PAYMENT_METHOD_SELECT[$row->payment_method] : '';
            });

            $table->editColumn('nominal', function ($row) {
                return money($row->nominal);
            });

            $table->rawColumns(['actions', 'placeholder', 'vendor', 'semester']);

            return $table->make(true);
        }

        return view('admin.productionPayments.index');
    }

    public function create()
    {
        abort_if(Gate::denies('production_payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $vendors = Vendor::whereHas('fee')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $vendors = Vendor::all()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_payment = ProductionPayment::generateNoPayment(setting('current_semester'));

        $today= Carbon::now()->format('d-m-Y');

        return view('admin.productionPayments.create', compact('vendors', 'no_payment', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'vendor_id' => 'required',
            'payment_method' => 'nullable',
            'nominal' => 'required|numeric|min:1',
            'note' => 'nullable'
        ]);

        $date = $validatedData['date'];
        $vendor = $validatedData['vendor_id'];
        $semester = setting('current_semester');
        $payment_method = $validatedData['payment_method'];
        $nominal = $validatedData['nominal'];
        $note = $validatedData['note'];

        DB::beginTransaction();
        try {
            $payment = ProductionPayment::create([
                'no_payment' => ProductionPayment::generateNoPayment($semester),
                'date' => $date,
                'vendor_id' => $vendor,
                'semester_id' => $semester,
                'nominal' => $nominal,
                'payment_method' => $payment_method,
                'note' => $note
            ]);

            TransactionService::createProductionTransaction($date, 'Pembayaran dengan No Payment ' .$payment->no_payment.' dan Catatan :'. $note, $vendor, $semester, 'bayar', $payment->id, $payment->no_payment, $nominal, 'debet');

            DB::commit();

            Alert::success('Success', 'Pembayaran berhasil di simpan');

            return redirect()->route('admin.production-payments.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }

        $productionPayment = ProductionPayment::create($request->all());

        return redirect()->route('admin.production-payments.index');
    }

    public function edit(ProductionPayment $productionPayment)
    {
        abort_if(Gate::denies('production_payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::whereHas('fee')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productionPayment->load('vendor', 'semester');

        return view('admin.productionPayments.edit', compact('productionPayment', 'vendors'));
    }

    public function update(Request $request, ProductionPayment $productionPayment)
    {
        TransactionService::editProductionTransaction($request->date, 'Pembayaran dengan No Payment ' .$productionPayment->no_payment.' dan Catatan :'. $request->note, $request->vendor_id, $productionPayment->semester_id, 'bayar', $productionPayment->id, $productionPayment->no_payment, $request->nominal, 'debet');

        $productionPayment->update($request->all());

        Alert::success('Success', 'Pembayaran berhasil di simpan');

        return redirect()->route('admin.production-payments.index');
    }

    public function show(ProductionPayment $productionPayment)
    {
        abort_if(Gate::denies('production_payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionPayment->load('vendor', 'semester');

        return view('admin.productionPayments.show', compact('productionPayment'));
    }

    public function destroy(ProductionPayment $productionPayment)
    {
        abort_if(Gate::denies('production_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionPayment->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductionPaymentRequest $request)
    {
        $productionPayments = ProductionPayment::find(request('ids'));

        foreach ($productionPayments as $productionPayment) {
            $productionPayment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getTagihan(Request $request) {

        $vendor = $request->vendor;
        $total = ProductionTransactionTotal::where('vendor_id', $vendor)->first();

        if ($total) {
            return response()->json(['status' => 'success', 'tagihan' => $total]);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    public function kwitansi(ProductionPayment $productionPayment)
    {
        return view('admin.productionPayments.kwitansi', compact('productionPayment'));
    }
}

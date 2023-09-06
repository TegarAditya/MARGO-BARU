<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBillAdjustmentRequest;
use App\Http\Requests\StoreBillAdjustmentRequest;
use App\Http\Requests\UpdateBillAdjustmentRequest;
use App\Models\BillAdjustment;
use App\Models\Salesperson;
use App\Models\Semester;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use Alert;
use App\Services\TransactionService;

class BillAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('bill_adjustment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BillAdjustment::with(['salesperson', 'semester'])->select(sprintf('%s.*', (new BillAdjustment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return '
                    <a class="px-1" href="'.route('admin.bill-adjustments.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.bill-adjustments.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('no_adjustment', function ($row) {
                return $row->no_adjustment ? $row->no_adjustment : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('amount', function ($row) {
                return money($row->amount);
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'salesperson', 'semester']);

            return $table->make(true);
        }

        return view('admin.billAdjustments.index');
    }

    public function create()
    {
        abort_if(Gate::denies('bill_adjustment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $no_adjustment = BillAdjustment::generateNoAdjustment(setting('current_semester'));
        $today = Carbon::now()->format('d-m-Y');

        return view('admin.billAdjustments.create', compact('salespeople', 'no_adjustment', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'salesperson_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable'
        ]);

        $date = $validatedData['date'];
        $salesperson = $validatedData['salesperson_id'];
        $semester = setting('current_semester');
        $amount = $validatedData['amount'];
        $note = $validatedData['note'];

        DB::beginTransaction();
        try {
            $adjustment = BillAdjustment::create([
                'no_adjustment' => BillAdjustment::generateNoAdjustment($semester),
                'date' => $date,
                'salesperson_id' => $salesperson,
                'semester_id' => $semester,
                'amount' => $amount,
                'note' => $note
            ]);

            TransactionService::createTransaction($date, 'Adjustment dengan No Adjustment ' .$adjustment->no_adjustment.' dan Catatan : '. $note, $salesperson, $semester, 'adjustment', $adjustment->id, $adjustment->no_adjustment, $amount, 'credit');

            DB::commit();

            Alert::success('Success', 'Adjustment berhasil di simpan');

            return redirect()->route('admin.bill-adjustments.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(BillAdjustment $billAdjustment)
    {
        abort_if(Gate::denies('bill_adjustment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_adjustment = noRevisi($billAdjustment->no_adjustment);

        $billAdjustment->load('salesperson', 'semester');

        return view('admin.billAdjustments.edit', compact('billAdjustment', 'salespeople', 'no_adjustment'));
    }

    public function update(Request $request, BillAdjustment $billAdjustment)
    {
         // Validate the form data
         $validatedData = $request->validate([
            'no_adjustment' => 'required',
            'date' => 'required',
            'salesperson_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable'
        ]);

        $no_adjustment = $validatedData['no_adjustment'];
        $date = $validatedData['date'];
        $salesperson = $validatedData['salesperson_id'];
        $semester = setting('current_semester');
        $amount = $validatedData['amount'];
        $note = $validatedData['note'];

        DB::beginTransaction();
        try {
            $billAdjustment->update([
                'no_adjustment' => $no_adjustment ,
                'date' => $date,
                'salesperson_id' => $salesperson,
                'semester_id' => $semester,
                'amount' => $amount,
                'note' => $note
            ]);

            TransactionService::editTransaction($date, 'Adjustment dengan No Adjustment ' .$billAdjustment->no_adjustment.' dan Catatan : '. $note, $salesperson, $semester, 'adjustment', $billAdjustment->id, $no_adjustment, $amount, 'credit');

            DB::commit();

            Alert::success('Success', 'Adjustment berhasil di simpan');

            return redirect()->route('admin.bill-adjustments.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show(BillAdjustment $billAdjustment)
    {
        abort_if(Gate::denies('bill_adjustment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $billAdjustment->load('salesperson', 'semester', 'created_by', 'updated_by');

        return view('admin.billAdjustments.show', compact('billAdjustment'));
    }

    public function destroy(BillAdjustment $billAdjustment)
    {
        abort_if(Gate::denies('bill_adjustment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $billAdjustment->delete();

        return back();
    }

    public function massDestroy(MassDestroyBillAdjustmentRequest $request)
    {
        $billAdjustments = BillAdjustment::find(request('ids'));

        foreach ($billAdjustments as $billAdjustment) {
            $billAdjustment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

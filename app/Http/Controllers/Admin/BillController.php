<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BillController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Bill::with(['semester', 'salesperson'])->select(sprintf('%s.*', (new Bill)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'bill_show';
                $editGate      = 'bill_edit';
                $deleteGate    = 'bill_delete';
                $crudRoutePart = 'bills';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
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
}

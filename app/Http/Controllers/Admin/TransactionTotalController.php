<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionTotal;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TransactionTotalController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('transaction_total_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = TransactionTotal::with(['salesperson'])->select(sprintf('%s.*', (new TransactionTotal)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'transaction_total_show';
                $editGate      = 'transaction_total_edit';
                $deleteGate    = 'transaction_total_delete';
                $crudRoutePart = 'transaction-totals';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('salesperson_code', function ($row) {
                return $row->salesperson ? $row->salesperson->code : '';
            });

            $table->editColumn('total_invoice', function ($row) {
                return $row->total_invoice ? $row->total_invoice : '';
            });
            $table->editColumn('total_diskon', function ($row) {
                return $row->total_diskon ? $row->total_diskon : '';
            });
            $table->editColumn('total_retur', function ($row) {
                return $row->total_retur ? $row->total_retur : '';
            });
            $table->editColumn('total_bayar', function ($row) {
                return $row->total_bayar ? $row->total_bayar : '';
            });
            $table->editColumn('total_potongan', function ($row) {
                return $row->total_potongan ? $row->total_potongan : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'salesperson']);

            return $table->make(true);
        }

        return view('admin.transactionTotals.index');
    }

    public function show(TransactionTotal $transactionTotal)
    {
        abort_if(Gate::denies('transaction_total_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $transactionTotal->load('salesperson');

        return view('admin.transactionTotals.show', compact('transactionTotal'));
    }
}

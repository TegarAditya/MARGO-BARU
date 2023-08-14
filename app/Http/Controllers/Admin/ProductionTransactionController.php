<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductionTransactionRequest;
use App\Http\Requests\StoreProductionTransactionRequest;
use App\Http\Requests\UpdateProductionTransactionRequest;
use App\Models\Invoice;
use App\Models\ProductionTransaction;
use App\Models\Semester;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vendor;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductionTransactionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('production_transaction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductionTransaction::with(['vendor', 'semester', 'reference', 'reversal_of', 'created_by', 'updated_by'])->select(sprintf('%s.*', (new ProductionTransaction)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'production_transaction_show';
                $editGate      = 'production_transaction_edit';
                $deleteGate    = 'production_transaction_delete';
                $crudRoutePart = 'production-transactions';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->addColumn('vendor_name', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? ProductionTransaction::TYPE_SELECT[$row->type] : '';
            });
            $table->addColumn('reference_no_faktur', function ($row) {
                return $row->reference ? $row->reference->no_faktur : '';
            });

            $table->editColumn('reference_no', function ($row) {
                return $row->reference_no ? $row->reference_no : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });
            $table->editColumn('category', function ($row) {
                return $row->category ? ProductionTransaction::CATEGORY_SELECT[$row->category] : '';
            });
            $table->editColumn('status', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->status ? 'checked' : null) . '>';
            });
            $table->addColumn('reversal_of_description', function ($row) {
                return $row->reversal_of ? $row->reversal_of->description : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('updated_by_name', function ($row) {
                return $row->updated_by ? $row->updated_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'vendor', 'semester', 'reference', 'status', 'reversal_of', 'created_by', 'updated_by']);

            return $table->make(true);
        }

        return view('admin.productionTransactions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('production_transaction_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $references = Invoice::pluck('no_faktur', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reversal_ofs = Transaction::pluck('description', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productionTransactions.create', compact('created_bies', 'references', 'reversal_ofs', 'semesters', 'updated_bies', 'vendors'));
    }

    public function store(StoreProductionTransactionRequest $request)
    {
        $productionTransaction = ProductionTransaction::create($request->all());

        return redirect()->route('admin.production-transactions.index');
    }

    public function edit(ProductionTransaction $productionTransaction)
    {
        abort_if(Gate::denies('production_transaction_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $references = Invoice::pluck('no_faktur', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reversal_ofs = Transaction::pluck('description', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productionTransaction->load('vendor', 'semester', 'reference', 'reversal_of', 'created_by', 'updated_by');

        return view('admin.productionTransactions.edit', compact('created_bies', 'productionTransaction', 'references', 'reversal_ofs', 'semesters', 'updated_bies', 'vendors'));
    }

    public function update(UpdateProductionTransactionRequest $request, ProductionTransaction $productionTransaction)
    {
        $productionTransaction->update($request->all());

        return redirect()->route('admin.production-transactions.index');
    }

    public function show(ProductionTransaction $productionTransaction)
    {
        abort_if(Gate::denies('production_transaction_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionTransaction->load('vendor', 'semester', 'reference', 'reversal_of', 'created_by', 'updated_by');

        return view('admin.productionTransactions.show', compact('productionTransaction'));
    }

    public function destroy(ProductionTransaction $productionTransaction)
    {
        abort_if(Gate::denies('production_transaction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productionTransaction->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductionTransactionRequest $request)
    {
        $productionTransactions = ProductionTransaction::find(request('ids'));

        foreach ($productionTransactions as $productionTransaction) {
            $productionTransaction->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

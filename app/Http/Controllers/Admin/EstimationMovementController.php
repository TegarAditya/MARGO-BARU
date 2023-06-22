<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstimationMovement;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class EstimationMovementController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('estimation_movement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = EstimationMovement::with(['reference', 'product', 'reversal_of'])->select(sprintf('%s.*', (new EstimationMovement)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'estimation_movement_show';
                $editGate      = 'estimation_movement_edit';
                $deleteGate    = 'estimation_movement_delete';
                $crudRoutePart = 'estimation-movements';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('movement_type', function ($row) {
                return $row->movement_type ? EstimationMovement::MOVEMENT_TYPE_SELECT[$row->movement_type] : '';
            });
            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });
            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '-';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? EstimationMovement::TYPE_SELECT[$row->type] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'product']);

            return $table->make(true);
        }

        return view('admin.estimationMovements.index');
    }
}

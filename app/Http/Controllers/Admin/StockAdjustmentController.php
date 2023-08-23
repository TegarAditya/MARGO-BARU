<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockAdjustmentRequest;
use App\Http\Requests\StoreStockAdjustmentRequest;
use App\Http\Requests\UpdateStockAdjustmentRequest;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use App\Services\StockService;
use Carbon\Carbon;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_adjustment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockAdjustment::query()->select(sprintf('%s.*', (new StockAdjustment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return '
                    <a class="px-1" href="'.route('admin.stock-adjustments.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.stock-adjustments.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('operation', function ($row) {
                return $row->operation ? StockAdjustment::OPERATION_SELECT[$row->operation] : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? StockAdjustment::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('reason', function ($row) {
                return $row->reason ? $row->reason : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.stockAdjustments.index');
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('stock_adjustment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $today = Carbon::now()->format('d-m-Y');

        if ($request->type == 'material') {
            return view('admin.stockAdjustments.createMaterial', compact('today'));
        }

        return view('admin.stockAdjustments.create', compact('today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' =>'required',
            'operation' => 'required',
            'type' => 'required',
            'reason' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $operation = $validatedData['operation'];
        $type = $validatedData['type'];
        $reason = $validatedData['reason'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $adjustment = StockAdjustment::create([
                'date' => $date,
                'type' => $type,
                'operation' => $operation,
                'reason' => $reason,
                'note' => $note,
            ]);

            $multiplier = ($operation == 'increase') ? 1 : -1;
            $movement = ($operation == 'increase') ? 'in' : 'out';

            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];
                $quantity = $quantities[$i];

                if ($type == 'book') {
                    $adjustment_item = StockAdjustmentDetail::create([
                        'product_id' => $product,
                        'stock_adjustment_id' => $adjustment->id,
                        'quantity' => $quantity
                    ]);

                    StockService::createMovement($movement, 'adjustment', $adjustment->id, $date, $product, $multiplier * $quantity);
                    StockService::updateStock($product, $multiplier * $quantity);
                } else if($type == 'material') {
                    $adjustment_item = StockAdjustmentDetail::create([
                        'material_id' => $product,
                        'stock_adjustment_id' => $adjustment->id,
                        'quantity' => $quantity
                    ]);

                    StockService::createMovementMaterial($movement, 'adjustment', $adjustment->id, $date, $product, $multiplier * $quantity);
                    StockService::updateStockMaterial($product, $multiplier * $quantity);
                }
            }

            DB::commit();

            Alert::success('Success', 'Stock Adjustment berhasil di simpan');

            return redirect()->route('admin.stock-adjustments.index');;
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        abort_if(Gate::denies('stock_adjustment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($stockAdjustment->type == 'material') {
            return view('admin.stockAdjustments.editMaterial', compact('stockAdjustment'));
        }

        return view('admin.stockAdjustments.edit', compact('stockAdjustment'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' =>'required',
            'type' => 'required',
            'reason' => 'required',
            'note' => 'nullable',
            'adjustment_details' => 'required|array',
            'adjustment_details.*' => 'exists:stock_adjustment_details,id',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $type = $validatedData['type'];
        $reason = $validatedData['reason'];
        $note = $validatedData['note'];
        $adjustment_details = $validatedData['adjustment_details'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        $operation = $stockAdjustment->operation;

        DB::beginTransaction();
        try {
            $stockAdjustment->update([
                'date' => $date,
                'reason' => $reason,
                'note' => $note,
            ]);

            $multiplier = ($operation == 'increase') ? 1 : -1;
            $movement = ($operation == 'increase') ? 'in' : 'out';

            for ($i = 0; $i < count($products); $i++) {
                $adjustment_detail = $adjustment_details[$i];
                $product = $products[$i];
                $quantity = $quantities[$i];

                $adjustment_item = StockAdjustmentDetail::find($adjustment_detail);

                $old_quantity = $adjustment_item->quantity;
                $adjustment_item->quantity = $quantity;

                $adjustment_item->save();

                if ($type == 'book') {
                    StockService::editMovement($movement, 'adjustment', $stockAdjustment->id, $date, $product, $multiplier * $quantity);
                    StockService::updateStock($product, ($multiplier * $quantity) - ($multiplier * $old_quantity));
                } else if($type == 'material') {
                    StockService::editMovementMaterial($movement, 'adjustment', $stockAdjustment->id, $date, $product, $multiplier * $quantity);
                    StockService::updateStockMaterial($product, ($multiplier * $quantity) - ($multiplier * $old_quantity));
                }
            }

            DB::commit();

            Alert::success('Success', 'Stock Adjustment berhasil di simpan');

            return redirect()->route('admin.stock-adjustments.index');;
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        abort_if(Gate::denies('stock_adjustment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $adjustment_details = StockAdjustmentDetail::where('stock_adjustment_id', $stockAdjustment->id)->get();

        return view('admin.stockAdjustments.show', compact('stockAdjustment', 'adjustment_details'));
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        abort_if(Gate::denies('stock_adjustment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockAdjustment->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockAdjustmentRequest $request)
    {
        $stockAdjustments = StockAdjustment::find(request('ids'));

        foreach ($stockAdjustments as $stockAdjustment) {
            $stockAdjustment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

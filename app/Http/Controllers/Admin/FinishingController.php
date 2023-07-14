<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFinishingRequest;
use App\Http\Requests\StoreFinishingRequest;
use App\Http\Requests\UpdateFinishingRequest;
use App\Models\Finishing;
use App\Models\FinishingItem;
use App\Models\Semester;
use App\Models\Vendor;
use App\Models\BookVariant;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Carbon\Carbon;
use App\Services\EstimationService;
use App\Services\StockService;

class FinishingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('finishing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Finishing::with(['semester', 'vendor'])->select(sprintf('%s.*', (new Finishing)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'finishing_show';
                $editGate      = 'finishing_edit';
                $deleteGate    = 'finishing_delete';
                $crudRoutePart = 'finishings';

                $btn = '
                    <a class="px-1" href="'.route('admin.finishings.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.finishings.printSpk', $row->id).'" title="Print SPK">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.finishings.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.finishings.realisasi', $row->id).'" title="Realisasi">
                        <i class="fas fa-tasks text-danger fa-lg"></i>
                    </a>
                ';

                return $btn;
            });

            $table->editColumn('no_spk', function ($row) {
                return $row->no_spk ? $row->no_spk : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('vendor_name', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
            });

            $table->editColumn('total_cost', function ($row) {
                return $row->total_cost ? $row->total_cost : '';
            });
            $table->editColumn('total_oplah', function ($row) {
                return $row->total_oplah ? $row->total_oplah : '';
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'vendor']);

            return $table->make(true);
        }

        return view('admin.finishings.index');
    }

    public function create()
    {
        abort_if(Gate::denies('finishing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'finishing')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.finishings.create', compact('semesters', 'vendors'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'semester_id' => 'required',
            'vendor_id' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = $validatedData['semester_id'];
        $vendor = $validatedData['vendor_id'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $finishing = Finishing::create([
                'no_spk' => Finishing::generateNoSPK($semester, $vendor),
                'date' => $date,
                'semester_id' => $semester,
                'vendor_id' => $vendor,
                'estimasi_oplah' => array_sum($quantities),
                'total_cost' => array_sum($quantities) * 100,
                'note' => $note
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];

                $finishing_item = FinishingItem::create([
                    'finishing_id' => $finishing->id,
                    'semester_id' => $semester,
                    'product_id' => $product->id,
                    'estimasi' => $quantity,
                    'quantity'=> $quantity,
                    'cost' => 0,
                    'done' => 0,
                ]);

                EstimationService::createMovement('out', 'finishing', $finishing->id, $product->id, -1 * $quantity, $product->type);
                EstimationService::createProduction($product->id, -1 * $quantity, $product->type);

                foreach($product->components as $item) {
                    StockService::createMovement('out', 'produksi', $finishing->id, $date, $item->id, -1 * $quantity);
                    StockService::updateStock($item->id, -1 * $quantity);
                }
            }

            DB::commit();

            Alert::success('Success', 'Finishing Order berhasil di simpan');

            return redirect()->route('admin.finishings.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing_items = FinishingItem::with('product', 'product.components')->where('finishing_id', $finishing->id)->get();

        if ($finishing_items->min('done') > 0) {
            return redirect()->route('admin.finishings.show', $finishing->id);
        }

        $finishing->load('semester', 'vendor');

        return view('admin.finishings.edit', compact('finishing', 'semesters', 'vendors', 'finishing_items'));
    }

    public function update(Request $request, Finishing $finishing)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'finishing_items' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $finishing_items = $validatedData['finishing_items'];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];
                $finishing_item = $finishing_items[$i];

                if ($finishing_item) {
                    $detail = FinishingItem::find($finishing_item);
                    $old_quantity = $detail->estimasi;
                    $detail->update([
                        'estimasi' => $quantity,
                        'quantity' => $quantity,
                    ]);

                    EstimationService::editMovement('out', 'finishing', $finishing->id, $product->id, -1 * $quantity, $product->type);
                    EstimationService::editProduction($product->id, ($quantity - $old_quantity), $product->type);

                    foreach($product->components as $item) {
                        StockService::editMovement('out', 'produksi', $finishing->id, $date, $item->id, -1 * $quantity);
                        StockService::updateStock($item->id, ($quantity - $old_quantity));
                    }
                } else {
                    $detail = FinishingItem::create([
                        'finishing_id' => $finishing->id,
                        'semester_id' => $finishing->semester_id,
                        'product_id' => $product->id,
                        'estimasi' => $quantity,
                        'quantity'=> $quantity,
                        'cost' => 0,
                        'done' => 0,
                    ]);

                    EstimationService::createMovement('out', 'finishing', $finishing->id, $product->id, -1 * $quantity, $product->type);
                    EstimationService::createProduction($product->id, -1 * $quantity, $product->type);

                    foreach($product->components as $item) {
                        StockService::createMovement('out', 'produksi', $finishing->id, $date, $item->id, -1 * $quantity);
                        StockService::updateStock($item->id, -1 * $quantity);
                    }
                }
            }

            $finishing->update([
                'date' => $date,
                'estimasi_oplah' => array_sum($quantities),
                'total_cost' => array_sum($quantities) * 100,
                'note' => $note
            ]);

            DB::commit();

            Alert::success('Success', 'Finishing Order berhasil di simpan');

            return redirect()->route('admin.finishings.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }

        $finishing->update($request->all());

        return redirect()->route('admin.finishings.index');
    }

    public function show(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishing->load('semester', 'vendor');

        $finishing_items = FinishingItem::with('product')->where('finishing_id', $finishing->id)->get();

        return view('admin.finishings.show', compact('finishing', 'finishing_items'));
    }

    public function destroy(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $finishing->delete();

        return back();
    }

    public function massDestroy(MassDestroyFinishingRequest $request)
    {
        $finishings = Finishing::find(request('ids'));

        foreach ($finishings as $finishing) {
            $finishing->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function realisasi(Finishing $finishing)
    {
        abort_if(Gate::denies('finishing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing->load('semester', 'vendor');

        $finishing_items = FinishingItem::with('product', 'semester')->where('finishing_id', $finishing->id)->orderBy('product_id')->get();

        return view('admin.finishings.realisasi', compact('finishing', 'semesters', 'vendors', 'finishing_items'));
    }

    public function realisasiStore(Request $request, Finishing $finishing)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'finishing_items' => 'required|array',
            'finishing_items.*' => 'exists:finishing_items,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'done' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $products = $validatedData['products'];
        $finishing_items = $validatedData['finishing_items'];
        $quantities = $validatedData['quantities'];
        $done = $validatedData['done'];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $finishing_item = FinishingItem::find($finishing_items[$i]);

                $status = $done[$i];
                if ($finishing_item->done || !$status) {
                    continue;
                }

                $product = $products[$i];
                $quantity = $quantities[$i];

                $finishing_item->update([
                    'quantity' => $quantity,
                    'done' => $status
                ]);

                StockService::createMovement('in', 'produksi', $finishing->id, $date, $product, $quantity);
                StockService::updateStock($product, $quantity);
            }

            $finishing->update([
                'date' => $date,
                'total_oplah' => array_sum($quantities),
            ]);

            DB::commit();

            Alert::success('Success', 'Finishing Order berhasil di simpan');

            return redirect()->route('admin.finishings.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function printSpk(Finishing $finishing, Request $request)
    {
        $finishing->load('semester', 'vendor');

        $finishing_items = FinishingItem::with('product')->where('finishing_id', $finishing->id)->get();

        return view('admin.finishings.spk', compact('finishing', 'finishing_items'));
    }
}

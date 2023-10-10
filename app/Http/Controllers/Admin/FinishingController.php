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
use App\Models\Halaman;
use App\Models\Jenjang;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Carbon\Carbon;
use App\Services\EstimationService;
use App\Services\StockService;
use App\Exports\FinishingRekapExport;
use Illuminate\Support\Facades\Date;

class FinishingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('finishing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Finishing::with(['semester', 'vendor', 'finishing_items'])->select(sprintf('%s.*', (new Finishing)->table))->latest();
            if (!empty($request->vendor)) {
                $query->where('vendor_id', $request->vendor);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $finishingStatus = $row->finishing_items->where('done', '=', 1)->count();

                if ($finishingStatus == 0) {
                    $realisasi = 'danger';
                } elseif ($finishingStatus == $row->finishing_items->count()) {
                    $realisasi = 'success';
                } else {
                    $realisasi = 'warning';
                }

                $btn = '
                    <a class="px-1" href="'.route('admin.finishings.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.finishings.printSpk', $row->id).'" title="Print SPK" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.finishings.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.finishings.realisasi', $row->id).'" title="Realisasi '.Finishing::TIPE_REALISASI[$realisasi].'">
                        <i class="fas fa-tasks text-'.$realisasi.' fa-lg"></i>
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
                return $row->total_cost ? money($row->total_cost) : '';
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

        $vendors = Vendor::where('type', 'finishing')->get()->pluck('full_name', 'id')->prepend('All', '');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.finishings.index', compact('vendors', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('finishing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::where('type', 'finishing')->orderBy('code', 'ASC')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_spk = Finishing::generateNoSPKTemp(setting('current_semester'));

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.finishings.create', compact('vendors', 'jenjangs', 'no_spk', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            // 'semester_id' => 'required',
            'jenjang_id' => 'required',
            'vendor_id' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = setting('current_semester');
        $vendor = $validatedData['vendor_id'];
        $jenjang = $validatedData['jenjang_id'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $finishing = Finishing::create([
                'no_spk' => Finishing::generateNoSPK($semester, $vendor),
                'date' => $date,
                'semester_id' => $semester,
                'jenjang_id' => $jenjang,
                'vendor_id' => $vendor,
                'estimasi_oplah' => array_sum($quantities),
                'note' => $note
            ]);

            $total_cost = 0;

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];

                $halaman = Halaman::find($product->halaman_id)->value;
                $cost = $this->costFinishing($halaman, $quantity);
                $total_cost += $cost;

                $finishing_item = FinishingItem::create([
                    'finishing_id' => $finishing->id,
                    'semester_id' => $semester,
                    'product_id' => $product->id,
                    'estimasi' => $quantity,
                    'quantity'=> 0,
                    'cost' => $cost,
                    'done' => 0,
                ]);

                EstimationService::createMovement('out', 'finishing', $finishing->id, $product->id, $quantity, 'produksi');
                EstimationService::createCetak($product->id, $quantity, $product->type);

                foreach($product->components as $item) {
                    StockService::createMovement('out', 'produksi', $finishing->id, $date, $item->id, -1 * $quantity);
                    StockService::updateStock($item->id, -1 * $quantity);
                }
            }

            $finishing->total_cost = $total_cost;
            $finishing->save();

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

        $vendors = Vendor::where('type', 'finishing')->orderBy('code', 'ASC')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing_items = FinishingItem::with('product', 'product.components')->where('finishing_id', $finishing->id)->get();

        if ($finishing_items->min('done') > 0) {
            return redirect()->route('admin.finishings.show', $finishing->id);
        }

        $finishing->load('semester', 'vendor', 'jenjang');

        return view('admin.finishings.edit', compact('finishing', 'vendors', 'finishing_items', 'jenjangs'));
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
        $total_cost = 0;

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];
                $finishing_item = $finishing_items[$i];

                $halaman = Halaman::find($product->halaman_id)->value;
                $cost = $this->costFinishing($halaman, $quantity);
                $total_cost += $cost;

                if ($finishing_item) {
                    $detail = FinishingItem::find($finishing_item);
                    $old_quantity = $detail->estimasi;
                    $detail->update([
                        'estimasi' => $quantity,
                        'quantity' => 0,
                        'cost' => $cost,
                    ]);

                    EstimationService::editMovement('out', 'finishing', $finishing->id, $product->id, $quantity, 'produksi');
                    EstimationService::editCetak($product->id, ($quantity - $old_quantity), $product->type);

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
                        'quantity'=> 0,
                        'cost' => $cost,
                        'done' => 0,
                    ]);

                    EstimationService::createMovement('out', 'finishing', $finishing->id, $product->id, $quantity, 'produksi');
                    EstimationService::createCetak($product->id, $quantity, $product->type);

                    foreach($product->components as $item) {
                        StockService::createMovement('out', 'produksi', $finishing->id, $date, $item->id, -1 * $quantity);
                        StockService::updateStock($item->id, -1 * $quantity);
                    }
                }
            }

            $finishing->update([
                'date' => $date,
                'estimasi_oplah' => array_sum($quantities),
                'total_cost' => $total_cost,
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

        $vendors = Vendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finishing->load('semester', 'vendor');

        $finishing_items = FinishingItem::with('product', 'semester')->where('finishing_id', $finishing->id)->orderBy('product_id')->get();

        return view('admin.finishings.realisasi', compact('finishing', 'vendors', 'finishing_items'));
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
            'quantities.*' => 'numeric|min:0',
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

                $quantity_old = $finishing_item->quantity;
                $product = $products[$i];
                $quantity = $quantities[$i];

                $finishing_item->update([
                    'quantity' => $quantity,
                    'done' => $status
                ]);

                EstimationService::createMovement('out', 'finishing', $finishing->id, $product, $quantity - $quantity_old, 'realisasi');
                EstimationService::createUpdateRealisasi($product, $quantity);

                StockService::createMovement('in', 'produksi', $finishing->id, $date, $product, $quantity - $quantity_old);
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

    public function masuk()
    {
        $vendors = Vendor::where('type', 'finishing')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.finishings.masuk', compact('vendors', 'jenjangs'));
    }

    public function masukStore(Request $request)
    {
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'finishing_items' => 'required|array',
            'finishing_items.*' => 'exists:finishing_items,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'done' => 'required|array'
        ]);

        $products = $validatedData['products'];
        $finishing_items = $validatedData['finishing_items'];
        $quantities = $validatedData['quantities'];
        $status = $validatedData['done'];
        $date = Carbon::now()->format('d-m-Y');
        $finishing = collect();

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $finishing_item = FinishingItem::with('finishing')->find($finishing_items[$i]);

                $finishing->push($finishing_item->finishing_id);

                $product = $products[$i];
                $quantity = $quantities[$i];
                $done = $status[$i];

                $finishing_item->update([
                    'quantity' => DB::raw("quantity + $quantity"),
                    'done' => $done
                ]);

                EstimationService::createMovement('out', 'finishing', $finishing_item->finishing->id, $product, $quantity, 'realisasi');
                EstimationService::createUpdateRealisasi($product, $quantity);

                StockService::createMovement('in', 'produksi', $finishing_item->finishing->id, $date, $product, $quantity);
                StockService::updateStock($product, $quantity);
            }

            $finishings = Finishing::whereIn('id', $finishing->unique())->get();

            foreach($finishings as $item) {
                $item->update([
                    'total_oplah' => $item->finishing_items->sum('quantity'),
                ]);
            }

            DB::commit();

            Alert::success('Success', 'Buku Masuk Finishing berhasil di simpan');

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

    public function rekap(Request $request)
    {
        if ($request->has('date') && $request->date && $dates = explode(' - ', $request->date)) {
            $start = Date::parse($dates[0])->startOfDay();
            $end = !isset($dates[1]) ? $start->clone()->endOfMonth() : Date::parse($dates[1])->endOfDay();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
        }

        $query = Finishing::whereBetween('date', [$start, $end]);

        if (!empty($request->type)) {
            $query->where('type', $request->type);
        }
        if (!empty($request->vendor)) {
            $query->where('vendor_id', $request->vendor);
        }
        if (!empty($request->semester)) {
            $query->where('semester_id', $request->semester);
        }

        $rekap = $query->orderBy('date', 'ASC')->get();

        return (new FinishingRekapExport($rekap))->download('REKAP FINISHING PERIODE ' . $start->format('d-F-Y') .' sd '. $end->format('d-F-Y') .'.xlsx');
    }

    function costFinishing($halaman, $quantity)
    {
        if ($halaman <= 64) {
            return 45 * $quantity;
        }
        if ($halaman <= 80) {
            return 50 * $quantity;
        }
        if ($halaman <= 96) {
            return 55 * $quantity;
        }
        if ($halaman <= 112) {
            return 60 * $quantity;
        }
        if ($halaman <= 128) {
            return 65 * $quantity;
        }

    }
}

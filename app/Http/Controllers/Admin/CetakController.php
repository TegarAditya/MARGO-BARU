<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCetakRequest;
use App\Http\Requests\StoreCetakRequest;
use App\Http\Requests\UpdateCetakRequest;
use App\Models\Cetak;
use App\Models\CetakItem;
use App\Models\Semester;
use App\Models\Material;
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

class CetakController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('cetak_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Cetak::with(['semester', 'vendor'])->select(sprintf('%s.*', (new Cetak)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.cetaks.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.cetaks.printSpc', $row->id).'" title="Print SPC">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.cetaks.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.cetaks.realisasi', $row->id).'" title="Realisasi">
                        <i class="fas fa-tasks text-danger fa-lg"></i>
                    </a>
                ';

                return $btn;
            });

            $table->editColumn('no_spc', function ($row) {
                return $row->no_spc ? $row->no_spc : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('vendor_name', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? Cetak::TYPE_SELECT[$row->type] : '';
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

        return view('admin.cetaks.index');
    }

    public function create()
    {
        abort_if(Gate::denies('cetak_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.cetaks.create', compact('semesters', 'vendors'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'semester_id' => 'required',
            'vendor_id' => 'required',
            'type' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = $validatedData['semester_id'];
        $vendor = $validatedData['vendor_id'];
        $type = $validatedData['type'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];

        DB::beginTransaction();
        try {
            $cetak = Cetak::create([
                'no_spc' => Cetak::generateNoSPC($semester, $vendor, $type),
                'date' => $date,
                'semester_id' => $semester,
                'vendor_id' => $vendor,
                'type' => $type,
                'estimasi_oplah' => array_sum($quantities),
                'note' => $note
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];

                $cetak_item = CetakItem::create([
                    'cetak_id' => $cetak->id,
                    'semester_id' => $semester,
                    'product_id' => $product->id,
                    'halaman_id' => $product->halaman_id,
                    'estimasi' => $quantity,
                    'quantity' => $quantity,
                    'plate_id' => $plate,
                    'plate_cost' => $plate_quantity,
                    'done' => 0,
                ]);

                EstimationService::createMovement('out', 'cetak', $cetak->id, $product->id, -1 * $quantity, $product->type);
                EstimationService::createProduction($product->id, -1 * $quantity, $product->type);
            }

            DB::commit();

            Alert::success('Success', 'Cetak Order berhasil di simpan');

            return redirect()->route('admin.cetaks.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(Cetak $cetak)
    {
        abort_if(Gate::denies('cetak_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::where('category', 'plate')->whereHas('vendors', function ($q) use ($cetak) {
            $q->where('id', $cetak->vendor_id);
        })->orderBy('code', 'ASC')->pluck('name', 'id');

        $cetak->load('semester', 'vendor');

        $cetak_items = CetakItem::with('product', 'semester', 'product.estimasi_produksi')->where('cetak_id', $cetak->id)->orderBy('product_id')->get();

        return view('admin.cetaks.edit', compact('cetak', 'cetak_items', 'semesters', 'vendors', 'materials'));
    }

    public function update(Request $request, Cetak $cetak)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
            'cetak_items' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $cetak_items = $validatedData['cetak_items'];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];
                $cetak_item = $cetak_items[$i];

                if ($cetak_item) {
                    $detail = CetakItem::find($cetak_item);
                    $old_quantity = $detail->estimasi;
                    $detail->update([
                        'estimasi' => $quantity,
                        'quantity' => $quantity,
                        'plate_id' => $plate,
                        'plate_cost' => $plate_quantity,
                    ]);

                    EstimationService::editMovement('out', 'cetak', $cetak->id, $product->id, -1 * $quantity, $product->type);
                    EstimationService::editProduction($product->id, ($quantity - $old_quantity), $product->type);
                } else {
                    $detail = CetakItem::create([
                        'cetak_id' => $cetak->id,
                        'semester_id' => $cetak->semester_id,
                        'product_id' => $product->id,
                        'halaman_id' => $product->halaman_id,
                        'estimasi' => $quantity,
                        'quantity' => $quantity,
                        'plate_id' => $plate,
                        'plate_cost' => $plate_quantity,
                        'done' => 0,
                    ]);

                    EstimationService::createMovement('out', 'cetak', $cetak->id, $product->id, -1 * $quantity, $product->type);
                    EstimationService::createProduction($product->id, -1 * $quantity, $product->type);
                }
            }

            $cetak->update([
                'date' => $date,
                'estimasi_oplah' => array_sum($quantities),
                'note' => $note
            ]);

            DB::commit();

            Alert::success('Success', 'Cetak Order berhasil di simpan');

            return redirect()->route('admin.cetaks.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }

        $cetak->update($request->all());

        return redirect()->route('admin.cetaks.index');
    }

    public function show(Cetak $cetak)
    {
        abort_if(Gate::denies('cetak_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cetak->load('semester', 'vendor');

        $cetak_items = CetakItem::with('product')->where('cetak_id', $cetak->id)->get();

        return view('admin.cetaks.show', compact('cetak', 'cetak_items'));
    }

    public function destroy(Cetak $cetak)
    {
        abort_if(Gate::denies('cetak_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cetak->delete();

        return back();
    }

    public function massDestroy(MassDestroyCetakRequest $request)
    {
        $cetaks = Cetak::find(request('ids'));

        foreach ($cetaks as $cetak) {
            $cetak->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function realisasi(Cetak $cetak)
    {
        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cetak->load('semester', 'vendor');

        $cetak_items = CetakItem::with('product', 'semester', 'product.estimasi_produksi')->where('cetak_id', $cetak->id)->orderBy('product_id')->get();

        return view('admin.cetaks.realisasi', compact('cetak', 'cetak_items', 'semesters', 'vendors'));
    }

    public function realisasiStore(Request $request, Cetak $cetak)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'cetak_items' => 'required|array',
            'cetak_items.*' => 'exists:cetak_items,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'done' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $products = $validatedData['products'];
        $cetak_items = $validatedData['cetak_items'];
        $quantities = $validatedData['quantities'];
        $done = $validatedData['done'];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $cetak_item = CetakItem::find($cetak_items[$i]);

                $status = $done[$i];
                if ($cetak_item->done || !$status) {
                    continue;
                }

                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];

                $cetak_item->update([
                    'quantity' => $quantity,
                    'done' => $status
                ]);

                StockService::createMovement('in', 'cetak', $cetak->id, $date, $product->id, $quantity);
                StockService::updateStock($product->id, $quantity);
            }

            $cetak->update([
                'date' => $date,
                'total_oplah' => array_sum($quantities),
            ]);

            DB::commit();

            Alert::success('Success', 'Cetak Order berhasil di simpan');

            return redirect()->route('admin.cetaks.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function printSpc(Cetak $cetak, Request $request)
    {
        $cetak->load('semester', 'vendor');

        $cetak_items = CetakItem::with('product')->where('cetak_id', $cetak->id)->get();

        $cetak_items = $cetak_items->sortBy('product.kelas_id')->sortBy('product.mapel_id')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        return view('admin.cetaks.spc', compact('cetak', 'cetak_items'));
    }

    function costIsi($halaman, $quantity)
    {
        $kat = $halaman / 16;

        if ($quantity >= 5000) {
           $cost = $kat * 25 * $quantity;
        } else {
            $cost = $kat * (25 * (5000/$quantity)) * $quantity;
        }

        return $cost;
    }

    function costCover($cost, $quantity)
    {
        return $cost * $quantity;
    }
}

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
use App\Models\VendorCost;
use App\Models\BookVariant;
use App\Models\Halaman;
use App\Models\Jenjang;
use App\Models\Isi;
use App\Models\Cover;
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
            $query = Cetak::with(['semester', 'vendor', 'cetak_items'])->select(sprintf('%s.*', (new Cetak)->table))->latest();

            if (!empty($request->type)) {
                $query->where('type', $request->type);
            }
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
                $btn = '
                    <a class="px-1" href="'.route('admin.cetaks.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.cetaks.printSpc', $row->id).'" title="Print SPC" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.cetaks.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.cetaks.realisasi', $row->id).'" title="Realisasi">
                        <i class="fas fa-tasks text-danger fa-lg"></i>
                    </a>
                ';
                // if($row->cetak_items->where('done', 0)->count()) {
                //     $btn .= '<a class="px-1" href="'.route('admin.cetaks.realisasi', $row->id).'" title="Realisasi">
                //         <i class="fas fa-tasks text-danger fa-lg"></i>
                //     </a>';
                // }

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

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend('All', '');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.cetaks.index', compact('vendors', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('cetak_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_spc = Cetak::generateNoSPCTemp(setting('current_semester'));

        return view('admin.cetaks.create', compact('semesters', 'vendors', 'jenjangs', 'no_spc'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            // 'semester_id' => 'required',
            'vendor_id' => 'required',
            'jenjang_id' => 'required',
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
        $semester = setting('current_semester');
        $vendor = $validatedData['vendor_id'];
        $jenjang = $validatedData['jenjang_id'];
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
                'jenjang_id' => $jenjang,
                'type' => $type,
                'estimasi_oplah' => array_sum($quantities),
                'note' => $note
            ]);

            $total_cost = 0;

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];

                if ($type == 'isi') {
                    $halaman = Halaman::find($product->halaman_id)->value;
                    $cost = $this->costIsi($halaman, $quantity);
                } else if ($type == 'cover') {
                    $vendor = VendorCost::where('vendor_id', $vendor)->where('key', 'cover_cost')->first();
                    $cost = $this->costCover($vendor ? $vendor->value : 50, $quantity);
                }

                $total_cost += $cost;

                $cetak_item = CetakItem::create([
                    'cetak_id' => $cetak->id,
                    'semester_id' => $semester,
                    'product_id' => $product->id,
                    'halaman_id' => $product->halaman_id,
                    'estimasi' => $quantity,
                    'quantity' => $quantity,
                    'cost'  => $cost,
                    'plate_id' => $plate,
                    'plate_cost' => $plate_quantity,
                    'done' => 0,
                ]);

                EstimationService::createMovement('out', 'cetak', $cetak->id, $product->id, -1 * $quantity, $product->type);
                EstimationService::createProduction($product->id, -1 * $quantity, $product->type);
            }

            $cetak->total_cost = $total_cost;
            $cetak->save();

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

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::where('category', 'plate')->whereHas('vendors', function ($q) use ($cetak) {
            $q->where('id', $cetak->vendor_id);
        })->orderBy('code', 'ASC')->pluck('name', 'id');

        $cetak->load('semester', 'vendor', 'jenjang');

        $cetak_items = CetakItem::with('product', 'semester', 'product.estimasi_produksi')->where('cetak_id', $cetak->id)->orderBy('product_id')->get();

        return view('admin.cetaks.edit', compact('cetak', 'cetak_items', 'semesters', 'vendors', 'materials', 'jenjangs'));
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
        $type = $cetak->type;
        $vendor = $cetak->vendor_id;
        $total_cost = 0;

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $quantity = $quantities[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];
                $cetak_item = $cetak_items[$i];

                if ($type == 'isi') {
                    $halaman = Halaman::find($product->halaman_id)->value;
                    $cost = $this->costIsi($halaman, $quantity);
                } else if ($type == 'cover') {
                    $vendor = VendorCost::where('vendor_id', $vendor)->where('key', 'cover_cost')->first();
                    $cost = $this->costCover($vendor ? $vendor->value : 35, $quantity);
                }

                $total_cost += $cost;

                if ($cetak_item) {
                    $detail = CetakItem::find($cetak_item);
                    $old_quantity = $detail->estimasi;
                    $detail->update([
                        'estimasi' => $quantity,
                        'quantity' => $quantity,
                        'cost' => $cost,
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
                        'cost' => $cost,
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
                'total_cost' => $total_cost,
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

        $cetak_items = CetakItem::with('product', 'product.jenjang', 'product.isi', 'product.cover', 'product.kurikulum')->where('cetak_id', $cetak->id)->get();

        $cetak_items = $cetak_items->sortBy('product.kelas_id')->sortBy('product.mapel_id')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        if($cetak->type == 'isi') {
            return view('admin.cetaks.spc_isi', compact('cetak', 'cetak_items'));
        }

        if($cetak->type == 'cover') {
            return view('admin.cetaks.spc_cover', compact('cetak', 'cetak_items'));
        }

        return view('admin.cetaks.spc_isi', compact('cetak', 'cetak_items'));
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

    public function getIsiCover(Request $request)
    {
        $type = $request->input('type');

        if ($type == 'isi') {
            $isi_cover = Isi::all();
        } else {
            $isi_cover = Cover::all();
        }

        $formattedItems = [];

        foreach ($isi_cover as $item) {
            $formattedItems[] = [
                'id' => $item->id,
                'text' => $item->code . ' - ' . $item->name,
            ];
        }

        return response()->json($formattedItems);
    }
}

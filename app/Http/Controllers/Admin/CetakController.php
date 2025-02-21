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
use App\Models\Kurikulum;
use App\Models\Isi;
use App\Models\Cover;
use App\Models\PlatePrint;
use App\Models\PlatePrintItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Alert;
use Carbon\Carbon;
use App\Services\EstimationService;
use App\Services\StockService;
use App\Services\TransactionService;
use App\Exports\CetakRekapExport;
use Illuminate\Support\Facades\Date;

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
            if ($request->semester) {
                $query->where('semester_id', $request->semester);
            } else {
                $query->where('semester_id', setting('current_semester'));
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $cetakItemStatus = $row->cetak_items->where('done', '=', 1)->count();

                if ($cetakItemStatus == 0) {
                    $realisasi = 'danger';
                } elseif ($cetakItemStatus == $row->cetak_items->count()) {
                    $realisasi = 'success';
                } else {
                    $realisasi = 'warning';
                }

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
                    <a class="px-1" href="'.route('admin.cetaks.realisasi', $row->id).'" title="Realisasi '.Cetak::TIPE_REALISASI[$realisasi].'">
                        <i class="fas fa-tasks text-'.$realisasi.' fa-lg"></i>
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

        $semesters = Semester::where('status', 1)->orderBy('id', 'DESC')->pluck('name', 'id');

        return view('admin.cetaks.index', compact('vendors', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('cetak_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->orderBy('code', 'ASC')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_spc = Cetak::generateNoSPCTemp(setting('current_semester'));

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.cetaks.create', compact('semesters', 'kurikulums', 'vendors', 'jenjangs', 'no_spc', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'semester_id' => 'required',
            'vendor_id' => 'required',
            'jenjang_id' => 'nullable',
            'type' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
            'plates' => 'required|array',
            'plates.*' => 'nullable',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:0',
        ]);

        $date = $validatedData['date'];
        $semester = $validatedData['semester_id'] ?? setting('current_semester');
        $vendor = $validatedData['vendor_id'];
        $jenjang = $validatedData['jenjang_id'] ?? null;
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

            $print_plate = PlatePrint::create([
                'no_spk' => $cetak->no_spc,
                'date' => $date,
                'semester_id' => $semester,
                'vendor_id' => $vendor,
                'customer' => null,
                'type' => 'internal',
                'fee' => 0,
                'note' => "Tolong Periksa dengan Seksama SPK nya",
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
                    'plate_id' => $plate == 0 ? null : $plate,
                    'plate_cost' => $plate_quantity,
                    'done' => 0,
                ]);

                $print_plate_item = PlatePrintItem::create([
                    'plate_print_id' => $print_plate->id,
                    'semester_id' => $semester,
                    'product_id' => $product->id,
                    'plate_id' => $plate == 0 ? null : $plate,
                    'estimasi' => $plate_quantity,
                    'realisasi' => $plate_quantity,
                    'note' => null,
                    'status' => 'created'
                ]);

                EstimationService::createMovement('out', 'cetak', $cetak->id, $product->id, $quantity, 'produksi');
                EstimationService::createCetak($product->id, $quantity, $product->type);
            }

            TransactionService::createProductionTransaction($date, 'Ongkos Cetak Produsi SPc '. $cetak->no_spc, $vendor, $semester, 'cetak', $cetak->id, $cetak->no_spc, $total_cost, 'credit');

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

        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->orderBy('code', 'ASC')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend('Tidak Dipilih', '');

        $no_spc = noRevisi($cetak->no_spc);

        $cetak->load('semester', 'vendor', 'jenjang');

        $cetak_items = CetakItem::with('product', 'semester', 'product.estimasi_produksi')->where('cetak_id', $cetak->id)->orderBy('product_id')->get();

        $print_plate = PlatePrint::where('no_spk', $cetak->no_spc)->whereHas('details', function ($q) {
            $q->whereNot('status', 'created');
        })->first();

        if ($print_plate) {
            Alert::warning('Warning', 'Perintah Plate sudah dieksekusi, tidak disarankan untuk di edit');
        }

        return view('admin.cetaks.edit', compact('no_spc', 'cetak', 'cetak_items', 'semesters', 'vendors', 'jenjangs'));
    }

    public function update(Request $request, Cetak $cetak)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'vendor_id' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:0',
            'plates' => 'required|array',
            'plates.*' => 'nullable',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:0',
            'cetak_items' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $vendor = $validatedData['vendor_id'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $cetak_items = $validatedData['cetak_items'];
        $type = $cetak->type;
        $semester = $cetak->semester_id;
        $total_cost = 0;

        DB::beginTransaction();
        try {
            $print_plate = PlatePrint::where('no_spk', $cetak->no_spc)->first();

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
                    $vendor_cost = VendorCost::where('vendor_id', $vendor)->where('key', 'cover_cost')->first();
                    $cost = $this->costCover($vendor_cost ? $vendor_cost->value : 35, $quantity);
                }

                $total_cost += $cost;

                if ($cetak_item) {
                    $detail = CetakItem::find($cetak_item);
                    $old_quantity = $detail->estimasi;
                    $detail->update([
                        'estimasi' => $quantity,
                        'quantity' => $quantity,
                        'cost' => $cost,
                        'plate_id' => $plate == 0 ? null : $plate,
                        'plate_cost' => $plate_quantity,
                    ]);

                    $print_plate_item = PlatePrintItem::where('product_id', $product->id)->update([
                        'plate_id' => $plate == 0 ? null : $plate,
                        'estimasi' => $plate_quantity,
                        'realisasi' => $plate_quantity
                    ]);

                    EstimationService::editMovement('out', 'cetak', $cetak->id, $product->id, $quantity, 'produksi');
                    EstimationService::editCetak($product->id, ($quantity - $old_quantity), $product->type);
                } else {
                    $detail = CetakItem::create([
                        'cetak_id' => $cetak->id,
                        'semester_id' => $cetak->semester_id,
                        'product_id' => $product->id,
                        'halaman_id' => $product->halaman_id,
                        'estimasi' => $quantity,
                        'quantity' => $quantity,
                        'cost' => $cost,
                        'plate_id' => $plate == 0 ? null : $plate,
                        'plate_cost' => $plate_quantity,
                        'done' => 0,
                    ]);

                    $print_plate_item = PlatePrintItem::create([
                        'plate_print_id' => $print_plate->id,
                        'semester_id' => $semester,
                        'product_id' => $product->id,
                        'plate_id' => $plate == 0 ? null : $plate,
                        'estimasi' => $plate_quantity,
                        'realisasi' => 0,
                        'note' => null,
                        'status' => 'created'
                    ]);

                    EstimationService::createMovement('out', 'cetak', $cetak->id, $product->id, $quantity, 'produksi');
                    EstimationService::createCetak($product->id, $quantity, $product->type);
                }
            }

            TransactionService::editProductionTransaction($date, 'Ongkos Cetak Produsi SPC '. $cetak->no_spc, $vendor, $semester, 'cetak', $cetak->id, $cetak->no_spc, $total_cost, 'credit');

            $cetak->update([
                'date' => $date,
                'vendor_id' => $vendor,
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

        $cetak_items = CetakItem::with('product')->where('cetak_id', $cetak->id)->orderBy('id', 'ASC')->get();

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
        $semesters = Semester::latest()->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cetak->load('semester', 'vendor');

        $cetak_items = CetakItem::with('product', 'semester', 'product.estimasi_produksi')->where('cetak_id', $cetak->id)->orderBy('product_id')->get();

        return view('admin.cetaks.realisasi', compact('cetak', 'cetak_items', 'semesters', 'vendors', 'jenjangs'));
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
            'quantities.*' => 'numeric|min:0',
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

                $quantity_old = $cetak_item->quantity;
                $product = $products[$i];
                $quantity = $quantities[$i];
                $status = $done[$i];

                if ($cetak_item->done && $quantity_old == $quantity) {
                    // if ($finishing_item->done || !$status) {
                    continue;
                }

                $cetak_item->update([
                    'quantity' => $quantity,
                    'done' => $status
                ]);

                EstimationService::createMovement('out', 'cetak', $cetak->id, $product, $quantity, 'realisasi');
                EstimationService::createUpdateRealisasi($product, $quantity);

                StockService::createMovement('in', 'cetak', $cetak->id, $date, $product, $quantity);
                StockService::updateStock($product, $quantity);
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

        $cetak_items = CetakItem::with('product', 'product.jenjang', 'product.isi', 'product.cover', 'product.kurikulum')->where('cetak_id', $cetak->id)->orderBy('id', 'ASC')->get();

        // $cetak_items = $cetak_items->sortBy('product.nama_urut')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

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

        if ($quantity <= 0) return 0;

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

    public function rekap(Request $request)
    {
        if ($request->has('date') && $request->date && $dates = explode(' - ', $request->date)) {
            $start = Date::parse($dates[0])->startOfDay();
            $end = !isset($dates[1]) ? $start->clone()->endOfMonth() : Date::parse($dates[1])->endOfDay();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
        }

        $query = Cetak::whereBetween('date', [$start, $end]);

        if (!empty($request->type)) {
            $query->where('type', $request->type);
        }
        if (!empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if (!empty($request->semester_id)) {
            $query->where('semester_id', $request->semester_id);
        }

        $rekap = $query->orderBy('date', 'ASC')->get();

        return (new CetakRekapExport($rekap))->download('REKAP CETAK PERIODE ' . $start->format('d-F-Y') .' sd '. $end->format('d-F-Y') .'.xlsx');
    }
}

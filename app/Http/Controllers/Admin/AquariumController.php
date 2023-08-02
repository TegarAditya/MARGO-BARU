<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPlatePrintRequest;
use App\Http\Requests\StorePlatePrintRequest;
use App\Http\Requests\UpdatePlatePrintRequest;
use App\Models\PlatePrint;
use App\Models\PlatePrintItem;
use App\Models\Semester;
use App\Models\Vendor;
use App\Models\Material;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Services\StockService;
use DB;
use Alert;
use Carbon\Carbon;

class AquariumController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PlatePrint::with(['semester', 'vendor'])->select(sprintf('%s.*', (new PlatePrint)->table));
            $query->whereHas('details', function ($q) {
                $q->where('status', 'created');
            });
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.aquarium.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.aquarium.edit', $row->id).'" title="Accept Task">
                        <i class="fas fa-check text-secondary fa-lg"></i>
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

            $table->addColumn('vendor_code', function ($row) {
                return $row->vendor ? $row->vendor->code : '';
            });

            $table->editColumn('customer', function ($row) {
                return $row->customer ? $row->customer : 'Internal';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? PlatePrint::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('fee', function ($row) {
                return $row->fee ? $row->fee : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'vendor']);

            return $table->make(true);
        }

        return view('admin.aquarium.index');
    }

    public function working(Request $request)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PlatePrint::with(['semester', 'vendor'])->select(sprintf('%s.*', (new PlatePrint)->table));
            $query->whereHas('details', function ($q) {
                $q->where('status', 'accepted');
            });
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.aquarium.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.aquarium.realisasi', $row->id).'" title="Realisasi Task">
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

            $table->addColumn('vendor_code', function ($row) {
                return $row->vendor ? $row->vendor->code : '';
            });

            $table->editColumn('customer', function ($row) {
                return $row->customer ? $row->customer : 'Internal';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? PlatePrint::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('fee', function ($row) {
                return $row->fee ? $row->fee : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'vendor']);

            return $table->make(true);
        }

        return view('admin.aquarium.working');
    }


    public function create()
    {
        abort_if(Gate::denies('plate_print_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.aquarium.create', compact('semesters', 'vendors'));
    }

    public function store(StorePlatePrintRequest $request)
    {
        $platePrint = PlatePrint::create($request->all());

        return redirect()->route('admin.plate-prints.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platePrint = PlatePrint::find($id);

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $platePrint->load('semester', 'vendor');

        $plate_items = PlatePrintItem::with('plate')->where('plate_print_id', $platePrint->id)->where('status', 'created')->get();

        if ($platePrint->vendor_id) {
            $materials = Material::where('category', 'plate')->whereHas('vendors', function ($q) use ($platePrint) {
                $q->where('id', $platePrint->vendor_id);
            })->orderBy('code', 'ASC')->pluck('name', 'id');
        } else {
            $materials = Material::where('category', 'plate')->orderBy('code', 'ASC')->pluck('name', 'id');
        }

        return view('admin.aquarium.edit', compact('platePrint', 'semesters', 'vendors', 'plate_items', 'materials'));
    }

    public function update(Request $request, $id)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'plate_items' => 'required|array',
            'plate_items.*' => 'exists:plate_print_items,id',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
            'check_mapel' => 'array',
            'check_kelas' => 'array',
            'check_kurikulum' => 'array',
            'check_kolomnama' => 'array',
            'check_naskah' => 'array',
            'check_ready' => 'required|array',
            'check_ready' => 'required',
        ]);

        $plate_items = $validatedData['plate_items'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $check_mapel = $validatedData['check_mapel'];
        $check_kelas = $validatedData['check_kelas'];
        $check_kurikulum = $validatedData['check_kurikulum'];
        $check_kolomnama = $validatedData['check_kolomnama'] ?? null;
        $check_naskah = $validatedData['check_naskah'] ?? null;
        $check_ready = $validatedData['check_ready'];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($plates); $i++) {
                $plate_item = $plate_items[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];

                $mapel = $check_mapel[$i];
                $kelas = $check_kelas[$i];
                $kurikulum = $check_kurikulum[$i];
                $kolomnama = $check_kolomnama[$i] ?? null;
                $naskah = $check_naskah[$i] ?? null;
                $ready = $check_ready[$i];

                $cetak_item = PlatePrintItem::where('id', $plate_item)->update([
                    'plate_id' => $plate,
                    'estimasi' => $plate_quantity,
                    'realisasi' => $plate_quantity,
                    'status' =>  $ready == 1 ? 'accepted' : 'created',
                    'check_mapel' => $mapel,
                    'check_kelas' => $kelas,
                    'check_kurikulum' => $kurikulum,
                    'check_kolomnama' => $kolomnama,
                    'check_naskah' => $naskah,
                ]);
            }

            DB::commit();

            Alert::success('Success', 'Cetak Plate Order berhasil di simpan');

            return redirect()->route('admin.aquarium.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $platePrint = PlatePrint::find($id);
        $platePrint->load('semester', 'vendor');

        $items = PlatePrintItem::with('product')->where('plate_print_id', $platePrint->id)->get();

        return view('admin.aquarium.show', compact('platePrint', 'items'));
    }

    public function destroy(PlatePrint $platePrint)
    {
        abort_if(Gate::denies('plate_print_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platePrint->delete();

        return back();
    }

    public function realisasi($id)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platePrint = PlatePrint::find($id);

        $platePrint->load('semester', 'vendor');

        $plate_items = PlatePrintItem::with('plate')->where('plate_print_id', $platePrint->id)->whereIn('status', ['accepted', 'done'])->get();

        return view('admin.aquarium.realisasi', compact('platePrint', 'plate_items'));
    }

    public function realisasiStore(Request $request, $id)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'plate_items' => 'required|array',
            'plate_items.*' => 'exists:plate_print_items,id',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
            'notes' => 'required|array',
            'dones' => 'required|array',
        ]);

        $plate_items = $validatedData['plate_items'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $notes = $validatedData['notes'];
        $dones = $validatedData['dones'];

        $printPlate = PlatePrint::find($id);

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($plate_items); $i++) {
                $plate_item = $plate_items[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];
                $note = $notes[$i];
                $done = $dones[$i];

                $plate_print_item = PlatePrintItem::where('id', $plate_item)->first();

                if ($plate_print_item->status == 'done' || !$done) {
                    continue;
                }

                $cetak_item = PlatePrintItem::where('id', $plate_item)->update([
                    'realisasi' => $plate_quantity,
                    'note' => $note,
                    'status' =>  $done == 1 ? 'done' : 'accepted',
                ]);



                $plate = Material::find($plate);

                if ($printPlate->type == 'internal') {

                    $material = Material::updateOrCreate([
                        'code' => $plate->code.'|'. $plate_print_item->product->code,
                    ], [
                        'name' => $plate->name .' ('. $plate_print_item->product->name .')',
                        'category' => 'printed_plate',
                        'unit_id' => $plate->unit_id,
                        'cost' => 0,
                        'stock' => DB::raw("stock + $plate_print_item->estimasi"),
                        'warehouse_id' => 3,
                    ]);

                    $material->vendors()->syncWithoutDetaching($printPlate->vendor_id);

                    StockService::createMovementMaterial('in', 'plating', $printPlate->id, $printPlate->date, $material->id, $plate_print_item->estimasi);
                }

                StockService::printPlate($printPlate->id, $printPlate->date, $plate->id, $plate_quantity);
            }

            DB::commit();

            Alert::success('Success', 'Cetak Plate Order berhasil di simpan');

            return redirect()->route('admin.aquarium.working');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }
}

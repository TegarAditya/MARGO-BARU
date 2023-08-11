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
            $query = PlatePrint::with(['semester', 'vendor'])->select(sprintf('%s.*', (new PlatePrint)->table))->latest();
            
            if (!empty($request->type)) {
                $query->where('type', $request->type);
            }
            if (!empty($request->vendor)) {
                $query->where('vendor_id', $request->vendor);
            }
            
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.aquarium.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
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
                return $row->vendor ? $row->vendor->code : $row->customer;
            });

            $table->editColumn('customer', function ($row) {
                return $row->vendor ? $row->vendor->code : $row->customer;
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

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend('All', '');

        return view('admin.aquarium.index', compact('vendors'));
    }

    public function task(Request $request)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PlatePrintItem::with(['plate_print', 'semester'])->where('status', 'created')
                ->where('semester_id', setting('current_semester'));

            $query->whereHas('plate_print', function ($q) use ($request) {
                if (!empty($request->type)) {
                    $q->where('type', $request->type);
                }
                if (!empty($request->vendor)) {
                    $q->where('vendor_id', $request->vendor);
                }
                if (!empty($request->spk)) {
                    $q->where('no_spk', $request->spk);
                }
            });

            $query->select(sprintf('%s.*', (new PlatePrintItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.aquarium.edit', $row->id).'" title="Accept Task">
                        <i class="fas fa-check text-secondary fa-lg"></i>
                    </a>
                ';

                // <a class="px-1" href="'.route('admin.aquarium.show', $row->plate_print_id).'" title="Show">
                //         <i class="fas fa-eye text-success fa-lg"></i>
                //     </a>

                return $btn;
            });

            $table->addColumn('plate_print_no_spk', function ($row) {
                return $row->plate_print ? $row->plate_print->no_spk : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->name : $row->product_text;
            });

            $table->addColumn('plate_code', function ($row) {
                return $row->plate ? $row->plate->name : 'Belum Tahu';
            });

            $table->addColumn('vendor', function ($row) {
                return $row->plate_print->vendor ? $row->plate_print->vendor->name : $row->plate_print->customer;
            });

            $table->editColumn('estimasi', function ($row) {
                return $row->estimasi ? $row->estimasi : '';
            });

            $table->editColumn('realisasi', function ($row) {
                return $row->realisasi ? $row->realisasi : '';
            });

            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? PlatePrintItem::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'plate_print', 'semester', 'product', 'plate', 'vendor']);

            return $table->make(true);
        }

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend('All', '');
        $spks = PlatePrint::whereHas('details', function ($q) {
            $q->where('status', 'created');
        })->pluck('no_spk', 'id')->prepend('All', '');

        return view('admin.aquarium.task', compact('vendors', 'spks'));
    }

    public function working(Request $request)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PlatePrintItem::with(['plate_print', 'semester'])->where('status', 'accepted')
                ->where('plate_print_items.semester_id', setting('current_semester'));

            $query->whereHas('plate_print', function ($q) use ($request) {
                if (!empty($request->type)) {
                    $q->where('type', $request->type);
                }
                if (!empty($request->vendor)) {
                    $q->where('vendor_id', $request->vendor);
                }
                if (!empty($request->spk)) {
                    $q->where('no_spk', $request->spk);
                }
            });

            $query->select(sprintf('%s.*', (new PlatePrintItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.aquarium.realisasi', $row->id).'" title="Realisasi Task">
                        <i class="fas fa-tasks text-danger fa-lg"></i>
                    </a>
                ';

                // <a class="px-1" href="'.route('admin.aquarium.show', $row->plate_print_id).'" title="Show">
                //         <i class="fas fa-eye text-success fa-lg"></i>
                //     </a>

                return $btn;
            });

            $table->addColumn('plate_print_no_spk', function ($row) {
                return $row->plate_print ? $row->plate_print->no_spk : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->name : $row->product_text;
            });

            $table->addColumn('plate_code', function ($row) {
                return $row->plate ? $row->plate->name : 'Belum Tahu';
            });

            $table->addColumn('vendor', function ($row) {
                return $row->plate_print->vendor ? $row->plate_print->vendor->name : $row->plate_print->customer;
            });

            $table->editColumn('estimasi', function ($row) {
                return $row->estimasi ? $row->estimasi : '';
            });

            $table->editColumn('realisasi', function ($row) {
                return $row->realisasi ? $row->realisasi : '';
            });

            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? PlatePrintItem::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'plate_print', 'semester', 'product', 'plate', 'vendor']);

            return $table->make(true);
        }

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend('All', '');
        $spks = PlatePrint::whereHas('details', function ($q) {
            $q->where('status', 'accepted');
        })->pluck('no_spk', 'id')->prepend('All', '');

        return view('admin.aquarium.working', compact('vendors', 'spks'));
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

        $plate_item = PlatePrintItem::with('plate')->find($id);

        $platePrint = PlatePrint::find($plate_item->plate_print_id);
        $platePrint->load('semester', 'vendor');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        if ($platePrint->vendor_id) {
            $materials = Material::where('category', 'plate')->whereHas('vendors', function ($q) use ($platePrint) {
                $q->where('id', $platePrint->vendor_id);
            })->orderBy('code', 'ASC')->pluck('name', 'id');
        } else {
            $materials = Material::where('category', 'plate')->orderBy('code', 'ASC')->pluck('name', 'id');
        }

        return view('admin.aquarium.edit-new', compact('platePrint', 'plate_item', 'semesters', 'vendors', 'materials'));
    }

    public function editbackup($id)
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

        $plate_item = PlatePrintItem::with('plate')->find($id);

        $platePrint = PlatePrint::find($plate_item->plate_print_id);
        $platePrint->load('semester', 'vendor');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        if ($platePrint->vendor_id) {
            $materials = Material::where('category', 'plate')->whereHas('vendors', function ($q) use ($platePrint) {
                $q->where('id', $platePrint->vendor_id);
            })->orderBy('code', 'ASC')->pluck('name', 'id');
        } else {
            $materials = Material::where('category', 'plate')->orderBy('code', 'ASC')->pluck('name', 'id');
        }

        return view('admin.aquarium.realisasi', compact('platePrint', 'plate_item', 'semesters', 'vendors', 'materials'));
    }

    public function realisasiStore(Request $request, $id)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'plate_quantity' => 'numeric|min:1',
            'note' => 'required',
            'done' => 'required',
        ]);

        $plate_quantity = $validatedData['plate_quantity'];
        $note = $validatedData['note'];
        $done = $validatedData['done'];

        $plate_item = PlatePrintItem::with('plate_print', 'plate')->find($id);

        DB::beginTransaction();
        try {
            if ($plate_item->status !== 'done' && $done) {

                if ($plate_item->plate_print->type == 'internal') {

                    $material = Material::updateOrCreate([
                        'code' => $plate_item->plate->code.'|'. $plate_item->product->code,
                    ], [
                        'name' => $plate_item->plate->name .' ('. $plate_item->product->name .')',
                        'category' => 'printed_plate',
                        'unit_id' => $plate_item->plate->unit_id,
                        'cost' => 0,
                        'stock' => DB::raw("stock + $plate_item->estimasi"),
                        'warehouse_id' => 3,
                    ]);
    
                    $material->vendors()->syncWithoutDetaching($plate_item->plate_print->vendor_id);
    
                    StockService::createMovementMaterial('in', 'plating', $plate_item->plate_print->id, $plate_item->plate_print->date, $plate_item->plate->id, $plate_item->estimasi);
                }
    
                StockService::printPlate($plate_item->plate_print->id, $plate_item->plate_print->date, $plate_item->plate->id, $plate_quantity);
            
            } else if ($plate_item->status == 'done' && $plate_quantity > $plate_item->realisasi) {
                
                $qty = $plate_quantity - $plate_item->realisasi;
                StockService::printPlate($plate_item->plate_print->id, $plate_item->plate_print->date, $plate_item->plate->id, $qty);
            }

            $plate_item->update([
                'realisasi' => $plate_quantity,
                'note' => $note,
                'status' =>  $done == 1 ? 'done' : 'accepted',
            ]);

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

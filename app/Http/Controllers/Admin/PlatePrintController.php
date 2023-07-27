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
use App\Models\Jenjang;
use App\Models\BookVariant;
use App\Models\Material;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Services\StockService;
use DB;
use Alert;

class PlatePrintController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('plate_print_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PlatePrint::with(['semester', 'vendor'])->select(sprintf('%s.*', (new PlatePrint)->table));

            if (!empty($request->type)) {
                $query->where('plate_prints.type', $request->type);
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
                $viewGate      = 'plate_print_show';
                $editGate      = 'plate_print_edit';
                $deleteGate    = 'plate_print_delete';
                $crudRoutePart = 'plate-prints';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_spk', function ($row) {
                return $row->no_spk ? $row->no_spk : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('vendor_code', function ($row) {
                return $row->vendor ? $row->vendor->name : '';
            });

            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'vendor']);

            return $table->make(true);
        }

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend('All', '');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.platePrints.index', compact('vendors', 'semesters'));
    }

    public function create()
    {
        abort_if(Gate::denies('plate_print_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_spk = PlatePrint::generateNoSPKTemp(setting('current_semester'));

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.platePrints.create', compact('vendors','jenjangs', 'no_spk'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'vendor_id' => 'required',
            'type' => 'required',
            'note' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
            'chemicals' => 'required|array',
            'chemicals.*' => 'exists:materials,id',
            'chemical_quantities' => 'required|array',
            'chemical_quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = setting('current_semester');
        $vendor = $validatedData['vendor_id'];
        $type = $validatedData['type'];
        $note = $validatedData['note'];
        $products = $validatedData['products'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $chemicals = $validatedData['chemicals'];
        $chemical_quantities = $validatedData['chemical_quantities'];

        DB::beginTransaction();
        try {
            $cetak = PlatePrint::create([
                'no_spk' => PlatePrint::generateNoSPK($semester, $vendor),
                'date' => $date,
                'type' => $type,
                'semester_id' => $semester,
                'vendor_id' => $vendor,
                'note' => $note
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $plate = Material::find($plates[$i]);
                $chemical = Material::find($chemicals[$i]);

                $plate_quantity = $plate_quantities[$i];
                $chemical_quantity = $chemical_quantities[$i];

                $cetak_item = PlatePrintItem::create([
                    'plate_print_id' => $cetak->id,
                    'semester_id' => $semester,
                    'vendor_id' => $vendor,
                    'product_id' => $product->id,
                    'plate_id' => $plate->id,
                    'plate_qty' => $plate_quantity,
                    'chemical_id' => $chemical->id,
                    'chemical_qty' => $chemical_quantity,
                ]);

                $material = Material::updateOrCreate([
                    'code' => $plate->code.'|'. $product->code,
                ], [
                    'name' => $plate->name .' ('. $product->name .')',
                    'category' => 'printed_plate',
                    'unit_id' => $plate->unit_id,
                    'cost' => 0,
                    'stock' => DB::raw("stock + $plate_quantity"),
                    'warehouse_id' => 2,
                ]);

                $material->vendors()->sync($plate->vendors->pluck('id')->toArray());

                StockService::createMovementMaterial('in', 'plating', $cetak->id, $date, $material->id, $plate_quantity);

                StockService::createMovementMaterial('out', 'plating', $cetak->id, $date, $plate->id, -1 * $plate_quantity);
                StockService::updateStockMaterial($plate->id, -1 * $plate_quantity);
                StockService::createMovementMaterial('out', 'plating', $cetak->id, $date, $chemical->id, -1 * $chemical_quantity);
                StockService::updateStockMaterial($chemical->id, -1 * $chemical_quantity);
            }

            DB::commit();

            Alert::success('Success', 'Cetak Plate Order berhasil di simpan');

            return redirect()->route('admin.plate-prints.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(PlatePrint $platePrint)
    {
        abort_if(Gate::denies('plate_print_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $platePrint->load('semester', 'vendor');

        return view('admin.platePrints.edit', compact('platePrint', 'semesters', 'vendors'));
    }

    public function update(UpdatePlatePrintRequest $request, PlatePrint $platePrint)
    {
        $platePrint->update($request->all());

        return redirect()->route('admin.plate-prints.index');
    }

    public function show(PlatePrint $platePrint)
    {
        abort_if(Gate::denies('plate_print_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platePrint->load('semester', 'vendor', );

        $items = PlatePrintItem::with('product')->where('plate_print_id', $platePrint->id)->get();

        return view('admin.platePrints.show', compact('platePrint', 'items'));
    }

    public function destroy(PlatePrint $platePrint)
    {
        abort_if(Gate::denies('plate_print_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platePrint->delete();

        return back();
    }

    public function massDestroy(MassDestroyPlatePrintRequest $request)
    {
        $platePrints = PlatePrint::find(request('ids'));

        foreach ($platePrints as $platePrint) {
            $platePrint->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMaterialRequest;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\BookVariant;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Alert;
use Excel;
use App\Imports\MaterialImport;
use App\Exports\MaterialTemplate;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;
use DB;

class MaterialsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('material_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Material::with(['unit', 'warehouse', 'vendors'])->select(sprintf('%s.*', (new Material)->table));

            if (!empty($request->category)) {
                $query->where('category', $request->category);
            }
            if (!empty($request->vendor)) {
                $query->whereHas('vendors', function ($q) use ($request) {
                    $q->where('id', $request->vendor);
                });
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'material_show';
                $editGate      = 'material_edit';
                $deleteGate    = 'material_delete';
                $crudRoutePart = 'materials';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->addColumn('unit_name', function ($row) {
                return $row->unit ? $row->unit->code : '';
            });

            $table->editColumn('stock', function ($row) {
                return $row->stock ? angka($row->stock) : 0;
            });

            $table->editColumn('vendor', function ($row) {
                $labels = [];
                foreach ($row->vendors as $vendor) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>;', $vendor->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'unit', 'vendor']);

            return $table->make(true);
        }

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend('All', '');

        return view('admin.materials.index', compact('vendors'));
    }

    public function jangka(Request $request)
    {
        if ($request->has('date') && $request->date && $dates = explode(' - ', $request->date)) {
            $start = Date::parse($dates[0])->startOfDay();
            $end = !isset($dates[1]) ? $start->clone()->endOfMonth() : Date::parse($dates[1])->endOfDay();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
        }

        $saldo_awal = Material::withSum(['movement as in' => function ($q) use ($start) {
            $q->where('movement_type', 'in')->where('movement_date', '<', $start)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->withSum(['movement as out' => function ($q) use ($start) {
            $q->where('movement_type', 'out')->where('movement_date', '<', $start)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->whereIn('category', ['paper', 'plate', 'chemical'])->get();

        $materials = Material::withSum(['movement as in' => function ($q) use ($start, $end) {
            $q->where('movement_type', 'in')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
            $q->where('movement_type', 'out')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->whereIn('category', ['paper', 'plate', 'chemical'])->get();

        return view('admin.materials.saldo', compact('start', 'end', 'saldo_awal', 'materials'));
    }

    public function create()
    {
        abort_if(Gate::denies('material_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $units = Unit::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->pluck('name', 'id');

        return view('admin.materials.create', compact('units', 'vendors'));
    }

    public function store(StoreMaterialRequest $request)
    {
        $material = Material::create($request->all());
        $material->vendors()->sync($request->input('vendors', []));

        Alert::success('Success', 'Material telah disimpan !');

        return redirect()->route('admin.materials.index');
    }

    public function edit(Material $material)
    {
        abort_if(Gate::denies('material_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $units = Unit::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vendors = Vendor::where('type', 'cetak')->pluck('name', 'id');

        $material->load('unit', 'warehouse', 'vendors');

        return view('admin.materials.edit', compact('material', 'units', 'vendors'));
    }

    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $material->update($request->all());
        $material->vendors()->sync($request->input('vendors', []));

        Alert::success('Success', 'Material telah disimpan !');

        return redirect()->route('admin.materials.index');
    }

    public function show(Material $material)
    {
        abort_if(Gate::denies('material_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $material->load('unit', 'warehouse', 'vendors');

        return view('admin.materials.show', compact('material'));
    }

    public function destroy(Material $material)
    {
        abort_if(Gate::denies('material_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $relationMethods = ['plate_items', 'movement'];

        foreach ($relationMethods as $relationMethod) {
            if ($material->$relationMethod()->count() > 0) {
                Alert::warning('Error', 'Material telah digunakan, tidak bisa dihapus !');
                return back();
            }
        }

        $material->delete();

        Alert::success('Success', 'Material telah dihapus !');

        return back();
    }

    public function massDestroy(MassDestroyMaterialRequest $request)
    {
        $materials = Material::find(request('ids'));

        foreach ($materials as $material) {
            $material->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        try {
            Excel::import(new MaterialImport(), $file);
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return redirect()->back();
        }

        Alert::success('Success', 'Material berhasil di import');
        return redirect()->back();
    }

    public function template_import() {
        return (new MaterialTemplate())->download('Template Import Material.xlsx');
    }

    public function getPlates(Request $request) {
        $query = $request->input('q');

        $materials = Material::where('category', 'plate')->where(function($q) use ($query) {
            $q->where('code', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%");
        })->orderBy('code', 'ASC')->get();

        $formattedMaterials = [];

        foreach ($materials as $material) {
            $formattedMaterials[] = [
                'id' => $material->id,
                'text' => $material->code .' - '.$material->name,
                'stock' => $material->stock,
                'code' => $material->code,
                'name' => $material->name,
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getPrintedPlates(Request $request) {
        $vendor = $request->input('vendor');
        $product = BookVariant::find($request->input('product'));

        $materials = Material::where('category', 'printed_plate')
                ->where('code', 'LIKE', '%'.$product->code.'%')
                ->whereHas('vendors', function ($q) use ($vendor) {
                    $q->where('id', $vendor);
                })->orderBy('code', 'ASC')->get();

        $formattedMaterials = [];

        foreach ($materials as $material) {
            $formattedMaterials[] = [
                'id' => $material->id,
                'text' => '('. $material->stock .') '.$material->code,
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getPlateRaws(Request $request) {
        $vendor = $request->input('vendor');

        $materials = Material::where('category', 'plate')->whereHas('vendors', function ($q) use ($vendor) {
                    $q->where('id', $vendor);
                })->orderBy('code', 'ASC')->get();

        $formattedMaterials = [];

        $formattedMaterials[] = [
            'id' => 0,
            'text' => 'Belum Tahu',
        ];

        foreach ($materials as $material) {
            $formattedMaterials[] = [
                'id' => $material->id,
                'text' => '['. $material->stock .'] '.$material->name,
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getChemicals(Request $request) {
        $materials = Material::where('category', 'chemical')->orderBy('code', 'ASC')->get();

        $formattedMaterials = [];

        foreach ($materials as $material) {
            $formattedMaterials[] = [
                'id' => $material->id,
                'text' => $material->code . ' - ' . $material->name,
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getMaterials(Request $request) {
        $query = $request->input('q');

        $materials = Material::whereIn('category', ['plate', 'chemical'])->where(function($q) use ($query) {
            $q->where('code', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%");
        })->orderBy('code', 'ASC')->get();

        $formattedMaterials = [];

        foreach ($materials as $material) {
            $formattedMaterials[] = [
                'id' => $material->id,
                'text' => $material->code .' - '.$material->name,
                'stock' => $material->stock,
                'code' => $material->code,
                'name' => $material->name,
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getMaterial(Request $request)
    {
        $id = $request->input('id');

        $product = Material::find($id);
        $product->load('unit');

        return response()->json($product);
    }

    public function getAdjustment(Request $request)
    {
        $query = $request->input('q');
        $adjustment = $request->input('adjustment');

        $products = Material::whereHas('adjustment', function ($q) use ($adjustment) {
                    $q->where('stock_adjustment_id', $adjustment);
                })->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
                })->orderBy('code', 'ASC')->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->code. ' - '. $product->name,
                'stock' => $product->stock,
                'name' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function getInfoAdjustment(Request $request)
    {
        $id = $request->input('id');
        $adjustment = $request->input('adjustment');

        $product = Material::join('stock_adjustment_details', 'stock_adjustment_details.product_id', '=', 'materials.id')
                ->where('materials.id', $id)
                ->where('stock_adjustment_details.stock_adjustment_id', $adjustment)
                ->first(['materials.*', 'stock_adjustment_details.quantity as quantity', 'stock_adjustment_details.id as adjustment_detail_id']);
        $product->load('unit');

        return response()->json($product);
    }

}

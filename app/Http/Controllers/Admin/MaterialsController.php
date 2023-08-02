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

        $material->delete();

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
}

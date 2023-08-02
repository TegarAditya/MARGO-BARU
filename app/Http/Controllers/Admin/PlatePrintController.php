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
                $btn = '
                    <a class="px-1" href="'.route('admin.plate-prints.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.plate-prints.printSpk', $row->id).'" title="Print SPK" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.plate-prints.printSpk', $row->id).'?done=1" title="Print SPK Yang Telah Selesai" target="_blank">
                        <i class="fas fa-print text-danger fa-lg"></i>
                    </a>
                ';

                if ($row->type == 'external') {
                    $btn .= '<a class="px-1" href="'.route('admin.plate-prints.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>';
                }

                return $btn;
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

            $table->editColumn('type', function ($row) {
                return $row->type ? PlatePrint::TYPE_SELECT[$row->type] : '';
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

        $no_spk = PlatePrint::generateNoSPK(setting('current_semester'));

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.platePrints.sell', compact('vendors','jenjangs', 'no_spk'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'customer' => 'required',
            'bayar' => 'required|min:0',
            'note' => 'nullable',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
            'mapels' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $customer = $validatedData['customer'];
        $bayar = $validatedData['bayar'];
        $semester = setting('current_semester');
        $note = $validatedData['note'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $mapels = $validatedData['mapels'];

        DB::beginTransaction();
        try {
            $cetak = PlatePrint::create([
                'no_spk' => PlatePrint::generateNoSPK($semester),
                'date' => $date,
                'type' => 'external',
                'semester_id' => $semester,
                'customer' => $customer,
                'fee' => $bayar,
                'note' => $note,
            ]);

            for ($i = 0; $i < count($plates); $i++) {
                $mapel = $mapels[$i];
                $plate = $plates[$i];
                $plate_quantity = $plate_quantities[$i];

                $cetak_item = PlatePrintItem::create([
                    'plate_print_id' => $cetak->id,
                    'semester_id' => $semester,
                    'product_text' => $mapel,
                    'plate_id' => $plate,
                    'estimasi' => $plate_quantity,
                    'realisasi' => $plate_quantity,
                    'note' => null,
                    'status' => 'created',
                ]);
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

        $plate_items = PlatePrintItem::with('plate')->where('plate_print_id', $platePrint->id)->get();

        if ($plate_items->whereIn('status', ['accepted', 'done'])->count() > 0) {
            Alert::error('Warning', 'Data Plate Order Sudah Dikonfirmasi, Sebaiknya tidak diedit lagi');
        }

        return view('admin.platePrints.edit', compact('platePrint', 'semesters', 'vendors', 'plate_items'));
    }

    public function update(Request $request, PlatePrint $platePrint)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'customer' => 'required',
            'bayar' => 'required|min:0',
            'note' => 'nullable',
            'plates' => 'required|array',
            'plates.*' => 'exists:materials,id',
            'plate_quantities' => 'required|array',
            'plate_quantities.*' => 'numeric|min:1',
            'plate_items' => 'required|array',
            'mapels' => 'required|array',
        ]);

        $date = $validatedData['date'];
        $customer = $validatedData['customer'];
        $bayar = $validatedData['bayar'];
        $note = $validatedData['note'];
        $plates = $validatedData['plates'];
        $plate_quantities = $validatedData['plate_quantities'];
        $plate_items = $validatedData['plate_items'];
        $mapels = $validatedData['mapels'];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($plates); $i++) {
                $mapel = $mapels[$i];
                $plate = $plates[$i];
                $plate_item = $plate_items[$i];
                $plate_quantity = $plate_quantities[$i];

                if ($plate_item) {
                    $print_plate_item = PlatePrintItem::where('id', $plate_item)->update([
                        'product_text' => $mapel,
                        'plate_id' => $plate,
                        'estimasi' => $plate_quantity,
                        'realisasi' => $plate_quantity
                    ]);
                } else {
                    $print_plate_item = PlatePrintItem::create([
                        'plate_print_id' => $platePrint->id,
                        'semester_id' => $platePrint->semester_id,
                        'product_text' => $mapel,
                        'plate_id' => $plate,
                        'estimasi' => $plate_quantity,
                        'realisasi' => $plate_quantity,
                        'note' => null,
                        'status' => 'created',
                    ]);
                }
            }

            $platePrint->update([
                'date' => $date,
                'customer' => $customer,
                'fee' => $bayar,
                'note' => $note,
            ]);

            DB::commit();

            Alert::success('Success', 'Cetak Plate Order berhasil di simpan');

            return redirect()->route('admin.plate-prints.index');
        } catch (\Exception $e) {
            DB::rollback();

            dd($e);

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }

        $platePrint->update($request->all());

        return redirect()->route('admin.plate-prints.index');
    }

    public function show(PlatePrint $platePrint)
    {
        abort_if(Gate::denies('plate_print_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platePrint->load('semester', 'vendor');

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

    public function printSpk(PlatePrint $plate, Request $request)
    {
        $plate->load('semester', 'vendor');

        $query= PlatePrintItem::with('product', 'product.jenjang', 'product.isi', 'product.cover', 'product.kurikulum')->where('plate_print_id', $plate->id);

        if (!empty($request->done)) {
            $query->where('status', 'done');
        }

        $items = $query->get();

        $items = $items->sortBy('product.kelas_id')->sortBy('product.mapel_id')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        return view('admin.platePrints.spk', compact('plate', 'items'));
    }
}

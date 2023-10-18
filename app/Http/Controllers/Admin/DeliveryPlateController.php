<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDeliveryPlateRequest;
use App\Http\Requests\StoreDeliveryPlateRequest;
use App\Http\Requests\UpdateDeliveryPlateRequest;
use App\Models\DeliveryPlate;
use App\Models\User;
use App\Models\Vendor;
use App\Models\PlatePrintItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use Alert;

class DeliveryPlateController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DeliveryPlate::with(['vendor', 'created_by', 'updated_by'])->select(sprintf('%s.*', (new DeliveryPlate)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.delivery-plates.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.delivery-plates.printSj', $row->id).'" title="Print Surat Jalan" target="_blank">
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>
                ';

                return $btn;
            });

            $table->editColumn('no_suratjalan', function ($row) {
                return $row->no_suratjalan ? $row->no_suratjalan : '';
            });

            $table->addColumn('vendor_code', function ($row) {
                return $row->vendor ? $row->vendor->code : '';
            });

            $table->editColumn('customer', function ($row) {
                return $row->vendor ? $row->vendor->code : $row->customer;
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'vendor']);

            return $table->make(true);
        }

        return view('admin.deliveryPlates.index');
    }

    public function create()
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::where('type', 'cetak')->get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $no_suratjalan = DeliveryPlate::generateNoSJ(setting('current_semester'));

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.deliveryPlates.create', compact('no_suratjalan', 'today', 'vendors'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'type' => 'required',
            'vendor_id' => 'nullable',
            'customer' => 'nullable',
            'plate_items' => 'required|array',
            'plate_items.*' => 'exists:plate_print_items,id',
            'note' => 'nullable'
        ]);

        $date = $validatedData['date'];
        $semester = setting('current_semester');
        $type = $validatedData['type'];
        $vendor = $validatedData['vendor_id'];
        $customer = $validatedData['customer'];
        $plate_items = $validatedData['plate_items'];
        $note = $validatedData['note'];

        DB::beginTransaction();
        try {
            $delivery = DeliveryPlate::create([
                'no_suratjalan' => DeliveryPlate::generateNoSJ($semester),
                'date' => $date,
                'semester_id' => $semester,
                'vendor_id' => $vendor,
                'customer' => $customer,
                'note' => $note,
            ]);

            for ($i = 0; $i < count($plate_items); $i++) {
                $plate_item = $plate_items[$i];

                PlatePrintItem::where('id', $plate_item)->update([
                    'surat_jalan_id' => $delivery->id,
                ]);
            }

            DB::commit();

            Alert::success('Success', 'Delivery Plate berhasil di simpan');

            return redirect()->route('admin.delivery-plates.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(DeliveryPlate $deliveryPlate)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vendors = Vendor::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $deliveryPlate->load('vendor', 'created_by', 'updated_by');

        return view('admin.deliveryPlates.edit', compact('created_bies', 'deliveryPlate', 'updated_bies', 'vendors'));
    }

    public function update(UpdateDeliveryPlateRequest $request, DeliveryPlate $deliveryPlate)
    {
        $deliveryPlate->update($request->all());

        return redirect()->route('admin.delivery-plates.index');
    }

    public function show(DeliveryPlate $deliveryPlate)
    {
        abort_if(Gate::denies('aquarium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryPlate->load('vendor');

        $items = PlatePrintItem::with('product')->where('surat_jalan_id', $deliveryPlate->id)->get();

        return view('admin.deliveryPlates.show', compact('deliveryPlate', 'items'));
    }

    public function destroy(DeliveryPlate $deliveryPlate)
    {
        abort_if(Gate::denies('delivery_plate_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryPlate->delete();

        return back();
    }

    public function massDestroy(MassDestroyDeliveryPlateRequest $request)
    {
        $deliveryPlates = DeliveryPlate::find(request('ids'));

        foreach ($deliveryPlates as $deliveryPlate) {
            $deliveryPlate->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getPlateItems(Request $request) {
        $query = $request->input('q');
        $type = $request->input('type');
        $vendor = $request->input('vendor');

        $materials = PlatePrintItem::where('status', 'done')
        ->whereNull('surat_jalan_id')
        ->whereHas('plate', function ($q) use ($query) {
            $q->orWhere('code', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%");
        })->whereHas('plate_print', function ($q) use ($type, $vendor) {
            $q->where('type', $type);
            if ($vendor) {
                $q->where('vendor_id', $vendor);
            }
        })->latest()->get();

        $formattedMaterials = [];

        foreach ($materials as $item) {
            $formattedMaterials[] = [
                'id' => $item->id,
                'text' => $item->plate->code .' - '.$item->plate->name,
                'quantity' => $item->estimasi,
                'spk' => $item->plate_print->no_spk,
                'mapel' => $item->product ? $item->product->name : $item->product_text,
            ];
        }

        return response()->json($formattedMaterials);
    }

    public function getInfoPlateItem(Request $request) {
        $id = $request->input('id');

        $product = PlatePrintItem::find($id);
        $product->load('plate', 'plate_print', 'plate_print', 'product');

        return response()->json($product);
    }

    public function printSj(DeliveryPlate $deliveryPlate, Request $request)
    {
        $deliveryPlate->load('vendor');

        $items = PlatePrintItem::with('product', 'product.jenjang', 'product.isi', 'product.cover', 'product.kurikulum')->where('surat_jalan_id', $deliveryPlate->id)->get();

        $items = $items->sortBy('product.nama_urut')->sortBy('product.kurikulum_id')->sortBy('product.jenjang_id');

        return view('admin.deliveryPlates.sj', compact('deliveryPlate', 'items'));
    }
}

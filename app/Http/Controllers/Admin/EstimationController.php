<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEstimationRequest;
use App\Http\Requests\StoreEstimationRequest;
use App\Http\Requests\UpdateEstimationRequest;
use App\Models\BookVariant;
use App\Models\SalesOrder;
use App\Models\Estimation;
use App\Models\EstimationItem;
use App\Models\Salesperson;
use App\Models\Semester;
use App\Models\User;
use App\Models\Jenjang;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use Alert;
use App\Services\EstimationService;

class EstimationController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('estimation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Estimation::with(['semester', 'salesperson', 'created_by', 'updated_by'])->select(sprintf('%s.*', (new Estimation)->table));

            if (!empty($request->salesperson)) {
                $query->where('salesperson_id', $request->salesperson);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');


            $table->editColumn('actions', function ($row) {
                $btn = '
                    <a class="px-1" href="'.route('admin.estimations.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.estimations.edit', $row->id).'" title="Edit">
                        <i class="fas fa-edit fa-lg"></i>
                    </a>
                    <a class="px-1" href="'.route('admin.sales-orders.show', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]).'" title="Checklist Estimasi">
                        <i class="fas fa-receipt text-danger fa-lg"></i>
                    </a>
                ';

                if ($row->salesperson) {
                    $btn .= '<a class="px-1" href="'.route('admin.sales-orders.estimasi', ['salesperson' => $row->salesperson_id, 'semester' => $row->semester_id]).'" target="_blank" title="Print Estimasi" >
                        <i class="fas fa-print text-secondary fa-lg"></i>
                    </a>';
                }

                return $btn;
            });

            $table->editColumn('no_estimasi', function ($row) {
                return $row->no_estimasi ? $row->no_estimasi : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : 'Internal';
            });

            $table->editColumn('payment_type', function ($row) {
                return $row->payment_type ? Estimation::PAYMENT_TYPE_SELECT[$row->payment_type] : '';
            });
            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('updated_by_name', function ($row) {
                return $row->updated_by ? $row->updated_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'semester', 'salesperson', 'created_by', 'updated_by']);

            return $table->make(true);
        }

        $salespeople = Salesperson::get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $today = Carbon::now()->format('d-m-Y');

        return view('admin.estimations.index', compact('semesters', 'salespeople', 'today'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('estimation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $no_estimasi = Estimation::generateNoEstimasi(setting('current_semester'));
        $today = Carbon::now()->format('d-m-Y');

        if ($request->salesperson == 'internal') {
            return view('admin.estimations.createInternal', compact('jenjangs', 'no_estimasi', 'today'));
        }

        $semesters = Semester::orderBy('code', 'DESC')->where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $salespeople = Salesperson::get()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.estimations.create', compact('salespeople', 'semesters', 'jenjangs', 'no_estimasi', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required',
            'salesperson_id' => 'nullable',
            'jenjang_id' => 'nullable',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'payment_types' => 'required|array',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        $date = $validatedData['date'];
        $semester = setting('current_semester');
        $salesperson = $validatedData['salesperson_id'] ?? null;
        $payment_types = $validatedData['payment_types'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];

        DB::beginTransaction();
        try {
            $estimasi = Estimation::create([
                'no_estimasi' => Estimation::generateNoEstimasi($semester),
                'date' => $date,
                'semester_id' => $semester,
                'salesperson_id' => $salesperson,
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $payment = $payment_types[$i];
                $quantity = $quantities[$i];

                $estimasi_item = EstimationItem::create([
                    'estimation_id' => $estimasi->id,
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'payment_type' => $payment,
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id,
                    'quantity' => $quantity
                ]);

                $order = SalesOrder::updateOrCreate([
                    'semester_id' => $semester,
                    'salesperson_id' => $salesperson,
                    'product_id' => $product->id,
                    'jenjang_id' => $product->jenjang_id,
                    'kurikulum_id' => $product->kurikulum_id
                ], [
                    'payment_type' => $payment,
                    'no_order' => SalesOrder::generateNoOrder($semester, $salesperson, $payment),
                    'quantity' => DB::raw("quantity + $quantity"),
                ]);

                if ($product->semester_id == $semester) {
                    if ($salesperson) {
                        EstimationService::createMovement('in', 'sales_order', $estimasi->id, $product->id, $quantity, $product->type);
                        EstimationService::createProduction($product->id, $quantity, $product->type);

                        foreach($product->components as $item) {
                            EstimationService::createMovement('in', 'sales_order', $estimasi->id, $item->id, $quantity, $item->type);
                            EstimationService::createProduction($item->id, $quantity, $item->type);
                        }
                    } else {
                        EstimationService::createMovement('in', 'sales_order', $estimasi->id, $product->id, $quantity, $product->type);
                        EstimationService::createInternal($product->id, $quantity, $product->type);

                        foreach($product->components as $item) {
                            EstimationService::createMovement('in', 'sales_order', $estimasi->id, $item->id, $quantity, $item->type);
                            EstimationService::createInternal($item->id, $quantity, $item->type);
                        }
                    }
                }
            }

            DB::commit();

            Alert::success('Success', 'Estimasi berhasil di simpan');

            return redirect()->route('admin.estimations.index');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function edit(Estimation $estimation)
    {
        abort_if(Gate::denies('estimation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jenjangs = Jenjang::pluck('name', 'id')->prepend('All', '');

        $estimation->load('semester', 'salesperson');

        $no_estimasi = noRevisi($estimation->no_estimasi);

        $estimasi_list = EstimationItem::where('estimation_id', $estimation->id)->get();

        if ($estimation->salesperson_id == 0) {
            return view('admin.estimations.editInternal', compact('estimation', 'estimasi_list', 'jenjangs', 'no_estimasi'));
        }

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.estimations.edit', compact('estimation', 'estimasi_list', 'salespeople', 'jenjangs', 'no_estimasi'));
    }

    public function update(Request $request, Estimation $estimation)
    {
         // Validate the form data
         $validatedData = $request->validate([
            'no_estimasi' => 'required',
            'date' => 'required',
            'products' => 'required|array',
            'products.*' => 'exists:book_variants,id',
            'payment_types' => 'required|array',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:0',
            'estimasi_items' => 'required|array',
            'estimasi_items.*' => 'exists:estimation_items,id',
        ]);
        $no_estimasi = $validatedData['no_estimasi'];
        $date = $validatedData['date'];
        $estimasi_items = $validatedData['estimasi_items'];
        $products = $validatedData['products'];
        $quantities = $validatedData['quantities'];
        $payment_types = $validatedData['payment_types'];

        $semester = $estimation->semester_id;
        $salesperson = $estimation->salesperson_id;

        DB::beginTransaction();
        try {
            $estimation->update([
                'no_estimasi' => $no_estimasi,
                'date' => $date,
            ]);

            for ($i = 0; $i < count($products); $i++) {
                $product = BookVariant::find($products[$i]);
                $type = $payment_types[$i];
                $quantity = $quantities[$i];

                $estimasi_item = EstimationItem::find($estimasi_items[$i]);
                $order = SalesOrder::where('semester_id', $semester)
                        ->where('salesperson_id', $salesperson)
                        ->where('product_id', $product->id)
                        ->first();

                $old_quantity = $estimasi_item->quantity;
                $old_order = $order->quantity;

                if ($type !== $order->payment_type) {
                    $order->no_order = SalesOrder::generateNoOrder($semester, $salesperson, $type);
                    $order->payment_type = $type;
                }
                $order->quantity = ($old_order - $old_quantity) + $quantity;
                $order->save();

                $estimasi_item->update([
                    'quantity' => $quantity
                ]);

                if ($product->semester_id == $semester) {
                    if ($salesperson) {
                        EstimationService::editMovement('in', 'sales_order', $estimation->id, $product->id, $quantity, $product->type);
                        EstimationService::editProduction($product->id, ($quantity - $old_quantity), $product->type);

                        foreach($product->components as $item) {
                            EstimationService::editMovement('in', 'sales_order', $estimation->id, $item->id, $quantity, $item->type);
                            EstimationService::editProduction($item->id, ($quantity - $old_quantity), $item->type);
                        }
                    } else {
                        EstimationService::editMovement('in', 'sales_order', $estimation->id, $product->id, $quantity, $product->type);
                        EstimationService::editInternal($product->id, ($quantity - $old_quantity), $product->type);

                        foreach($product->components as $item) {
                            EstimationService::editMovement('in', 'sales_order', $estimation->id, $item->id, $quantity, $item->type);
                            EstimationService::editInternal($item->id, ($quantity - $old_quantity), $item->type);
                        }
                    }
                }
            }

            DB::commit();
            Alert::success('Success', 'Sales Order berhasil di simpan');

            return redirect()->route('admin.estimations.index');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return redirect()->back()->with('error-message', $e->getMessage())->withInput();
        }
    }

    public function show(Estimation $estimation)
    {
        abort_if(Gate::denies('estimation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $estimation->load('semester', 'salesperson');

        $estimasi_list = EstimationItem::where('estimation_id', $estimation->id)->get();

        return view('admin.estimations.show', compact('estimation', 'estimasi_list'));
    }

    public function destroy(Estimation $estimation)
    {
        abort_if(Gate::denies('estimation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $estimation->delete();

        return back();
    }

    public function massDestroy(MassDestroyEstimationRequest $request)
    {
        $estimations = Estimation::find(request('ids'));

        foreach ($estimations as $estimation) {
            $estimation->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function import(Request $request)
    {
        dd($request->all());
        $file = $request->file('import_file');
        $request->validate([
            'import_file' => 'mimes:csv,txt,xls,xlsx',
        ]);

        try {
            Excel::import(new SalesOrderImport(9), $file);
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return redirect()->back();
        }

        Alert::success('Success', 'Sales Order berhasil di import');
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockSaldoRequest;
use App\Http\Requests\StoreStockSaldoRequest;
use App\Http\Requests\UpdateStockSaldoRequest;
use App\Models\BookVariant;
use App\Models\Material;
use App\Models\StockSaldo;
use App\Models\Halaman;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\Unit;
use App\Models\Isi;
use App\Models\Cover;
use App\Models\Kelas;
use App\Models\Mapel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Alert;
use DB;
use Illuminate\Support\Facades\Date;

class StockSaldoController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_saldo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockSaldo::whereHas('product', function ($q) use ($request) {
                if (!empty($request->type)) {
                    $q->where('type', $request->type);
                }
                if (!empty($request->jenjang)) {
                    $q->where('jenjang_id', $request->jenjang);
                }
                if (!empty($request->isi)) {
                    $q->where('isi_id', $request->isi);
                }
                if (!empty($request->cover)) {
                    $q->where('cover_id', $request->cover);
                }
                if (!empty($request->kelas)) {
                    $q->where('kelas_id', $request->kelas);
                }
                if (!empty($request->mapel)) {
                    $q->where('mapel_id', $request->mapel);
                }
                $q->orderBy('mapel_id', 'ASC')->orderBy('kelas_id', 'ASC');
            })->with(['product'])->select(sprintf('%s.*', (new StockSaldo)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');

            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '';
            });

            $table->editColumn('periode', function ($row) {
                return $row->periode ? $row->periode : '';
            });

            $table->editColumn('qty_awal', function ($row) {
                return $row->qty_awal ? angka($row->qty_awal) : 0;
            });
            $table->editColumn('in', function ($row) {
                return $row->in ? angka($row->in) : 0;
            });
            $table->editColumn('out', function ($row) {
                return $row->out ? angka($row->out) : 0;
            });
            $table->editColumn('qty_akhir', function ($row) {
                return $row->qty_akhir ? angka($row->qty_akhir) : 0;
            });

            $table->rawColumns(['placeholder', 'product']);

            return $table->make(true);
        }

        $jenjangs = Jenjang::pluck('name', 'id')->prepend('All', '');

        $mapels = Mapel::pluck('name', 'id')->prepend('All', '');

        $kelas = Kelas::pluck('name', 'id')->prepend('All', '');

        $covers = Cover::pluck('name', 'id')->prepend('All', '');

        $isis = Isi::pluck('name', 'id')->prepend('All', '');

        $periode = StockSaldo::groupBy('code', 'periode')->pluck('periode', 'code');

        return view('admin.stockSaldos.index', compact('covers', 'jenjangs', 'kelas', 'mapels', 'isis', 'periode'));
    }

    public function create()
    {
        abort_if(Gate::denies('stock_saldo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockSaldos.create', compact('materials', 'products'));
    }

    public function store(Request $request)
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $lastmonth = Carbon::now()->subMonth()->format('mY');
        $periode = $start->format('d F Y') .' -  '. $end->format('d F Y');
        $code = $start->format('mY');

        $semester = setting('current_semester');

        $bookvariant = BookVariant::withSum(['movement as in' => function ($q) use ($start, $end) {
                    $q->where('movement_type', 'in')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
                }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
                    $q->where('movement_type', 'out')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
                }], 'quantity')->where(function($q) use ($semester) {
                    $q->where('semester_id', $semester)
                    ->orWhere('stock' , '>', 0);
                })->whereIn('type', ['L', 'P', 'K'])->get();

        foreach($bookvariant as $book) {
            $before = StockSaldo::where('code', $lastmonth)->where('product_id', $book->id)->first();

            if ($before) {
                $qty_awal = $before->qty_akhir;
            } else {
                $qty_awal = 0;
            }

            StockSaldo::updateOrCreate([
                'code' => $code,
                'product_id' => $book->id,
            ], [
                'periode' => $periode,
                'start_date' => $start->format('d-m-Y'),
                'end_date' => $end->format('d-m-Y'),
                'qty_awal' => $qty_awal,
                'in' => $book->in,
                'out' => $book->out,
                'qty_akhir' => $qty_awal + ($book->in - $book->out),
            ]);
        }

        return redirect()->route('admin.stock-saldos.index');
    }

    public function edit(StockSaldo $stockSaldo)
    {
        abort_if(Gate::denies('stock_saldo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = BookVariant::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $materials = Material::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stockSaldo->load('product', 'material');

        return view('admin.stockSaldos.edit', compact('materials', 'products', 'stockSaldo'));
    }

    public function update(UpdateStockSaldoRequest $request, StockSaldo $stockSaldo)
    {
        $stockSaldo->update($request->all());

        return redirect()->route('admin.stock-saldos.index');
    }

    public function show(StockSaldo $stockSaldo)
    {
        abort_if(Gate::denies('stock_saldo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockSaldo->load('product', 'material');

        return view('admin.stockSaldos.show', compact('stockSaldo'));
    }

    public function destroy(StockSaldo $stockSaldo)
    {
        abort_if(Gate::denies('stock_saldo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockSaldo->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockSaldoRequest $request)
    {
        $stockSaldos = StockSaldo::find(request('ids'));

        foreach ($stockSaldos as $stockSaldo) {
            $stockSaldo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
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

        $semester = setting('current_semester');

        $awal = BookVariant::withSum(['movement as in' => function ($q) use ($start, $end) {
            $q->where('movement_type', 'in')->where('movement_date', '<', $start)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
            $q->where('movement_type', 'out')->where('movement_date', '<', $start)->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->where(function($q) use ($semester) {
            $q->where('semester_id', $semester)
            ->orWhere('stock' , '>', 0);
        })->whereIn('type', ['L', 'P', 'K']);

        if (!empty($request->type)) {
            $awal->where('type', $request->type);
        }
        if (!empty($request->jenjang)) {
            $awal->where('jenjang_id', $request->jenjang);
        }
        if (!empty($request->isi)) {
            $awal->where('isi_id', $request->isi);
        }
        if (!empty($request->cover)) {
            $awal->where('cover_id', $request->cover);
        }
        if (!empty($request->kelas)) {
            $awal->where('kelas_id', $request->kelas);
        }
        if (!empty($request->mapel)) {
            $awal->where('mapel_id', $request->mapel);
        }

        $saldo_awal = $awal->get();

        $akhir = BookVariant::withSum(['movement as in' => function ($q) use ($start, $end) {
            $q->where('movement_type', 'in')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->withSum(['movement as out' => function ($q) use ($start, $end) {
            $q->where('movement_type', 'out')->whereBetween('movement_date', [$start, $end])->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }], 'quantity')->where(function($q) use ($semester) {
            $q->where('semester_id', $semester)
            ->orWhere('stock' , '>', 0);
        })->whereIn('type', ['L', 'P', 'K']);

        if (!empty($request->type)) {
            $akhir->where('type', $request->type);
        }
        if (!empty($request->jenjang)) {
            $akhir->where('jenjang_id', $request->jenjang);
        }
        if (!empty($request->isi)) {
            $akhir->where('isi_id', $request->isi);
        }
        if (!empty($request->cover)) {
            $akhir->where('cover_id', $request->cover);
        }
        if (!empty($request->kelas)) {
            $akhir->where('kelas_id', $request->kelas);
        }
        if (!empty($request->mapel)) {
            $akhir->where('mapel_id', $request->mapel);
        }
        $saldo_akhir = $akhir->orderBy('semester_id', 'DESC')->orderBy('jenjang_id', 'ASC')->orderBy('mapel_id', 'ASC')->orderBy('kelas_id', 'ASC')->orderBy('cover_id', 'ASC')->get();

        return view('admin.stockSaldos.billing', compact('start', 'end', 'saldo_awal', 'saldo_akhir'));
    }
}

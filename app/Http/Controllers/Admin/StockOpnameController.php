<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockOpnameRequest;
use App\Http\Requests\StoreStockOpnameRequest;
use App\Http\Requests\UpdateStockOpnameRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Book;
use App\Models\BookVariant;
use App\Models\Halaman;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\Unit;
use App\Models\Cover;
use App\Models\Kelas;
use App\Models\Mapel;
use Yajra\DataTables\Facades\DataTables;
use DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_opname_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BookVariant::with(['book', 'components', 'jenjang', 'semester', 'kurikulum', 'halaman', 'warehouse', 'unit'])->select(sprintf('%s.*', (new BookVariant)->table));

            if (!empty($request->type)) {
                $query->where('type', $request->type);
            }
            if (!empty($request->semester)) {
                $query->where('semester_id', $request->semester);
            }
            if (!empty($request->cover)) {
                $query->where('cover_id', $request->cover);
            }
            if (!empty($request->kurikulum)) {
                $query->where('kurikulum_id', $request->kurikulum);
            }
            if (!empty($request->kelas)) {
                $query->where('kelas_id', $request->kelas);
            }
            if (!empty($request->mapel)) {
                $query->where('mapel_id', $request->mapel);
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return '
                    <a class="px-1" href="'.route('admin.book-variants.show', $row->id).'" title="Show">
                        <i class="fas fa-eye text-success fa-lg"></i>
                    </a>
                ';
            });

            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? BookVariant::TYPE_SELECT[$row->type] : '';
            });

            $table->addColumn('jenjang_code', function ($row) {
                return $row->jenjang ? $row->jenjang->code : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->addColumn('kurikulum_code', function ($row) {
                return $row->kurikulum ? $row->kurikulum->code : '';
            });

            $table->addColumn('halaman_name', function ($row) {
                return $row->halaman ? $row->halaman->name : '';
            });

            $table->editColumn('stock', function ($row) {
                return $row->stock ? $row->stock : 0;
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('cost', function ($row) {
                return $row->cost ? $row->cost : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'jenjang', 'semester', 'kurikulum', 'halaman', 'buku']);

            return $table->make(true);
        }

        $jenjangs = Jenjang::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kurikulums = Kurikulum::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $mapels = Mapel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $kelas = Kelas::pluck('name', 'id');

        $covers = Cover::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockOpnames.index', compact('covers', 'jenjangs', 'kelas', 'kurikulums', 'mapels', 'semesters'));
    }


    public function summary(Request $request) {

        $buku = BookVariant::where('type', 'L')->where('stock','>', 0)->selectRaw('stock * price AS total_price')->get();
        $pg = BookVariant::where('type', 'P')->where('stock','>', 0)->selectRaw('stock * price AS total_price')->get();
        
        // $summary_jenjang = Product::where('stock', '>', '0')->with('jenjang')->selectRaw('jenjang_id, SUM(stock) as total_stock, SUM(stock * price) AS total_price, SUM(stock * hpp) AS total_hpp')->groupBy('jenjang_id')->get();
        // $summary_semester = Product::where('stock', '>', '0')->with('semester')->selectRaw('semester_id, SUM(stock) as total_stock, SUM(stock * price) AS total_price, SUM(stock * hpp) AS total_hpp')->groupBy('semester_id')->get();

        // $jenjangs = Jenjang::pluck('name', 'id')->prepend('All', '');

        // $covers = Cover::pluck('name', 'id')->prepend('All', '');

        // $semesters = Semester::where('status', 1)->pluck('name', 'id')->prepend('All', '');

        return view('admin.stockOpnames.summary', compact('buku', 'pg'));
    }


    public function create()
    {
        abort_if(Gate::denies('stock_opname_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.create');
    }

    public function store(StoreStockOpnameRequest $request)
    {
        $stockOpname = StockOpname::create($request->all());

        return redirect()->route('admin.stock-opnames.index');
    }

    public function edit(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('stock_opname_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.edit', compact('stockOpname'));
    }

    public function update(UpdateStockOpnameRequest $request, StockOpname $stockOpname)
    {
        $stockOpname->update($request->all());

        return redirect()->route('admin.stock-opnames.index');
    }

    public function show(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('stock_opname_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stockOpnames.show', compact('stockOpname'));
    }

    public function destroy(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('stock_opname_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockOpname->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockOpnameRequest $request)
    {
        $stockOpnames = StockOpname::find(request('ids'));

        foreach ($stockOpnames as $stockOpname) {
            $stockOpname->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

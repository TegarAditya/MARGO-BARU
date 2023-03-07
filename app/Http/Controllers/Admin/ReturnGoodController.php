<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyReturnGoodRequest;
use App\Http\Requests\StoreReturnGoodRequest;
use App\Http\Requests\UpdateReturnGoodRequest;
use App\Models\ReturnGood;
use App\Models\Salesperson;
use App\Models\Semester;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ReturnGoodController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('return_good_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ReturnGood::with(['salesperson', 'semester'])->select(sprintf('%s.*', (new ReturnGood)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'return_good_show';
                $editGate      = 'return_good_edit';
                $deleteGate    = 'return_good_delete';
                $crudRoutePart = 'return-goods';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('no_retur', function ($row) {
                return $row->no_retur ? $row->no_retur : '';
            });

            $table->addColumn('salesperson_name', function ($row) {
                return $row->salesperson ? $row->salesperson->name : '';
            });

            $table->addColumn('semester_name', function ($row) {
                return $row->semester ? $row->semester->name : '';
            });

            $table->editColumn('nominal', function ($row) {
                return $row->nominal ? $row->nominal : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'salesperson', 'semester']);

            return $table->make(true);
        }

        return view('admin.returnGoods.index');
    }

    public function create()
    {
        abort_if(Gate::denies('return_good_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.returnGoods.create', compact('salespeople', 'semesters'));
    }

    public function store(StoreReturnGoodRequest $request)
    {
        $returnGood = ReturnGood::create($request->all());

        return redirect()->route('admin.return-goods.index');
    }

    public function edit(ReturnGood $returnGood)
    {
        abort_if(Gate::denies('return_good_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salespeople = Salesperson::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $semesters = Semester::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $returnGood->load('salesperson', 'semester');

        return view('admin.returnGoods.edit', compact('returnGood', 'salespeople', 'semesters'));
    }

    public function update(UpdateReturnGoodRequest $request, ReturnGood $returnGood)
    {
        $returnGood->update($request->all());

        return redirect()->route('admin.return-goods.index');
    }

    public function show(ReturnGood $returnGood)
    {
        abort_if(Gate::denies('return_good_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returnGood->load('salesperson', 'semester');

        return view('admin.returnGoods.show', compact('returnGood'));
    }

    public function destroy(ReturnGood $returnGood)
    {
        abort_if(Gate::denies('return_good_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $returnGood->delete();

        return back();
    }

    public function massDestroy(MassDestroyReturnGoodRequest $request)
    {
        $returnGoods = ReturnGood::find(request('ids'));

        foreach ($returnGoods as $returnGood) {
            $returnGood->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.cetakItem.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.cetak-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.semester') }}
                        </th>
                        <td>
                            {{ $cetakItem->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.product') }}
                        </th>
                        <td>
                            {{ $cetakItem->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.halaman') }}
                        </th>
                        <td>
                            {{ $cetakItem->halaman->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.quantity') }}
                        </th>
                        <td>
                            {{ $cetakItem->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.cost') }}
                        </th>
                        <td>
                            {{ $cetakItem->cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.plate') }}
                        </th>
                        <td>
                            {{ $cetakItem->plate->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.plate_cost') }}
                        </th>
                        <td>
                            {{ $cetakItem->plate_cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.paper') }}
                        </th>
                        <td>
                            {{ $cetakItem->paper->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.cetakItem.fields.paper_cost') }}
                        </th>
                        <td>
                            {{ $cetakItem->paper_cost }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.cetak-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
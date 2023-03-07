@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.stockAdjustment.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-adjustments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustment.fields.date') }}
                        </th>
                        <td>
                            {{ $stockAdjustment->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustment.fields.operation') }}
                        </th>
                        <td>
                            {{ App\Models\StockAdjustment::OPERATION_SELECT[$stockAdjustment->operation] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustment.fields.quantity') }}
                        </th>
                        <td>
                            {{ $stockAdjustment->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustment.fields.reason') }}
                        </th>
                        <td>
                            {{ $stockAdjustment->reason }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustment.fields.note') }}
                        </th>
                        <td>
                            {{ $stockAdjustment->note }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-adjustments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
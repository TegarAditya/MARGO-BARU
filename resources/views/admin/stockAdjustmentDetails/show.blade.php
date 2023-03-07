@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.stockAdjustmentDetail.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-adjustment-details.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustmentDetail.fields.product') }}
                        </th>
                        <td>
                            {{ $stockAdjustmentDetail->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustmentDetail.fields.material') }}
                        </th>
                        <td>
                            {{ $stockAdjustmentDetail->material->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustmentDetail.fields.stock_adjustment') }}
                        </th>
                        <td>
                            {{ $stockAdjustmentDetail->stock_adjustment->reason ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockAdjustmentDetail.fields.quantity') }}
                        </th>
                        <td>
                            {{ $stockAdjustmentDetail->quantity }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-adjustment-details.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
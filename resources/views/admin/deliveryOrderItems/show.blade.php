@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.deliveryOrderItem.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.delivery-order-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryOrderItem.fields.semester') }}
                        </th>
                        <td>
                            {{ $deliveryOrderItem->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryOrderItem.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $deliveryOrderItem->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryOrderItem.fields.sales_order') }}
                        </th>
                        <td>
                            {{ $deliveryOrderItem->sales_order->quantity ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryOrderItem.fields.delivery_order') }}
                        </th>
                        <td>
                            {{ $deliveryOrderItem->delivery_order->no_suratjalan ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryOrderItem.fields.product') }}
                        </th>
                        <td>
                            {{ $deliveryOrderItem->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryOrderItem.fields.quantity') }}
                        </th>
                        <td>
                            {{ $deliveryOrderItem->quantity }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.delivery-order-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
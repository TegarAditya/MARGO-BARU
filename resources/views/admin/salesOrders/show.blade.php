@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.salesOrder.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sales-orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.semester') }}
                        </th>
                        <td>
                            {{ $salesOrder->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $salesOrder->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.product') }}
                        </th>
                        <td>
                            {{ $salesOrder->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.jenjang') }}
                        </th>
                        <td>
                            {{ $salesOrder->jenjang->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.kurikulum') }}
                        </th>
                        <td>
                            {{ $salesOrder->kurikulum->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.quantity') }}
                        </th>
                        <td>
                            {{ $salesOrder->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.moved') }}
                        </th>
                        <td>
                            {{ $salesOrder->moved }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesOrder.fields.retur') }}
                        </th>
                        <td>
                            {{ $salesOrder->retur }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sales-orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.returnGoodItem.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.return-good-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.retur') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->retur->no_retur ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.semester') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.sales_order') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->sales_order->quantity ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.product') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.price') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.quantity') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.returnGoodItem.fields.total') }}
                        </th>
                        <td>
                            {{ $returnGoodItem->total }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.return-good-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
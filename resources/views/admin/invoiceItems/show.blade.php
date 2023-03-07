@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.invoiceItem.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.invoice-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.invoice') }}
                        </th>
                        <td>
                            {{ $invoiceItem->invoice->no_faktur ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.delivery_order') }}
                        </th>
                        <td>
                            {{ $invoiceItem->delivery_order->no_suratjalan ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.semester') }}
                        </th>
                        <td>
                            {{ $invoiceItem->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.salesperson') }}
                        </th>
                        <td>
                            {{ $invoiceItem->salesperson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.product') }}
                        </th>
                        <td>
                            {{ $invoiceItem->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.quantity') }}
                        </th>
                        <td>
                            {{ $invoiceItem->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.price_unit') }}
                        </th>
                        <td>
                            {{ $invoiceItem->price_unit }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.discount') }}
                        </th>
                        <td>
                            {{ $invoiceItem->discount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.price') }}
                        </th>
                        <td>
                            {{ $invoiceItem->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.invoiceItem.fields.total') }}
                        </th>
                        <td>
                            {{ $invoiceItem->total }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.invoice-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
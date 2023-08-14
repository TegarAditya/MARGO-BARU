@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productionPayment.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-payments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.no_payment') }}
                        </th>
                        <td>
                            {{ $productionPayment->no_payment }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.date') }}
                        </th>
                        <td>
                            {{ $productionPayment->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.vendor') }}
                        </th>
                        <td>
                            {{ $productionPayment->vendor->full_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.semester') }}
                        </th>
                        <td>
                            {{ $productionPayment->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.nominal') }}
                        </th>
                        <td>
                            {{ money($productionPayment->nominal) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.payment_method') }}
                        </th>
                        <td>
                            {{ App\Models\ProductionPayment::PAYMENT_METHOD_SELECT[$productionPayment->payment_method] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionPayment.fields.note') }}
                        </th>
                        <td>
                            {{ $productionPayment->note }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-payments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
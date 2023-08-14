@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productionTransaction.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-transactions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.date') }}
                        </th>
                        <td>
                            {{ $productionTransaction->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.description') }}
                        </th>
                        <td>
                            {{ $productionTransaction->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.vendor') }}
                        </th>
                        <td>
                            {{ $productionTransaction->vendor->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.semester') }}
                        </th>
                        <td>
                            {{ $productionTransaction->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\ProductionTransaction::TYPE_SELECT[$productionTransaction->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.reference') }}
                        </th>
                        <td>
                            {{ $productionTransaction->reference->no_faktur ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.reference_no') }}
                        </th>
                        <td>
                            {{ $productionTransaction->reference_no }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.transaction_date') }}
                        </th>
                        <td>
                            {{ $productionTransaction->transaction_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.amount') }}
                        </th>
                        <td>
                            {{ $productionTransaction->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.category') }}
                        </th>
                        <td>
                            {{ App\Models\ProductionTransaction::CATEGORY_SELECT[$productionTransaction->category] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.status') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $productionTransaction->status ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.reversal_of') }}
                        </th>
                        <td>
                            {{ $productionTransaction->reversal_of->description ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.created_by') }}
                        </th>
                        <td>
                            {{ $productionTransaction->created_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionTransaction.fields.updated_by') }}
                        </th>
                        <td>
                            {{ $productionTransaction->updated_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-transactions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
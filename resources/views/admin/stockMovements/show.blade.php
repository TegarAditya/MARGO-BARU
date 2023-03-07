@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.stockMovement.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-movements.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.stockMovement.fields.movement_date') }}
                        </th>
                        <td>
                            {{ $stockMovement->movement_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockMovement.fields.movement_type') }}
                        </th>
                        <td>
                            {{ App\Models\StockMovement::MOVEMENT_TYPE_SELECT[$stockMovement->movement_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockMovement.fields.product') }}
                        </th>
                        <td>
                            {{ $stockMovement->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockMovement.fields.material') }}
                        </th>
                        <td>
                            {{ $stockMovement->material->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockMovement.fields.quantity') }}
                        </th>
                        <td>
                            {{ $stockMovement->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockMovement.fields.transaction_type') }}
                        </th>
                        <td>
                            {{ App\Models\StockMovement::TRANSACTION_TYPE_SELECT[$stockMovement->transaction_type] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-movements.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.estimationMovement.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.estimation-movements.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.estimationMovement.fields.movement_date') }}
                        </th>
                        <td>
                            {{ $estimationMovement->movement_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.estimationMovement.fields.movement_type') }}
                        </th>
                        <td>
                            {{ App\Models\EstimationMovement::MOVEMENT_TYPE_SELECT[$estimationMovement->movement_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.estimationMovement.fields.product') }}
                        </th>
                        <td>
                            {{ $estimationMovement->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.estimationMovement.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\EstimationMovement::TYPE_SELECT[$estimationMovement->type] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.estimation-movements.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productionEstimation.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-estimations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.product') }}
                        </th>
                        <td>
                            {{ $productionEstimation->product->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.quantity') }}
                        </th>
                        <td>
                            {{ $productionEstimation->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.estimasi') }}
                        </th>
                        <td>
                            {{ $productionEstimation->estimasi }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.isi') }}
                        </th>
                        <td>
                            {{ $productionEstimation->isi }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.cover') }}
                        </th>
                        <td>
                            {{ $productionEstimation->cover }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productionEstimation.fields.finishing') }}
                        </th>
                        <td>
                            {{ $productionEstimation->finishing }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.production-estimations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
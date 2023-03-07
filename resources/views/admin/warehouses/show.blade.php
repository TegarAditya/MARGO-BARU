@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.warehouse.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.warehouses.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.warehouse.fields.code') }}
                        </th>
                        <td>
                            {{ $warehouse->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.warehouse.fields.name') }}
                        </th>
                        <td>
                            {{ $warehouse->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.warehouse.fields.description') }}
                        </th>
                        <td>
                            {{ $warehouse->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.warehouse.fields.address') }}
                        </th>
                        <td>
                            {{ $warehouse->address }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.warehouses.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
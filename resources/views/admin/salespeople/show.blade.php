@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.salesperson.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.salespeople.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.salesperson.fields.code') }}
                        </th>
                        <td>
                            {{ $salesperson->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesperson.fields.name') }}
                        </th>
                        <td>
                            {{ $salesperson->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesperson.fields.marketing_area') }}
                        </th>
                        <td>
                            {{ $salesperson->marketing_area->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesperson.fields.phone') }}
                        </th>
                        <td>
                            {{ $salesperson->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesperson.fields.company') }}
                        </th>
                        <td>
                            {{ $salesperson->company }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesperson.fields.address') }}
                        </th>
                        <td>
                            {{ $salesperson->address }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.salespeople.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
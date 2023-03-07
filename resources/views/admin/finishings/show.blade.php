@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.finishing.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.finishings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.no_spk') }}
                        </th>
                        <td>
                            {{ $finishing->no_spk }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.date') }}
                        </th>
                        <td>
                            {{ $finishing->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.semester') }}
                        </th>
                        <td>
                            {{ $finishing->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.vendor') }}
                        </th>
                        <td>
                            {{ $finishing->vendor->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.total_cost') }}
                        </th>
                        <td>
                            {{ $finishing->total_cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.total_oplah') }}
                        </th>
                        <td>
                            {{ $finishing->total_oplah }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.finishing.fields.note') }}
                        </th>
                        <td>
                            {{ $finishing->note }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.finishings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
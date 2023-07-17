@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.groupArea.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.group-areas.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.groupArea.fields.code') }}
                        </th>
                        <td>
                            {{ $groupArea->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.groupArea.fields.name') }}
                        </th>
                        <td>
                            {{ $groupArea->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.groupArea.fields.provinsi') }}
                        </th>
                        <td>
                            {{ App\Models\GroupArea::PROVINSI_SELECT[$groupArea->provinsi] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.group-areas.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
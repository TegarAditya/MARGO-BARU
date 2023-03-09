@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.semester.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.semesters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.semester.fields.code') }}
                        </th>
                        <td>
                            {{ $semester->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.semester.fields.name') }}
                        </th>
                        <td>
                            {{ $semester->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.semester.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\Semester::TYPE_SELECT[$semester->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.semester.fields.start_date') }}
                        </th>
                        <td>
                            {{ $semester->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.semester.fields.end_date') }}
                        </th>
                        <td>
                            {{ $semester->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.semester.fields.status') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $semester->status ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.semesters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
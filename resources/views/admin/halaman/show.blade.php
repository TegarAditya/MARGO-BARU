@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.halaman.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.halaman.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.halaman.fields.code') }}
                        </th>
                        <td>
                            {{ $halaman->code ?? null }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.halaman.fields.name') }}
                        </th>
                        <td>
                            {{ $halaman->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.halaman.fields.value') }}
                        </th>
                        <td>
                            {{ $halaman->value }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.halaman.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection

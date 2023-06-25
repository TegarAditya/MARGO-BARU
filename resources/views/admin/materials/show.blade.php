@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.material.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.materials.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.code') }}
                        </th>
                        <td>
                            {{ $material->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.name') }}
                        </th>
                        <td>
                            {{ $material->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.description') }}
                        </th>
                        <td>
                            {{ $material->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.category') }}
                        </th>
                        <td>
                            {{ App\Models\Material::CATEGORY_SELECT[$material->category] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.unit') }}
                        </th>
                        <td>
                            {{ $material->unit->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.cost') }}
                        </th>
                        <td>
                            {{ $material->cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.stock') }}
                        </th>
                        <td>
                            {{ $material->stock }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.material.fields.vendor') }}
                        </th>
                        <td>
                            @foreach($material->vendors as $key => $vendor)
                                <span class="label label-info">{{ $vendor->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.materials.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
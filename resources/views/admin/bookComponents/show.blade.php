@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bookComponent.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.book-components.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.code') }}
                        </th>
                        <td>
                            {{ $bookComponent->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.name') }}
                        </th>
                        <td>
                            {{ $bookComponent->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.description') }}
                        </th>
                        <td>
                            {{ $bookComponent->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\BookComponent::TYPE_SELECT[$bookComponent->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.jenjang') }}
                        </th>
                        <td>
                            {{ $bookComponent->jenjang->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.kurikulum') }}
                        </th>
                        <td>
                            {{ $bookComponent->kurikulum->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.isi') }}
                        </th>
                        <td>
                            {{ $bookComponent->isi->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.cover') }}
                        </th>
                        <td>
                            {{ $bookComponent->cover->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.mapel') }}
                        </th>
                        <td>
                            {{ $bookComponent->mapel->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.kelas') }}
                        </th>
                        <td>
                            {{ $bookComponent->kelas->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.halaman') }}
                        </th>
                        <td>
                            {{ $bookComponent->halaman->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.semester') }}
                        </th>
                        <td>
                            {{ $bookComponent->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.warehouse') }}
                        </th>
                        <td>
                            {{ $bookComponent->warehouse->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.stock') }}
                        </th>
                        <td>
                            {{ $bookComponent->stock }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.unit') }}
                        </th>
                        <td>
                            {{ $bookComponent->unit->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.price') }}
                        </th>
                        <td>
                            {{ $bookComponent->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.cost') }}
                        </th>
                        <td>
                            {{ $bookComponent->cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Material Of
                        </th>
                        <td>
                            @foreach($bookComponent->material_of as $key => $components)
                                <span class="label label-info">{{ $components->code }}</span>
                                <br>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.created_by') }}
                        </th>
                        <td>
                            {{ $bookComponent->created_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookComponent.fields.updated_by') }}
                        </th>
                        <td>
                            {{ $bookComponent->updated_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.book-components.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection

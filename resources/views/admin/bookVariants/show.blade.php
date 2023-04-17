@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bookVariant.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.book-variants.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.book') }}
                        </th>
                        <td>
                            {{ $bookVariant->book->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.parent') }}
                        </th>
                        <td>
                            {{ $bookVariant->parent->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.code') }}
                        </th>
                        <td>
                            {{ $bookVariant->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\BookVariant::TYPE_SELECT[$bookVariant->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.jenjang') }}
                        </th>
                        <td>
                            {{ $bookVariant->jenjang->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.semester') }}
                        </th>
                        <td>
                            {{ $bookVariant->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.kurikulum') }}
                        </th>
                        <td>
                            {{ $bookVariant->kurikulum->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.halaman') }}
                        </th>
                        <td>
                            {{ $bookVariant->halaman->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.stock') }}
                        </th>
                        <td>
                            {{ $bookVariant->stock }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.unit') }}
                        </th>
                        <td>
                            {{ $bookVariant->unit->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.price') }}
                        </th>
                        <td>
                            {{ $bookVariant->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bookVariant.fields.cost') }}
                        </th>
                        <td>
                            {{ $bookVariant->cost }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.book-variants.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection